<?php
/**
 * Block: Media Content Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaContentCard;

use WP_Block;

const COMPONENT  = 'parts.media-content-card';
const BLOCK_NAME = 'quark/media-content-card';

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
 * @param mixed[]       $attributes Block attributes.
 * @param string        $content Block default content.
 * @param WP_Block|null $block Block instance.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Check if we have an image.
	if ( empty( $block->attributes['image']['id'] ) ) {
		return '';
	}

	// Initialize attributes.
	$component_attributes = [
		'is_compact' => $block->attributes['isCompact'],
		'content'    => [],
	];

	// Add Image Id.
	$component_attributes['image_id'] = $block->attributes['image']['id'];

	// Loop through innerblocks and build attributes.
	foreach ( $block->inner_blocks as $index => $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			return $content;
		}

		// Assign heading.
		$component_attributes['content'][ $index ]['heading'] = $inner_block->attributes['heading'];

		// Loop through inner inner blocks.
		foreach ( $inner_block->inner_blocks as $inner_inner_block ) {
			// Check for block.
			if ( ! $inner_inner_block instanceof WP_Block ) {
				return $content;
			}

			// Update slot.
			if ( 'quark/media-content-info' !== $inner_inner_block->name ) {
				$component_attributes['content'][ $index ]['slot'] = implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );
			} else {
				$component_attributes['content'][ $index ]['content_info'][] = [
					'label'  => $inner_inner_block->attributes['label'],
					'value'  => $inner_inner_block->attributes['value'],
					'url'    => ! empty( $inner_inner_block->attributes['url']['url'] ) ? $inner_inner_block->attributes['url']['url'] : '',
					'target' => ! empty( $inner_inner_block->attributes['url']['newWindow'] ) ? '_blank' : '',
				];
			}
		}
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
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-column' ] = [
		'text' => [ 'heading' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-info' ] = [
		'text' => [
			'label',
			'value',
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
