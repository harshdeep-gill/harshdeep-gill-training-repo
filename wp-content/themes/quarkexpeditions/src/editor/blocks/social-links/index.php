<?php
/**
 * Block: Social Links.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SocialLinks;

use WP_Block;

const COMPONENT = 'social-links';

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
			'render_callback'   => __NAMESPACE__ . '\\render',
			'skip_inner_blocks' => true,
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize items.
	$social_links = [];

	// Loop thorugh inner blocks.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Skip if not a block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Content.
		if ( 'quark/social-links-link' === $inner_block->name ) {
			// Check for icon and url.
			if ( empty( $inner_block->attributes['icon'] ) || empty( $inner_block->attributes['url'] ) ) {
				continue;
			}

			// Add to items.
			$social_links[ $inner_block->attributes['icon'] ] = [
				'link'   => $inner_block->attributes['url']['url'],
				'target' => ! empty( $inner_block->attributes['url']['newWindow'] ) ? '_blank' : '',
			];
		}
	}

	// Return rendered component.
	return quark_get_component(
		COMPONENT,
		[
			'links' => $social_links,
		]
	);
}
