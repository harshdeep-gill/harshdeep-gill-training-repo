<?php
/**
 * Search Filters functions.
 *
 * @package quark
 */

namespace Quark\Theme\Search_Filters;

use function Quark\Search\Filters\build_filter_options;

use const Quark\Search\Filters\ADVENTURE_OPTION_FILTER_KEY;
use const Quark\Search\Filters\DURATION_FILTER_KEY;
use const Quark\Search\Filters\EXPEDITION_FILTER_KEY;
use const Quark\Search\Filters\MONTH_FILTER_KEY;
use const Quark\Search\Filters\SEASON_FILTER_KEY;
use const Quark\Search\Filters\SHIP_FILTER_KEY;

/**
 * Get filter options for dates rates.
 *
 * @param mixed[] $selected_filter Selected filters.
 *
 * @return mixed[]
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
