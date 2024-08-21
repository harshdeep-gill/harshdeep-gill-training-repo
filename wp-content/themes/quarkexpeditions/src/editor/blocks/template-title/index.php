<?php
/**
 * Block: Template Title.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TemplateTitle;

const COMPONENT = 'template-title';

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
	// Check for attributes.
	if ( empty( $attributes ) || ! isset( $attributes['title'] ) ) {
		return '';
	}

	// Return built component.
	return quark_get_component( COMPONENT, [ 'title' => $attributes['title'] ] );
}
