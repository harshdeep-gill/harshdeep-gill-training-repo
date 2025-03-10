<?php
/**
 * Block: Breadcrumbs.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Breadcrumbs;

use WP_Block;

use function Travelopia\Breadcrumbs\get_breadcrumbs;

const COMPONENT = 'breadcrumbs';

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

	// Get breadcrumbs.
	$breadcrumbs = get_breadcrumbs();

	// Check if breadcrumbs are empty.
	if ( empty( $breadcrumbs ) || ! is_array( $breadcrumbs ) ) {
		$breadcrumbs = [];
	}

	// Build component attributes.
	$component_attributes['breadcrumbs'] = $breadcrumbs;

	// Set appearance.
	$component_attributes['appearance'] = 'black' === $attributes['textColor'] ? 'light' : 'dark';

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
