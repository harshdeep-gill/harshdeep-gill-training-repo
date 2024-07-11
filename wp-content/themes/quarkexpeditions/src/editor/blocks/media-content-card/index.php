<?php
/**
 * Block: Media Content Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaContentCard;

use WP_Block;

const COMPONENT = 'parts.media-content-card';

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
