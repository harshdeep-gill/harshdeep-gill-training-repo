<?php
/**
 * Block: Hero Card Slider.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\HeroCardSlider;

use WP_Block;

const COMPONENT = 'parts.hero-card-slider';

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

	// Prepare the component attributes.
	$component_attributes = [
		'show_controls'   => $attributes['showControls'],
		'transition_type' => $attributes['transitionType'],
		'interval'        => $attributes['interval'],
		'items'           => [],
	];

	// Check for items and prepare them.
	if ( ! empty( $attributes['items'] ) && is_array( $attributes['items'] ) ) {
		foreach ( $attributes['items'] as $item ) {
			if ( empty( $item['id'] ) ) {
				continue;
			}

			// Append the item.
			$component_attributes['items'][] = [
				'image_id' => $item['id'],
			];
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
