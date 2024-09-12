<?php
/**
 * Filters namespace.
 *
 * @package quark-search
 */

namespace Quark\Search\Filters;

use function Quark\Search\Departures\search;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Search\Departures\FACET_TYPE_FIELD;
use const Quark\Search\Departures\FACET_TYPE_RANGE;

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
 * 1. Get filters data for selected filters.
 * 2. Get filters data for selected filters without last filter - done to conserve the last filter to its one step previous.
 *
 * @param mixed[] $selected_filters Selected filters.
 *
 * @return mixed[]
 */
function get_filters_for_dates_rates( array $selected_filters = [] ): array {
	// Filter mapping.
	$filter_mapping = [
		'seasons'           => [
			'key'        => 'seasons',
			'solr_facet' => [
				'key'  => 'region_season_str',
				'type' => FACET_TYPE_FIELD,
			],
			'function'   => '\Quark\Search\Departures\get_region_and_season_search_filter_data',
			'default'    => [],
		],
		'expeditions'       => [
			'key'        => 'expeditions',
			'solr_facet' => [
				'key'  => 'related_expedition_str',
				'type' => FACET_TYPE_FIELD,
			],
			'function'   => '\Quark\Search\Departures\get_expedition_search_filter_data',
			'default'    => [],
		],
		'adventure_options' => [
			'key'        => 'adventure_options',
			'solr_facet' => [
				'key'  => ADVENTURE_OPTION_CATEGORY . '_taxonomy_id',
				'type' => FACET_TYPE_FIELD,
			],
			'function'   => '\Quark\Search\Departures\get_adventure_options_search_filter_data',
			'default'    => [],
		],
		'ships'             => [
			'key'        => 'ships',
			'solr_facet' => [
				'key'  => 'related_ship_str',
				'type' => FACET_TYPE_FIELD,
			],
			'function'   => '\Quark\Search\Departures\get_ship_search_filter_data',
			'default'    => [],
		],
		'months'            => [
			'key'        => 'months',
			'solr_facet' => [
				'key'  => 'start_date_dt',
				'type' => FACET_TYPE_RANGE,
				'args' => [
					'start' => 'NOW/MONTH',
					'end'   => 'NOW/MONTH+2YEAR',
					'gap'   => '+1MONTH',
				],
			],
			'function'   => '\Quark\Search\Departures\get_month_search_filter_data',
			'default'    => [],
		],
		'durations'         => [
			'key'        => 'durations',
			'solr_facet' => [
				'key'  => 'duration_i',
				'type' => FACET_TYPE_RANGE,
				'args' => [
					'start' => 1,
					'end'   => 50,
					'gap'   => 7,
				],
			],
			'function'   => '\Quark\Search\Departures\get_duration_search_filter_data',
			'default'    => [],
		],
	];

	// Remove non-filter keys along with empty filter keys.
	foreach ( $selected_filters as $key => $value ) {
		if ( ! array_key_exists( $key, $filter_mapping ) || empty( $value ) ) {
			unset( $selected_filters[ $key ] );
		}
	}

	// Pluck solr_facet keys.
	$solr_facets = array_column( $filter_mapping, 'solr_facet' );

	// Run search.
	$result = search( $selected_filters, $solr_facets, true );

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

	// Get last filter key.
	$last_filter_key = array_key_last( $selected_filters );

	// Bail if empty or not in filter mapping.
	if ( empty( $last_filter_key ) || ! array_key_exists( $last_filter_key, $filter_mapping ) ) {
		return $filters;
	}

	// Get last filter key solr_facet key.
	$solr_facet_key = $filter_mapping[ $last_filter_key ]['solr_facet']['key'];

	// Remove last filter.
	array_pop( $selected_filters );

	// Pluck solr_facet keys except last filter.
	$solr_facets = array_filter(
		$solr_facets,
		function ( $solr_facet ) use ( $solr_facet_key ) {
			return $solr_facet['key'] === $solr_facet_key;
		}
	);

	// Run search.
	$result = search( $selected_filters, $solr_facets, true );

	// Departure ids.
	$departure_ids = $result['ids'];

	// Get last filter data.
	$filters[ $last_filter_key ] = $filter_mapping[ $last_filter_key ]['function']( $departure_ids );

	// Get complete filters.
	return $filters;
}
