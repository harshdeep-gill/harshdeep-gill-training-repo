<?php
/**
 * Block: Bento Collage.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BentoCollage;

use WP_Block;

const COMPONENT = 'parts.bento-collage';

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

	// Initialize component attributes.
	$component_attributes = [];

	// Build component attributes.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if we have an image.
		if ( ! $inner_block instanceof WP_Block || empty( $inner_block->attributes['image']['id'] ) ) {
			continue;
		}

		// Build collage items with attributes.
		$component_attributes['items'][] = [
			'size'             => $inner_block->attributes['size'] ?? 'medium',
			'image_id'         => $inner_block->attributes['image']['id'] ?? 0,
			'title'            => $inner_block->attributes['title'] ?? '',
			'description'      => $inner_block->attributes['description'] ?? '',
			'content_position' => $inner_block->attributes['contentPosition'] ?? 'bottom',
			'cta'              => [
				'text'   => $inner_block->attributes['ctaText'] ?? '',
				'url'    => $inner_block->attributes['link']['url'] ?? '',
				'target' => empty( $inner_block->attributes['link']['newWindow'] ) ? '' : '_blank',
			],
		];
	}

	// Render the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
