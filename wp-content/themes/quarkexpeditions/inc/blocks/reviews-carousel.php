<?php
/**
 * Block: Reviews Carousel.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewsCarousel;

use function _PHPStan_8b6260c21\Symfony\Component\String\s;

const BLOCK_NAME = 'quark/reviews-carousel';
const COMPONENT  = 'reviews-carousel';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize slot.
	$slot = '';

	// Build slot.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Add component to slot.
		$slot .= quark_get_component(
			COMPONENT . '.slide',
			[
				'title'  => $inner_block['attrs']['title'] ?? '',
				'author' => $inner_block['attrs']['author'] ?? '',
				'rating' => $inner_block['attrs']['rating'] ?? '5',
				'slot'   => apply_filters( 'the_content', $inner_block['attrs']['review'] ?? '' ),
			]
		);
	}

	// Build attributes.
	$attributes = [
		'slot' => quark_get_component(
			COMPONENT . '.carousel',
			[
				'slot' => $slot,
			]
		),
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
