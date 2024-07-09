<?php
/**
 * Block: Logo Grid.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LogoGrid;

use WP_Block;

const COMPONENT = 'logo-grid';

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
	foreach ( $block->parsed_block['innerBlocks'] as $inner_block ) {
		// Check if we have an image.
		if ( empty( $inner_block['attrs']['image']['id'] ) ) {
			continue;
		}

		// Add component to slot.
		$slot .= quark_get_component(
			COMPONENT . '.logo',
			[
				'image_id' => $inner_block['attrs']['image']['id'],
				'size'     => $attributes['size'],
			]
		);
	}

	// Build the component attributes.
	$component_attributes = [
		'alignment' => $attributes['alignment'],
		'size'      => $attributes['size'],
		'slot'      => $slot,
	];

	// Render the block markup.
	return quark_get_component( COMPONENT, $component_attributes );
}
