<?php
/**
 * Block: Two Columns.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TwoColumns;

const BLOCK_NAME = 'quark/two-columns';
const COMPONENT  = 'two-columns';

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

	// Initialize columns.
	$column_1 = '';
	$column_2 = '';

	// Column 1.
	if ( ! empty( $block['innerBlocks'][0]['innerBlocks'] ) ) {
		$column_1 .= implode( '', array_map( 'render_block', $block['innerBlocks'][0]['innerBlocks'] ) );
	}

	// Column 2.
	if ( ! empty( $block['innerBlocks'][1] ) ) {
		$column_2 .= implode( '', array_map( 'render_block', $block['innerBlocks'][1]['innerBlocks'] ) );
	}

	// Build attributes.
	$attributes = [
		'border' => $block['attrs']['hasBorder'] ?? true,
		'slot'   => quark_get_component(
			COMPONENT . '.column',
			[
				'slot' => $column_1,
			]
		) . quark_get_component(
			COMPONENT . '.column',
			[
				'slot' => $column_2,
			]
		),
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
