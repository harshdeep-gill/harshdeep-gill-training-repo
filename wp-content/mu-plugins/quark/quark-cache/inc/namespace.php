<?php
/**
 * Namespace functions.
 *
 * @package quark-cache
 */

namespace Quark\Cache;

use WP_CLI;
use WP_Post;

use const Quark\Cache\Edge\CACHED_POST_TYPE_SLUGS;

// Pricing blocks.
const PRICING_BLOCKS = [
	'quark/book-departures-expeditions',
	'quark/book-departures-ships',
	'quark/dates-and-rates',
	'quark/detailed-expeditions-carousel',
	'quark/expedition-details',
	'quark/expedition-search',
	'quark/expeditions',
	'quark/itineraries',
	'quark/product-cards',
	'quark/product-departures-card',
];

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-cache pricing-information', __NAMESPACE__ . '\\WP_CLI\\Pricing_Information' );
	}

	// Set meta for pricing block page.
	add_action( 'save_post', __NAMESPACE__ . '\\set_meta_for_pricing_block_posts' );
}

/**
 * Set meta for pricing block page.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if meta set, false otherwise.
 */
function set_meta_for_pricing_block_posts( int $post_id = 0 ): bool {
	// Bail if saving a revision OR Bail if doing autosave.
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return false;
	}

	// Get post ID.
	if ( 0 === $post_id ) {
		$post_id = absint( get_the_ID() );
	}

	// Get post.
	$post = get_post( $post_id );

	// Bail if no post.
	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, CACHED_POST_TYPE_SLUGS, true ) ) {
		return false;
	}

	// get post content.
	$content = get_post_field( 'post_content', $post_id );

	// Parse blocks.
	$blocks = parse_blocks( $content );

	// Skip if we don't have any blocks.
	if ( empty( $blocks ) ) {
		return false;
	}

	// Flatten blocks.
	$blocks            = _flatten_blocks( $blocks );
	$has_pricing_block = false;

	// Loop through blocks to find the pricing blocks.
	foreach ( $blocks as $block ) {
		// Skip if we don't have a block name.
		if ( empty( $block['blockName'] ) ) {
			continue;
		}

		// Skip if block name is not in the pricing blocks.
		if ( in_array( $block['blockName'], PRICING_BLOCKS, true ) ) {
			// Set flag to true.
			$has_pricing_block = true;

			// break the loop.
			break;
		}
	}

	// Update post meta if pricing block found.
	if ( true === $has_pricing_block ) {
		// Set pricing block meta.
		update_post_meta( $post_id, '_has_a_block_with_pricing_information', true );

		// Return true if pricing block found.
		return true;
	} else {
		delete_post_meta( $post_id, '_has_a_block_with_pricing_information' );
	}

	// Return false if no pricing block found.
	return false;
}
