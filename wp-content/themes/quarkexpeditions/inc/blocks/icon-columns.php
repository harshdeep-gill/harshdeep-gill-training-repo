<?php
/**
 * Block: Icon Columns.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IconColumns;

const BLOCK_NAME = 'quark/icon-columns';
const COMPONENT  = 'icon-columns';

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

	// Start building slot.
	$slot = '';

	// Add columns.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		$slot .= quark_get_component(
			COMPONENT . '.column',
			[
				'slot' => quark_get_component(
					COMPONENT . '.icon',
					[
						'icon' => $inner_block['attrs']['icon'] ?? '',
					]
				) . quark_get_component(
					COMPONENT . '.title',
					[
						'title' => $inner_block['attrs']['title'] ?? '',
					]
				),
			]
		);
	}

	// Build attributes.
	$attributes = [
		'slot'    => $slot,
		'variant' => $block['attrs']['variant'] ?? '',
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
