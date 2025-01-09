<?php
/**
 * Block: Specifications.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Specifications;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.specifications';
const BLOCK_NAME = 'quark/specifications';

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

	// Initialize specifications.
	$specifications = [];

	// Get inner blocks.
	$specification_items = $block->inner_blocks;

	// Check for inner blocks.
	if ( $specification_items instanceof WP_Block_List ) {
		// Loop through inner blocks.
		foreach ( $specification_items as $specification_item ) {
			// Check for block.
			if ( ! $specification_item instanceof WP_Block ) {
				continue;
			}

			// Get attributes.
			$specification_attributes = $specification_item->attributes;

			// Prepare the specifications and add labels.
			$specifications[] = [
				'label' => $specification_attributes['label'],
				'value' => $specification_attributes['value'],
			];
		}
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'title'          => $attributes['title'],
			'specifications' => $specifications,
		]
	);
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
		'text' => [ 'title' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-item' ] = [
		'text' => [
			'label',
			'value',
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
