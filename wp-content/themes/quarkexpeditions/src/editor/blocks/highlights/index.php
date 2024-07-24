<?php
/**
 * Block: Highlights.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Highlights;

use WP_Block;

const COMPONENT = 'highlights';

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
	$slot = quark_get_component(
		COMPONENT . '.title',
		[
			'title' => $attributes['title'],
		]
	);

	// Columns.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for inner block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Build item slot.
		$slot .= quark_get_component(
			COMPONENT . '.item',
			[
				'slot' => quark_get_component(
					COMPONENT . '.icon',
					[
						'icon' => $inner_block->attributes['icon'],
					]
				) . quark_get_component(
					COMPONENT . '.item-title',
					[
						'title' => $inner_block->attributes['title'],
					]
				),
			]
		);
	}

	// End building slot.
	$slot .= quark_get_component(
		COMPONENT . '.info',
		[
			'slot' => $attributes['info'],
		]
	);

	// Build attributes.
	$component_attributes = [
		'slot' => $slot,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
