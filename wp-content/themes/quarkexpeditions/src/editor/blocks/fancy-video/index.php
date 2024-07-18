<?php
/**
 * Block: Fancy Video.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FancyVideo;

use WP_Block;

const COMPONENT = 'fancy-video';

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

	// Build component attributes.
	$component_attributes = [
		'image_id' => 0,
		'title'    => $block->attributes['title'],
		'url'      => $block->attributes['videoUrl'],
	];

	// Image.
	if ( ! empty( $block->attributes['image']['id'] ) ) {
		$component_attributes['image_id'] = absint( $block->attributes['image']['id'] );
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
