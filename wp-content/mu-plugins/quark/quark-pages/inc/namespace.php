<?php
/**
 * Namespace functions.
 *
 * @package quark-pages
 */

namespace Quark\Pages;

use WP_Post;

const POST_TYPE = 'page';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Limit revisions for Page post type.
	add_filter( 'wp_page_revisions_to_keep', __NAMESPACE__ . '\\limit_revisions_for_page' );
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
 * Limit revisions for Page post type.
 *
 * @return int Number of revisions to save for the post.
 */
function limit_revisions_for_page(): int {
	// Return revisions limit as 5.
	return 5;
}
