<?php
/**
 * Block: Search Filters Bar
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchFilterBar;

use WP_Block;

const COMPONENT = 'parts.search-filters-bar';

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
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content The block content.
 * @param WP_Block|null $block The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize the component attributes.
	$component_attributes = [
		'antarctic_image_id' => 0,
		'arctic_image_id'    => 0,
		'antarctic_cta'      => [],
		'arctic_cta'         => [],
	];

	// Check if antarctic image exists.
	if ( ! empty( $attributes['antarcticImage'] ) && is_array( $attributes['antarcticImage'] ) ) {
		$component_attributes['antarctic_image_id'] = $attributes['antarcticImage']['id'];
		$component_attributes['arctic_image_id']    = $attributes['arcticImage']['id'];
	}

	// Check if arctic image exists.
	if ( ! empty( $attributes['arcticImage'] ) && is_array( $attributes['arcticImage'] ) ) {
		$component_attributes['arctic_image_id'] = $attributes['arcticImage']['id'];
	}

	// Check is antarctic cta url exists.
	if ( ! empty( $attributes['antarcticCtaUrl'] ) && is_array( $attributes['antarcticCtaUrl'] ) ) {
		$component_attributes['antarctic_cta'] = [
			'url'  => $attributes['antarcticCtaUrl']['url'] ?? 0,
			'text' => $attributes['antarcticCtaUrl']['text'] ?? 0,
		];
	}

	// Check is arctic cta url exists.
	if ( ! empty( $attributes['arcticCtaUrl'] ) && is_array( $attributes['arcticCtaUrl'] ) ) {
		$component_attributes['arctic_cta'] = [
			'url'  => $attributes['arcticCtaUrl']['url'] ?? '',
			'text' => $attributes['arcticCtaUrl']['text'] ?? '',
		];
	}

	// Return built component.
	return quark_get_component( COMPONENT, $component_attributes );
}
