<?php
/**
 * Filters namespace.
 *
 * @package quark-search
 */

namespace Quark\Search\Filters;

use WP_Post;
use WP_Term;

use function Quark\Expeditions\get as get_expedition_post;
use function Quark\Expeditions\get_destination_term_by_code;
use function Quark\Search\Departures\search;
use function Quark\Ships\get as get_ship_post;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Search\Departures\FACET_TYPE_FIELD;
use const Quark\Search\Departures\FACET_TYPE_RANGE;
use const Quark\StaffMembers\SEASON_TAXONOMY;

const SEASON_FILTER_KEY           = 'seasons';
const EXPEDITION_FILTER_KEY       = 'expeditions';
const ADVENTURE_OPTION_FILTER_KEY = 'adventure_options';
const SHIP_FILTER_KEY             = 'ships';
const MONTH_FILTER_KEY            = 'months';
const DURATION_FILTER_KEY         = 'durations';

const FILTERS_MAPPING = [
	SEASON_FILTER_KEY           => [
		'key'        => SEASON_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'region_season_str',
			'type' => FACET_TYPE_FIELD,
		],
		'function'   => '\Quark\Search\Filters\get_region_and_season_filter',
		'default'    => [],
	],
	EXPEDITION_FILTER_KEY       => [
		'key'        => EXPEDITION_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'related_expedition_str',
			'type' => FACET_TYPE_FIELD,
		],
		'function'   => '\Quark\Search\Filters\get_expedition_filter',
		'default'    => [],
	],
	ADVENTURE_OPTION_FILTER_KEY => [
		'key'        => ADVENTURE_OPTION_FILTER_KEY,
		'solr_facet' => [
			'key'  => ADVENTURE_OPTION_CATEGORY . '_taxonomy_id',
			'type' => FACET_TYPE_FIELD,
		],
		'function'   => '\Quark\Search\Filters\get_adventure_options_filter',
		'default'    => [],
	],
	SHIP_FILTER_KEY             => [
		'key'        => SHIP_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'related_ship_str',
			'type' => FACET_TYPE_FIELD,
		],
		'function'   => '\Quark\Search\Filters\get_ship_filter',
		'default'    => [],
	],
	MONTH_FILTER_KEY            => [
		'key'        => MONTH_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'start_date_dt',
			'type' => FACET_TYPE_RANGE,
			'args' => [
				'start' => 'NOW/MONTH',
				'end'   => 'NOW/MONTH+2YEAR',
				'gap'   => '+1MONTH',
			],
		],
		'function'   => '\Quark\Search\Filters\get_month_filter',
		'default'    => [],
	],
	DURATION_FILTER_KEY         => [
		'key'        => DURATION_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'duration_i',
			'type' => FACET_TYPE_RANGE,
			'args' => [
				'start' => 1,
				'end'   => 50,
				'gap'   => 7,
			],
		],
		'function'   => '\Quark\Search\Filters\get_duration_filter',
		'default'    => [],
	],
];

/**
 * Bootstrap filters.
 *
 * @return void
 */
function bootstrap(): void {
	// Bootstrap filters.
}

/**
 * Construct region season filter from facet.
 *
 * @param mixed[] $region_season_facet Region season facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: string,
 *   count: int,
 * }>
 */
function get_region_and_season_filter( array $region_season_facet = [] ): array {
	// Bail if empty.
	if ( empty( $region_season_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through region season facet.
	foreach ( $region_season_facet as $region_season => $count ) {
		// Validate count.
		if ( empty( absint( $count ) ) || empty( $region_season ) || ! is_string( $region_season ) ) {
			continue;
		}

		// Get first 3 characters as region.
		$region_code = substr( $region_season, 0, 3 );
		$region_term = get_destination_term_by_code( $region_code );

		// Validate term.
		if ( ! $region_term instanceof WP_Term ) {
			continue;
		}

		// Get last 4 characters as season.
		$season = substr( $region_season, 4 );

		// Get term data.
		$season_term = get_term_by( 'slug', $season, SEASON_TAXONOMY );

		// Validate term.
		if ( ! $season_term instanceof WP_Term ) {
			continue;
		}

		// Continue if already set.
		if ( ! empty( $filter_data[ $region_season ] ) ) {
			continue;
		}

		// Prepare region and season data.
		$filter_data[ $region_season ] = [
			'label' => sprintf( '%s %s', $region_term->name, $season_term->name ),
			'value' => $region_season,
			'count' => absint( $count ),
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct expedition filter from facet.
 *
 * @param mixed[] $expedition_facet Expedition facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: int,
 *   count: int,
 * }>
 */
function get_expedition_filter( array $expedition_facet = [] ): array {
	// Bail if empty.
	if ( empty( $expedition_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through expedition facet.
	foreach ( $expedition_facet as $expedition_id => $count ) {
		// Convert to integer.
		$expedition_id = absint( $expedition_id );
		$count         = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $expedition_id ) ) {
			continue;
		}

		// Get expedition post.
		$expedition_post = get_expedition_post( $expedition_id );

		// Validate post.
		if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Expedition name.
		$expedition_title = $expedition_post['post']->post_title;

		// Continue if already set or empty title.
		if ( empty( $expedition_title ) || ! empty( $filter_data[ $expedition_id ] ) ) {
			continue;
		}

		// Prepare expedition data.
		$filter_data[ $expedition_id ] = [
			'label' => $expedition_title,
			'value' => $expedition_id,
			'count' => $count,
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct ship filter from facet.
 *
 * @param mixed[] $ship_facet Ship facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: int,
 *   count: int,
 * }>
 */
function get_ship_filter( array $ship_facet = [] ): array {
	// Bail if empty.
	if ( empty( $ship_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through ship facet.
	foreach ( $ship_facet as $ship_id => $count ) {
		// Convert to integer.
		$ship_id = absint( $ship_id );
		$count   = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $ship_id ) ) {
			continue;
		}

		// Get ship post.
		$ship_post = get_ship_post( $ship_id );

		// Validate post.
		if ( ! $ship_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Ship name.
		$ship_name = $ship_post['post']->post_title;

		// Continue if already set or empty title.
		if ( empty( $ship_name ) || ! empty( $filter_data[ $ship_id ] ) ) {
			continue;
		}

		// Prepare ship data.
		$filter_data[ $ship_id ] = [
			'label' => $ship_name,
			'value' => $ship_id,
			'count' => $count,
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct adventure options filter from facet.
 *
 * @param mixed[] $adventure_options_facet Adventure options facet.
 *
 * @return array<int, array{
 *    label: string,
 *    value: int,
 *    count: int,
 * }>
 */
function get_adventure_options_filter( array $adventure_options_facet = [] ): array {
	// Bail if empty.
	if ( empty( $adventure_options_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through adventure options facet.
	foreach ( $adventure_options_facet as $adventure_option_id => $count ) {
		// Convert to integer.
		$adventure_option_id = absint( $adventure_option_id );
		$count               = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $adventure_option_id ) ) {
			continue;
		}

		// Get term.
		$adventure_option_term = get_term_by( 'id', $adventure_option_id, ADVENTURE_OPTION_CATEGORY );

		// Validate term.
		if ( ! $adventure_option_term instanceof WP_Term ) {
			continue;
		}

		// Continue if already set.
		if ( ! empty( $filter_data[ $adventure_option_id ] ) ) {
			continue;
		}

		// Prepare adventure option data.
		$filter_data[ $adventure_option_id ] = [
			'label' => $adventure_option_term->name,
			'value' => $adventure_option_id,
			'count' => $count,
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct month filter from facet.
 *
 * @param mixed[] $month_facet Month facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: string,
 *   count: int,
 * }>
 */
function get_month_filter( array $month_facet = [] ): array {
	// Bail if empty.
	if ( empty( $month_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through month facet.
	foreach ( $month_facet as $month => $count ) {
		// Convert to integer.
		$count = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $month ) || ! is_string( $month ) ) {
			continue;
		}

		// Unix timestamp.
		$month = absint( strtotime( $month ) );

		// Validate month.
		if ( empty( $month ) ) {
			continue;
		}

		// Get month label and value.
		$month_value = gmdate( 'm-Y', $month );
		$month_label = gmdate( 'F Y', $month );

		// Continue if already set.
		if ( ! empty( $filter_data[ $month_value ] ) ) {
			continue;
		}

		// Prepare month data.
		$filter_data[ $month_value ] = [
			'label' => $month_label,
			'value' => $month_value,
			'count' => $count,
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct duration filter from facet.
 *
 * @param mixed[] $duration_facet Duration facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: string,
 *   count: int,
 * }>
 */
function get_duration_filter( array $duration_facet = [] ): array {
	// Bail if empty.
	if ( empty( $duration_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through duration facet.
	foreach ( $duration_facet as $duration => $count ) {
		// Convert to integer.
		$count    = absint( $count );
		$duration = absint( $duration );

		// Validate count.
		if ( empty( $count ) || empty( $duration ) ) {
			continue;
		}

		// Continue if already set.
		if ( ! empty( $filter_data[ $duration ] ) ) {
			continue;
		}

		// Duration value.
		$duration_value = sprintf( '%d-%d', $duration, $duration + 6 );

		// Prepare duration data.
		$filter_data[ $duration ] = [
			'label' => sprintf( '%s Days', $duration_value ),
			'value' => $duration_value,
			'count' => absint( $count ),
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Build filter options.
 *
 * @param string[] $filter_keys The list of filters to include (e.g., ['season', 'expedition', 'month', 'duration']).
 * @param mixed[]  $selected_filters The currently selected filters (e.g., ['season' => [1, 2], 'expedition' => [4, 5]]).
 *
 * @return mixed[]
 */
function build_filter_options( array $filter_keys = [], array $selected_filters = [] ): array {
	// Remove invalid filter keys.
	$filter_keys = array_filter(
		$filter_keys,
		function ( $filter_key ) {
			return array_key_exists( $filter_key, FILTERS_MAPPING );
		}
	);

	// If empty filter keys, return empty array.
	if ( empty( $filter_keys ) ) {
		return [];
	}

	// Remove non-filter keys along with empty filter keys.
	foreach ( $selected_filters as $key => $value ) {
		if ( empty( $key ) || ! array_key_exists( $key, FILTERS_MAPPING ) || empty( $value ) ) {
			unset( $selected_filters[ $key ] );
		}
	}

	// Pluck solr_facet from mapping whose key is in filter keys.
	$solr_facets = array_column(
		array_filter(
			FILTERS_MAPPING,
			function ( $filter, $key ) use ( $filter_keys ) {
				return in_array( $key, $filter_keys, true );
			},
			ARRAY_FILTER_USE_BOTH
		),
		'solr_facet'
	);

	// Run search.
	$result            = search( $selected_filters, $solr_facets );
	$solr_facet_result = $result['facet_results'];

	// Initialize filter options.
	$filter_options = [];

	// Filter options.
	foreach ( $filter_keys as $filter_key ) {
		// Filter value.
		$filter = FILTERS_MAPPING[ $filter_key ];

		// Bail if function is not callable.
		if ( ! is_callable( $filter['function'] ) ) {
			continue;
		}

		// Check if filter key exists in solr facet result.
		if ( empty( $solr_facet_result[ $filter['solr_facet']['key'] ] ) ) {
			continue;
		}

		// Get facet data.
		$facet_data = $solr_facet_result[ $filter['solr_facet']['key'] ];

		// Validate facet data.
		if ( ! is_array( $facet_data ) || empty( $facet_data['values'] ) ) {
			continue;
		}

		// Get filter data.
		$filter_options[ $filter_key ] = $filter['function']( $facet_data['values'] );
	}

	// Return filter options if no selected filters.
	if ( empty( $selected_filters ) ) {
		return $filter_options;
	}

	/**
	 * Get filters data for the last filter.
	 * This is done to conserve the last filter to its one step previous.
	 */

	// Get last filter key.
	$last_filter_key = array_key_last( $selected_filters );

	// Bail if empty or not in filter mapping.
	if ( empty( $last_filter_key ) || ! array_key_exists( $last_filter_key, FILTERS_MAPPING ) ) {
		return $filter_options;
	}

	// Get last filter key solr_facet key.
	$solr_facet_key = FILTERS_MAPPING[ $last_filter_key ]['solr_facet']['key'];

	// Remove last filter.
	array_pop( $selected_filters );

	// Pluck solr_facet keys for last filter.
	$solr_facets = array_filter(
		$solr_facets,
		function ( $solr_facet ) use ( $solr_facet_key ) {
			return $solr_facet['key'] === $solr_facet_key;
		}
	);

	// Run search.
	$result     = search( $selected_filters, $solr_facets );
	$facet_data = $result['facet_results'][ $solr_facet_key ];

	// Validate facet results.
	if ( empty( $facet_data ) || ! is_array( $facet_data ) || empty( $facet_data['values'] ) ) {
		return $filter_options;
	}

	// Get last filter data.
	$filter_options[ $last_filter_key ] = FILTERS_MAPPING[ $last_filter_key ]['function']( $facet_data['values'] );

	// Get complete filters.
	return $filter_options;
}

/**
 * Extracts the selected filters from query params.
 *
 * @return mixed[]
 */
function get_selected_filters_from_query_params(): array {
	// Filter query data.
	$raw_query_data = filter_input_array( INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	// Bail if empty.
	if ( empty( $raw_query_data ) ) {
		return [];
	}

	// Loop through filter query data.
	foreach ( $raw_query_data as $key => $value ) {
		// Validate key.
		if ( array_key_exists( $key, FILTERS_MAPPING ) ) {
			// Explode value.
			$raw_query_data[ $key ] = explode( ',', $value );
		}
	}

	// Return filter query data.
	return $raw_query_data;
}
