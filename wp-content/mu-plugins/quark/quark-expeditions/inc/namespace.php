<?php
/**
 * Namespace functions.
 *
 * @package quark-expeditions
 */

namespace Quark\Expeditions;

use WP_Post;
use WP_REST_Response;
use WP_Taxonomy;
use WP_REST_Request;

use function Quark\Core\prepare_content_with_blocks;

const POST_TYPE                      = 'qrk_expedition';
const DESTINATIONS_TAXONOMY          = 'qrk_destination';
const EXPEDITION_CATEGORY_TAXONOMY   = 'qrk_expedition_category';
const DEPARTURE_DESTINATION_TAXONOMY = 'qrk_departure_destination';
const CACHE_KEY                      = POST_TYPE;
const CACHE_GROUP                    = POST_TYPE;

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
	add_action( 'init', __NAMESPACE__ . '\\register_departure_destination_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_expedition_category_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_departure_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'rest_prepare_taxonomy', __NAMESPACE__ . '\\hide_departure_destination_metabox', 10, 3 );

	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/destinations.php';
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
		'menu_position'       => 25,
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
	register_taxonomy( DESTINATIONS_TAXONOMY, (array) apply_filters( 'qe_destination_taxonomy_post_types', [] ), $args );
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
 * Register Departure Destinations taxonomy.
 *
 * @return void
 */
function register_departure_destination_taxonomy(): void {
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
	register_taxonomy( DEPARTURE_DESTINATION_TAXONOMY, (array) apply_filters( 'qe_departure_destination_taxonomy_post_types', [] ), $args );
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
function hide_departure_destination_metabox( WP_REST_Response $response = null, WP_Taxonomy $taxonomy = null, WP_REST_Request $request = null ): WP_REST_Response|null {
	// Check if taxonomy is Icon.
	if (
		! $taxonomy instanceof WP_Taxonomy
		|| ! $response instanceof WP_REST_Response
		|| ! $request instanceof WP_REST_Request
		|| DEPARTURE_DESTINATION_TAXONOMY !== $taxonomy->name
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
 * Layout for this post type.
 *
 * @return void
 */
function layout(): void {
	// Only add filter when viewing a single post.
	if ( is_singular( POST_TYPE ) ) {
		add_filter( 'quark_front_end_data', __NAMESPACE__ . '\\layout_single' );
	}
}

/**
 * Layout: Single.
 *
 * @param mixed[] $data Front-end data.
 *
 * @return mixed[]
 */
function layout_single( array $data = [] ): array {
	// Get post.
	$post = get();

	// Bail if post does not exist or not an instance of WP_Post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return [];
	}

	// Layout.
	$data['layout'] = 'single';

	// Build data.
	$data['data'] = array_merge( $data['data'] ?? [], $post );

	// Post content.
	$data['data']['post_content'] = $post['post']->post_content;

	// Prepare blocks.
	prepare_content_with_blocks( $data['data']['post_content'] );

	// Return front-end data.
	return $data;
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
