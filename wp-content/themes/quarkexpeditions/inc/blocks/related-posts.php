<?php
/**
 * Block: Related Posts.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\RelatedPosts;

use WP_Query;

const BLOCK_NAME = 'quark/related-posts';
const COMPONENT  = 'parts.related-posts';

/**
 * Block initialization.
 *
 * @return void
 */
function bootstrap(): void {
	// Avoid registering in admin to fix a conflict with Blade views.
	if ( ! is_admin() ) {
		add_action( 'wp_loaded', __NAMESPACE__ . '\\register' );
	}
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Register block.
	register_block_type(
		BLOCK_NAME,
		[
			'attributes'      => [
				'selection'  => [
					'type'    => 'string',
					'default' => 'manual',
				],
				'ids'        => [
					'type'    => 'array',
					'default' => [],
				],
				'totalPosts' => [
					'type'    => 'number',
					'default' => 3,
				],
			],
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes Block attributes.
 *
 * @return string
 */
function render( array $attributes = [] ): string {
	// Build query args.
	$args = [
		'post_type'              => \Quark\Blog\POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'posts_per_page'         => $attributes['totalPosts'],
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'date',
		'order'                  => 'DESC',
	];

	// If the selection is manual, we need to check if we have IDs.
	if ( 'manual' === $attributes['selection'] ) {
		// Return empty if manual select, but no IDs were selected.
		if ( empty( $attributes['ids'] ) ) {
			return '';
		}

		// Set WP_Query args for manual selection.
		$args['post__in']       = $attributes['ids'];
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $attributes['ids'] ); // phpcs:ignore
	}

	// Get posts.
	$posts = new WP_Query( $args );

	// Get posts in array format of IDs.
	$posts = $posts->posts;

	// Check if we have posts.
	if ( empty( $posts ) ) {
		return '';
	}

	// Build related posts data.
	$related_posts = [];

	// Loop through posts and get the related posts.
	foreach ( $posts as $post_id ) {
		$related_posts[] = \Quark\Blog\get( absint( $post_id ) );
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'related_posts' => $related_posts,
		]
	);
}
