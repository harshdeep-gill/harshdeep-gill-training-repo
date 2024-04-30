<?php
/**
 * Namespace functions.
 *
 * @package quark-brochures
 */

namespace Quark\Brochures;

use WP_Post;

use function Quark\Core\prepare_content_with_blocks;

const POST_TYPE = 'qrk_brochures';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_brochures_post_type' );

	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/brochures.php';
	}
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

	// Return front-end data.
	return $data;
}

/**
 * Get a Brochures.
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
		'permalink' => strval( get_permalink( $post ) ? : '' ),
	];
}
