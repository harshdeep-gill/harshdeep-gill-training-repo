<?php
/**
 * Namespace functions.
 *
 * @package quark-regions
 */

namespace Quark\Regions;

use WP_Post;

use function Quark\Core\prepare_content_with_blocks;

const POST_TYPE   = 'qrk_region';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type.
	add_action( 'init', __NAMESPACE__ . '\\register_region_post_type' );

	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Opt into stuff.
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );
}

/**
 * Register Regions post type.
 *
 * @return void
 */
function register_region_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Regions',
			'singular_name'      => 'Region',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Region',
			'edit_item'          => 'Edit Region',
			'new_item'           => 'New Region',
			'view_item'          => 'View Region',
			'search_items'       => 'Search Regions',
			'not_found'          => 'No Regions found',
			'not_found_in_trash' => 'No Regions found in Trash',
			'parent_item_colon'  => 'Parent Region:',
			'menu_name'          => 'Regions',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-admin-site',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
		],
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'regions',
			'with_front' => false,
		],
		'capability_type'     => 'post',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append this post type for taxonomy.
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
	// Add single layout if viewing a single post.
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
	$page = get();

	// Bail if post does not exist or not an instance of WP_Post.
	if ( empty( $page['post'] ) || ! $page['post'] instanceof WP_Post ) {
		return $data;
	}

	// Layout.
	$data['layout'] = 'single';

	// Build data.
	$data['data'] = array_merge( $data['data'] ?? [], $page );

	// Post content.
	$data['data']['post_content'] = $page['post']->post_content;

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
	do_action( 'qe_region_cache_busted', $post_id );
}

/**
 * Get a Region page.
 *
 * @param int $page_id Region Post ID.
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
