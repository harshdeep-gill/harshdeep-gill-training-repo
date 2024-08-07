<?php
/**
 * Block Name: Excursion Accordions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExcursionAccordions;

use WP_Block;

use function Quark\Core\order_terms_by_hierarchy;

use const Quark\Expeditions\DESTINATION_TAXONOMY;

const COMPONENT = 'parts.excursion-accordions';

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

	// Check for terms.
	if ( ! isset( $attributes['destinationTermIds'] ) || ! is_array( $attributes['destinationTermIds'] ) ) {
		return $content;
	}

	// Convert terms to integers.
	$terms = array_map( 'absint', $attributes['destinationTermIds'] );

	// Organise terms.
	$organised_terms = order_terms_by_hierarchy( $terms, DESTINATION_TAXONOMY );

	// Initialize items.
	$items = [];

	// Prepare data required for the component.
	foreach ( $organised_terms as $term ) {
		// Initialize accordian item.
		$accordion_item = [];

		// Get parent term.
		$parent_term = $term['parent_term'];

		// Add accordion title.
		$accordion_item['accordion_title'] = $parent_term->name;

		// Check for accordian content.
		if ( ! empty( $term['child_terms'] ) ) {
			// Loop through child terms.
			foreach ( $term['child_terms'] as $child_term ) {
				// Initialize child item.
				$child_item = [
					'title'       => $child_term->name,
					'description' => apply_filters( 'the_content', $child_term->description ),
				];

				// Append child item to accordion item.
				$accordion_item['accordion_items'][] = $child_item;
			}
		}

		// Append accordion item to items.
		$items[] = $accordion_item;
	}

	// Build component attributes.
	$component_attributes = [
		'items' => $items,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
