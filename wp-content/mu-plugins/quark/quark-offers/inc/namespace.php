<?php
/**
 * Namespace functions.
 *
 * @package quark-offers
 */

namespace Quark\Offers;

use WP_Post;

const POST_TYPE   = 'qrk_offer';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type.
	add_action( 'init', __NAMESPACE__ . '\\register_offer_post_type' );

	// Other hooks.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache' );
}

/**
 * Register Offers post type.
 *
 * @return void
 */
function register_offer_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Offers',
			'singular_name'      => 'Offer',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Offer',
			'edit_item'          => 'Edit Offer',
			'new_item'           => 'New Offer',
			'view_item'          => 'View Offer',
			'search_items'       => 'Search Offers',
			'not_found'          => 'No Offers found',
			'not_found_in_trash' => 'No Offers found in Trash',
			'parent_item_colon'  => 'Parent Offer:',
			'menu_name'          => 'Offers',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-money-alt',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
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
			'slug'       => 'offer',
			'with_front' => false,
		],
		'capability_type'     => 'post',
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

	// Bail if post type does not match.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_offer_cache_busted', $post_id );
}

/**
 * Get an Offer page.
 *
 * @param int $page_id Offer Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 * }
 */
function get( int $page_id = 0 ): array {
	// Get post ID.
	if ( 0 === $page_id ) {
		$page_id = absint( get_the_ID() );
	}

	// Check for cached version.
	$cache_key    = CACHE_KEY . "_$page_id";
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value['post'] ) && $cached_value['post'] instanceof WP_Post ) {
		return [
			'post'      => $cached_value['post'],
			'permalink' => $cached_value['permalink'] ?? '',
		];
	}

	// Get post.
	$page = get_post( $page_id );

	// Return empty array fields if post type does not match or not an instance of WP_Post.
	if ( ! $page instanceof WP_Post || POST_TYPE !== $page->post_type ) {
		return [
			'post'      => null,
			'permalink' => '',
		];
	}

	// Build data.
	$data = [
		'post'      => $page,
		'permalink' => strval( get_permalink( $page ) ? : '' ),
	];

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}
