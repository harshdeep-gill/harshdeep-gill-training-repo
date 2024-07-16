<?php
/**
 * Block: Simple Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SimpleCards;

use WP_Block;

const COMPONENT = 'simple-cards';

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
 * @param mixed[]       $attributes Block attributes.
 * @param string        $content Block default content.
 * @param WP_Block|null $block Block instance.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check if block is an instance of WP_Block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize slot.
	$slot = '';

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if we have an image.
		if ( ! $inner_block instanceof WP_Block || empty( $inner_block->attributes['image']['id'] ) ) {
			continue;
		}

		// Add item.
		$slot .= quark_get_component(
			COMPONENT . '.card',
			[
				'title'    => $inner_block->attributes['title'],
				'image_id' => $inner_block->attributes['image']['id'],
				'url'      => $inner_block->attributes['url']['url'] ?? '',
				'target'   => ! empty( $inner_block->attributes['url']['newWindow'] ) ? '_blank' : '',
			]
		);
	}

	// Build attributes.
	$attributes = [
		'slot' => $slot,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
