<?php
/**
 * Block: Icon Badge.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IconBadge;

const COMPONENT = 'icon-badge';

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
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Build component attributes.
	$component_attributes = [
		'background_color' => $attributes['color'],
		'icon'             => $attributes['icon'],
		'text'             => $attributes['text'],
		'class'            => $attributes['className'] ?? '',
	];

	// Return the markup.
	return quark_get_component( COMPONENT, $component_attributes );
}
