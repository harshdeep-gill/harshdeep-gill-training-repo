<?php
/**
 * Block Name: Accordion.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Accordion;

const BLOCK_NAME = 'quark/accordion';
const COMPONENT  = 'parts.accordion';

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

	// Build component attributes.
	$attributes = [
		'has_border' => $block['attrs']['hasBorder'] ?? true,
		'items'      => [],
	];

	// Items.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Check if inner block is for accordion item and has title.
		if (
			'quark/accordion-item' !== $inner_block['blockName'] ||
			empty( $inner_block['attrs']['title'] )
		) {
			continue;
		}

		// Initialize current item.
		$current_item = [];

		// Add title.
		$current_item['title'] = $inner_block['attrs']['title'] ?? '';

		// Add content.
		$current_item['content'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );

		// Add isOpen attribute.
		$current_item['open'] = $inner_block['attrs']['isOpen'] ?? false;

		// Add current item to array of items.
		$attributes['items'][] = $current_item;
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
