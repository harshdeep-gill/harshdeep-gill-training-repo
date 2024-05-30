<?php
/**
 * Block: Blog Post Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BlogPostCards;

use WP_Query;

use function Quark\Blog\get_cards_data;

use const Quark\Blog\POST_TYPE as BLOG_POST_TYPE;

const BLOCK_NAME = 'quark/blog-post-cards';
const COMPONENT  = 'parts.blog-post-cards';

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
				'layout'           => [
					'type'    => 'string',
					'default' => 'collage',
				],
				'ids'              => [
					'type'    => 'array',
					'default' => [],
				],
				'isMobileCarousel' => [
					'type'    => 'boolean',
					'default' => true,
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
		'post_type'              => BLOG_POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'post__in'               => $attributes['ids'],
		'posts_per_page'         => count( (array) $attributes['ids'] ), // phpcs:ignore
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'post__in',
	];

	// Get posts.
	$posts = new WP_Query( $args );

	// Get posts in array format of IDs.
	$post_ids = $posts->posts;

	// Check if we have posts.
	if ( empty( $post_ids ) ) {
		return '';
	}

	// Get blog post cards data.
	$cards_data = get_cards_data( array_map( 'absint', $post_ids ) );

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'layout'             => $attributes['layout'] ?? 'collage',
			'is_mobile_carousel' => $attributes['isMobileCarousel'] ?: false,
			'cards'              => $cards_data,
		]
	);
}
