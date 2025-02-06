<?php
/**
 * Block: Blog Post Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BlogPostCards;

use WP_Query;
use WP_Term;

use function Quark\Blog\get_cards_data;
use function Quark\Core\get_pagination_links;
use function Quark\Core\get_first_pagination_link;
use function Quark\Core\get_last_pagination_link;
use function Quark\Core\is_block_editor;

use const Quark\Blog\POST_TYPE as BLOG_POST_TYPE;

const COMPONENT  = 'parts.blog-post-cards';
const BLOCK_NAME = 'quark/blog-post-cards';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);

	// Disable translation for this block.
	add_filter( 'qrk_translation_disable_blocks', __NAMESPACE__ . '\\disable_translation' );
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Get current page.
	$current_page = get_query_var( 'paged' ) ?: 1;

	// Build query args.
	$args = [
		'post_type'              => BLOG_POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'posts_per_page'         => ( false === $attributes['hasPagination'] ) ? $attributes['totalPosts'] : $attributes['postsPerPage'],
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'paged'                  => $current_page,
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
		$args['posts_per_page'] = ( true === $attributes['hasPagination'] ) ? $attributes['postsPerPage'] : count( $attributes['ids'] );
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
	} elseif ( 'automatic' === $attributes['selection'] ) {
		/*
		 * This is specifically for archive page configuration. This will only work for Category archive page.
		 *
		 * It will not be available for block editor.
		 */
		// check for Category archive page.
		if ( is_archive() && is_category() ) {
			$term = get_queried_object();

			// check if its term.
			if ( ! $term instanceof WP_Term ) {
				return '';
			}

			// Get term ID and taxonomy.
			$term_id  = $term->term_id;
			$taxonomy = $term->taxonomy;

			// Build tax query.
			$tax_query[] = [
				'taxonomy' => $taxonomy,
				'terms'    => $term_id,
				'field'    => 'term_id',
				'operator' => 'IN',
			];

			// Add tax query to args.
			$args['tax_query'] = $tax_query;
		}
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

	// Initialize pagination.
	$pagination = '';

	// Check if we have cards data pagination.
	if ( ! empty( $attributes['hasPagination'] ) && ! is_block_editor() ) {
		$pagination = get_pagination_links(
			[
				'query' => $posts,
			]
		);
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'layout'             => ( false === $attributes['hasPagination'] ) ? $attributes['layout'] : 'grid',
			'is_mobile_carousel' => $attributes['isMobileCarousel'],
			'cards'              => $cards_data,
			'pagination'         => $pagination,
			'current_page'       => $current_page,
			'total_pages'        => $posts->max_num_pages,
			'first_page_link'    => 1 !== $current_page ? get_first_pagination_link() : '',
			'last_page_link'     => $current_page !== $posts->max_num_pages ? get_last_pagination_link( [ 'total' => $posts->max_num_pages ] ) : '',
		]
	);
}

/**
 * Disable translation for this block.
 *
 * @param string[] $blocks The block names.
 *
 * @return string[] The block names.
 */
function disable_translation( array $blocks = [] ): array {
	// Add block name to disable translation.
	$blocks[] = BLOCK_NAME;

	// Return block names.
	return $blocks;
}
