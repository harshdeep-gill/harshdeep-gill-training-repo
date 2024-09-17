<?php
/**
 * Block: Media Text CTA Carousel.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaTextCTACarousel;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'media-text-cta-carousel';

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
 * @param mixed[]       $attributes Block attributes.
 * @param string        $content Block default content.
 * @param WP_Block|null $block Block instance.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check if block is an instance of WP_Block.
	if ( ! $block instanceof WP_Block || ! $block->inner_blocks instanceof WP_Block_List ) {
		return $content;
	}

	// Initialize variables.
	$media_text_cta_carousel = '';

	// Process inner blocks.
	foreach ( $block->inner_blocks as $media_text_cta_block ) {
		// Check for media-text-cta block.
		if ( ! $media_text_cta_block instanceof WP_Block || 'quark/media-text-cta' !== $media_text_cta_block->name ) {
			continue;
		}

		// Render & Append the media-text-cta block.
		$media_text_cta_carousel .= quark_get_component(
			COMPONENT . '.item',
			[
				'slot' => render_block( $media_text_cta_block->parsed_block ),
			]
		);
	}

	// Return rendered component.
	return quark_get_component(
		COMPONENT,
		[
			'slot' => $media_text_cta_carousel,
		]
	);
}
