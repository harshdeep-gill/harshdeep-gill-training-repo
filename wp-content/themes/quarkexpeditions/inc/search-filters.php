<?php
/**
 * Search Filters functions.
 *
 * @package quark
 */

namespace Quark\Theme\Search_Filters;

use function Quark\Search\Filters\build_filter_options;

use const Quark\Search\Filters\ADVENTURE_OPTION_FILTER_KEY;
use const Quark\Search\Filters\CABIN_CLASS_FILTER_KEY;
use const Quark\Search\Filters\DESTINATION_FILTER_KEY;
use const Quark\Search\Filters\DURATION_FILTER_KEY;
use const Quark\Search\Filters\EXPEDITION_FILTER_KEY;
use const Quark\Search\Filters\ITINERARY_LENGTH_FILTER_KEY;
use const Quark\Search\Filters\LANGUAGE_FILTER_KEY;
use const Quark\Search\Filters\MONTH_FILTER_KEY;
use const Quark\Search\Filters\SEASON_FILTER_KEY;
use const Quark\Search\Filters\SHIP_FILTER_KEY;
use const Quark\Search\Filters\TRAVELERS_FILTER_KEY;

/**
 * Get filter options for dates rates.
 *
 * @param mixed[] $selected_filter Selected filters.
 *
 * @return array<string, array<int, array{
 *     label: string,
 *     value: string|int,
 *     count?:int,
 *     children?: array<int, array{
 *         label: string,
 *         value:int|string,
 *         count?:int,
 *         parent_id: int|string
 *      }>
 * }>>
 */
function get_filters_for_dates_rates( array $selected_filter = [] ): array {
	// Available filters on dates-rates.
	$filters_keys = [
		SEASON_FILTER_KEY,
		EXPEDITION_FILTER_KEY,
		ADVENTURE_OPTION_FILTER_KEY,
		MONTH_FILTER_KEY,
		DURATION_FILTER_KEY,
		SHIP_FILTER_KEY,
	];

	// Build filter options.
	$filter_options = build_filter_options( $filters_keys, $selected_filter );

	// Return available filters.
	return $filter_options;
}

/**
 * Get filter options for sidebar search filters.
 *
 * @param mixed[] $selected_filter Selected filters.
 *
 * @return array<string, array<int, array{
 *     label: string,
 *     value: string|int,
 *     count?:int,
 *     children?: array<int, array{
 *         label: string,
 *         value:int|string,
 *         count?:int,
 *         parent_id: int|string
 *      }>
 * }>>
 */
function get_filters_for_sidebar_search( array $selected_filter = [] ): array {
	// Available filters on sidebar.
	$filters_keys = [
		DESTINATION_FILTER_KEY,
		EXPEDITION_FILTER_KEY,
		ADVENTURE_OPTION_FILTER_KEY,
		MONTH_FILTER_KEY,
		SHIP_FILTER_KEY,
		ITINERARY_LENGTH_FILTER_KEY,
		LANGUAGE_FILTER_KEY,
		CABIN_CLASS_FILTER_KEY,
		TRAVELERS_FILTER_KEY,
	];

	// Build filter options.
	$filter_options = build_filter_options( $filters_keys, $selected_filter );

	// Return available filters.
	return $filter_options;
}
