<?php
/**
 * Filters namespace.
 *
 * @package quark-search
 */

namespace Quark\Search\Filters;

use function Quark\Search\Departures\search;

/**
 * Bootstrap filters.
 *
 * @return void
 */
function bootstrap(): void {
	// Bootstrap filters.
}

/**
 * Get filters data for dates-rates.
 *
 * @param mixed[] $selected_filters Selected filters.
 *
 * @return mixed[]
 */
function get_filters_for_dates_rates( array $selected_filters = [] ): array {
	// Filter mapping.
	$filter_mapping = [
		'seasons'           => [
			'key'      => 'seasons',
			'function' => '\Quark\Search\Departures\get_region_and_season_search_filter_data',
			'default'  => [],
		],
		'expeditions'       => [
			'key'      => 'expeditions',
			'function' => '\Quark\Search\Departures\get_expedition_search_filter_data',
			'default'  => [],
		],
		'adventure_options' => [
			'key'      => 'adventure_options',
			'function' => '\Quark\Search\Departures\get_adventure_options_search_filter_data',
			'default'  => [],
		],
		'months'            => [
			'key'      => 'months',
			'function' => '\Quark\Search\Departures\get_month_search_filter_data',
			'default'  => [],
		],
		'durations'         => [
			'key'      => 'durations',
			'function' => '\Quark\Search\Departures\get_duration_search_filter_data',
			'default'  => [],
		],
		'ships'             => [
			'key'      => 'ships',
			'function' => '\Quark\Search\Departures\get_ship_search_filter_data',
			'default'  => [],
		],
	];

	// Remove non-filter keys.
	$selected_filters = array_filter(
		$selected_filters,
		function ( $key ) use ( $filter_mapping ) {
			return array_key_exists( $key, $filter_mapping );
		},
		ARRAY_FILTER_USE_KEY
	);

	// Run search.
	$result = search( $selected_filters, true );

	// Departure ids.
	$departure_ids = $result['ids'];

	// Filters.
	foreach ( $filter_mapping as $filter_key => $filter ) {
		// Check if function callable.
		if ( ! is_callable( $filter['function'] ) ) {
			continue;
		}

        // Get filter data.
		$filters[ $filter_key ] = $filter['function']( $departure_ids );
	}

    // Return filters if no selected filters.
	if ( empty( $selected_filters ) ) {
		return $filters;
	}

	// Unset the last filter.
	$last_filter = array_pop( $selected_filters );

	// Run search.
	$result = search( $selected_filters, true );

	// Departure ids.
	$departure_ids = $result['ids'];

    // Get complete filters.
	return $filters;
}
