<?php
/**
 * Namespace functions.
 *
 * @package quark-brochures
 */

namespace Quark\Brochures;

use WP_Post;

const POST_TYPE   = 'qrk_brochures';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_brochures_post_type' );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/brochures.php';
	}
}

/**
 * Register brochures post type.
 *
 * @return void
 */
function register_brochures_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Brochures',
			'singular_name'      => 'Brochure',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Brochure',
			'edit_item'          => 'Edit Brochure',
			'new_item'           => 'New Brochure',
			'view_item'          => 'View Brochure',
			'search_items'       => 'Search Brochures',
			'not_found'          => 'No Brochures found',
			'not_found_in_trash' => 'No Brochures found in Trash',
			'parent_item_colon'  => 'Parent Brochure:',
			'menu_name'          => 'Brochures',
		],
		'supports'            => [
			'title',
			'revisions',
			'thumbnail',
		],
		'hierarchical'        => false,
		'public'              => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-pdf',
		'show_in_nav_menus'   => false,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
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
	do_action( 'qe_brochure_post_cache_busted', $post_id );
}

/**
 * Get a Brochures.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_thumbnail: int,
 *     post_meta: mixed[]
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
			'post'           => $cached_value['post'],
			'post_thumbnail' => $cached_value['post_thumbnail'] ?? 0,
			'post_meta'      => $cached_value['post_meta'] ?? [],
		];
	}

	// Get post.
	$post = get_post( $post_id );

	// Check for post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'           => null,
			'post_thumbnail' => 0,
			'post_meta'      => [],
		];
	}

	// Build data.
	$data = [
		'post'           => $post,
		'post_thumbnail' => get_post_thumbnail_id( $post ) ?: 0,
		'post_meta'      => [],
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

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}
