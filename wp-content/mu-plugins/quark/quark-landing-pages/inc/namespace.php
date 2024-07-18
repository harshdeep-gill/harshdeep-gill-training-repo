<?php
/**
 * Namespace functions.
 *
 * @package quark-landing-pages
 */

namespace Quark\LandingPages;

use WP_Post;

const POST_TYPE = 'qrk_landing_page';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_landing_page_post_type' );

	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );
}

/**
 * Register landing page post type.
 *
 * @return void
 */
function register_landing_page_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Landing Page',
			'singular_name'      => 'Landing Page',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Landing Page',
			'edit_item'          => 'Edit Landing Page',
			'new_item'           => 'New Landing Page',
			'view_item'          => 'View Landing Page',
			'search_items'       => 'Search Landing Pages',
			'not_found'          => 'No Landing Pages found',
			'not_found_in_trash' => 'No Landing Pages found in Trash',
			'parent_item_colon'  => 'Parent Landing Page:',
			'menu_name'          => 'Landing Pages',
		],
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor' ],
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-chart-bar',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'ppc-landing-pages',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'show_in_rest'        => true,
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
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

	// Return front-end data.
	return array_merge( $data, $page );
}

/**
 * Get a page.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 * }
 */
function get( int $post_id = 0 ): array {
	// Get post.
	$post = get_post( $post_id );

	// Return empty array fields if post type does not match or not an instance of WP_Post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'      => null,
			'permalink' => '',
		];
	}

	// Return post data.
	return [
		'post'      => $post,
		'permalink' => strval( get_permalink( $post ) ?: '' ),
	];
}
