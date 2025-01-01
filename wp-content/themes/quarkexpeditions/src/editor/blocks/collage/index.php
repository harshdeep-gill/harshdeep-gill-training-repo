<?php
/**
 * Block: Collage.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Collage;

use WP_Block;

const COMPONENT  = 'parts.collage';
const BLOCK_NAME = 'quark/collage';

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

	// Set block count.
	static $count = 0;

	// Initialize component attributes.
	$component_attributes = [];

	// Set collage name.
	$component_attributes['name'] = sprintf( 'collage_%d', ++$count );

	// Build component attributes.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if we have an image.
		if ( ! $inner_block instanceof WP_Block || empty( $inner_block->attributes['image']['id'] ) ) {
			continue;
		}

		// Build collage items with attributes.
		$component_attributes['items'][] = [
			'media_type' => $inner_block->attributes['mediaType'],
			'size'       => $inner_block->attributes['size'],
			'image_id'   => $inner_block->attributes['image']['id'],
			'title'      => $inner_block->attributes['caption'],
			'video_url'  => $inner_block->attributes['videoUrl'],
		];
	}

	// Render the component.
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
	$blocks_and_attributes[ BLOCK_NAME . '-media-item' ] = [
		'image' => [ 'image' ],
		'text'  => [ 'caption' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
