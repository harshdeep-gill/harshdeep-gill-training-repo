<?php
/**
 * Block: Product Departures Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ProductDeparturesCard;

const BLOCK_NAME = 'quark/product-departures-card';
const COMPONENT  = 'parts.product-departures-card';

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

	// Initialize attributes.
	$attributes = [
		'images'         => [],
		'title'          => '',
		'cta_badge_text' => '',
		'cta'            => '',
		'departures'     => [],
	];

	// Add Images.
	$attributes['images'][0] = $block['attrs']['image1']['id'] ?? 0;
	$attributes['images'][1] = $block['attrs']['image2']['id'] ?? 0;

	// Add CTA Badge Text.
	if ( ! empty( $block['attrs']['hasCtaBadge'] ) && ! empty( $block['attrs']['ctaBadgeText'] ) ) {
		$attributes['cta_badge_text'] = $block['attrs']['ctaBadgeText'];
	}

	// Build slot.
	foreach ( $block['innerBlocks'] as $inner_block ) {

		// Title.
		if ( 'quark/product-departures-card-title' === $inner_block['blockName'] ) {
			// Add title.
			$attributes['title'] = ! empty( $inner_block['attrs']['title'] ) ? $inner_block['attrs']['title'] : '';
		}

		// CTA.
		if ( 'quark/product-departures-card-cta' === $inner_block['blockName'] ) {
			if ( ! empty( $inner_block['innerBlocks'] ) ) {
				foreach ( $inner_block['innerBlocks'] as $child_block ) {
					// Get the slot.
					$attributes['cta'] .= render_block( $child_block );
				}
			}
		}

		// Departures.
		if ( 'quark/product-departures-card-departures' === $inner_block['blockName'] ) {
			$attributes['departures']['overline'] = $inner_block['attrs']['overline'] ?? '';

			// Initialize departure dates.
			$attributes['departures']['dates'] = [];

			// Loop through inner blocks.
			if ( ! empty( $inner_block['innerBlocks'] ) ) {

				// Initialize current departure date.
				$current_departure_date = [];

				// Loop through inner blocks.
				foreach ( $inner_block['innerBlocks'] as $index => $inner_inner_block ) {
					if ( 'quark/product-departures-card-dates' === $inner_inner_block['blockName'] ) {
						// Add offers.
						$current_departure_date['offer']      = $inner_inner_block['attrs']['offer'] ?? '';
						$current_departure_date['offer_text'] = $inner_inner_block['attrs']['offerText'] ?? '';

						// Add Is Sold Out.
						$current_departure_date['is_sold_out'] = ! empty( $inner_inner_block['attrs']['isSoldOut'] ) ? true : false;

						// Initialize date text.
						$current_departure_date['dates'] = '';

						// Loop through child blocks of departures dates.
						if ( ! empty( $inner_inner_block['innerBlocks'] ) ) {
							foreach ( $inner_inner_block['innerBlocks'] as $child_block ) {
								$current_departure_date['dates'] .= render_block( $child_block );
							}
						}
					}

					// Add current departure date to attributes.
					$attributes['departures']['dates'][] = $current_departure_date;
				}
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
