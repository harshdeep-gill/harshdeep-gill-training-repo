<?php
/**
 * Block: Search Filters Bar
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchFilterBar;

use WP_Block;

const COMPONENT  = 'parts.search-filters-bar';
const BLOCK_NAME = 'quark/search-filters-bar';

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

	// Disable translation for this block.
	add_filter( 'qrk_translation_disable_blocks', __NAMESPACE__ . '\\disable_translation' );
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
		'antarctic_image_id'   => 0,
		'arctic_image_id'      => 0,
		'antarctic_cta'        => [],
		'arctic_cta'           => [],
		'all_destinations_cta' => [],
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

	// check if all destintions url exists.
	if ( ! empty( $attributes['allDestinationsUrl'] && is_array( $attributes['allDestinationsUrl'] ) ) ) {
		$component_attributes['all_destinations_cta'] = [
			'url'  => $attributes['allDestinationsUrl']['url'] ?? '',
			'text' => $attributes['allDestinationsUrl']['text'] ?? '',
		];
	}

	// Return built component.
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
		'image'  => [
			'antarcticImage',
			'arcticImage',
		],
		'object' => [
			'antarcticCtaUrl'    => [ 'text' ],
			'arcticCtaUrl'       => [ 'text' ],
			'allDestinationsUrl' => [ 'text' ],
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}

/**
 * Disable translation for this block.
 *
 * @param string[] $blocks The block names.
 *
 * @return string[] The block names.
 */
function disable_translation( array $blocks = [] ): array {
	// Add block name to disable translation.
	$blocks[] = BLOCK_NAME;

	// Return block names.
	return $blocks;
}
