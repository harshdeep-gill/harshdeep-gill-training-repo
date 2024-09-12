<?php
/**
 * Partial: Dates and Rates.
 *
 * @package quark
 */

namespace Quark\Theme\Partials\DatesAndRates;

use function Quark\Departures\get_dates_rates_cards_data;
use function Quark\Search\Departures\search;

const PARTIAL_NAME = 'dates-and-rates';

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Hook this partial in.
	add_filter( 'qrk_get_partial', __NAMESPACE__ . '\\render', 10, 3 );
}

/**
 * Render the partial.
 *
 * @param mixed[] $output Partial output.
 * @param string  $name   Partial name.
 * @param mixed[] $data   Partial data.
 *
 * @return mixed[]
 */
function render( array $output = [], string $name = '', array $data = [] ): array {
	// Check for partial name.
	if ( PARTIAL_NAME !== $name ) {
		return $output;
	}

	// Init selected filters.
	$selected_filter = [
		'posts_per_load' => 8,
		'currency'       => 'USD',
		'expeditions'    => [],
	];

	// Verify and get selected filters.
	if ( ! empty( $data['selectedFilters'] ) && is_array( $data['selectedFilters'] ) ) {
		$selected_filter = wp_parse_args(
			$data['selectedFilters'],
			$selected_filter
		);
	}

	// Search for Departure post.
	$search_results = search( $selected_filter );

	// Get currency.
	$currency = $selected_filter['currency'] ? strval( $selected_filter['currency'] ) : 'USD';

	// Build component attributes.
	$attributes = [
		'cards' => get_dates_rates_cards_data( array_map( 'absint', $search_results['ids'] ), $currency ),
	];

	// Return rendered partial.
	return [
		'markup'          => quark_get_component(
			'parts.dates-rates-cards',
			$attributes
		),
		'noResultsMarkup' => 'No results found.',
		'data'            => [
			'resultCount'    => $search_results['result_count'],
			'page'           => $search_results['current_page'],
			'nextPage'       => $search_results['next_page'],
			'remainingCount' => $search_results['remaining_count'],
		],
	];
}
