<?php
/**
 * Block: Hero Card Slider.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\HeroCardSlider;

use WP_Block;

const COMPONENT  = 'parts.hero-card-slider';
const BLOCK_NAME = 'quark/hero-card-slider';

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
		'is_lightbox'     => $attributes['isLightbox'],
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
		'image' => [ 'items' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
