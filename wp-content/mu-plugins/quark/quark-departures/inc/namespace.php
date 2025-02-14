<?php
/**
 * Namespace functions.
 *
 * @package quark-departures
 */

namespace Quark\Departures;

use WP_Post;
use WP_Term_Query;

use function Quark\CabinCategories\get_cabin_details_by_departure;
use function Quark\Core\format_price;
use function Quark\Expeditions\get_region_terms;
use function Quark\Itineraries\get_starting_from_location;
use function Quark\Itineraries\get_end_location;
use function Quark\Itineraries\get_included_transfer_package_details;
use function Quark\Itineraries\get_policy_banner_details;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Expeditions\get_itineraries;
use function Quark\Ships\get as get_ship;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Departures\get_end_date;
use function Quark\Softrip\Departures\get_lowest_price;
use function Quark\Softrip\Departures\get_related_ship;
use function Quark\Softrip\Departures\get_start_date;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\AdventureOptions\get_adventure_option_by_departure_post_id;
use function Quark\AdventureOptions\get as get_adventure_option_post_data;
use function Quark\CabinCategories\get_cabin_price_data_by_departure;
use function Quark\Itineraries\get_tax_type_details;
use function Quark\Leads\get_request_a_quote_url;
use function Quark\Localization\get_currencies;

use function Travelopia\Multilingual\get_post_translations;
use function Travelopia\Multilingual\get_term_translations;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\CabinCategories\AVAILABLE_STATUS;
use const Quark\CabinCategories\SOLD_OUT_STATUS;
use const Quark\Expeditions\EXPEDITION_CATEGORY_TAXONOMY;
use const Quark\Localization\DEFAULT_CURRENCY;

const POST_TYPE                = 'qrk_departure';
const SPOKEN_LANGUAGE_TAXONOMY = 'qrk_spoken_language';
const PROMOTION_TAG            = 'qrk_promotion_tags';
const CACHE_KEY                = POST_TYPE;
const CACHE_GROUP              = POST_TYPE;
const FLIGHT_SEEING_TID        = 289; // Flight seeing Adventure Option Term ID.
const ULTRAMARINE_SHIP_CODE    = 'ULT'; // Ultramarine Ship Code.

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_departure_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_spoken_language_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_promotion_tag_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_spoken_language_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_promotion_tag_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Bust cache for departure card data.
	add_action( 'qe_departure_post_cache_busted', __NAMESPACE__ . '\\bust_card_data_cache' );
	add_action( 'qe_expedition_post_cache_busted', __NAMESPACE__ . '\\bust_card_data_cache_on_expedition_update' );
	add_action( 'qe_itinerary_post_cache_busted', __NAMESPACE__ . '\\bust_card_data_cache_on_itinerary_update' );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/departures.php';
		require_once __DIR__ . '/../custom-fields/spoken-languages.php';
		require_once __DIR__ . '/../custom-fields/promotion-tags.php';
	}
}

/**
 * Register Departure post type.
 *
 * @return void
 */
function register_departure_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Departures',
			'singular_name'      => 'Departure',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Departure',
			'edit_item'          => 'Edit Departure',
			'new_item'           => 'New Departure',
			'view_item'          => 'View Departure',
			'search_items'       => 'Search Departures',
			'not_found'          => 'No Departures found',
			'not_found_in_trash' => 'No Departures found in Trash',
			'parent_item_colon'  => 'Parent Departure:',
			'menu_name'          => 'Departures',
		],
		'public'              => false,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-clipboard',
		'hierarchical'        => false,
		'supports'            => [
			'title',
		],
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'capabilities'        => [
			'create_posts' => 'do_not_allow',
		],
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Spoken Languages taxonomy.
 *
 * @return void
 */
function register_spoken_language_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Spoken Languages',
		'singular_name'              => 'Spoken Language',
		'search_items'               => 'Search Spoken Languages',
		'popular_items'              => 'Popular Spoken Languages',
		'all_items'                  => 'All Spoken Languages',
		'parent_item'                => 'Parent Spoken Language',
		'parent_item_colon'          => 'Parent Spoken Language:',
		'edit_item'                  => 'Edit Spoken Language',
		'update_item'                => 'Update Spoken Language',
		'add_new_item'               => 'Add New Spoken Language',
		'new_item_name'              => 'New Spoken Language',
		'separate_items_with_commas' => 'Separate Spoken Languages with commas',
		'add_or_remove_items'        => 'Add or remove Spoken Languages',
		'choose_from_most_used'      => 'Choose from the most used Spoken Languages',
		'menu_name'                  => 'Spoken Languages',
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
	register_taxonomy( SPOKEN_LANGUAGE_TAXONOMY, (array) apply_filters( 'qe_spoken_language_taxonomy_post_types', [] ), $args );
}

/**
 * Register Promotion Tag taxonomy.
 *
 * @return void
 */
function register_promotion_tag_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Promotion Tags',
		'singular_name'              => 'Promotion Tag',
		'search_items'               => 'Search Promotion Tags',
		'popular_items'              => 'Popular Promotion Tags',
		'all_items'                  => 'All Promotion Tags',
		'parent_item'                => 'Parent Promotion Tag',
		'parent_item_colon'          => 'Parent Promotion Tag:',
		'edit_item'                  => 'Edit Promotion Tag',
		'update_item'                => 'Update Promotion Tag',
		'add_new_item'               => 'Add New Promotion Tag',
		'new_item_name'              => 'New Promotion Tag',
		'separate_items_with_commas' => 'Separate Promotion Tags with commas',
		'add_or_remove_items'        => 'Add or remove Promotion Tags',
		'choose_from_most_used'      => 'Choose from the most used Promotion Tags',
		'menu_name'                  => 'Promotion Tags',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'meta_box_cb'       => false,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( PROMOTION_TAG, (array) apply_filters( 'qe_promotion_tag_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Departure post type for taxonomy.
	$post_types[] = POST_TYPE;

	// Return modified array.
	return $post_types;
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

	// Bail out if not Departure post type.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_departure_post_cache_busted', $post_id );
}

/**
 * Get a Departure.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
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
			'post_meta'       => [],
			'post_taxonomies' => [],
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
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
 * Get paid adventure options.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return array<int, string>
 */
function get_paid_adventure_options( int $post_id = 0 ): array {
	// Get departure.
	$departure = get( $post_id );

	// Check post_meta is not empty.
	if ( ! $departure['post_meta'] ) {
		return [];
	}

	// Get Adventure Options from meta.
	$adventure_options = $departure['post_meta']['adventure_options'] ?? '';

	// Check meta is empty.
	if ( ! $adventure_options ) {
		return [];
	}

	// Get Adventure Options terms.
	$adventure_options_terms = new WP_Term_Query(
		[
			'taxonomy'   => ADVENTURE_OPTION_CATEGORY,
			'include'    => $adventure_options,
			'hide_empty' => false,
			'fields'     => 'id=>name',
		]
	);

	// Check Adventure Options terms are empty.
	if ( ! $adventure_options_terms->terms ) {
		return [];
	}

	// Return Adventure Options.
	return $adventure_options_terms->terms;
}

/**
 * Get Included adventure options.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return array{}|array{
 *     array{
 *         term_id: int,
 *         name: string,
 *         slug: string,
 *         term_group: int,
 *         term_taxonomy_id: int,
 *         taxonomy: string,
 *         description: string,
 *         parent: int,
 *     }
 * }
 */
function get_included_adventure_options( int $post_id = 0 ): array {
	// Get departure.
	$departure                  = get( $post_id );
	$included_adventure_options = [];

	// Check post_meta is not empty.
	if ( empty( $departure['post_meta'] ) ) {
		return $included_adventure_options;
	}

	// Get Expedition ID from meta.
	$expedition_id = $departure['post_meta']['related_expedition'] ?? '';

	// Get ship ID from departure.
	$ship_code = get_post_meta( $post_id, 'ship_code', true );

	// Check meta is empty.
	if ( empty( $expedition_id ) ) {
		return $included_adventure_options;
	}

	// Get Expedition.
	$expedition = get_expedition( absint( $expedition_id ) );

	// Get Adventure Options from meta.
	$adventure_option_post_ids = $expedition['post_meta']['included_activities'] ?? [];

	// Check meta is empty.
	if ( empty( $adventure_option_post_ids ) || ! is_array( $adventure_option_post_ids ) ) {
		return $included_adventure_options;
	}

	// Loop through Adventure Options posts.
	foreach ( $adventure_option_post_ids as $adventure_option_post_id ) {
		$adventure_option_post = get_adventure_option_post_data( absint( $adventure_option_post_id ) );

		// validate Adventure Option taxonomy.
		if ( empty( $adventure_option_post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] ) || ! is_array( $adventure_option_post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] ) ) {
			continue;
		}

		// Loop through Adventure Option taxonomies.
		foreach ( $adventure_option_post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] as $adventure_option ) {
			// Check for term_id.
			if ( ! is_array( $adventure_option ) || empty( $adventure_option['term_id'] ) ) {
				continue;
			}

			// Add Adventure Option to included options.
			$included_adventure_options[ $adventure_option['term_id'] ] = $adventure_option;
		}
	}

	// Remove Flight seeing for all except Ultramarine.
	if ( ULTRAMARINE_SHIP_CODE !== $ship_code && array_key_exists( FLIGHT_SEEING_TID, $included_adventure_options ) ) {
		unset( $included_adventure_options[ FLIGHT_SEEING_TID ] );
	}

	// Return Adventure Options.
	return array_values( $included_adventure_options );
}

/**
 * Get departure languages.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return array<int, string> Languages.
 */
function get_languages( int $post_id = 0 ): array {
	// Get departure.
	$departure = get( $post_id );
	$languages = [];

	// Get languages from post_taxonomy.
	$language_terms = $departure['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] ?? [];

	// Check languages are empty.
	if ( ! $language_terms || ! is_array( $language_terms ) ) {
		return $languages;
	}

	// Loop through languages - get name field.
	foreach ( $language_terms as $language ) {
		$languages[] = $language['name'] ? strval( $language['name'] ) : '';
	}

	// Return languages.
	return $languages;
}

/**
 * Get departure promotion tags.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return string[] Promotion Tags.
 */
function get_promotion_tags( int $post_id = 0 ): array {
	// Get departure.
	$departure      = get( $post_id );
	$promotion_tags = [];

	// Check for post.
	if ( ! $departure['post'] instanceof WP_Post || empty( $departure['post_meta'] ) || empty( $departure['post_meta']['related_promotion_tags'] ) ) {
		return $promotion_tags;
	}

	// Get promotion tags from post_meta.
	$promotion_tag_term_ids = $departure['post_meta']['related_promotion_tags'];

	// Check promotion tags are empty.
	if ( ! is_array( $promotion_tag_term_ids ) ) {
		return $promotion_tags;
	}

	// Get Promotion Tags term names.
	foreach ( $promotion_tag_term_ids as $promotion_tag_term_id ) {
		$promotion_tag_term = get_term( absint( $promotion_tag_term_id ), PROMOTION_TAG, ARRAY_A );

		// Check for term.
		if ( empty( $promotion_tag_term ) || ! is_array( $promotion_tag_term ) || empty( $promotion_tag_term['name'] ) ) {
			continue;
		}

		// Add term name to promotion tags.
		$promotion_tags[] = $promotion_tag_term['name'];
	}

	// Return promotion tags.
	return $promotion_tags;
}

/**
 * Get departure cards details.
 *
 * @param int    $departure_id The departure ID.
 * @param string $currency     The currency.
 *
 * @return array{}|array{
 *     departure_id: int,
 *     expedition_name: string,
 *     expedition_link: string,
 *     expedition_slider_images: int[],
 *     package_id: string,
 *     duration_days: int,
 *     duration_dates: string,
 *     starting_from_location: string,
 *     languages: string,
 *     paid_adventure_options: string[],
 *     lowest_price: array<string, string>,
 *     request_a_quote_url: string,
 *     departure_status: string,
 *     transfer_package_details: array{
 *       title: string,
 *       sets: string[],
 *       price: int,
 *       formatted_price: string,
 *     },
 *     promotion_tags: string[],
 *     ship_name: string,
 *     promotion_banner: string,
 *     promotions: array<string, string>,
 *     banner_details: array{
 *        title: string,
 *        description: string,
 *        icon_id: int,
 *        permalink: string,
 *     },
 *     cabins ?: array<int|string, array{
 *        name: string,
 *        description: string,
 *        gallery: mixed,
 *        cabin_code: string,
 *        type: string,
 *        sort_priority: int,
 *        specifications: array{
 *           availability_status: string,
 *           availability_description: string,
 *           spaces_available: int,
 *           occupancy: string,
 *           location: string,
 *           size: string,
 *           bed_configuration: string
 *       },
 *       from_price: array{
 *          discounted_price: string,
 *          original_price: string,
 *      },
 *      occupancies: array<int<0, max>, array<string, mixed>>,
 *      promo_codes: string[],
 *     }>,
 * }
 */
function get_card_data( int $departure_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Set cache key.
	$cache_key = 'departure_card_data_' . $departure_id . '_' . $currency;

	// Get cached value.
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value ) ) {
		return $cached_value;
	}

	// Get departure.
	$departure = get( $departure_id );

	// Get the related expedition.
	$expedition_post = get_expedition( absint( $departure['post_meta']['related_expedition'] ?? 0 ) );

	// Get the itinerary ID.
	$itinerary_id = absint( $departure['post_meta']['itinerary'] ?? 0 );

	// Check if the expedition post is valid.
	if ( ! $expedition_post['post'] instanceof WP_Post || ! $itinerary_id ) {
		return [];
	}

	// Get Expedition name.
	$expedition_name = $expedition_post['post']->post_title;

	// Prepare the departure details.
	$ship_id   = get_related_ship( $departure_id );
	$ship_data = get_ship( $ship_id );
	$ship_name = '';

	// Validate ship_data.
	if ( ! empty( $ship_data['post'] ) && $ship_data['post'] instanceof WP_Post ) {
		$ship_name = $ship_data['post']->post_title;
	}

	// Initialize hero image ids.
	$hero_slider_image_ids = [];

	// Get hero slider image ids.
	if ( ! empty( $expedition_post['data'] ) && ! empty( $expedition_post['data']['hero_card_slider_image_ids'] ) && is_array( $expedition_post['data']['hero_card_slider_image_ids'] ) ) {
		$hero_slider_image_ids = array_map( 'absint', $expedition_post['data']['hero_card_slider_image_ids'] );
	}

	// Get the lowest price.
	$lowest_price = get_lowest_price( $departure_id, $currency );

	// Format the prices.
	$prices['discounted_price'] = format_price( $lowest_price['discounted'], $currency );
	$prices['original_price']   = format_price( $lowest_price['original'], $currency );

	// Initialize expedition category.
	$expedition_category = [];

	// Prepare expedition category.
	if ( ! empty( $expedition_post['post_taxonomies'][ EXPEDITION_CATEGORY_TAXONOMY ] ) ) {
		// Get expedition categories.
		$expedition_categories = $expedition_post['post_taxonomies'][ EXPEDITION_CATEGORY_TAXONOMY ];

		// Validate expedition categories.
		if ( is_array( $expedition_categories ) ) {
			// Loop through expedition categories.
			foreach ( $expedition_categories as $category ) {
				// Validate category.
				if ( empty( $category['name'] ) ) {
					continue;
				}

				// Add category to expedition category.
				$expedition_category[] = [
					'name'        => $category['name'],
					'description' => $category['description'] ?? '',
				];
			}
		}
	}

	// Get cabins.
	$cabins = get_cabin_details_by_departure( $departure_id, $currency );

	// Departure status.
	$departure_status = get_departure_availability_status( $departure_id, $cabins );

	// Prepare the departure card details.
	$data = [
		'departure_id'             => $departure_id,
		'expedition_name'          => $expedition_name,
		'expedition_link'          => $expedition_post['permalink'],
		'expedition_slider_images' => $hero_slider_image_ids,
		'expedition_categories'    => $expedition_category,
		'package_id'               => strval( $departure['post_meta']['softrip_package_code'] ?? '' ),
		'duration_days'            => absint( $departure['post_meta']['duration'] ?? 0 ),
		'duration_dates'           => get_start_end_departure_date( $departure_id ),
		'starting_from_location'   => get_starting_from_location( $itinerary_id ),
		'languages'                => implode( ', ', get_languages( $departure_id ) ),
		'paid_adventure_options'   => get_paid_adventure_options( $departure_id ),
		'lowest_price'             => $prices,
		'transfer_package_details' => get_included_transfer_package_details( $itinerary_id, $currency ),
		'promotion_tags'           => get_promotion_tags( $departure_id ),
		'ship_name'                => $ship_name,
		'banner_details'           => get_policy_banner_details( $itinerary_id ),
		'cabins'                   => SOLD_OUT_STATUS === $departure_status ? [] : $cabins,
		'promotion_banner'         => get_discount_label( $lowest_price['original'], $lowest_price['discounted'] ),
		'promotions'               => get_promotions_description( $departure_id, $currency ),
		'request_a_quote_url'      => get_request_a_quote_url( $departure_id ),
		'departure_status'         => $departure_status,
	];

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return departure card data.
	return $data;
}

/**
 * Get departure date range string by combining start and end date.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return string
 */
function get_start_end_departure_date( int $post_id = 0 ): string {
	// Bail if no post ID.
	if ( empty( $post_id ) ) {
		return '';
	}

	// Get start and end date.
	$start_date = get_start_date( $post_id );
	$end_date   = get_end_date( $post_id );

	// Parse the dates.
	$start_timestamp = strtotime( $start_date );
	$end_timestamp   = strtotime( $end_date );

	// Validate.
	if ( empty( $start_timestamp ) || empty( $end_timestamp ) ) {
		return '';
	}

	// Extract the date parts from start date.
	$start_year  = gmdate( 'Y', $start_timestamp );
	$start_month = gmdate( 'F', $start_timestamp );
	$start_day   = gmdate( 'j', $start_timestamp );

	// Extract the date parts from end date.
	$end_year  = gmdate( 'Y', $end_timestamp );
	$end_month = gmdate( 'F', $end_timestamp );
	$end_day   = gmdate( 'j', $end_timestamp );

	// Initialize the date range.
	$date_range = '';

	// Prepare the date range.
	if ( $start_year !== $end_year ) {
		// Different years.
		$date_range = sprintf( '%s %s, %s - %s %s, %s', $start_month, $start_day, $start_year, $end_month, $end_day, $end_year );
	} elseif ( $start_month !== $end_month ) {
		$date_range = sprintf( '%s %s - %s %s, %s', $start_month, $start_day, $end_month, $end_day, $start_year );
	} else {
		$date_range = sprintf( '%s %s-%s, %s', $start_month, $start_day, $end_day, $start_year );
	}

	// Return the date range string.
	return $date_range;
}

/**
 * Get departure cards data.
 *
 * @param int[]  $departure_ids The departure IDs.
 * @param string $currency      The currency.
 *
 * @return array{}|array{
 *     array{
 *      departure_id: int,
 *      expedition_name: string,
 *      expedition_link: string,
 *      package_id: string,
 *      duration_days: int,
 *      duration_dates: string,
 *      starting_from_location: string,
 *      languages: string,
 *      paid_adventure_options: string[],
 *      lowest_price: array<string, string>,
 *      request_a_quote_url: string,
 *      departure_status: string,
 *      transfer_package_details: array{
 *        title: string,
 *        sets: array<string>,
 *        price: int,
 *        formatted_price: string,
 *      },
 *      promotion_tags: string[],
 *      ship_name: string,
 *      banner_details: array{
 *         title: string,
 *         description: string,
 *         icon_id: int,
 *         permalink: string,
 *      },
 *      cabins ?: array<int|string, array{
 *         name: string,
 *         description: string,
 *         gallery: mixed,
 *         cabin_code: string,
 *         type: string,
 *         specifications: array{
 *            availability_status: string,
 *            availability_description: string,
 *            spaces_available: int,
 *            occupancy: string,
 *            location: string,
 *            size: string,
 *            bed_configuration: string
 *        },
 *       from_price: array{
 *           discounted_price: string,
 *           original_price: string,
 *       },
 *       occupancies: array<int<0, max>, array<string, mixed>>
 *      }>,
 *    }
 * }
 */
function get_cards_data( array $departure_ids = [], string $currency = DEFAULT_CURRENCY ): array {
	// Prepare the departure cards data.
	$departure_cards = [];

	// Validate departure_ids.
	if ( empty( $departure_ids ) || ! is_array( $departure_ids ) ) {
		return $departure_cards;
	}

	// Loop through departure_ids.
	foreach ( $departure_ids as $departure_id ) {
		// Get departure card data.
		$departure_cards[ $departure_id ] = get_card_data( $departure_id, $currency );
	}

	// Return departure cards data.
	return $departure_cards;
}

/**
 * Bust departure card data and dates rates card data cache on departure post update.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return void
 */
function bust_card_data_cache( int $post_id = 0 ): void {
	// Get currency list.
	$currencies = get_currencies();

	// Loop through currencies.
	foreach ( $currencies as $currency ) {
		wp_cache_delete( 'departure_card_data_' . $post_id . '_' . $currency, CACHE_GROUP );
		wp_cache_delete( 'date_and_rates_card_data_' . $post_id . '_' . $currency, CACHE_GROUP );
	}
}

/**
 * Bust Departure card data cache on Itinerary update.
 *
 * @param int $itinerary_id Itinerary Post ID.
 *
 * @return void
 */
function bust_card_data_cache_on_itinerary_update( int $itinerary_id = 0 ): void {
	// Get departures for the itinerary.
	$departures = get_departures_by_itinerary( $itinerary_id );

	// Check Departures are empty.
	if ( empty( $departures ) ) {
		return;
	}

	// Loop through Departures.
	foreach ( $departures as $departure ) {
		// Bust departure card cache by departure.
		bust_card_data_cache( $departure );
	}
}

/**
 * Bust Departure card data cache on Expedition update.
 *
 * @param int $expedition_id Expedition Post ID.
 *
 * @return void
 */
function bust_card_data_cache_on_expedition_update( int $expedition_id = 0 ): void {
	// Get Itineraries for the Expedition.
	$itineraries = get_itineraries( $expedition_id );

	// Check Itineraries are empty.
	if ( empty( $itineraries ) ) {
		return;
	}

	// Loop through Itineraries.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Bust departure card cache by itinerary.
		bust_card_data_cache_on_itinerary_update( $itinerary['post']->ID );
	}
}

/**
 * Get Dates & Rates Card Data.
 *
 * @param int    $departure_id Departure ID.
 * @param string $currency     Currency.
 *
 * @return array{}|array{
 *     departure_id: int,
 *     region: string,
 *     ship_title: string,
 *     ship_link: string|false,
 *     expedition_title: string,
 *     expedition_link: string,
 *     duration_days: int,
 *     duration_dates: string,
 *     start_location: string,
 *     end_location: string,
 *     languages: string,
 *     included_adventure_options: array{}|array{
 *         array{
 *             title: string,
 *             icon_image_id: int,
 *         }
 *     },
 *     paid_adventure_options: array{}|array{
 *          array{
 *             title: string,
 *             icon_image_id: int,
 *             spaces_available: int,
 *             price_per_person: string,
 *             currency: string,
 *         }
 *     },
 *     transfer_package_details: array{
 *         title: string,
 *         sets: array<string>,
 *         price: int,
 *         formatted_price: string,
 *     },
 *     available_promos: array{}| array{
 *         string: array{
 *             id: int,
 *             code: string,
 *             start_date: string,
 *             end_date: string,
 *             description: string,
 *             discount_type: string,
 *             discount_value: string,
 *             is_pif: int,
 *         },
 *     },
 *     cabin_data: array<string, array{
 *             name: string,
 *             availability_status: string,
 *             availability_description: string,
 *             spaces_available: int,
 *             checkout_url: string,
 *             brochure_price: string,
 *             promos: array{}|string[],
 *             type: string,
 *             sort_priority: int,
 *          }
 *     >,
 * }
 */
function get_dates_rates_card_data( int $departure_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Set cache key.
	$cache_key = 'date_and_rates_card_data_' . $departure_id . '_' . $currency;

	// Get cached value.
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value ) ) {
		return $cached_value;
	}

	// Get departure.
	$departure = get( $departure_id );

	// Check post_meta is not empty.
	if ( ! is_array( $departure['post_meta'] ) ) {
		return [];
	}

	// Get the related expedition.
	$expedition_post = get_expedition( absint( $departure['post_meta']['related_expedition'] ?? 0 ) );

	// Get the itinerary ID.
	$itinerary_id = absint( $departure['post_meta']['itinerary'] ?? 0 );

	// Check if the expedition post is valid.
	if ( ! $expedition_post['post'] instanceof WP_Post || ! $itinerary_id ) {
		return [];
	}

	// Get Expedition name.
	$expedition_name = $expedition_post['post']->post_title;

	// Get Regions.
	$region_terms = get_region_terms( $expedition_post['post']->ID );
	$regions      = [];

	// Check for regions.
	if ( ! empty( $region_terms ) ) {
		// Get region name comma separated.
		foreach ( $region_terms as $region ) {
			if ( ! is_array( $region ) || empty( $region['name'] ) ) {
				continue;
			}

			// Add region name to array.
			$regions[] = $region['name'];
		}
	}

	// Get the ship ID.
	$ship_id = get_related_ship( $departure_id );

	// Get the ship data.
	$ship_data = get_ship( $ship_id );

	// Get the ship name.
	$ship_name = '';

	// Validate ship_data.
	if ( ! empty( $ship_data['post'] ) && $ship_data['post'] instanceof WP_Post ) {
		$ship_name = $ship_data['post']->post_title;
	}

	// Prepare Included Adventure Options details.
	$include_options_data = [];
	$include_options      = get_included_adventure_options( $departure_id );

	// Loop through include_options.
	foreach ( $include_options as $include_option ) {
		if ( empty( $include_option['term_id'] ) ) {
			continue;
		}

		// Add include option data.
		$include_options_data[] = [
			'title'         => $include_option['name'],
			'icon_image_id' => get_term_meta( $include_option['term_id'], 'icon', true ),
		];
	}

	// Prepare the paid adventure options.
	$paid_adventure_options_data = [];
	$paid_adventure_options      = get_adventure_option_by_departure_post_id( $departure_id );

	// Loop through paid_adventure_options.
	foreach ( $paid_adventure_options as $paid_adventure_option ) {
		// Add paid adventure option data.
		$paid_adventure_options_data[] = [
			'title'            => get_term_field( 'name', $paid_adventure_option['adventure_option_term_id'] ),
			'icon_image_id'    => get_term_meta( $paid_adventure_option['adventure_option_term_id'], 'icon', true ),
			'spaces_available' => $paid_adventure_option['spaces_available'],
			'price_per_person' => format_price( floatval( $paid_adventure_option[ 'price_per_person_' . strtolower( $currency ) ] ), $currency ),
			'currency'         => strtoupper( $currency ),
		];
	}

	// Available promos.
	$available_promos = [];

	// Get Available Promos for the Departure.
	if ( ! empty( $departure['post_meta']['promotion_codes'] ) ) {
		$promotion_codes = $departure['post_meta']['promotion_codes'];

		// Get promo details.
		foreach ( $promotion_codes as $promo_code ) {
			$promo_data = get_promotions_by_code( strval( $promo_code ) );

			// Bail if promo data is empty.
			if ( empty( $promo_data ) ) {
				continue;
			}

			// First element is the promo data.
			$promo_data = $promo_data[0];

			// Check for currency.
			if ( ! empty( $promo_data['currency'] ) && $currency !== $promo_data['currency'] ) {
				continue;
			}

			// Add promo data to available promos.
			$available_promos[ strval( $promo_code ) ] = $promo_data;
		}

		// Sort promos.
		$available_promos = sort_promotions_by_type_and_value( $available_promos );
	}

	// Prepare the departure card details.
	$data = [
		'departure_id'               => $departure_id,
		'region'                     => implode( ', ', $regions ),
		'ship_title'                 => $ship_name,
		'ship_link'                  => get_permalink( $ship_id ),
		'expedition_title'           => $expedition_name,
		'expedition_link'            => $expedition_post['permalink'],
		'duration_days'              => absint( $departure['post_meta']['duration'] ?? 0 ),
		'duration_dates'             => get_start_end_departure_date( $departure_id ),
		'start_location'             => get_starting_from_location( $itinerary_id ),
		'end_location'               => get_end_location( $itinerary_id ),
		'languages'                  => implode( ', ', get_languages( $departure_id ) ),
		'included_adventure_options' => $include_options_data,
		'paid_adventure_options'     => $paid_adventure_options_data,
		'transfer_package_details'   => get_included_transfer_package_details( $itinerary_id, $currency ),
		'available_promos'           => $available_promos,
		'cabin_data'                 => get_cabin_price_data_by_departure( $departure_id, $currency ),
		'request_a_quote_url'        => get_request_a_quote_url( $departure_id ),
		'tax_types'                  => get_tax_type_details( $itinerary_id ),
	];

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return departure card data.
	return $data;
}

/**
 * Get departure cards data.
 *
 * @param int[]  $departure_ids The departure IDs.
 * @param string $currency      The currency.
 *
 * @return array{}|array<int, array{}|array{
 *     departure_id: int,
 *     region: string,
 *     ship_title: string,
 *     ship_link: string|false,
 *     expedition_title: string,
 *     expedition_link: string,
 *     duration_days: int,
 *     duration_dates: string,
 *     start_location: string,
 *     end_location: string,
 *     languages: string,
 *     included_adventure_options: array{}|array{
 *         array{
 *             title: string,
 *             icon_image_id: int,
 *         }
 *     },
 *     paid_adventure_options: array{}|array{
 *          array{
 *             title: string,
 *             icon_image_id: int,
 *             spaces_available: int,
 *             price_per_person: string,
 *             currency: string,
 *         }
 *     },
 *     transfer_package_details: array{
 *         title: string,
 *         sets: array<string>,
 *         price: int,
 *         formatted_price: string,
 *     },
 *     available_promos: array{}| array{
 *         string: array{
 *             id: int,
 *             code: string,
 *             start_date: string,
 *             end_date: string,
 *             description: string,
 *             discount_type: string,
 *             discount_value: string,
 *             is_pif: int,
 *         },
 *     },
 *     cabin_data: array<string, array{
 *             name: string,
 *             availability_status: string,
 *             availability_description: string,
 *             spaces_available: int,
 *             brochure_price: string,
 *             promos: array{}|string[],
 *             type: string,
 *             sort_priority: int,
 *          }
 *     >,
 * }>
 */
function get_dates_rates_cards_data( array $departure_ids = [], string $currency = DEFAULT_CURRENCY ): array {
	// Prepare the departure cards data.
	$departure_cards = [];

	// Validate departure_ids.
	if ( empty( $departure_ids ) || ! is_array( $departure_ids ) ) {
		return $departure_cards;
	}

	// Loop through departure_ids.
	foreach ( $departure_ids as $departure_id ) {
		// Get departure card data.
		$card_data = get_dates_rates_card_data( $departure_id, $currency );

		// Validate card_data.
		if ( empty( $card_data ) ) {
			continue;
		}

		// Add card data to departure cards.
		$departure_cards[ $departure_id ] = $card_data;
	}

	// Return departure cards data.
	return $departure_cards;
}

/**
 * Get discount label.
 *
 * @param int $original_price   Original price.
 * @param int $discounted_price Discounted price.
 *
 * @return string
 */
function get_discount_label( int $original_price = 0, int $discounted_price = 0 ): string {
	// Validate prices.
	if ( empty( $original_price ) || empty( $discounted_price ) || $original_price <= $discounted_price ) {
		return '';
	}

	// Calculate the discount.
	$discount = $original_price - $discounted_price;

	// Calculate the discount percentage.
	$discount_percentage = ( $discount / $original_price ) * 100;

	// Prepare the discount label.
	$discount_label = sprintf(
		// translators: %s: Discount Percentage.
		__( 'Save up to %s%%', 'qrk' ),
		number_format( $discount_percentage, 0 )
	);

	// Return the discount label.
	return $discount_label;
}

/**
 * Get promotions description.
 *
 * @param int    $departure_id Departure ID.
 * @param string $currency     Currency.
 *
 * @return array<string, string>
 */
function get_promotions_description( int $departure_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Check for departure ID.
	if ( empty( $departure_id ) ) {
		return [];
	}

	// Get departure post.
	$departure = get( $departure_id );

	// Check for post.
	if ( ! $departure['post'] instanceof WP_Post || empty( $departure['post_meta'] ) ) {
		return [];
	}

	// Get available promos from post_meta.
	$available_promos = $departure['post_meta']['promotion_codes'] ?? [];

	// Check available promos are empty.
	if ( empty( $available_promos ) || ! is_array( $available_promos ) ) {
		return [];
	}

	// Initialize the promo descriptions.
	$promo_descriptions = [];

	// Loop through available_promos.
	foreach ( $available_promos as $promo_code ) {
		// Promo code.
		$promo_code = strval( $promo_code );

		// Get promo data.
		$promo_data = get_promotions_by_code( $promo_code );

		// Check for promo data.
		if ( empty( $promo_data ) ) {
			continue;
		}

		// Get first promo data.
		$promo_data = $promo_data[0];

		// Check for currency.
		if ( ! empty( $promo_data['currency'] ) && $currency !== $promo_data['currency'] ) {
			continue;
		}

		// Prepare promo description.
		$promo_description = sprintf(
			// translators: %1$s: Promo Description, %2$s: Promo Code.
			__(
				'%1$s - Offer Code %2$s',
				'qrk'
			),
			$promo_data['description'],
			$promo_data['code']
		);

		// Add promo description to array.
		$promo_descriptions[ $promo_code ] = $promo_description;
	}

	// Return promo descriptions.
	return $promo_descriptions;
}

/**
 * Get Departure Availability Status.
 *
 * @param int          $departure_id Departure ID.
 * @param mixed[]|null $cabins       Cabin details.
 *
 * @return string
 */
function get_departure_availability_status( int $departure_id = 0, array|null $cabins = null ): string {
	// If cabins are not provided, get them.
	if ( ! is_array( $cabins ) ) {
		$cabins = get_cabin_details_by_departure( $departure_id );
	}

	// Check for cabins.
	if ( empty( $cabins ) ) {
		return SOLD_OUT_STATUS;
	}

	// Initialize departure availability status.
	$departure_availability_status = SOLD_OUT_STATUS;

	// Get Departure CTA Button status.
	foreach ( $cabins as $cabin ) {
		// Check for availability status.
		if ( ! is_array( $cabin ) || empty( $cabin['specifications'] ) || empty( $cabin['specifications']['availability_status'] ) ) {
			continue;
		}

		// Check for available status.
		if ( SOLD_OUT_STATUS !== $cabin['specifications']['availability_status'] ) {
			// Set status to Available.
			$departure_availability_status = AVAILABLE_STATUS;

			// Break the loop.
			break;
		}
	}

	// Return.
	return $departure_availability_status;
}

/**
 * Sort promotions by discount type, value.
 * Order of sorting: fixed_off, percentage_off, pif.
 *
 * @param mixed[] $promotions Promotions.
 *
 * @return mixed[]
 */
function sort_promotions_by_type_and_value( array $promotions = [] ): array {
	// Bail if empty.
	if ( empty( $promotions ) || ! is_array( $promotions ) ) {
		return [];
	}

	// Sort.
	uasort(
		$promotions,
		function ( $a, $b ) {
			// Check if a and b are arrays.
			if ( ! is_array( $a ) || ! is_array( $b ) ) {
				return 0;
			}

			// Check for discount type.
			if ( empty( $a['discount_type'] ) || empty( $b['discount_type'] ) || empty( $a['discount_value'] ) || empty( $b['discount_value'] ) || ! array_key_exists( 'is_pif', $a ) || ! array_key_exists( 'is_pif', $b ) ) {
				return 0;
			}

			// The sort order for discount types.
			$sort_order = [
				'fixed_off'      => 1,
				'percentage_off' => 2,
			];

			// For same discount type, sort by discount value (highest to lowest).
			if ( $a['discount_type'] === $b['discount_type'] ) {
				// If any one has pif, sort by pif.
				if ( $a['is_pif'] || $b['is_pif'] ) {
					return $a['is_pif'] <=> $b['is_pif'];
				}

				// Sort by discount value.
				return $b['discount_value'] <=> $a['discount_value'];
			}

			// For different discount types, sort by fixed_off first, then percentage_off, then pif.
			return $sort_order[ $a['discount_type'] ] <=> $sort_order[ $b['discount_type'] ];
		}
	);

	// Return sorted promotions.
	return $promotions;
}

/**
 * Translate meta keys.
 *
 * @param array<string, string> $meta_keys Meta keys.
 *
 * @return array<string, string|string[]>
 */
function translate_meta_keys( array $meta_keys = [] ): array {
	// Meta keys for translation.
	$extra_keys = [
		'related_expedition'                       => 'post',
		'related_ship'                             => 'post',
		'itinerary'                                => 'post',
		'expedition_team_\d+_staff_member'         => 'post',
		'related_departures'                       => __NAMESPACE__ . '\\translate_meta_key',
		'adventure_options'                        => __NAMESPACE__ . '\\translate_meta_key',
		'related_promotion_tags'                   => __NAMESPACE__ . '\\translate_meta_key',
		'expedition_team_\d+_departure_staff_role' => __NAMESPACE__ . '\\translate_meta_key',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}

/**
 * Callable to translate a meta value by meta key.
 *
 * @param string $meta_key            Meta key name.
 * @param string $meta_value          Meta key value.
 * @param int    $source_site_id      Source site ID.
 * @param int    $destination_site_id Destination site ID.
 *
 * @return string Translated value.
 */
function translate_meta_key( string $meta_key = '', string $meta_value = '', int $source_site_id = 0, int $destination_site_id = 0 ): string {
	// Bail if required data is not available.
	if ( empty( $meta_key ) || empty( $meta_value ) || empty( $source_site_id ) || empty( $destination_site_id ) ) {
		return $meta_value;
	}

	// Taxonomies keys.
	$taxonomies_keys = [
		'adventure_options',
		'related_promotion_tags',
	];

	// Translate the taxonomies meta key.
	if (
		in_array( $meta_key, $taxonomies_keys, true )
		|| preg_match( '/expedition_team_\d+_departure_staff_role/', $meta_key )
	) {
		// Get translated term id.
		$translated_terms = get_term_translations(
			absint( $meta_value ),
			$source_site_id,
		);

		// Loop through translated terms.
		foreach ( $translated_terms as $term ) {
			if ( $term['site_id'] === $destination_site_id ) {
				// Update meta value with translated term id.
				$meta_value = $term['term_id'];
				break;
			}
		}
	} elseif ( 'related_departures' === $meta_key ) {
		// Get translated deck ID.
		$deck_post = get_post_translations(
			absint( $meta_value ),
			$source_site_id
		);

		// Loop through translated posts.
		foreach ( $deck_post as $post ) {
			if ( $post['site_id'] === $destination_site_id ) {
				// Update meta value.
				$meta_value = $post['post_id'];
			}
		}
	}

	// Return meta value.
	return strval( $meta_value );
}
