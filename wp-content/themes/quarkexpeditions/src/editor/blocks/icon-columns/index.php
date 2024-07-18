<?php
/**
 * Block: Icon Columns.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IconColumns;

use WP_Block;

const COMPONENT = 'icon-columns';

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

	// Start building slot.
	$slot = '';

	// Columns.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for inner block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Build slot.
		$slot .= quark_get_component(
			COMPONENT . '.column',
			[
				'slot' => quark_get_component(
					COMPONENT . '.icon',
					[
						'icon' => $inner_block->attributes['icon'],
					]
				) . quark_get_component(
					COMPONENT . '.title',
					[
						'title' => $inner_block->attributes['title'],
					]
				),
			]
		);
	}

	// Build attributes.
	$component_attributes = [
		'slot'    => $slot,
		'variant' => $attributes['variant'],
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
