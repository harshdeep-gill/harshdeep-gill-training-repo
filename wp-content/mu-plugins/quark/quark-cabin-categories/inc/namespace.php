<?php
/**
 * Namespace functions.
 *
 * @package quark-cabin-categories
 */

namespace Quark\CabinCategories;

use WP_Post;

use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

const POST_TYPE            = 'qrk_cabin_category';
const CABIN_CLASS_TAXONOMY = 'qrk_cabin_class';
const CACHE_KEY            = POST_TYPE;
const CACHE_GROUP          = POST_TYPE;

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

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_cabin_code_lookup_cache' );

	// Admin stuff.
	if ( is_admin() || ( defined( 'WP_CLI' ) && true === WP_CLI ) ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/cabin-categories.php';

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
 * Convert cabin category id to cabin_category post ID.
 *
 * @param string $cabin_id The ship code.
 *
 * @return int
 */
function get_id_from_cabin_code( string $cabin_id = '' ): int {
	// Check for cached version.
	$cache_key = CACHE_KEY . '_all_cabins';
	$cabins    = wp_cache_get( $cache_key, CACHE_GROUP );

	// If cache not set, lets build it with a DB query.
	if ( empty( $cabins ) ) {
		// Post Meta.
		global $wpdb;
		$cabins = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT
				m.*
			FROM
				$wpdb->postmeta as m
			RIGHT JOIN
				$wpdb->posts as p ON m.post_id = p.ID
			WHERE
				m.meta_key = 'cabin_category_id'
			AND
				p.post_type = %s
			",
				POST_TYPE
			),
			ARRAY_A
		);

		// Set cache and return data.
		wp_cache_set( $cache_key, $cabins, CACHE_GROUP );
	}

	// Find the cabin code.
	foreach ( $cabins as $cabin ) {
		if ( $cabin_id === $cabin['meta_value'] ) {
			return $cabin['post_id'];
		}
	}

	// Not found, return 0.
	return 0;
}

/**
 * Bust Cabin code lookup cache.
 *
 * @return void
 */
function bust_cabin_code_lookup_cache(): void {
	// Delete the code cache.
	wp_cache_delete( CACHE_KEY . '_all_cabins', CACHE_GROUP );

	// Trigger action to clear cache.
	do_action( 'qe_cabin_code_lookup_cache_busted' );
}
