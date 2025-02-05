<?php
/**
 * Block: Reviews Carousel.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewsCarousel;

use WP_Block;

const COMPONENT  = 'reviews-carousel';
const BLOCK_NAME = 'quark/reviews-carousel';

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

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-item' ] = [
		'text' => [ 'title', 'review', 'author' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
