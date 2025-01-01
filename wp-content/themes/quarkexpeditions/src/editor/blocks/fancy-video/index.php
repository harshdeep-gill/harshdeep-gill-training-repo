<?php
/**
 * Block: Fancy Video.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FancyVideo;

use WP_Block;

const COMPONENT  = 'fancy-video';
const BLOCK_NAME = 'quark/fancy-video';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
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
		'text'  => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
