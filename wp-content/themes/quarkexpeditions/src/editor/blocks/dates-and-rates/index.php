<?php
/**
 * Block Name: Dates and Rates.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\DatesAndRates;

use WP_Block;

use function Quark\Departures\get_dates_rates_cards_data;
use function Quark\Localization\get_current_currency;
use function Quark\Search\Departures\search;
use function Quark\Search\Filters\get_selected_filters_from_query_params;
use function Quark\Theme\Search_Filters\get_filters_for_dates_rates;

const COMPONENT  = 'parts.dates-rates';
const BLOCK_NAME = 'quark/dates-and-rates';

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

	// Disable translation for this block.
	add_filter( 'qrk_translation_disable_blocks', __NAMESPACE__ . '\\disable_translation' );
}

/**
 * Render this block.
 *
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content The block content.
 * @param WP_Block|null $block The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Get current currency.
	$currency = get_current_currency();

	// Selected filters from query params.
	$filter_query = get_selected_filters_from_query_params();

	// Init selected filters.
	$selected_filters = array_merge(
		[
			'posts_per_load' => 12,
			'currency'       => $currency,
			'sort'           => [ 'related_ship', 'date-now' ],
		],
		$filter_query
	);

	// Get filters data.
	$dates_rates_filter_data = get_filters_for_dates_rates( $selected_filters );

	// Search for Departure post.
	$result = search( $selected_filters );

	// Build component attributes.
	$component_attributes = [
		'filter_data'  => $dates_rates_filter_data,
		'result_count' => $result['result_count'],
		'cards'        => get_dates_rates_cards_data( array_map( 'absint', $result['ids'] ), $currency ),
		'per_page'     => $selected_filters['posts_per_load'],
		'currency'     => $currency,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Disable translation for this block.
 *
 * @param string[] $blocks The block names.
 *
 * @return string[] The block names.
 */
function disable_translation( array $blocks = [] ): array {
	// Add block name to disable translation.
	$blocks[] = BLOCK_NAME;

	// Return block names.
	return $blocks;
}
