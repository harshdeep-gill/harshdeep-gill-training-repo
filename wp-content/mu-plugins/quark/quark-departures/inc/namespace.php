<?php
/**
 * Namespace functions.
 *
 * @package quark-departures
 */

namespace Quark\Departures;

use Quark\Softrip\Departure;
use Quark\Softrip\Itinerary;
use WP_Post;
use WP_Term_Query;

use function Quark\Core\format_price;
use function Quark\Core\get_available_currencies;
use function Quark\Itineraries\get as get_itinerary;
use function Quark\Itineraries\get_starting_from_location;
use function Quark\Itineraries\get_included_transfer_package_details;
use function Quark\Itineraries\get_policy_banner_details;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Expeditions\get_itineraries;

use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;

const POST_TYPE                = 'qrk_departure';
const SPOKEN_LANGUAGE_TAXONOMY = 'qrk_spoken_language';
const PROMOTION_TAG            = 'qrk_promotion_tags';
const CACHE_KEY                = POST_TYPE;
const CACHE_GROUP              = POST_TYPE;

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

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Bust cache for departure card data.
	add_action( 'qe_departure_post_cache_busted', __NAMESPACE__ . '\\bust_card_data_cache' );
	add_action( 'qe_expedition_post_cache_busted', __NAMESPACE__ . '\\bust_card_data_cache_on_expedition_update' );
	add_action( 'qe_itinerary_post_cache_busted', __NAMESPACE__ . '\\bust_card_data_cache_on_itinerary_update' );

	// Bust cache on term update.
	add_action( 'set_object_terms', __NAMESPACE__ . '\\bust_post_cache_on_term_assign', 10, 6 );

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
			'revisions',
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
 * Bust cache on term assign.
 *
 * @param int                    $object_id Object ID.
 * @param array{string|int}|null $terms     An array of object term IDs or slugs.
 * @param array{string|int}|null $tt_ids    An array of term taxonomy IDs.
 * @param string                 $taxonomy  Taxonomy slug.
 *
 * @return void
 */
function bust_post_cache_on_term_assign( int $object_id = 0, array $terms = null, array $tt_ids = null, string $taxonomy = '' ): void {
	// Check for spoken language taxonomy.
	if ( in_array( $taxonomy, [ SPOKEN_LANGUAGE_TAXONOMY, PROMOTION_TAG ], true ) ) {
		// Get post.
		$post = get( $object_id );

		// Check for post.
		if ( ! $post['post'] instanceof WP_Post || POST_TYPE !== $post['post']->post_type ) {
			return;
		}

		// Bust cache.
		bust_post_cache( $post['post']->ID );
	}
}

/**
 * Get departure Season.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return string
 */
function get_season( int $post_id = 0 ): string {
	// Get departure.
	$departure = get( $post_id );

	// Check post_meta is not empty.
	if ( ! $departure['post_meta'] ) {
		return '';
	}

	// Get itinerary from meta.
	$itinerary = $departure['post_meta']['itinerary'] ?? '';

	// Check start date meta is empty.
	if ( ! $itinerary ) {
		return '';
	}

	// Get itinerary.
	$itinerary = get_itinerary( absint( $itinerary ) );

	// Check for Itinerary.
	if ( empty( $itinerary['post_taxonomies'][ SEASON_TAXONOMY ] ) || ! is_array( $itinerary['post_taxonomies'][ SEASON_TAXONOMY ] ) ) {
		return '';
	}

	// Check for Season.
	if ( ! isset( $itinerary['post_taxonomies'][ SEASON_TAXONOMY ][0] ) ) {
		return '';
	}

	// Return Season.
	return $itinerary['post_taxonomies'][ SEASON_TAXONOMY ][0]['name'] ?? '';
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
 * Get departure region and Season.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return string
 */
function get_region_and_season( int $post_id = 0 ): string {
	// Get departure.
	$departure = get( $post_id );

	// Check post_meta is not empty.
	if ( ! $departure['post_meta'] ) {
		return '';
	}

	// Get region from meta.
	$region = $departure['post_meta']['region'] ?? '';

	// Get season.
	$season = get_season( $post_id );

	// Check region and season are empty.
	if ( ! $region && ! $season ) {
		return '';
	}

	// Return region and season.
	return $region . '-' . $season;
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

	// Get promotion tags from post_taxonomy.
	$promotion_tag_terms = $departure['post_taxonomies'][ PROMOTION_TAG ] ?? [];

	// Check promotion tags are empty.
	if ( ! $promotion_tag_terms || ! is_array( $promotion_tag_terms ) ) {
		return $promotion_tags;
	}

	// Loop through promotion tags - get name field.
	foreach ( $promotion_tag_terms as $promotion_tag ) {
		$promotion_tags[] = $promotion_tag['name'] ? strval( $promotion_tag['name'] ) : '';
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
 *     package_id: string,
 *     duration_days: int,
 *     duration_dates: string,
 *     starting_from_location: string,
 *     languages: string,
 *     paid_adventure_options: string[],
 *     lowest_price: array<string, string>,
 *     transfer_package_details: array{
 *       title: string,
 *       sets: string[],
 *       price: float,
 *     },
 *     promotion_tags: string[],
 *     ship_name: string,
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
 *        specifications: array{
 *           availability_status: string,
 *           availability_description: string,
 *           spaces_available: string,
 *           occupancy: string,
 *           location: string,
 *           size: string,
 *           bed_configuration: string
 *       },
 *       from_price: array{
 *          discounted_price: string,
 *          original_price: string,
 *      },
 *      occupancies: array<int<0, max>, array<string, mixed>>
 *     }>,
 * }
 */
function get_card_data( int $departure_id = 0, string $currency = 'USD' ): array {
	// Set cache key.
	$cache_key = 'departure_card_data_' . $departure_id . '_' . $currency;

	// Get cached value.
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value ) ) {
		return $cached_value;
	}

	// Get the departure object.
	$departure = new Departure();
	$departure->load( $departure_id );

	// Get the related expedition.
	$expedition_post = get_expedition( absint( $departure->get_post_meta( 'related_expedition' ) ) );

	// Get the itinerary ID.
	$itinerary_id = $departure->get_post_meta( 'itinerary' ) ? absint( $departure->get_post_meta( 'itinerary' ) ) : 0;

	// Check if the expedition post is valid.
	if ( ! $expedition_post['post'] instanceof WP_Post || ! $itinerary_id ) {
		return [];
	}

	// Get Expedition name.
	$expedition_name = $expedition_post['post']->post_title;

	// Prepare the departure details.
	$ship_data = $departure->get_ship();
	$ship_name = '';

	// Validate ship_data.
	if ( ! empty( $ship_data['post'] ) && $ship_data['post'] instanceof WP_Post ) {
		$ship_name = $ship_data['post']->post_title;
	}

	// Get the lowest price.
	$prices = $departure->get_lowest_price( $currency );

	// Format the prices.
	$prices['discounted_price'] = format_price( $prices['discounted_price'], $currency );
	$prices['original_price']   = format_price( $prices['original_price'], $currency );

	// Prepare the departure card details.
	$data = [
		'departure_id'             => $departure_id,
		'expedition_name'          => $expedition_name,
		'expedition_link'          => $expedition_post['permalink'],
		'package_id'               => strval( $departure->get_post_meta( 'softrip_package_id' ) ),
		'duration_days'            => absint( $departure->get_post_meta( 'duration' ) ),
		'duration_dates'           => $departure->get_date_range(),
		'starting_from_location'   => get_starting_from_location( $itinerary_id ),
		'languages'                => implode( ', ', get_languages( $departure_id ) ),
		'paid_adventure_options'   => get_paid_adventure_options( $departure_id ),
		'lowest_price'             => $prices,
		'transfer_package_details' => get_included_transfer_package_details( $itinerary_id, $currency ),
		'promotion_tags'           => get_promotion_tags( $departure_id ),
		'ship_name'                => $ship_name,
		'banner_details'           => get_policy_banner_details( $itinerary_id ),
		'cabins'                   => $departure->get_cabin_details( $currency ),
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
 *      transfer_package_details: array{
 *        title: string,
 *        sets: string[],
 *        price: float,
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
 *            spaces_available: string,
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
function get_cards_data( array $departure_ids = [], string $currency = 'USD' ): array {
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
 * Bust Departure card data cache.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return void
 */
function bust_card_data_cache( int $post_id = 0 ): void {
	// Get currency list.
	$currencies = get_available_currencies();

	// Loop through currencies.
	foreach ( $currencies as $currency ) {
		wp_cache_delete( 'departure_card_data_' . $post_id . '_' . $currency );
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
	// Create Itinerary object.
	$itinerary = new Itinerary( $itinerary_id );

	// Get Departures for the Itinerary.
	$departures = $itinerary->get_departures();

	// Check Departures are empty.
	if ( empty( $departures ) ) {
		return;
	}

	// Loop through Departures.
	foreach ( $departures as $departure ) {
		// Check for Departure.
		if ( ! $departure instanceof Departure ) {
			continue;
		}

		// Bust departure card cache by departure.
		bust_card_data_cache( $departure->get_id() );
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
