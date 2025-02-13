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
use function Quark\Softrip\Occupancies\get_masks_mapping;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Search\Departures\FACET_TYPE_FIELD;
use const Quark\Search\Departures\FACET_TYPE_RANGE;
use const Quark\StaffMembers\SEASON_TAXONOMY;

const SEASON_FILTER_KEY           = 'seasons';
const EXPEDITION_FILTER_KEY       = 'expeditions';
const ADVENTURE_OPTION_FILTER_KEY = 'adventure_options';
const SHIP_FILTER_KEY             = 'ships';
const MONTH_FILTER_KEY            = 'months';
const DURATION_FILTER_KEY         = 'durations';
const ITINERARY_LENGTH_FILTER_KEY = 'itinerary_lengths';
const LANGUAGE_FILTER_KEY         = 'languages';
const DESTINATION_FILTER_KEY      = 'destinations';
const CABIN_CLASS_FILTER_KEY      = 'cabin_classes';
const TRAVELERS_FILTER_KEY        = 'travelers';
const SORT_FILTER_KEY             = 'sort';
const PAGE_FILTER_KEY             = 'page';
const PER_PAGE_FILTER_KEY         = 'posts_per_load';
const CURRENCY_FILTER_KEY         = 'currency';

const FILTERS_MAPPING = [
	SEASON_FILTER_KEY           => [
		'key'        => SEASON_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'region_season_str',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_region_season_filter_options',
		'default'    => [],
	],
	EXPEDITION_FILTER_KEY       => [
		'key'        => EXPEDITION_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'related_expedition_str',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_expedition_filter_options',
		'default'    => [],
	],
	ADVENTURE_OPTION_FILTER_KEY => [
		'key'        => ADVENTURE_OPTION_FILTER_KEY,
		'solr_facet' => [
			'key'  => ADVENTURE_OPTION_CATEGORY . '_taxonomy_id',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_adventure_options_filter_options',
		'default'    => [],
	],
	SHIP_FILTER_KEY             => [
		'key'        => SHIP_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'related_ship_str',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_ship_filter_options',
		'default'    => [],
	],
	MONTH_FILTER_KEY            => [
		'key'        => MONTH_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'start_date_dt',
			'type' => FACET_TYPE_RANGE,
			'args' => [
				'start' => 'NOW/MONTH',
				'end'   => 'NOW/MONTH+3YEAR',
				'gap'   => '+1MONTH',
			],
		],
		'handler'    => __NAMESPACE__ . '\\get_month_filter_options',
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
		'handler'    => __NAMESPACE__ . '\\get_duration_filter_options',
		'default'    => [],
	],
	ITINERARY_LENGTH_FILTER_KEY => [
		'key'        => ITINERARY_LENGTH_FILTER_KEY,
		'solr_facet' => [
			'key'  => 'duration_i',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_itinerary_length_filter_options',
		'default'    => [],
	],
	LANGUAGE_FILTER_KEY         => [
		'key'        => LANGUAGE_FILTER_KEY,
		'solr_facet' => [
			'key'  => SPOKEN_LANGUAGE_TAXONOMY . '_taxonomy_id',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_language_filter_options',
		'default'    => [],
	],
	DESTINATION_FILTER_KEY      => [
		'key'        => DESTINATION_FILTER_KEY,
		'solr_facet' => [
			'key'  => DESTINATION_TAXONOMY . '_taxonomy_id',
			'type' => FACET_TYPE_FIELD,
		],
		'handler'    => __NAMESPACE__ . '\\get_destination_filter_options',
		'default'    => [],
	],
	CABIN_CLASS_FILTER_KEY      => [
		'key'     => CABIN_CLASS_FILTER_KEY,
		'handler' => __NAMESPACE__ . '\\get_cabin_class_filter',
		'default' => [],
	],
	TRAVELERS_FILTER_KEY        => [
		'key'     => TRAVELERS_FILTER_KEY,
		'handler' => __NAMESPACE__ . '\\get_travelers_filter',
		'default' => [],
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
function get_region_season_filter_options( array $region_season_facet = [] ): array {
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

		// Get term meta.
		$term_meta = get_term_meta( $region_term->term_id, 'show_next_year', true );

		// Check if term meta is not empty.
		$to_show_next_year = ! empty( $term_meta );

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

		// Set tab title.
		if ( $to_show_next_year ) {
			$season_label = sprintf( '%d.%d', $season_term->name, absint( substr( $season_term->name, -2 ) ) + 1 );
		} else {
			$season_label = sprintf( '%d', $season_term->name );
		}

		// Prepare region and season data.
		$filter_data[ $region_season ] = [
			'label' => sprintf( '%s %s', $region_term->name, $season_label ),
			'value' => $region_season,
			'count' => absint( $count ),
		];
	}

	// Sort alphabetically by label.
	uasort(
		$filter_data,
		function ( $a, $b ) {
			return strcasecmp( $a['label'], $b['label'] );
		}
	);

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
function get_expedition_filter_options( array $expedition_facet = [] ): array {
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

	// Sort alphabetically by label.
	uasort(
		$filter_data,
		function ( $a, $b ) {
			return strcasecmp( $a['label'], $b['label'] );
		}
	);

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
function get_ship_filter_options( array $ship_facet = [] ): array {
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

	// Sort alphabetically by label.
	uasort(
		$filter_data,
		function ( $a, $b ) {
			return strcasecmp( $a['label'], $b['label'] );
		}
	);

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
function get_adventure_options_filter_options( array $adventure_options_facet = [] ): array {
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

	// Sort alphabetically by label.
	uasort(
		$filter_data,
		function ( $a, $b ) {
			return strcasecmp( $a['label'], $b['label'] );
		}
	);

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
function get_month_filter_options( array $month_facet = [] ): array {
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
function get_duration_filter_options( array $duration_facet = [] ): array {
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
			/* translators: %s: Duration value. */
			'label' => sprintf( __( '%s Days', 'qrk' ), $duration_value ),
			'value' => $duration_value,
			'count' => absint( $count ),
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct itinerary length filter from facet.
 *
 * @param mixed[] $itinerary_length_facet Itinerary length facet.
 *
 * @return array<int, array{
 *    label: string,
 *    value: int,
 *    count: int,
 * }>
 */
function get_itinerary_length_filter_options( array $itinerary_length_facet = [] ): array {
	// Bail if empty.
	if ( empty( $itinerary_length_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through itinerary length facet.
	foreach ( $itinerary_length_facet as $itinerary_length => $count ) {
		// Convert to integer.
		$itinerary_length = absint( $itinerary_length );
		$count            = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $itinerary_length ) ) {
			continue;
		}

		// Continue if already set.
		if ( ! empty( $filter_data[ $itinerary_length ] ) ) {
			continue;
		}

		// Prepare itinerary length data.
		$filter_data[ $itinerary_length ] = [
			'label' => sprintf( '%d %s', $itinerary_length, _n( 'Day', 'Days', $itinerary_length, 'qrk' ) ),
			'value' => $itinerary_length,
			'count' => $count,
		];
	}

	// Sort by length.
	ksort( $filter_data );

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct language filter from facet.
 *
 * @param mixed[] $language_facet Language facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: int,
 *   count: int,
 * }>
 */
function get_language_filter_options( array $language_facet = [] ): array {
	// Bail if empty.
	if ( empty( $language_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through language facet.
	foreach ( $language_facet as $language_id => $count ) {
		// Convert to integer.
		$language_id = absint( $language_id );
		$count       = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $language_id ) ) {
			continue;
		}

		// Get term.
		$language_term = get_term_by( 'id', $language_id, SPOKEN_LANGUAGE_TAXONOMY );

		// Validate term.
		if ( ! $language_term instanceof WP_Term ) {
			continue;
		}

		// Continue if already set.
		if ( ! empty( $filter_data[ $language_id ] ) ) {
			continue;
		}

		// Prepare language data.
		$filter_data[ $language_id ] = [
			'label' => $language_term->name,
			'value' => $language_id,
			'count' => $count,
		];
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Construct destination filter from facet.
 *
 * @param mixed[] $destination_facet Destination facet.
 *
 * @return array<int, array{
 *   label: string,
 *   value: int,
 *   count: int,
 *   children: array<int, array{
 *      label: string,
 *      value: int,
 *      count: int,
 *      parent_id: int,
 *   }>,
 * }>
 */
function get_destination_filter_options( array $destination_facet = [] ): array {
	// Bail if empty.
	if ( empty( $destination_facet ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through destination facet.
	foreach ( $destination_facet as $destination_id => $count ) {
		// Convert to integer.
		$destination_id = absint( $destination_id );
		$count          = absint( $count );

		// Validate count.
		if ( empty( $count ) || empty( $destination_id ) ) {
			continue;
		}

		// Get term.
		$destination_term = get_term_by( 'id', $destination_id, DESTINATION_TAXONOMY );

		// Validate term.
		if ( ! $destination_term instanceof WP_Term ) {
			continue;
		}

		// Get parent term.
		if ( ! empty( $destination_term->parent ) ) {
			// Get parent term.
			$parent_term = get_term( $destination_term->parent, DESTINATION_TAXONOMY );

			// Validate parent term.
			if ( $parent_term instanceof WP_Term ) {
				// Add parent to filter data if not set.
				if ( empty( $filter_data[ $parent_term->term_id ] ) ) {
					// Prepare parent term data.
					$term_element = [
						'label'    => $parent_term->name,
						'value'    => $parent_term->term_id,
						'count'    => 0,
						'children' => [],
					];

					// Get cover image id.
					$cover_image_id = absint( get_term_meta( $parent_term->term_id, 'destination_image', true ) );

					// Add cover image if available.
					if ( ! empty( $cover_image_id ) ) {
						$term_element['image_id'] = $cover_image_id;
					}

					// Add parent term to filter data.
					$filter_data[ $parent_term->term_id ] = $term_element;
				}

				// Prepare destination data.
				$term_element = [
					'label'     => $destination_term->name,
					'value'     => $destination_id,
					'count'     => $count,
					'parent_id' => $parent_term->term_id,
				];

				// Get cover image id.
				$cover_image_id = absint( get_term_meta( $destination_id, 'destination_image', true ) );

				// Add cover image if available.
				if ( ! empty( $cover_image_id ) ) {
					$term_element['image_id'] = $cover_image_id;
				}

				// Add destination term to parent term.
				$filter_data[ $parent_term->term_id ]['children'][ $destination_id ] = $term_element;
			}
		} else {
			// Update count.
			if ( ! empty( $filter_data[ $destination_id ] ) ) {
				$filter_data[ $destination_id ]['count'] = $count;
				continue;
			}

			// Prepare destination data.
			$filter_data[ $destination_id ] = [
				'label'    => $destination_term->name,
				'value'    => $destination_id,
				'count'    => $count,
				'children' => [],
			];

			// Get cover image id.
			$cover_image_id = absint( get_term_meta( $destination_id, 'destination_image', true ) );

			// Add cover image if available.
			if ( ! empty( $cover_image_id ) ) {
				$filter_data[ $destination_id ]['image_id'] = $cover_image_id;
			}
		}
	}

	// Flatten children destinations.
	foreach ( $filter_data as $destination_term_id => $destination ) {
		// Continue if no children.
		if ( empty( $destination['children'] ) ) {
			continue;
		}

		// Flatten children.
		$filter_data[ $destination_term_id ]['children'] = array_values( $destination['children'] );
	}

	// Return filter data.
	return array_values( $filter_data );
}

/**
 * Get departure cabin class search filter data.
 *
 * @return array<int, array{
 *    label: string,
 *    value: int,
 * }>
 */
function get_cabin_class_filter(): array {
	// Get terms.
	$the_terms = get_terms(
		[
			'taxonomy'   => CABIN_CLASS_TAXONOMY,
			'hide_empty' => true,
		]
	);

	// Validate terms.
	if ( empty( $the_terms ) || ! is_array( $the_terms ) ) {
		return [];
	}

	// Initialize filter data.
	$filter_data = [];

	// Loop through terms and prepare data.
	foreach ( $the_terms as $term ) {
		// Validate term.
		if ( ! $term instanceof WP_Term ) {
			continue;
		}

		// Prepare filter data.
		$filter_data[] = [
			'label' => $term->name,
			'value' => $term->term_id,
		];
	}

	// Return filter data.
	return $filter_data;
}

/**
 * Get travelers filter data.
 *
 * @return array<int, array{
 *    label: string,
 *    value: string,
 * }>
 */
function get_travelers_filter(): array {
	// Get occupancy mask.
	$mask_mapping = get_masks_mapping();

	// Prepare travelers data.
	$travelers_data = [];

	// Loop through occupancy mask.
	foreach ( $mask_mapping as $mask => $mask_data ) {
		// Validate mask data.
		if ( ! is_array( $mask_data ) || empty( $mask_data['description'] ) ) {
			continue;
		}

		// Prepare travelers data.
		$travelers_data[] = [
			'label' => $mask_data['description'],
			'value' => $mask,
		];
	}

	// Return travelers data.
	return $travelers_data;
}

/**
 * Build filter options.
 *
 * @param string[] $filter_keys       The list of filters to include (e.g., ['season', 'expedition', 'month', 'duration']).
 * @param mixed[]  $selected_filters  The currently selected filters (e.g., ['season' => [1, 2], 'expedition' => [4, 5]]).
 *
 * @return array<string, array<int, array{
 *    label: string,
 *    value: string|int,
 *    count?:int,
 *    children?: array<int, array{
 *       label: string,
 *       value:int|string,
 *       count?:int,
 *       parent_id: int|string
 *     }>
 * }>>
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

	// Initialize solr facet result.
	$solr_facet_result = [];

	// Run Solr search if solr facets are not empty.
	if ( ! empty( $solr_facets ) ) {
		$result            = search( $selected_filters, $solr_facets );
		$solr_facet_result = $result['facet_results'];
	}

	// Initialize filter options.
	$filter_options = [];

	// Filter options.
	foreach ( $filter_keys as $filter_key ) {
		// Filter value.
		$filter = FILTERS_MAPPING[ $filter_key ];

		// Bail if function is not callable.
		if ( ! is_callable( $filter['handler'] ) ) {
			continue;
		}

		// If filter doesn't have solr_facet, call handler directly.
		if ( empty( $filter['solr_facet'] ) ) {
			$filter_options[ $filter_key ] = call_user_func( $filter['handler'] );
			continue;
		}

		// Check if filter key exists in solr facet result.
		if ( empty( $solr_facet_result[ $filter['solr_facet']['key'] ] ) ) {
			$filter_options[ $filter_key ] = [];
			continue;
		}

		// Get facet data.
		$facet_data = $solr_facet_result[ $filter['solr_facet']['key'] ];

		// Validate facet data.
		if ( ! is_array( $facet_data ) || empty( $facet_data['values'] ) ) {
			$filter_options[ $filter_key ] = [];
			continue;
		}

		// Get filter data.
		$filter_options[ $filter_key ] = call_user_func( $filter['handler'], $facet_data['values'] );
	}

	// Return filter options if no selected filters.
	if ( empty( $selected_filters ) ) {
		return $filter_options;
	}

	/**
	 * Get filters data for the last filter with solr facet.
	 * This is done to conserve the last filter to its one step previous.
	 */

	// Get last filter key from selected filters where solr_facet exists in filter mapping.
	$last_filter_key = array_key_last(
		array_filter(
			$selected_filters,
			function ( $key ) {
				return array_key_exists( $key, FILTERS_MAPPING ) && ! empty( FILTERS_MAPPING[ $key ]['solr_facet'] );
			},
			ARRAY_FILTER_USE_KEY
		)
	);

	// Bail if empty or not in filter mapping.
	if ( empty( $last_filter_key ) || ! array_key_exists( $last_filter_key, FILTERS_MAPPING ) ) {
		return $filter_options;
	}

	// Bail if last filter doesn't have solr_facet.
	if ( empty( FILTERS_MAPPING[ $last_filter_key ]['solr_facet'] ) ) {
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
	$filter_options[ $last_filter_key ] = FILTERS_MAPPING[ $last_filter_key ]['handler']( $facet_data['values'] );

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

/**
 * Get destination and month filter options.
 *
 * @param int    $destination_term_id Destination term ID.
 * @param string $month               Month.
 *
 * @return array<string, array<int, array{
 *    label: string,
 *    value: string|int,
 *    count?: int,
 *    children?: array<int, array{
 *       label: string,
 *       value: string|int,
 *       parent_id: int|string,
 *       image_id?: int,
 *       count?: int
 *    }>
 * }>>
 */
function get_destination_and_month_filter_options( int $destination_term_id = 0, string $month = '' ): array {
	// Filter keys.
	$filter_keys = [
		DESTINATION_FILTER_KEY,
		MONTH_FILTER_KEY,
	];

	// Initialize selected filters.
	$selected_filters = [];

	// Set selected filters.
	if ( ! empty( $destination_term_id ) ) {
		$selected_filters[ DESTINATION_FILTER_KEY ] = [ $destination_term_id ];
	} elseif ( ! empty( $month ) ) {
		$selected_filters[ MONTH_FILTER_KEY ] = [ $month ];
	}

	// Get filter options.
	$filter_options = build_filter_options( $filter_keys, $selected_filters );

	// Return filter options.
	return $filter_options;
}

/**
 * Get month options by expedition.
 *
 * @param int $expedition_id Expedition ID.
 *
 * @return array<string, array<int, array{
 *    label: string,
 *    value: string|int,
 *    count?:int,
 *    children?: array<int, array{
 *       label: string,
 *       value:int|string,
 *       count?:int,
 *       parent_id: int|string
 *     }>
 * }>>
 */
function get_expeditions_and_month_options_by_expedition( int $expedition_id = 0 ): array {
	// Filter keys.
	$filter_keys = [
		EXPEDITION_FILTER_KEY,
		MONTH_FILTER_KEY,
	];

	// Initialize selected filters.
	$selected_filters = [];

	// Check if expedition ID is empty.
	if ( $expedition_id ) {
		$selected_filters[ EXPEDITION_FILTER_KEY ] = [ $expedition_id ];
	}

	// Get filter options.
	$filter_options = build_filter_options( $filter_keys, $selected_filters );

	// Return filter options.
	return $filter_options;
}
