<?php
/**
 * Block: Icon Info Grid.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IconInfoGrid;

use WP_Block;

const COMPONENT = 'icon-info-grid';

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

	// Build component attributes.
	$component_attributes = [
		'slot'             => '',
		'desktop_carousel' => $attributes['desktopCarousel'],
	];

	// Render inner blocks.
	foreach ( $block->inner_blocks as $icon_info_grid_item_block ) {
		// Check for inner block.
		if ( ! $icon_info_grid_item_block instanceof WP_Block ) {
			continue;
		}

		// Icon Info Grid Item.
		$icon_info_grid_item_component_attributes = [
			'slot' => '',
		];

		// Icon.
		$icon_info_grid_item_component_attributes['slot'] .= quark_get_component(
			COMPONENT . '.icon',
			[
				'icon' => $icon_info_grid_item_block->attributes['icon'],
			]
		);

		// Render inner blocks.
		foreach ( $icon_info_grid_item_block->inner_blocks as $inner_block ) {
			// Check for inner block.
			if ( ! $inner_block instanceof WP_Block ) {
				continue;
			}

			// Build slot.
			$icon_info_grid_item_component_attributes['slot'] .= $inner_block->render();
		}

		// Build slot.
		$component_attributes['slot'] .= quark_get_component(
			COMPONENT . '.item',
			$icon_info_grid_item_component_attributes
		);
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
