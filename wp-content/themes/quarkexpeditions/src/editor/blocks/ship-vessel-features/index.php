<?php
/**
 * Block: Ship Vessel Features.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ShipVesselFeatures;

use WP_Block;

const COMPONENT = 'parts.media-description-cards';

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

	// Initialize Cards.
	$cards = [];

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Check if we have an image.
		if ( empty( $inner_block->attributes['image']['id'] ) || 'quark/ship-vessel-features-card' !== $inner_block->name ) {
			continue;
		}

		// Add item.
		$cards[] = [
			'image_id'    => $inner_block->attributes['image']['id'] ?? 0,
			'title'       => $inner_block->attributes['title'] ?? '',
			'description' => $inner_block->attributes['description'] ?? '',
		];
	}

	// Build attributes.
	$component_attributes = [
		'cards' => $cards,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
