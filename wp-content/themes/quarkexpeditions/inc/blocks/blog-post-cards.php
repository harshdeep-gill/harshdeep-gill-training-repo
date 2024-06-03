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
				'selection'        => [
					'type'    => 'string',
					'default' => 'manual',
				],
				'layout'           => [
					'type'    => 'string',
					'default' => 'collage',
				],
				'ids'              => [
					'type'    => 'array',
					'default' => [],
				],
				'taxonomies'       => [
					'type'    => 'array',
					'default' => [],
				],
				'termIds'          => [
					'type'    => 'array',
					'default' => [],
				],
				'totalPosts'       => [
					'type'    => 'number',
					'default' => 5,
				],
				'isMobileCarousel' => [
					'type'    => 'boolean',
					'default' => true,
				],
				'hasOfferTag'      => [
					'type'    => 'boolean',
					'default' => false,
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
		'posts_per_page'         => $attributes['totalPosts'],
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'date',
		'order'                  => 'DESC',
	];

	// If the selection is manual, check if we have IDs.
	if ( 'manual' === $attributes['selection'] ) {
		// Return empty if selection is manual, but no IDs were selected.
		if ( empty( $attributes['ids'] ) ) {
			return '';
		}

		// Set WP_Query args for manual selection.
		$args['post__in']       = $attributes['ids'];
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $attributes['ids'] ); // phpcs:ignore
	} elseif ( 'byTerms' === $attributes['selection'] ) {
		// Return empty if selection by terms, but no terms or taxonomy were selected.
		if ( empty( $attributes['termIds'] ) || empty( $attributes['taxonomies'] ) ) {
			return '';
		}

		// Build tax query.
		$tax_query = [
			'relation' => 'AND',
		];

		// Add taxonomies to query and add to the tax query.
		foreach ( $attributes['taxonomies'] as $taxonomy ) {
			$tax_query[] = [
				'taxonomy'         => $taxonomy,
				'terms'            => $attributes['termIds'],
				'field'            => 'term_id',
				'include_children' => false,
				'operator'         => 'IN',
			];
		}

		// Add tax query to args.
		$args['tax_query'] = $tax_query;
	}

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
			'has_offer_tag'      => $attributes['hasOfferTag'] ?: false,
			'cards'              => $cards_data,
		]
	);
}
