<?php
/**
 * Block: Media Carousel.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaCarousel;

const COMPONENT  = 'media-carousel';
const BLOCK_NAME = 'quark/media-carousel';

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
 * @param mixed[] $attributes Block attributes.
 *
 * @return string
 */
function render( array $attributes = [] ): string {
	// Initialize component slot.
	$slot = '';

	// Get Image ID.
	if ( is_array( $attributes['media'] ) && ! empty( $attributes['media'] ) ) {
		// Loop through the images.
		foreach ( $attributes['media'] as $image ) {
			// Build the slot.
			$slot .= quark_get_component( COMPONENT . '.item', [ 'image_id' => $image['id'] ] );
		}
	}

	// Render the component.
	return quark_get_component( COMPONENT, [ 'slot' => $slot ] );
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
		'image' => [ 'media' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
