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
use function Quark\Search\Departures\get_region_and_season_search_filter_data;
use function Quark\Search\Departures\get_expedition_search_filter_data;
use function Quark\Search\Departures\get_adventure_options_search_filter_data;
use function Quark\Search\Departures\get_month_search_filter_data;
use function Quark\Search\Departures\get_duration_search_filter_data;
use function Quark\Search\Departures\get_ship_search_filter_data;
use function Quark\Search\Departures\search;

const COMPONENT = 'parts.dates-rates';

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

	// Init selected filters.
	$initial_filters = [
		'posts_per_load' => 5,
		'currency'       => $currency,
	];

	// Get dates and rates filter data.
	$dates_rates_filter_data = [
		'seasons'           => get_region_and_season_search_filter_data(),
		'expeditions'       => get_expedition_search_filter_data(),
		'adventure_options' => get_adventure_options_search_filter_data(),
		'months'            => get_month_search_filter_data(),
		'durations'         => get_duration_search_filter_data(),
		'ships'             => get_ship_search_filter_data(),
	];

	// Search for Departure post.
	$result = search( $initial_filters );

	// Build component attributes.
	$component_attributes = [
		'filter_data'  => $dates_rates_filter_data,
		'result_count' => $result['result_count'],
		'cards'        => get_dates_rates_cards_data( array_map( 'absint', $result['ids'] ), $currency ),
		'per_page'     => $initial_filters['posts_per_load'],
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
