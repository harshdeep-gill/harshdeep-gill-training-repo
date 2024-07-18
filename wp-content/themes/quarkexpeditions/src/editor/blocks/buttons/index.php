<?php
/**
 * Block: Buttons.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Buttons;

const COMPONENT = 'buttons';

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
 * @param string  $content    The block default content.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '' ): string {
	// Build component attributes.
	$component_attributes = [
		'horizontal_align' => $attributes['horizontalAlignment'],
		'vertical_align'   => $attributes['verticalAlignment'],
		'slot'             => $content,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
