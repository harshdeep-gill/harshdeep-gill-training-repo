<?php
/**
 * Block: Thumbnail Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ThumbnailCards;

use WP_Block;

const COMPONENT = 'thumbnail-cards';

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

	// Initialize slot.
	$slot = '';

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Check if we have an image.
		if ( empty( $inner_block->attributes['image']['id'] ) || 'quark/thumbnail-cards-card' !== $inner_block->name ) {
			continue;
		}

		// Add item.
		$slot .= quark_get_component(
			COMPONENT . '.card',
			[
				'slot'        => quark_get_component( COMPONENT . '.title', [ 'title' => $inner_block->attributes['title'] ?? '' ] ),
				'image_id'    => $inner_block->attributes['image']['id'],
				'url'         => $inner_block->attributes['url']['url'],
				'size'        => $inner_block->attributes['size'],
				'orientation' => $inner_block->attributes['orientation'],
			]
		);
	}

	// Build attributes.
	$component_attributes = [
		'slot'        => $slot,
		'is_carousel' => $attributes['isCarousel'],
		'full_width'  => $attributes['isFullWidth'],
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
