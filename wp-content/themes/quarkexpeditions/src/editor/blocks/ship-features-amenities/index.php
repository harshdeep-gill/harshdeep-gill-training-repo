<?php
/**
 * Block: Ship Features & Amenities.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ShipFeaturesAmenities;

use WP_Block;

const COMPONENT  = 'parts.media-description-cards';
const BLOCK_NAME = 'quark/ship-features-amenities';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
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
		if ( empty( $inner_block->attributes['image']['id'] ) || 'quark/ship-features-amenities-card' !== $inner_block->name ) {
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

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-card' ] = [
		'image' => [ 'image' ],
		'text'  => [
			'title',
			'description',
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
