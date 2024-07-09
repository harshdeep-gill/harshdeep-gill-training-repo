<?php
/**
 * Block: Reviews Carousel.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewsCarousel;

use WP_Block;

const COMPONENT = 'reviews-carousel';

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
		// Check for inner block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Add component to slot.
		$slot .= quark_get_component(
			COMPONENT . '.slide',
			[
				'title'  => $inner_block->attributes['title'],
				'author' => $inner_block->attributes['author'],
				'rating' => $inner_block->attributes['rating'],
				'slot'   => apply_filters( 'the_content', $inner_block->attributes['review'] ),
			]
		);
	}

	// Build the component attributes.
	$component_attributes = [
		'slot' => quark_get_component(
			COMPONENT . '.carousel',
			[
				'slot' => $slot,
			]
		),
	];

	// Return the component markup.
	return quark_get_component( COMPONENT, $component_attributes );
}
