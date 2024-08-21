<?php
/**
 * Namespace functions.
 *
 * @package quark-expeditions
 */

namespace Quark\Expeditions;

use WP_Post;
use WP_Error;
use WP_Term;
use WP_REST_Response;
use WP_Taxonomy;
use WP_REST_Request;

use function Quark\Itineraries\get as get_itinerary;
use function Quark\Departures\get as get_departure;
use function Quark\Core\format_price;
use function Quark\Ships\get as get_ship;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Itineraries\get_end_date;
use function Quark\Softrip\Itineraries\get_lowest_price;
use function Quark\Softrip\Itineraries\get_related_ships;
use function Quark\Softrip\Itineraries\get_start_date;

use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;

const POST_TYPE                    = 'qrk_expedition';
const DESTINATION_TAXONOMY         = 'qrk_destination';
const EXPEDITION_CATEGORY_TAXONOMY = 'qrk_expedition_category';
const EXPEDITION_TAG_TAXONOMY      = 'qrk_expedition_tag';
const EXCURSION_TAXONOMY           = 'qrk_excursion';
const CACHE_KEY                    = POST_TYPE;
const CACHE_GROUP                  = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_expedition_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_destination_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_expedition_category_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_expedition_tag_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_excursion_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_expedition_category_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_expedition_tag_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_excursion_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'rest_prepare_taxonomy', __NAMESPACE__ . '\\hide_excursion_metabox', 10, 3 );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Bust cache for details data.
	add_action( 'qe_expedition_post_cache_busted', __NAMESPACE__ . '\\bust_details_cache' );
	add_action( 'qe_itinerary_post_cache_busted', __NAMESPACE__ . '\\bust_details_cache_on_itinerary_update', 1 );
	add_action( 'qe_departure_post_cache_busted', __NAMESPACE__ . '\\bust_details_cache_on_departure_update', 1 );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/expeditions.php';
		require_once __DIR__ . '/../custom-fields/destinations.php';
		require_once __DIR__ . '/../custom-fields/excursion.php';
	}
}

/**
 * Register Expedition post type.
 *
 * @return void
 */
function register_expedition_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Expeditions',
			'singular_name'      => 'Expedition',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Expedition',
			'edit_item'          => 'Edit Expedition',
			'new_item'           => 'New Expedition',
			'view_item'          => 'View Expedition',
			'search_items'       => 'Search Expeditions',
			'not_found'          => 'No Expeditions found',
			'not_found_in_trash' => 'No Expeditions found in Trash',
			'parent_item_colon'  => 'Parent Expedition:',
			'menu_name'          => 'Expeditions',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-flag',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
			'revisions',
		],
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'expeditions',
			'with_front' => false,
		],
		'template'            => [],
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Destination taxonomy.
 *
 * @return void
 */
function register_destination_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Destinations',
		'singular_name'              => 'Destination',
		'search_items'               => 'Search Destinations',
		'popular_items'              => 'Popular Destinations',
		'all_items'                  => 'All Destinations',
		'parent_item'                => 'Parent Destination',
		'parent_item_colon'          => 'Parent Destination:',
		'edit_item'                  => 'Edit Destination',
		'update_item'                => 'Update Destination',
		'add_new_item'               => 'Add New Destination',
		'new_item_name'              => 'New Destination',
		'separate_items_with_commas' => 'Separate Destinations with commas',
		'add_or_remove_items'        => 'Add or remove Destinations',
		'choose_from_most_used'      => 'Choose from the most used Destinations',
		'menu_name'                  => 'Destinations',
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
	register_taxonomy( DESTINATION_TAXONOMY, (array) apply_filters( 'qe_destination_taxonomy_post_types', [] ), $args );
}

/**
 * Register Expedition Category taxonomy.
 *
 * @return void
 */
function register_expedition_category_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Expedition Categories',
		'singular_name'              => 'Expedition Category',
		'search_items'               => 'Search Expedition Categories',
		'popular_items'              => 'Popular Expedition Categories',
		'all_items'                  => 'All Expedition Categories',
		'parent_item'                => 'Parent Expedition Category',
		'parent_item_colon'          => 'Parent Expedition Category:',
		'edit_item'                  => 'Edit Expedition Category',
		'update_item'                => 'Update Expedition Category',
		'add_new_item'               => 'Add New Expedition Category',
		'new_item_name'              => 'New Expedition Category',
		'separate_items_with_commas' => 'Separate Expedition Categories with commas',
		'add_or_remove_items'        => 'Add or remove Expedition Categories',
		'choose_from_most_used'      => 'Choose from the most used Expedition Categories',
		'menu_name'                  => 'Expedition Categories',
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
	register_taxonomy( EXPEDITION_CATEGORY_TAXONOMY, (array) apply_filters( 'qe_expedition_category_taxonomy_post_types', [] ), $args );
}

/**
 * Register Expedition tag taxonomy.
 *
 * @return void
 */
function register_expedition_tag_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Expedition Tags',
		'singular_name'              => 'Expedition Tag',
		'search_items'               => 'Search Expedition Tags',
		'popular_items'              => 'Popular Expedition Tags',
		'all_items'                  => 'All Expedition Tags',
		'parent_item'                => 'Parent Expedition Tag',
		'parent_item_colon'          => 'Parent Expedition Tag:',
		'edit_item'                  => 'Edit Expedition Tag',
		'update_item'                => 'Update Expedition Tag',
		'add_new_item'               => 'Add New Expedition Tag',
		'new_item_name'              => 'New Expedition Tag',
		'separate_items_with_commas' => 'Separate Expedition Tags with commas',
		'add_or_remove_items'        => 'Add or remove Expedition Tags',
		'choose_from_most_used'      => 'Choose from the most used Expedition Tags',
		'menu_name'                  => 'Expedition Tags',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( EXPEDITION_TAG_TAXONOMY, (array) apply_filters( 'qe_expedition_tag_taxonomy_post_types', [] ), $args );
}

/**
 * Register Departure Destinations taxonomy.
 *
 * @return void
 */
function register_excursion_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Excursions',
		'singular_name'              => 'Excursion',
		'search_items'               => 'Search Excursion',
		'popular_items'              => 'Popular Excursions',
		'all_items'                  => 'All Excursions',
		'parent_item'                => 'Parent Excursion',
		'parent_item_colon'          => 'Parent Excursion:',
		'edit_item'                  => 'Edit Excursion',
		'update_item'                => 'Update Excursion',
		'add_new_item'               => 'Add New Excursion',
		'new_item_name'              => 'New Excursion',
		'separate_items_with_commas' => 'Separate Excursions with commas',
		'add_or_remove_items'        => 'Add or remove Excursions',
		'choose_from_most_used'      => 'Choose from the most used Excursions',
		'menu_name'                  => 'Excursions',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => false,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
		'meta_box_cb'       => false,
	];

	// Register taxonomy.
	register_taxonomy( EXCURSION_TAXONOMY, (array) apply_filters( 'qe_excursion_taxonomy_post_types', [] ), $args );
}

/**
 * Hide Departure Destination taxonomy metabox.
 *
 * @param WP_REST_Response|null $response The response object.
 * @param WP_Taxonomy| null     $taxonomy The original taxonomy object.
 * @param WP_REST_Request|null  $request  Request used to generate the response.
 *
 * @return WP_REST_Response|null
 */
function hide_excursion_metabox( WP_REST_Response $response = null, WP_Taxonomy $taxonomy = null, WP_REST_Request $request = null ): WP_REST_Response|null {
	// Check if taxonomy is Icon.
	if (
		! $taxonomy instanceof WP_Taxonomy
		|| ! $response instanceof WP_REST_Response
		|| ! $request instanceof WP_REST_Request
		|| EXCURSION_TAXONOMY !== $taxonomy->name
	) {
		return $response;
	}

	// Get context.
	$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

	// Context is edit in the editor.
	if ( 'edit' === $context && false === $taxonomy->meta_box_cb ) {
		$data_response = $response->get_data();

		// Check if data response is not an array.
		if ( ! is_array( $data_response ) ) {
			$data_response = [];
		}

		// Hide UI.
		$data_response['visibility']['show_ui'] = false;
		$response->set_data( $data_response );
	}

	// Return response.
	return $response;
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Expeditions post type for taxonomy.
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
	do_action( 'qe_expedition_post_cache_busted', $post_id );
}

/**
 * Get an Expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_meta: mixed[],
 *     post_taxonomies: mixed[],
 *     permalink: string,
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
			'permalink'       => $cached_value['permalink'],
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
			'permalink'       => '',
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
		'post_meta'       => [],
		'post_taxonomies' => [],
		'permalink'       => strval( get_permalink( $post ) ? : '' ),
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
 * Get Region for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}| array{
 *     array{
 *         term_id: int,
 *         name: string,
 *         slug: string,
 *         taxonomy: string,
 *         description: string,
 *         parent: int,
 *         term_group: int,
 *     }
 * }
 */
function get_region_terms( int $post_id = 0 ): array {
	// Get post.
	$post   = get( $post_id );
	$region = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $region;
	}

	// Get parent of qrk_destination taxonomy.
	if (
		array_key_exists( DESTINATION_TAXONOMY, $post['post_taxonomies'] )
		&& is_array( $post['post_taxonomies'][ DESTINATION_TAXONOMY ] )
	) {
		// Loop through taxonomy and get all with no parent term name.
		foreach ( $post['post_taxonomies'][ DESTINATION_TAXONOMY ] as $term ) {
			if ( empty( $term['parent'] ) ) {
				$region[] = $term;
			}
		}
	}

	// Return regions.
	return $region;
}

/**
 * Get Itineraries for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}| array{
 *     array{
 *      post: WP_Post|null,
 *      post_meta: mixed[],
 *      post_taxonomies: mixed[]
 *  }
 * }
 */
function get_itineraries( int $post_id = 0 ): array {
	// Get post.
	$post        = get( $post_id );
	$itineraries = [];

	// Check for post_meta.
	if ( empty( $post['post_meta'] ) || empty( $post['post_meta']['related_itineraries'] ) ) {
		return $itineraries;
	}

	// Get Itineraries from related_itineraries post meta.
	$itinerary_ids = $post['post_meta']['related_itineraries'];

	// Check for itinerary_ids array.
	if ( ! is_array( $itinerary_ids ) ) {
		return $itineraries;
	}

	// Check for Itinerary IDs.
	foreach ( $itinerary_ids as $itinerary_id ) {
		// Get Itinerary.
		$itinerary = get_itinerary( absint( $itinerary_id ) );

		// Check for Itinerary.
		if ( empty( $itinerary['post'] ) || ! $itinerary['post'] instanceof WP_Post ) {
			return $itineraries;
		}

		// Check Itinerary is published.
		if ( 'publish' !== $itinerary['post']->post_status ) {
			continue;
		}

		// Add Itinerary to array.
		$itineraries[] = $itinerary;
	}

	// Return Itineraries.
	return $itineraries;
}

/**
 * Get Minimum Duration days for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return int
 */
function get_minimum_duration( int $post_id = 0 ): int {
	// Get post.
	$post = get( $post_id );

	// Minimum duration.
	$minimum_duration = 0;

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $minimum_duration;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $minimum_duration;
	}

	// Loop through itineraries and get minimum duration.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if ( ! is_array( $itinerary ) || empty( $itinerary['post_meta'] ) || empty( $itinerary['post_meta']['duration_in_days'] ) ) {
			continue;
		}

		// Get duration.
		$duration = absint( $itinerary['post_meta']['duration_in_days'] );

		// Check minimum duration.
		if ( empty( $minimum_duration ) || $duration < $minimum_duration ) {
			$minimum_duration = $duration;
		}
	}

	// Return minimum duration.
	return $minimum_duration;
}

/**
 * Get Starting From Price for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return int Price.
 */
function get_starting_from_price( int $post_id = 0 ): int {
	// Get post.
	$post = get( $post_id );

	// Starting from price.
	$starting_from_price = 0;

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $starting_from_price;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $starting_from_price;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get lowest price for Itinerary.
		$price = get_lowest_price( $itinerary['post']->ID )['original'];

		// Check minimum price.
		if ( ! empty( $price ) && ( empty( $starting_from_price ) || $price < $starting_from_price ) ) {
			$starting_from_price = $price;
		}
	}

	// Return starting from price.
	return absint( $starting_from_price );
}

/**
 * Get Starting From Locations for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return array{} | array{
 *     array{
 *        title: string,
 *     },
 * }
 */
function get_starting_from_locations( int $post_id = 0 ): array {
	// Get post.
	$post = get( $post_id );

	// Starting from locations.
	$starting_from_locations = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $starting_from_locations;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $starting_from_locations;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
			|| empty( $itinerary['post_meta']['start_location'] )
		) {
			continue;
		}

		// Get location.
		$location      = absint( $itinerary['post_meta']['start_location'] );
		$location_term = get_term_by( 'id', $location, DEPARTURE_LOCATION_TAXONOMY );

		// Check location.
		if ( $location_term instanceof WP_Term ) {
			// Add unique location to array.
			$starting_from_locations[ $location_term->term_id ] = [
				'title' => $location_term->name,
			];
		}
	}

	// Return starting from locations.
	return $starting_from_locations;
}

/**
 * Get Ships for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return array{} | array{
 *     array{
 *         post: WP_Post,
 *         post_meta: mixed[],
 *         permalink: string,
 *     },
 * }
 */
function get_ships( int $post_id = 0 ): array {
	// Get post.
	$post = get( $post_id );

	// Ships.
	$ships = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $ships;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $ships;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get related ships.
		$related_ship_post_ids = get_related_ships( $itinerary['post']->ID );

		// Loop through related ships.
		foreach ( $related_ship_post_ids as $ship_post_id ) {
			// Get Ship.
			$ship = get_ship( $ship_post_id );

			// Check for Ship.
			if ( empty( $ship['post'] ) || ! $ship['post'] instanceof WP_Post ) {
				continue;
			}

			// Add Ship to array.
			$ships[ $ship_post_id ] = $ship;
		}
	}

	// Return ships.
	return $ships;
}

/**
 * Get Total Departures for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return int
 */
function get_total_departures( int $post_id = 0 ): int {
	// Get post.
	$post = get( $post_id );

	// Total departures.
	$total_departures = 0;

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $total_departures;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $total_departures;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get total departures for Itinerary.
		$total_departures = $total_departures + count( get_departures_by_itinerary( $itinerary['post']->ID ) );
	}

	// Return total departures.
	return $total_departures;
}

/**
 * Get Starting From Date for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_starting_from_date( int $post_id = 0 ): string {
	// Get post.
	$post = get( $post_id );

	// Starting from date.
	$starting_from_date = '';

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $starting_from_date;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $starting_from_date;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get starting date.
		$test_date = get_start_date( $itinerary['post']->ID );

		// Check for date.
		if ( empty( $test_date ) ) {
			continue;
		}

		// Check if starting from date is empty or test date is earlier.
		if (
			empty( $starting_from_date )
			|| strtotime( $test_date ) < strtotime( $starting_from_date )
		) {
			$starting_from_date = $test_date;
		}
	}

	// Return starting from date.
	return $starting_from_date;
}

/**
 * Get Ending To Date for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_ending_to_date( int $post_id = 0 ): string {
	// Get post.
	$post = get( $post_id );

	// ending to date.
	$ending_to_date = '';

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $ending_to_date;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $ending_to_date;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get ending date.
		$test_date = get_end_date( $itinerary['post']->ID );

		// Check for date.
		if ( empty( $test_date ) ) {
			continue;
		}

		// Check if ending to date is empty or test date is later.
		if (
			empty( $ending_to_date )
			|| strtotime( $test_date ) > strtotime( $ending_to_date )
		) {
			$ending_to_date = $test_date;
		}
	}

	// Return ending to date.
	return $ending_to_date;
}

/**
 * Format Departure date range for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_formatted_date_range( int $post_id = 0 ): string {
	// Get starting from and ending to dates.
	$starting_from_date = get_starting_from_date( $post_id );
	$ending_to_date     = get_ending_to_date( $post_id );

	// Check if both dates are empty and return an empty string.
	if ( empty( $starting_from_date ) && empty( $ending_to_date ) ) {
		return '';
	}

	// Check if only the ending to date is empty.
	if ( empty( $ending_to_date ) ) {
		$ending_to_date = $starting_from_date; // Starting From will not be empty here.
	}

	// Get the month and year for both start and end dates.
	$start_month = gmdate( 'F', absint( strtotime( $starting_from_date ) ) );
	$start_year  = gmdate( 'Y', absint( strtotime( $starting_from_date ) ) );
	$end_month   = gmdate( 'F', absint( strtotime( $ending_to_date ) ) );
	$end_year    = gmdate( 'Y', absint( strtotime( $ending_to_date ) ) );

	// Same month and year.
	if ( $start_month === $end_month && $start_year === $end_year ) {
		return sprintf( 'in %s %s', $start_month, $start_year );
	}

	// Same year, different months OR Different year.
	return sprintf( 'between %s %s to %s %s', $start_month, $start_year, $end_month, $end_year );
}

/**
 * Get Expedition details card data.
 *
 * @param int $post_id Expedition Post ID.
 *
 * @return array{}|array{
 *     title: string,
 *     duration: int,
 *     region: string,
 *     from_price: string,
 *     starting_from ?: array{}|array{
 *         array{ title : string },
 *     },
 *     total_departures: int,
 *     ships ?: array{}|array{
 *         array{ title : string },
 *     },
 *     tags ?: array{}|array{
 *         array{ title : string },
 *     },
 *     date_range: string,
 * }
 */
function get_details_data( int $post_id = 0 ): array {
	// Check for cached version.
	$cache_key    = CACHE_KEY . "_details_$post_id";
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value ) ) {
		return $cached_value;
	}

	// Get post.
	$post = get( $post_id );
	$data = [];

	// Check for post.
	if (
		empty( $post['post'] )
		|| ! $post['post'] instanceof WP_Post
		|| POST_TYPE !== $post['post']->post_type
	) {
		return $data;
	}

	// Break title with colon.
	$title_parts = explode( ':', $post['post']->post_title );

	// Check if title parts are available.
	if ( ! empty( $title_parts[0] ) ) {
		$title = trim( $title_parts[0] );
	} else {
		$title = $post['post']->post_title;
	}

	// Set title.
	$data['title'] = $title;

	// Init $tags.
	$tags = [];

	// Get Expeditions tags.
	if ( array_key_exists( EXPEDITION_TAG_TAXONOMY, $post['post_taxonomies'] ) ) {
		$tags = $post['post_taxonomies'][ EXPEDITION_TAG_TAXONOMY ];
	}

	// Check for tags.
	if ( ! empty( $tags ) ) {
		// Loop through $tags and fetch tag name to title key.
		foreach ( $tags as $tag ) {
			if ( ! is_array( $tag ) || empty( $tag['name'] ) ) {
				continue;
			}

			// Add tag name to array.
			$data['tags'][] = [
				'title' => $tag['name'],
			];
		}
	}

	// Get Regions.
	$regions     = get_region_terms( $post_id );
	$data_region = [];

	// Check for regions.
	if ( ! empty( $regions ) ) {
		// Get region name comma seperated.
		foreach ( $regions as $region ) {
			if ( ! is_array( $region ) || empty( $region['name'] ) ) {
				continue;
			}

			// Add region name to array.
			$data_region[] = $region['name'];
		}
	}

	// Set region name.
	$data['region'] = implode( ', ', $data_region );

	// Set minimum duration.
	$data['duration'] = get_minimum_duration( $post_id );

	// Set starting from price.
	$data['from_price'] = format_price( get_starting_from_price( $post_id ) );

	// Set starting from locations list.
	$data['starting_from'] = get_starting_from_locations( $post_id );

	// Set ships.
	$ships_data = get_ships( $post_id );

	// Check for ships.
	if ( ! empty( $ships_data ) ) {
		foreach ( $ships_data as $ship ) {
			if ( ! $ship['post'] instanceof WP_Post ) {
				continue;
			}

			// Add ship name/title to array.
			$data['ships'][] = [
				'title' => $ship['post']->post_title,
			];
		}
	}

	// Get total number of Departures.
	$data['total_departures'] = get_total_departures( $post_id );

	// get date range.
	$data['date_range'] = get_formatted_date_range( $post_id );

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}

/**
 * Bust cache for Details Card.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_details_cache( int $post_id = 0 ): void {
	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_details_$post_id", CACHE_GROUP );
}

/**
 * Bust cache for Details Card from Itinerary.
 *
 * @param int $itinerary_id Itinerary Post ID.
 *
 * @return void
 */
function bust_details_cache_on_itinerary_update( int $itinerary_id = 0 ): void {
	// Get post ID.
	if ( 0 === $itinerary_id ) {
		$itinerary_id = absint( get_the_ID() );
	}

	// Get post.
	$itinerary = get_itinerary( $itinerary_id );

	// Check for Itinerary.
	if ( empty( $itinerary['post'] ) || ! $itinerary['post'] instanceof WP_Post ) {
		return;
	}

	// Get related Expedition ID.
	$expedition_ids = $itinerary['post_meta']['related_expedition'] ?? 0;

	// Check for Expedition IDs.
	if ( empty( $expedition_ids ) || ! is_array( $expedition_ids ) ) {
		return;
	}

	// Bust cache for each Expedition.
	foreach ( $expedition_ids as $expedition_id ) {
		// Bust cache for Expedition.
		bust_details_cache( absint( $expedition_id ) );
	}
}

/**
 * Bust cache for Details Card from Departure.
 *
 * @param int $departure_id Departure Post ID.
 *
 * @return void
 */
function bust_details_cache_on_departure_update( int $departure_id = 0 ): void {
	// Get post ID.
	if ( 0 === $departure_id ) {
		$departure_id = absint( get_the_ID() );
	}

	// Get post.
	$departure = get_departure( $departure_id );

	// Check for Departure.
	if ( empty( $departure['post'] ) || ! $departure['post'] instanceof WP_Post ) {
		return;
	}

	// Get related Expedition ID.
	$expedition_id = $departure['post_meta']['related_expedition'] ?? 0;

	// Validate for Expedition ID.
	if ( empty( $expedition_id ) || ! absint( $expedition_id ) ) {
		return;
	}

	// Bust cache for Expedition.
	bust_details_cache( absint( $expedition_id ) );
}

/**
 * Get Expedition Details Card.
 *
 * @param int $expedition_id Expedition Post ID.
 *
 * @return array<int, int>
 */
function get_expedition_ship_ids( int $expedition_id = 0 ): array {
	// Get the expedition.
	$expedition      = get( $expedition_id );
	$expedition_post = $expedition['post'];
	$ships_ids       = [];

	// Check for post.
	if ( ! $expedition_post instanceof WP_Post ) {
		return $ships_ids;
	}

	// Get the ships IDs.
	$ships = get_ships( $expedition_post->ID );

	// Check for ships.
	if ( empty( $ships ) ) {
		return $ships_ids;
	}

	// Get the ships IDs.
	foreach ( $ships as $ship ) {
		$ships_ids[] = absint( $ship['post']->ID );
	}

	// Return the ships IDs.
	return array_unique( $ships_ids );
}

/**
 * Get Destination term by Softrip code.
 *
 * @param string $code Softrip code.
 *
 * @return null|WP_Term
 */
function get_destination_term_by_code( string $code = '' ): null|WP_Term {
	// Check if code is empty.
	if ( empty( $code ) ) {
		return null;
	}

	// Prepare arguments for the query.
	$args = [
		'taxonomy'   => DESTINATION_TAXONOMY,
		'hide_empty' => false,
		'number'     => 1,
		'meta_query' => [
			[
				'key'     => 'softrip_id',
				'value'   => $code,
				'compare' => '=',
			],
		],
	];

	// Perform the query.
	$terms = get_terms( $args );

	// Check if any terms are found.
	if ( ! empty( $terms ) && ! $terms instanceof WP_Error && $terms[0] instanceof WP_Term ) {
		return $terms[0];
	}

	// Return null if no term is found.
	return null;
}
