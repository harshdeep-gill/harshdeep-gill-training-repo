<?php
/**
 * Block: Logo Grid.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LogoGrid;

use WP_Block;

const COMPONENT  = 'logo-grid';
const BLOCK_NAME = 'quark/logo-grid';

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

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME ] = [
		'image' => [ 'image' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
