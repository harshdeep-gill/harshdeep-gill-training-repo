<?php
/**
 * Block: Menu List.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MenuList;

const BLOCK_NAME = 'quark/menu-list';
const COMPONENT  = 'menu-list';

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
		if ( empty( $inner_block['attrs']['url']['url'] ) ) {
			continue;
		}

		// Add item.
		$slot .= quark_get_component(
			COMPONENT . '.item',
			[
				'title' => $inner_block['attrs']['title'] ?? '',
				'url'   => $inner_block['attrs']['url']['url'] ?? '',
			]
		);
	}

	// Build attributes.
	$attributes = [
		'title' => $block['attrs']['title'] ?? '',
		'slot'  => $slot,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
