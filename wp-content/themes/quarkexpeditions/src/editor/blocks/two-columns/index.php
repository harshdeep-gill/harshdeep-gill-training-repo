<?php
/**
 * Block: Two Columns.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TwoColumns;

const COMPONENT = 'two-columns';

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
 * @param mixed[]   $attributes The block attributes.
 * @param string    $content    The block content.
 * @param \WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', \WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof \WP_Block ) {
		return $content;
	}

	// Initialize columns.
	$column_1     = '';
	$column_2     = '';
	$columns_data = $block->parsed_block['innerBlocks'];

	// Column 1.
	if ( ! empty( $columns_data[0] ) ) {
		$column_1 .= implode( '', array_map( 'render_block', $columns_data[0]['innerBlocks'] ) );
	}

	// Column 2.
	if ( ! empty( $columns_data[1] ) ) {
		$column_2 .= implode( '', array_map( 'render_block', $columns_data[1]['innerBlocks'] ) );
	}

	// Build component attributes.
	$component_attributes = [
		'border' => $attributes['border'] ?? true,
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

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
