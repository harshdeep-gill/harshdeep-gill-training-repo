<?php
/**
 * Namespace functions.
 *
 * @package quark-blog
 */

namespace Quark\Blog;

use WP_Post;

use function Quark\Core\prepare_content_with_blocks;

const POST_TYPE = 'post';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Enable primary term.
	add_filter( 'travelopia_primary_term_taxonomies', __NAMESPACE__ . '\\primary_term_taxonomies', 10, 2 );

	// Admin stuff.
	if ( is_admin() ) {
		add_filter( 'post_type_labels_' . POST_TYPE, __NAMESPACE__ . '\\update_blog_posts_admin_menu_label' );
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
 * Update blog post admin menu name to "Blog Posts".
 *
 * @param object|null $labels Original labels.
 *
 * @return object
 */
function update_blog_posts_admin_menu_label( object $labels = null ): object {
	// Update menu name.
	if ( isset( $labels->menu_name ) ) {
		$labels->menu_name = 'Blog Posts';
	}

	// Return updated labels.
	return (object) $labels;
}

/**
 * Get a Blog Post.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 *     post_thumbnail: int,
 * }
 */
function get( int $post_id = 0 ): array {
	// Get post.
	$post = get_post( $post_id );

	// Return empty array fields if post type does not match or not an instance of WP_Post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'           => null,
			'permalink'      => '',
			'post_thumbnail' => 0,
		];
	}

	// Return post data.
	return [
		'post'           => $post,
		'permalink'      => strval( get_permalink( $post ) ?: '' ),
		'post_thumbnail' => absint( get_post_thumbnail_id( $post ) ?: 0 ),
	];
}

/**
 * Add category to primary term taxonomies.
 *
 * @param mixed[] $taxonomies Array of taxonomy objects.
 * @param string  $post_type  Post type.
 *
 * @return mixed[]|array{
 *     string,
 * }
 */
function primary_term_taxonomies( array $taxonomies = [], string $post_type = '' ): array {
	// Add category to primary term taxonomies.
	if ( POST_TYPE === $post_type ) {
		// Add category to primary term taxonomies.
		$taxonomies[] = 'category';
	}

	// Return taxonomies.
	return $taxonomies;
}
