<?php
/**
 * Block: Related Posts.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\RelatedPosts;

use WP_Query;
use WP_Post;

use function Quark\Blog\get_cards_data;

use const Quark\Blog\POST_TYPE as BLOG_POST_TYPE;

const COMPONENT  = 'parts.related-posts';
const BLOCK_NAME = 'quark/related-posts';

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

	// Get queried post object.
	$current_post = get_queried_object();

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
	} elseif ( 'auto' === $attributes['selection'] ) {
		// Check if we have a post object.
		if ( $current_post instanceof WP_Post && BLOG_POST_TYPE === $current_post->post_type ) {
			$categories = wp_get_post_categories( $current_post->ID );

			// Check if we have categories.
			if ( ! empty( $categories ) && is_array( $categories ) ) {
				$args['category__in']   = $categories;
				$args['posts_per_page'] = $attributes['totalPosts'] + 1;
			}
		}
	}

	// Query posts.
	$posts = new WP_Query( $args );

	// Get posts in array format of IDs.
	$post_ids = $posts->posts;

	// Check if we have posts.
	if ( 'auto' === $attributes['selection'] && $current_post instanceof WP_Post ) {
		// Get the key of the current staff member.
		$key = array_search( $current_post->ID, $post_ids, true );

		// Remove the current post from the list.
		if ( false !== $key ) {
			unset( $post_ids[ $key ] );
		}

		// Make sure we don't have extra post.
		$post_ids = array_slice( $post_ids, 0, $attributes['totalPosts'] );
	}

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
			'cards' => $cards_data,
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
