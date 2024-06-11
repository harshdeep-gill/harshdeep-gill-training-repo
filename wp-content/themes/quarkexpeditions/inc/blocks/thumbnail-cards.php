<?php
/**
 * Block: Thumbnail Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ThumbnailCards;

const BLOCK_NAME = 'quark/thumbnail-cards';
const COMPONENT  = 'thumbnail-cards';

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
		// Check if we have an image.
		if ( empty( $inner_block['attrs']['image']['id'] ) || 'quark/thumbnail-cards-card' !== $inner_block['blockName'] ) {
			continue;
		}

		// Add item.
		$slot .= quark_get_component(
			COMPONENT . '.card',
			[
				'slot'        => quark_get_component( COMPONENT . '.title', [ 'title' => $inner_block['attrs']['title'] ?? '' ] ),
				'image_id'    => $inner_block['attrs']['image']['id'] ?? 0,
				'url'         => $inner_block['attrs']['url']['url'] ?? '',
				'size'        => $inner_block['attrs']['size'] ?? 'medium',
				'orientation' => $inner_block['attrs']['orientation'] ?? 'portrait',
			]
		);
	}

	// Build attributes.
	$attributes = [
		'slot'        => $slot,
		'is_carousel' => $block['attrs']['isCarousel'] ?? true,
		'full_width'  => $block['attrs']['isFullWidth'] ?? true,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
