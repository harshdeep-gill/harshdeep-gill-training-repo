<?php
/**
 * Block: Logo Grid.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LogoGrid;

const BLOCK_NAME = 'quark/logo-grid';
const COMPONENT  = 'logo-grid';

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
		if ( empty( $inner_block['attrs']['image']['id'] ) ) {
			continue;
		}

		// Add component to slot.
		$slot .= quark_get_component(
			COMPONENT . '.logo',
			[
				'image_id' => $inner_block['attrs']['image']['id'],
			]
		);
	}

	// Build component attributes.
	$attributes = [
		'alignment' => $block['attrs']['alignment'] ?? 'left',
		'size'      => $block['attrs']['size'] ?? 'small',
		'slot'      => $slot,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
