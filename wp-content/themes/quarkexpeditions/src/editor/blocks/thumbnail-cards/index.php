<?php
/**
 * Block: Thumbnail Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ThumbnailCards;

use WP_Block;

const COMPONENT  = 'thumbnail-cards';
const BLOCK_NAME = 'quark/thumbnail-cards';

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

	// Initialize slot.
	$slot = '';

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Check if we have an image.
		if ( 'quark/thumbnail-cards-card' !== $inner_block->name ) {
			continue;
		}

		// Check for both media type and image.
		if ( empty( $inner_block->attributes['image'] ) && empty( $inner_block->attributes['video'] ) ) {
			continue;
		}

		// Attributes.
		$card_attributes = [
			'slot'        => quark_get_component( COMPONENT . '.title', [ 'title' => $inner_block->attributes['title'] ?? '' ] ),
			'url'         => $inner_block->attributes['url']['url'] ?? '',
			'target'      => ! empty( $inner_block->attributes['url']['newWindow'] ) ? '_blank' : '_self',
			'size'        => $inner_block->attributes['size'],
			'orientation' => $inner_block->attributes['orientation'],
		];

		// Check for media type.
		if ( 'image' === $inner_block->attributes['mediaType'] ) {
			$card_attributes['image_id'] = $inner_block->attributes['image']['id'];
		} elseif ( 'video' === $inner_block->attributes['mediaType'] ) {
			$card_attributes['video_id'] = $inner_block->attributes['video']['id'];
		} else {
			continue;
		}

		// Add item.
		$slot .= quark_get_component(
			COMPONENT . '.card',
			$card_attributes
		);
	}

	// Build attributes.
	$component_attributes = [
		'slot'        => $slot,
		'is_carousel' => $attributes['isCarousel'],
		'full_width'  => $attributes['isFullWidth'],
	];

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
		'image' => [ 'image', 'video' ],
		'text'  => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
