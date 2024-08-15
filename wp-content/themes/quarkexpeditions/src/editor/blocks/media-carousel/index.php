<?php
/**
 * Block: Media Carousel.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaCarousel;

const COMPONENT = 'media-carousel';

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
