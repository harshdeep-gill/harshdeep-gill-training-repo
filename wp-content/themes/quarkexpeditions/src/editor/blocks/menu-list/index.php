<?php
/**
 * Block: Menu List.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MenuList;

use WP_Block;

const COMPONENT = 'menu-list';

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
		if ( empty( $inner_block->attributes['url']['url'] ) ) {
			continue;
		}

		// Add item.
		$slot .= quark_get_component(
			COMPONENT . '.item',
			[
				'title'  => $inner_block->attributes['title'],
				'url'    => $inner_block->attributes['url']['url'],
				'target' => empty( $inner_block->attributes['url']['newWindow'] ) ? '_self' : '_blank',
			]
		);
	}

	// Build component attributes.
	$component_attributes = [
		'title' => $block->attributes['title'],
		'slot'  => $slot,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
