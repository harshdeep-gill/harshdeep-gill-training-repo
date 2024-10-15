<?php
/**
 * Namespace functions for Blog Authors.
 *
 * @package quark-blog
 */

namespace Quark\Blog\Authors;

use WP_Post;

const POST_TYPE   = 'qrk_blog_author';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_blog_author_post_type' );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Admin stuff.
	if ( is_admin() || ( defined( 'WP_CLI' ) && true === WP_CLI ) ) {
		// Custom fields.
		require_once __DIR__ . '/../../custom-fields/blog-authors.php';
	}
}

/**
 * Register Blog Authors post type.
 *
 * @return void
 */
function register_blog_author_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Blog Authors',
			'singular_name'      => 'Blog Author',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Blog Author',
			'edit_item'          => 'Edit Blog Author',
			'new_item'           => 'New Blog Author',
			'view_item'          => 'View Blog Author',
			'search_items'       => 'Search Blog Authors',
			'not_found'          => 'No Blog Authors found',
			'not_found_in_trash' => 'No Blog Authors found in Trash',
			'parent_item_colon'  => 'Parent Blog Author:',
			'menu_name'          => 'Blog Authors',
		],
		'supports'            => [
			'title',
			'editor',
			'thumbnail',
		],
		'hierarchical'        => false,
		'public'              => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php',
		'menu_icon'           => 'dashicons-admin-users',
		'show_in_nav_menus'   => false,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
		'show_in_rest'        => false,
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
	do_action( 'qe_blog_author_post_cache_busted', $post_id );
}

/**
 * Get a Blog Authors.
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
