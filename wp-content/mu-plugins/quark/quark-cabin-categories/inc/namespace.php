<?php
/**
 * Namespace functions.
 *
 * @package quark-cabin-categories
 */

namespace Quark\CabinCategories;

use WP_Post;

use function Quark\Checkout\get_checkout_url;
use function Quark\Core\format_price;
use function Quark\Departures\get as get_departure;
use function Quark\Localization\get_currencies;
use function Quark\ShipDecks\get as get_ship_deck;
use function Quark\Softrip\Occupancies\add_supplemental_and_mandatory_price;
use function Quark\Softrip\Occupancies\get_cabin_category_post_ids_by_departure;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\get_lowest_price_by_cabin_category_and_departure;
use function Quark\Softrip\Occupancies\get_lowest_price_by_cabin_category_and_departure_and_promotion_code;
use function Quark\Softrip\Occupancies\get_occupancies_by_cabin_category_and_departure;
use function Quark\Softrip\Occupancies\get_occupancy_data_by_id;
use function Quark\Softrip\Occupancies\is_occupancy_on_sale;
use function Quark\Softrip\OccupancyPromotions\get_lowest_price as get_occupancy_promotion_lowest_price;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\get_promotions_by_id;

use const Quark\Localization\DEFAULT_CURRENCY;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

const POST_TYPE            = 'qrk_cabin_category';
const CABIN_CLASS_TAXONOMY = 'qrk_cabin_class';
const CACHE_KEY            = POST_TYPE;
const CACHE_GROUP          = POST_TYPE;
const AVAILABLE_STATUS     = 'A';
const UNAVAILABLE_STATUS   = 'U';
const SOLD_OUT_STATUS      = 'S';
const ON_REQUEST_STATUS    = 'R';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_cabin_category_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_cabin_classes_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_cabin_classes_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Admin stuff.
	if ( is_admin() || ( defined( 'WP_CLI' ) && true === WP_CLI ) ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/cabin-categories.php';
		require_once __DIR__ . '/../custom-fields/cabin-classes.php';

		// Taxonomy menu position.
		add_action( 'admin_menu', __NAMESPACE__ . '\\set_cabin_classes_taxonomy_menu_position' );
	}
}

/**
 * Register Cabin category post type.
 *
 * @return void
 */
function register_cabin_category_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Cabin Categories',
			'singular_name'      => 'Cabin Category',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Cabin Category',
			'edit_item'          => 'Edit Cabin Category',
			'new_item'           => 'New Cabin Category',
			'view_item'          => 'View Cabin Category',
			'search_items'       => 'Search Cabin Categories',
			'not_found'          => 'No Cabin Categories found',
			'not_found_in_trash' => 'No Cabin Categories found in Trash',
			'parent_item_colon'  => 'Parent Cabin Category:',
			'menu_name'          => 'Cabin Categories',
		],
		'public'              => false,
		'show_in_rest'        => false,
		'menu_icon'           => 'dashicons-category',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
		],
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=qrk_ship',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Cabin Classes taxonomy.
 *
 * @return void
 */
function register_cabin_classes_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Cabin Classes',
		'singular_name'              => 'Cabin Class',
		'search_items'               => 'Search Cabin Classes',
		'popular_items'              => 'Popular Cabin Classes',
		'all_items'                  => 'All Cabin Classes',
		'parent_item'                => 'Parent Cabin Class',
		'parent_item_colon'          => 'Parent Cabin Class:',
		'edit_item'                  => 'Edit Cabin Class',
		'update_item'                => 'Update Cabin Class',
		'add_new_item'               => 'Add New Cabin Class',
		'new_item_name'              => 'New Cabin Class',
		'separate_items_with_commas' => 'Separate Cabin Classes with commas',
		'add_or_remove_items'        => 'Add or remove Cabin Classes',
		'choose_from_most_used'      => 'Choose from the most used Cabin Classes',
		'menu_name'                  => 'Cabin Classes',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( CABIN_CLASS_TAXONOMY, (array) apply_filters( 'qe_cabin_classes_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Cabin Categories post type for taxonomy.
	$post_types[] = POST_TYPE;

	// Return modified array.
	return $post_types;
}

/**
 * Set Cabin class taxonomy menu position.
 *
 * @return void
 */
function set_cabin_classes_taxonomy_menu_position(): void {
	// Add taxonomy page under CPT.
	add_submenu_page(
		'edit.php?post_type=qrk_ship',
		'Cabin Classes',
		'Cabin Classes',
		'manage_categories',
		'edit-tags.php?taxonomy=' . CABIN_CLASS_TAXONOMY . '&post_type=' . SHIP_POST_TYPE,
	);
}

/**
 * Busts cache for this post type.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_post_cache( int $post_id = 0 ): void {
	// Get post type.
	$post_type = get_post_type( $post_id );

	// Check for post type.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_cabin_category_post_cache_busted', $post_id );
}

/**
 * Get a Cabin Category.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_thumbnail: int,
 *     post_meta: mixed[],
 *     post_taxonomies: mixed[]
 * }
 */
function get( int $post_id = 0 ): array {
	// Get post ID.
	if ( 0 === $post_id ) {
		$post_id = absint( get_the_ID() );
	}

	// Check for cached version.
	$cache_key    = CACHE_KEY . "_$post_id";
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value['post'] ) && $cached_value['post'] instanceof WP_Post ) {
		return [
			'post'            => $cached_value['post'],
			'post_thumbnail'  => $cached_value['post_thumbnail'] ?? 0,
			'post_meta'       => $cached_value['post_meta'] ?? [],
			'post_taxonomies' => $cached_value['post_taxonomies'] ?? [],
		];
	}

	// Get post.
	$post = get_post( $post_id );

	// Check for post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'            => null,
			'post_thumbnail'  => 0,
			'post_meta'       => [],
			'post_taxonomies' => [],
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
		'post_thumbnail'  => get_post_thumbnail_id( $post ) ? : 0,
		'post_meta'       => [],
		'post_taxonomies' => [],
	];

	// Get all post meta.
	$meta = get_post_meta( $post->ID );

	// Check for post meta.
	if ( ! empty( $meta ) && is_array( $meta ) ) {
		$data['post_meta'] = array_filter(
			array_map(
				fn( $value ) => maybe_unserialize( $value[0] ?? '' ),
				$meta
			),
			fn( $key ) => ! str_starts_with( $key, '_' ),
			ARRAY_FILTER_USE_KEY
		);
	}

	// Taxonomy terms.
	global $wpdb;
	$taxonomy_terms = $wpdb->get_results(
		$wpdb->prepare(
			"
			SELECT
				t.*,
				tt.taxonomy,
				tt.description,
				tt.parent
			FROM
				$wpdb->term_relationships AS tr
			LEFT JOIN
				$wpdb->term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			LEFT JOIN
				$wpdb->terms AS t ON t.term_id = tt.term_taxonomy_id
			WHERE
				tr.object_id = %d
			ORDER BY
				t.name ASC
			",
			[
				$post->ID,
			]
		),
		ARRAY_A
	);

	// Check for taxonomy terms.
	if ( ! empty( $taxonomy_terms ) ) {
		foreach ( $taxonomy_terms as $taxonomy_term ) {
			if ( ! array_key_exists( $taxonomy_term['taxonomy'], $data['post_taxonomies'] ) ) {
				$data['post_taxonomies'][ $taxonomy_term['taxonomy'] ] = [];
			}
			$data['post_taxonomies'][ $taxonomy_term['taxonomy'] ][] = $taxonomy_term;
		}
	}

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}

/**
 * Get cabin categories data.
 *
 * @param int $cabin_id Cabin ID.
 *
 * @return array{
 *    id: string,
 *    title: string,
 *    image_id: int,
 *    description: string,
 *    details: array{
 *        size_from: string,
 *        size_to: string,
 *        occupancy_from: string,
 *        occupancy_to: string,
 *        bed_configuration: string,
 *        class: string,
 *        location: string,
 *    }
 * }|array{}
 */
function get_cabin_categories_data( int $cabin_id = 0 ): array {
	// Get the cabin category.
	$cabin_category = get( $cabin_id );

	// Get the post, post meta, post taxonomies and post thumbnail.
	$cabin_category_post       = $cabin_category['post'];
	$cabin_category_meta       = $cabin_category['post_meta'];
	$cabin_category_taxonomies = $cabin_category['post_taxonomies'];
	$cabin_category_thumbnail  = $cabin_category['post_thumbnail'];

	// Check for post.
	if ( ! $cabin_category_post instanceof WP_Post ) {
		return [];
	}

	// Prepare cabin option data.
	$cabin_category_data = [
		'id'          => $cabin_category_post->post_name,
		'title'       => strval( $cabin_category_meta['cabin_name'] ),
		'image_id'    => $cabin_category_thumbnail,
		'description' => strval( apply_filters( 'the_content', $cabin_category_post->post_content ) ),
		'details'     => [
			'size_from'         => isset( $cabin_category_meta['cabin_category_size_range_from'] ) ? strval( $cabin_category_meta['cabin_category_size_range_from'] ) : '',
			'size_to'           => isset( $cabin_category_meta['cabin_category_size_range_to'] ) ? strval( $cabin_category_meta['cabin_category_size_range_to'] ) : '',
			'occupancy_from'    => isset( $cabin_category_meta['cabin_occupancy_pax_range_from'] ) ? strval( $cabin_category_meta['cabin_occupancy_pax_range_from'] ) : '',
			'occupancy_to'      => isset( $cabin_category_meta['cabin_occupancy_pax_range_to'] ) ? strval( $cabin_category_meta['cabin_occupancy_pax_range_to'] ) : '',
			'bed_configuration' => isset( $cabin_category_meta['cabin_bed_configuration'] ) ? strval( apply_filters( 'the_content', $cabin_category_meta['cabin_bed_configuration'] ) ) : '',
			'class'             => '',
			'location'          => '',
		],
	];

	// Add Class if available.
	if ( ! empty( $cabin_category_taxonomies ) && is_array( $cabin_category_taxonomies[ CABIN_CLASS_TAXONOMY ] ) && isset( $cabin_category_taxonomies[ CABIN_CLASS_TAXONOMY ][0]['name'] ) ) {
		$cabin_category_data['details']['class'] = $cabin_category_taxonomies[ CABIN_CLASS_TAXONOMY ][0]['name'];
	}

	// Add location if available.
	if ( ! empty( $cabin_category_meta['related_decks'] ) && is_array( $cabin_category_meta['related_decks'] ) ) {
		// Prepare location data.
		$related_decks_ids = array_map( 'absint', $cabin_category_meta['related_decks'] );

		// Loop through the related decks IDs.
		$locations = [];

		// Loop through the related decks IDs.
		foreach ( $related_decks_ids as $related_deck_id ) {
			$related_deck = get_ship_deck( $related_deck_id );

			// Get the post and post meta.
			$related_deck_meta = $related_deck['post_meta'];

			// Check for post meta.
			if ( empty( $related_deck_meta['deck_name'] ) ) {
				continue;
			}

			// Prepare location data.
			$locations[] = $related_deck_meta['deck_name'];
		}

		// Prepare comma separated location.
		$cabin_locations = implode( ', ', $locations );

		// Add location to details.
		$cabin_category_data['details']['location'] = $cabin_locations;
	}

	// Return cabin category data.
	return $cabin_category_data;
}

/**
 * Get cabin categories data by departure.
 *
 * @param int    $departure_post_id Departure Post ID.
 * @param string $currency          Currency.
 *
 * @return array<int|string, array{
 *     name: string,
 *     description: string,
 *     gallery: mixed,
 *     cabin_code: string,
 *     type: string,
 *     sort_priority: int,
 *     specifications: array{
 *         availability_status: string,
 *         availability_description: string,
 *         spaces_available: int,
 *         occupancy: string,
 *         location: string,
 *         size: string,
 *         bed_configuration: string
 *     },
 *     from_price: array{
 *         discounted_price: string,
 *         original_price: string,
 *     },
 *     occupancies: array<int<0, max>, array<string, mixed>>,
 *     promo_codes: array<string>
 * }>
 */
function get_cabin_details_by_departure( int $departure_post_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Bail out if no departure post ID.
	if ( empty( $departure_post_id ) ) {
		return [];
	}

	// Get the cabin categories post IDs.
	$cabin_category_post_ids = get_cabin_category_post_ids_by_departure( $departure_post_id );

	// Bail out if no cabin categories post IDs.
	if ( empty( $cabin_category_post_ids ) ) {
		return [];
	}

	// Initialize cabin categories data to be returned.
	$cabin_categories_data = [];

	// Loop through the cabin categories post IDs.
	foreach ( $cabin_category_post_ids as $cabin_category_post_id ) {
		// Get cabin category data.
		$cabin_data = get( $cabin_category_post_id );

		// Check if cabin category data is empty.
		if ( empty( $cabin_data['post'] ) || ! $cabin_data['post'] instanceof WP_Post ) {
			continue;
		}

		// Get cabin code from meta.
		$cabin_code = strval( $cabin_data['post_meta']['cabin_category_id'] ?? '' );

		// Skip if no cabin code.
		if ( empty( $cabin_code ) ) {
			continue;
		}

		// Get availability status.
		$availability_status = get_cabin_availability_status( $departure_post_id, $cabin_category_post_id );

		// If unavailable, skip.
		if ( UNAVAILABLE_STATUS === $availability_status ) {
			continue;
		}

		// Get lowest price for this cabin.
		$lowest_price = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, $currency );

		// Format price.
		$formatted_price['discounted_price'] = format_price( $lowest_price['discounted'], $currency );
		$formatted_price['original_price']   = format_price( $lowest_price['original'], $currency );

		// Get available cabin spaces.
		$cabin_spaces_available   = get_available_cabin_spaces( $departure_post_id, $cabin_category_post_id );
		$availability_description = get_availability_status_description( $availability_status );

		// Cabin class data.
		$cabin_class_data = get_cabin_category_class_data( $cabin_category_post_id );

		// Setup cabin structure data.
		$struct = [
			'name'           => strval( $cabin_data['post_meta']['cabin_name'] ?? '' ),
			'cabin_code'     => $cabin_code,
			'description'    => $cabin_data['post']->post_content,
			'gallery'        => $cabin_data['post_meta']['cabin_images'] ?? [],
			'type'           => $cabin_class_data['name'] ?? '',
			'sort_priority'  => $cabin_class_data['sort_priority'] ?? 0,
			'specifications' => [
				'availability_status'      => $availability_status,
				'availability_description' => $availability_description,
				'spaces_available'         => $cabin_spaces_available,
				'occupancy'                => get_pax_range( $cabin_category_post_id ),
				'location'                 => get_cabin_category_location( $cabin_category_post_id ),
				'size'                     => get_size_range( $cabin_category_post_id ),
				'bed_configuration'        => strval( $cabin_data['post_meta']['cabin_bed_configuration'] ?? '' ),
			],
			'from_price'     => $formatted_price,
			'occupancies'    => [],
			'promo_codes'    => [],
		];

		// Get all occupancies for this cabin and departure.
		$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );

		// Loop through the occupancies.
		foreach ( $occupancies as $occupancy ) {
			// Bail if empty.
			if ( empty( $occupancy['id'] ) ) {
				continue;
			}

			// Skip if not on sale.
			if ( ! is_occupancy_on_sale( $occupancy['availability_status'] ) ) {
				continue;
			}

			// Get occupancy detail.
			$occupancy_detail = get_occupancy_detail( $occupancy['id'], $departure_post_id, $currency );

			// Validate occupancy detail.
			if ( empty( $occupancy_detail ) ) {
				continue;
			}

			// Add occupancy detail to occupancies.
			$struct['occupancies'][] = $occupancy_detail;
		}

		// Get promotions.
		if ( ! empty( $occupancies ) ) {
			// First occupancy.
			$occupancy = $occupancies[0];

			// Bail if not valid.
			if ( empty( $occupancy['id'] ) ) {
				continue;
			}

			// Get occupancy promotions.
			$occupancy_promotions = get_occupancy_promotions_by_occupancy( $occupancy['id'] );

			// Validate.
			if ( empty( $occupancy_promotions ) ) {
				continue;
			}

			// Loop through the occupancy promotions.
			foreach ( $occupancy_promotions as $occupancy_promotion ) {
				// Get promotion detail.
				$promotion_detail = get_promotions_by_id( $occupancy_promotion['promotion_id'] );

				// Validate promotion detail.
				if ( empty( $promotion_detail ) ) {
					continue;
				}

				// Pick the first item.
				$promotion_detail = $promotion_detail[0];

				// Check for currency.
				if ( ! empty( $promotion_detail['currency'] ) && $currency !== $promotion_detail['currency'] ) {
					continue;
				}

				// Add promotion detail to cabin structure.
				$struct['promo_codes'][] = $promotion_detail['code'];
			}
		}

		// Add cabin category data to cabin categories data.
		$cabin_categories_data[ $cabin_code ] = $struct;
	}

	// Sort cabin categories data by sort priority.
	uasort(
		$cabin_categories_data,
		function ( $a, $b ) {
			return $a['sort_priority'] <=> $b['sort_priority'];
		}
	);

	// Return cabin details array.
	return $cabin_categories_data;
}

/**
 * Get cabin price data by departure.
 *
 * @param int    $departure_id Departure Post ID.
 * @param string $currency     Currency.
 *
 * @return array<string, array{
 *    name: string,
 *    availability_status: string,
 *    availability_description: string,
 *    spaces_available: int,
 *    checkout_url: string,
 *    brochure_price: string,
 *    promos: array{}|string[],
 *    type: string,
 *    sort_priority: int,
 * }>
 */
function get_cabin_price_data_by_departure( int $departure_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Bail out if no departure ID.
	if ( empty( $departure_id ) ) {
		return [];
	}

	// Check for cabins.
	$cabin_ids = get_cabin_category_post_ids_by_departure( $departure_id );

	// Bail out if no cabin IDs.
	if ( empty( $cabin_ids ) ) {
		return [];
	}

	// Get departure data.
	$departure = get_departure( $departure_id );

	// Validate departure data.
	if ( empty( $departure['post'] ) || ! $departure['post'] instanceof WP_Post ) {
		return [];
	}

	// Available promos.
	$available_promos = [];

	// Get Available Promos for the Departure.
	if ( ! empty( $departure['post_meta']['promotion_codes'] ) && is_array( $departure['post_meta']['promotion_codes'] ) ) {
		$promotion_codes = $departure['post_meta']['promotion_codes'];

		// Get promo details.
		foreach ( $promotion_codes as $promo_code ) {
			$promo_data = get_promotions_by_code( strval( $promo_code ) );

			// Bail if no promo data.
			if ( empty( $promo_data ) ) {
				continue;
			}

			// First item.
			$promo_data = $promo_data[0];

			// Check for currency & discount value - exclude free promotions.
			if ( ! empty( $promo_data['currency'] ) && $currency !== $promo_data['currency'] ) {
				continue;
			}

			// Check for promo data.
			$available_promos[ strval( $promo_code ) ] = $promo_data;
		}
	}

	// Prepare the cabin price data.
	$cabin_price_data = [];

	// Loop through cabin_ids.
	foreach ( $cabin_ids as $cabin_id ) {
		// Get cabin category data.
		$cabin_data = get( absint( $cabin_id ) );

		// Check if cabin category data is empty.
		if ( empty( $cabin_data['post'] ) || ! $cabin_data['post'] instanceof WP_Post ) {
			continue;
		}

		// Get cabin code from meta.
		$cabin_code = strval( $cabin_data['post_meta']['cabin_category_id'] ?? '' );

		// Skip if no cabin code.
		if ( empty( $cabin_code ) ) {
			continue;
		}

		// Get availability status.
		$availability_status = get_cabin_availability_status( $departure_id, $cabin_id );

		// Skip if unavailable.
		if ( UNAVAILABLE_STATUS === $availability_status ) {
			continue;
		}

		// Get availability description.
		$cabin_spaces_available   = get_available_cabin_spaces( $departure_id, $cabin_id );
		$availability_description = get_availability_status_description( $availability_status );

		// Get cabin class data.
		$cabin_class_data = get_cabin_category_class_data( $cabin_id );

		// Prepare the cabin data.
		$cabin_price_data[ $cabin_code ] = [
			'name'                     => strval( $cabin_data['post_meta']['cabin_name'] ?? '' ),
			'availability_status'      => $availability_status,
			'availability_description' => $availability_description,
			'spaces_available'         => $cabin_spaces_available,
			'promos'                   => [],
			'checkout_url'             => get_checkout_url( $departure_id, $cabin_id, $currency ),
			'type'                     => $cabin_class_data['name'] ?? '',
			'sort_priority'            => $cabin_class_data['sort_priority'] ?? 0,
			'brochure_price'           => '',
		];

		// Get the lowest price for the cabin.
		$cabin_price = get_lowest_price_by_cabin_category_and_departure( $cabin_id, $departure_id, $currency );

		// Skip if no cabin price.
		if ( empty( $cabin_price['original'] ) ) {
			continue;
		}

		// Set the brochure price.
		$cabin_price_data[ $cabin_code ]['brochure_price'] = format_price( $cabin_price['original'], $currency );

		// Loop through available_promos for each promo.
		foreach ( $available_promos as $promo_code => $promo_data ) {
			$discounted_lowest_price = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_id, $departure_id, $promo_code, $currency );

			// Skip if no discounted lowest price.
			if ( empty( $discounted_lowest_price ) ) {
				continue;
			}

			// Add promo to cabin data.
			$cabin_price_data[ $cabin_code ]['promos'][ $promo_code ] = format_price( $discounted_lowest_price, $currency );
		}
	}

	// Sort cabin data by sort priority.
	uasort(
		$cabin_price_data,
		function ( $a, $b ) {
			return $a['sort_priority'] <=> $b['sort_priority'];
		}
	);

	// Return cabin price data.
	return $cabin_price_data;
}

/**
 * Get cabin category class.
 *
 * @param int $cabin_category_id Cabin category ID.
 *
 * @return array{}|array{
 *   name: string,
 *   sort_priority: int,
 *   description: string,
 * }
 */
function get_cabin_category_class_data( int $cabin_category_id = 0 ): array {
	// Setup default return value.
	$class = [];

	// Bail out if no cabin category ID.
	if ( empty( $cabin_category_id ) ) {
		return $class;
	}

	// Get cabin category data.
	$cabin_category = get( $cabin_category_id );

	// Get the post taxonomies.
	$taxonomy_data = $cabin_category['post_taxonomies'][ CABIN_CLASS_TAXONOMY ] ?? [];

	// Bail out if no taxonomy data.
	if ( empty( $taxonomy_data ) || ! is_array( $taxonomy_data ) ) {
		return $class;
	}

	// Get the first taxonomy data.
	$taxonomy_data = $taxonomy_data[0] ?? [];

	// Bail if not array or key does not exist.
	if ( ! is_array( $taxonomy_data ) || empty( $taxonomy_data['name'] ) ) {
		return $class;
	}

	// Sort priority.
	$sort_priority = absint( get_term_meta( $taxonomy_data['term_id'], 'sort_priority', true ) );

	// Return class data.
	return [
		'name'          => strval( $taxonomy_data['name'] ),
		'sort_priority' => $sort_priority,
		'description'   => strval( $taxonomy_data['description'] ),
	];
}

/**
 * Get cabin category location.
 *
 * @param int $cabin_category_id Cabin category post ID.
 *
 * @return string
 */
function get_cabin_category_location( int $cabin_category_id = 0 ): string {
	// Setup default return value.
	$location = '';

	// Bail out if no cabin category ID.
	if ( empty( $cabin_category_id ) ) {
		return $location;
	}

	// Get cabin category data.
	$cabin_category = get( $cabin_category_id );

	// Get the post meta.
	$cabin_category_meta = $cabin_category['post_meta'];

	// Bail out if no post meta.
	if ( empty( $cabin_category_meta ) || ! is_array( $cabin_category_meta ) ) {
		return $location;
	}

	// Get the related decks.
	$related_decks = $cabin_category_meta['related_decks'] ?? [];

	// Bail out if no related decks.
	if ( empty( $related_decks ) || ! is_array( $related_decks ) ) {
		return $location;
	}

	// Prepare location data.
	$locations = [];

	// Loop through the related decks.
	foreach ( $related_decks as $related_deck_id ) {
		// Get the ship deck data.
		$related_deck = get_ship_deck( $related_deck_id );

		// Get the post meta.
		$related_deck_meta = $related_deck['post_meta'];

		// Bail out if no post meta.
		if ( empty( $related_deck_meta ) || ! is_array( $related_deck_meta ) ) {
			continue;
		}

		// Get the deck name.
		$deck_name = $related_deck_meta['deck_name'] ?? '';

		// Bail out if no deck name.
		if ( empty( $deck_name ) ) {
			continue;
		}

		// Add deck name to locations.
		$locations[] = strval( $deck_name );
	}

	// Prepare comma separated location.
	$location = implode( ', ', $locations );

	// Return location.
	return $location;
}

/**
 * Get formatted size range.
 *
 * @param string $from_size From size.
 * @param string $to_size   To size.
 *
 * @return string
 */
function get_formatted_size_range( string $from_size = '', string $to_size = '' ): string {
	// Setup default return value.
	$size_range = '';

	// Bail out if no from size.
	if ( empty( $from_size ) && empty( $to_size ) ) {
		return $size_range;
	}

	// Prepare size range.
	$size_range = $from_size;

	// Add to size if available.
	if ( ! empty( $to_size ) ) {
		$size_range .= ' - ' . $to_size;
	}

	// Return size range.
	return $size_range;
}

/**
 * Get cabin pax range from meta.
 *
 * @param int $cabin_category_post_id Cabin category post ID.
 *
 * @return string
 */
function get_pax_range( int $cabin_category_post_id = 0 ): string {
	// Bail out if no cabin category post ID.
	if ( empty( $cabin_category_post_id ) ) {
		return '';
	}

	// Get cabin category data.
	$cabin_category = get( $cabin_category_post_id );

	// Bail if no cabin category data.
	if ( empty( $cabin_category['post_meta'] ) ) {
		return '';
	}

	// Get the pax range.
	$from = strval( $cabin_category['post_meta']['cabin_occupancy_pax_range_from'] ?? '' );
	$to   = strval( $cabin_category['post_meta']['cabin_occupancy_pax_range_to'] ?? '' );

	// Return the formatted range.
	return get_formatted_size_range( $from, $to );
}

/**
 * Get cabin size range from meta.
 *
 * @param int $cabin_category_post_id Cabin category post ID.
 *
 * @return string
 */
function get_size_range( int $cabin_category_post_id = 0 ): string {
	// Bail out if no cabin category post ID.
	if ( empty( $cabin_category_post_id ) ) {
		return '';
	}

	// Get cabin category data.
	$cabin_category = get( $cabin_category_post_id );

	// Bail if no cabin category data.
	if ( empty( $cabin_category['post_meta'] ) ) {
		return '';
	}

	// Get the size range.
	$from = strval( $cabin_category['post_meta']['cabin_category_size_range_from'] ?? '' );
	$to   = strval( $cabin_category['post_meta']['cabin_category_size_range_to'] ?? '' );

	// Return the formatted range.
	return get_formatted_size_range( $from, $to );
}

/**
 * Get occupancy detail.
 *
 * @param int    $occupancy_id The occupancy ID.
 * @param int    $departure_post_id The departure post ID.
 * @param string $currency The currency code.
 *
 * @return array{}|array{
 *   name: string,
 *   description: string,
 *   no_of_guests: string,
 *   price: array{
 *     original_price: string,
 *     discounted_price: string,
 *   },
 *   checkout_url: string,
 * }
 */
function get_occupancy_detail( int $occupancy_id = 0, int $departure_post_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Uppercase currency.
	$currency = strtoupper( $currency );

	// Bail if empty.
	if ( empty( $occupancy_id ) || ! in_array( $currency, get_currencies(), true ) ) {
		return [];
	}

	// Get the occupancy data.
	$occupancy_data = get_occupancy_data_by_id( $occupancy_id );

	// Bail if empty.
	if ( empty( $occupancy_data ) ) {
		return [];
	}

	// First item.
	$occupancy = $occupancy_data[0];

	// Bail if empty.
	if ( ! is_array( $occupancy ) || empty( $occupancy['id'] ) ) {
		return [];
	}

	// Mask.
	$mask = $occupancy['mask'];

	// Get the description and pax count by mask.
	$description_and_pax_count = get_description_and_pax_count_by_mask( $mask );

	// Get price.
	$original_price = absint( $occupancy[ 'price_per_person_' . strtolower( $currency ) ] );

	// Get discounted price.
	$discounted_price = get_occupancy_promotion_lowest_price( $occupancy['id'], $currency );

	// If empty, set original price.
	if ( empty( $discounted_price ) ) {
		$discounted_price = $original_price;
	}

	// Add supplemental and mandatory price.
	$price_with_supplement_mandatory = add_supplemental_and_mandatory_price(
		[
			'original'   => $original_price,
			'discounted' => $discounted_price,
		],
		$departure_post_id,
		$currency
	);

	// Prepare data.
	$detail = [
		'name'         => $occupancy['mask'],
		'description'  => $description_and_pax_count['description'],
		'no_of_guests' => strval( $description_and_pax_count['pax_count'] ),
		'price'        => [
			'original_price'   => format_price( $price_with_supplement_mandatory['original'], $currency ),
			'discounted_price' => format_price( $price_with_supplement_mandatory['discounted'], $currency ),
		],
		'checkout_url' => get_checkout_url( $departure_post_id, $occupancy['cabin_category_post_id'], $currency, $mask ),
	];

	// Return the occupancy detail.
	return $detail;
}

/**
 * Get cabin availability status.
 *
 * @param int $departure_post_id      Departure post ID.
 * @param int $cabin_category_post_id Cabin category post ID.
 *
 * @return string
 */
function get_cabin_availability_status( int $departure_post_id = 0, int $cabin_category_post_id = 0 ): string {
	// Bail if empty.
	if ( empty( $departure_post_id ) || empty( $cabin_category_post_id ) ) {
		return UNAVAILABLE_STATUS;
	}

	// Fetch all occupancies for this cabin and departure.
	$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );

	// Check if occupancies is empty.
	if ( empty( $occupancies ) ) {
		return UNAVAILABLE_STATUS;
	}

	// Get cabin spaces available.
	$spaces_available = get_available_cabin_spaces( $departure_post_id, $cabin_category_post_id );

	// Check if spaces available.
	if ( $spaces_available > 0 ) {
		return AVAILABLE_STATUS;
	}

	// Initialize flag for sold out.
	$are_all_sold_out = true;

	// Loop through the occupancies.
	foreach ( $occupancies as $occupancy ) {
		// Bail if empty.
		if ( empty( $occupancy['id'] ) || empty( $occupancy['availability_status'] ) ) {
			continue;
		}

		// Sale status.
		$sale_status            = $occupancy['availability_status'];
		$is_occupancy_available = is_occupancy_on_sale( $sale_status );

		// Check if occupancy available.
		if ( $is_occupancy_available ) {
			$are_all_sold_out = false;
			break;
		}
	}

	// Check if all are sold out.
	if ( $are_all_sold_out ) {
		return SOLD_OUT_STATUS;
	}

	// Finally, return on request status.
	return ON_REQUEST_STATUS;
}

/**
 * Get cabin spaces available.
 *
 * @param int $departure_post_id      Departure post ID.
 * @param int $cabin_category_post_id Cabin category post ID.
 *
 * @return int
 */
function get_available_cabin_spaces( int $departure_post_id = 0, int $cabin_category_post_id = 0 ): int {
	// Bail if empty.
	if ( empty( $departure_post_id ) || empty( $cabin_category_post_id ) ) {
		return 0;
	}

	// Get departure post.
	$departure_post = get_departure( $departure_post_id );

	// Validate departure post.
	if ( ! $departure_post['post'] instanceof WP_Post || empty( $departure_post['post_meta'] ) ) {
		return 0;
	}

	// Meta key for cabin spaces available.
	$meta_key = 'cabin_spaces_available_' . $cabin_category_post_id;

	// Check if meta key exists.
	if ( ! isset( $departure_post['post_meta'][ $meta_key ] ) ) {
		return 0;
	}

	// Get cabin spaces available.
	$spaces_available = absint( $departure_post['post_meta'][ $meta_key ] );

	// Return spaces available.
	return $spaces_available;
}

/**
 * Get availability status description.
 *
 * @param string $status Availability status.
 *
 * @return string
 */
function get_availability_status_description( string $status = '' ): string {
	// Check status.
	switch ( $status ) {

		// Available.
		case AVAILABLE_STATUS:
			return __( 'Available', 'qrk' );

		// Unavailable.
		case UNAVAILABLE_STATUS:
			return __( 'Unavailable', 'qrk' );

		// Sold out.
		case SOLD_OUT_STATUS:
			return __( 'Sold Out', 'qrk' );

		// On request.
		case ON_REQUEST_STATUS:
			return __( 'Please Call', 'qrk' );

		// Default.
		default:
			return '';
	}
}
