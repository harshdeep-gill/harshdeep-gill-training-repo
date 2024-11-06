<?php
/**
 * Namespace functions for Pre-Post Trip Options post type.
 *
 * @package namespace.php
 */

namespace Quark\Expeditions\PrePostTripOptions;

use WP_Post;

const POST_TYPE   = 'qrk_pre_post_trip';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_pre_post_trip_post_type' );

	// Other hooks.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache' );
}

/**
 * Register pre-post trip options post type.
 *
 * @return void
 */
function register_pre_post_trip_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Pre-Post Trip Options',
			'singular_name'      => 'Pre-Post Trip Option',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Pre-Post Trip Option',
			'edit_item'          => 'Edit Pre-Post Trip Option',
			'new_item'           => 'New Pre-Post Trip Option',
			'view_item'          => 'View Pre-Post Trip Option',
			'search_items'       => 'Search Pre-Post Trip Options',
			'not_found'          => 'No Pre-Post Trip Options found',
			'not_found_in_trash' => 'No Pre-Post Trip Options found in Trash',
			'parent_item_colon'  => 'Parent Pre-Post Trip Option:',
			'menu_name'          => 'Pre-Post Trip Options',
		],
		'supports'            => [
			'title',
			'editor',
			'thumbnail',
		],
		'hierarchical'        => false,
		'public'              => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=qrk_expedition',
		'menu_icon'           => 'dashicons-tickets-alt',
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
	// Get post type.
	$post_type = get_post_type( $post_id );

	// Check for post type.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_pre_post_trip_post_cache_busted', $post_id );
}

/**
 * Get a Pre-Post Trip Options.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_thumbnail: int,
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
		];
	}

	// Get post.
	$post = get_post( $post_id );

	// Check for post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'           => null,
			'post_thumbnail' => 0,
		];
	}

	// Build data.
	$data = [
		'post'           => $post,
		'post_thumbnail' => get_post_thumbnail_id( $post ) ? : 0,
	];

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}
