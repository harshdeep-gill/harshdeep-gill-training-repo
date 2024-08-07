<?php
/**
 * Namespace functions.
 *
 * @package quark-departures
 */

namespace Quark\Departures;

use WP_Post;

use function Quark\Itineraries\get as get_itinerary;

use const Quark\StaffMembers\SEASON_TAXONOMY;

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
function get_departure_season( int $post_id = 0 ): string {
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
 * Get departure region and Season.
 *
 * @param int $post_id Departure Post ID.
 *
 * @return string
 */
function get_departure_region_and_season( int $post_id = 0 ): string {
	// Get departure.
	$departure = get( $post_id );

	// Check post_meta is not empty.
	if ( ! $departure['post_meta'] ) {
		return '';
	}

	// Get region from meta.
	$region = $departure['post_meta']['softrip_market_code'] ?? '';

	// Get season.
	$season = get_departure_season( $post_id );

	// Check region and season are empty.
	if ( ! $region && ! $season ) {
		return '';
	}

	// Return region and season.
	return $region . '-' . $season;
}
