<?php
/**
 * Departure search namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search\Departures;

use WP_Post;
use WP_Term;
use Solarium\QueryType\Update\Query\Document\Document;

use function Quark\Departures\get as get_departure;
use function Quark\Departures\get_included_adventure_options;
use function Quark\Departures\get_paid_adventure_options;
use function Quark\Expeditions\get_destination_term_by_code;
use function Quark\Ships\get as get_ship_post;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;

const CACHE_GROUP = 'quark_search';

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Filers.
	add_filter( 'solr_index_custom_fields', __NAMESPACE__ . '\\solr_index_custom_fields' );
	add_filter( 'solr_build_document', __NAMESPACE__ . '\\filter_solr_build_document', 10, 2 );

	// Bust search cache on departure update.
	// TODO - Improve this to bust cache once per auto sync or manual sync.
	add_action( 'save_post_' . DEPARTURE_POST_TYPE, __NAMESPACE__ . '\\bust_search_cache' );

	// Load search class.
	require_once __DIR__ . '/class-search.php';
}

/**
 * Filter Solr build document.
 *
 * @param Document|null $document Solr Query.
 * @param WP_Post|null  $post WP Post.
 *
 * @return Document|null Solr Query.
 */
function filter_solr_build_document( Document $document = null, WP_Post $post = null ): Document|null {
	// Bail if method doesn't exist or post is not a WP_Post.
	if ( ! $document instanceof Document || ! $post instanceof WP_Post || DEPARTURE_POST_TYPE !== $post->post_type ) {
		return $document;
	}

	// Check for fields.
	$fields = $document->getFields();

	// Bail if no fields.
	if ( empty( $fields ) ) {
		return $document;
	}

	// Set post title for sorting.
	$document->setField( 'post_title_s', get_the_title( $post->ID ) );

	// Return document.
	return $document;
}

/**
 * Custom fields to index.
 *
 * @param string[] $custom_fields Custom field slugs.
 *
 * @return string[]
 */
function solr_index_custom_fields( array $custom_fields = [] ): array {
	// Return custom fields to index.
	return array_merge(
		$custom_fields,
		[
			'start_date',
			'end_date',
			'related_expedition',
			'related_ship',
			'duration',
			'region',
			'region_season',
		]
	);
}

/**
 * Get filters from URL.
 *
 * @return array{
 *      seasons: string,
 *      expeditions: string,
 *      adventure_options: string,
 *      months: string,
 *      durations: string,
 *      ships: string,
 *      sort: string,
 *      page: string,
 *  }
 */
function get_filters_from_url(): array {
	// Get filters from URL.
	return [
		'seasons'           => isset( $_GET['seasons'] ) ? strval( $_GET['seasons'] ) : '', // phpcs:ignore
		'expeditions'       => isset( $_GET['expeditions'] ) ? strval( $_GET['expeditions'] ) : '', // phpcs:ignore
		'adventure_options' => isset( $_GET['adventure_options'] ) ? strval( $_GET['adventure_options'] ) : '', // phpcs:ignore
		'months'            => isset( $_GET['months'] ) ? strval( $_GET['months'] ) : '', // phpcs:ignore
		'durations'         => isset( $_GET['durations'] ) ? strval( $_GET['durations'] ) : '', // phpcs:ignore
		'ships'             => isset( $_GET['ships'] ) ? strval( $_GET['ships'] ) : '', // phpcs:ignore
		'page'              => isset( $_GET['page'] ) ? strval( $_GET['page'] ) : '1', // phpcs:ignore
		'sort'              => isset( $_GET['sort'] ) ? strval( $_GET['sort'] ) : 'date-now', // phpcs:ignore
	];
}

/**
 * Parse the filters.
 *
 * @param mixed[] $filters Filters.
 *
 * @return array{
 *     seasons: string[],
 *     expeditions: int[],
 *     adventure_options: string[],
 *     months: string[],
 *     durations: string[],
 *     ships: int[],
 *     sort: string,
 *     page: int,
 *     posts_per_load: int,
 * }
 */
function parse_filters( array $filters = [] ): array {
	// Prepare filter default values.
	$filters = wp_parse_args(
		$filters,
		[
			'seasons'           => '',
			'expeditions'       => '',
			'adventure_options' => '',
			'months'            => '',
			'durations'         => '',
			'ships'             => '',
			'sort'              => 'date-now',
			'page'              => 1,
			'posts_per_load'    => 10,
		]
	);

	// Parse expeditions.
	if ( is_string( $filters['expeditions'] ) || is_int( $filters['expeditions'] ) ) {
		$filters['expeditions'] = array_filter( array_map( 'trim', explode( ',', strval( $filters['expeditions'] ) ) ) );
	}

	// Parse months.
	if ( is_string( $filters['months'] ) ) {
		$filters['months'] = array_filter( array_map( 'trim', explode( ',', $filters['months'] ) ) );
	} elseif ( is_array( $filters['months'] ) ) {
		$filters['months'] = array_filter( array_map( 'trim', $filters['months'] ) );
	}

	// Parse adventure_options slugs.
	if ( is_string( $filters['adventure_options'] ) || is_int( $filters['adventure_options'] ) ) {
		$filters['adventure_options'] = array_filter( array_map( 'trim', explode( ',', strval( $filters['adventure_options'] ) ) ) );
	}

	// Parse duration slugs.
	if ( is_string( $filters['durations'] ) || is_int( $filters['durations'] ) ) {
		$filters['durations'] = array_filter( array_map( 'trim', explode( '-', strval( $filters['durations'] ) ) ) );
	}

	// Parse seasons slugs.
	if ( is_string( $filters['seasons'] ) ) {
		$filters['seasons'] = array_filter( array_map( 'trim', explode( ',', $filters['seasons'] ) ) );
	}

	// Parse ships.
	if ( is_string( $filters['ships'] ) || is_int( $filters['ships'] ) ) {
		$filters['ships'] = array_filter( array_map( 'trim', explode( ',', strval( $filters['ships'] ) ) ) );
	}

	// Return parsed filters.
	return [
		'seasons'           => (array) $filters['seasons'],
		'expeditions'       => (array) $filters['expeditions'],
		'months'            => (array) $filters['months'],
		'adventure_options' => (array) $filters['adventure_options'],
		'durations'         => (array) $filters['durations'],
		'ships'             => (array) $filters['ships'],
		'page'              => absint( $filters['page'] ),
		'sort'              => $filters['sort'],
		'posts_per_load'    => absint( $filters['posts_per_load'] ),
	];
}

/**
 * Fetch Departure as per the filters provided.
 *
 * @param mixed[] $filters Filters.
 *
 * @return array{
 *     ids: int[],
 *     current_page: int,
 *     next_page: int,
 *     result_count: int,
 *     remaining_count: int,
 * }
 */
function search( array $filters = [] ): array {
	// Parse filters.
	$filters = parse_filters( $filters );

	// Get the filters.
	$sort              = $filters['sort'];
	$seasons           = array_map( 'strval', (array) $filters['seasons'] );
	$months            = array_map( 'strval', (array) $filters['months'] );
	$expeditions       = array_map( 'absint', (array) $filters['expeditions'] );
	$adventure_options = array_map( 'absint', (array) $filters['adventure_options'] );
	$durations         = array_map( 'absint', (array) $filters['durations'] );
	$ships             = array_map( 'absint', (array) $filters['ships'] );

	// Prepare search object.
	$search = new Search();
	$search->set_months( $months );
	$search->set_seasons( $seasons );
	$search->set_expeditions( $expeditions );
	$search->set_adventure_options( $adventure_options );
	$search->set_durations( $durations );
	$search->set_ships( $ships );
	$search->set_page( absint( $filters['page'] ) );
	$search->set_posts_per_page( absint( $filters['posts_per_load'] ?: 5 ) );
	$search->set_sort( $sort );

	// Returned filtered trips.
	return [
		'ids'             => $search->search(),
		'current_page'    => $search->current_page,
		'next_page'       => $search->next_page,
		'result_count'    => $search->result_count,
		'remaining_count' => $search->remaining_count,
	];
}

/**
 * Bust search cache on departure update.
 *
 * @return void
 */
function bust_search_cache(): void {
	// Bust cache by group if supported.
	if ( function_exists( 'wp_cache_delete_group' ) ) {
		// Bust cache by group.
		wp_cache_delete_group( CACHE_GROUP );
	} else {
		// Bust cache by key.
		wp_cache_delete( 'search_filter_region_season_data', CACHE_GROUP );
		wp_cache_delete( 'search_filter_expeditions_data', CACHE_GROUP );
		wp_cache_delete( 'search_filter_adventure_options_data', CACHE_GROUP );
		wp_cache_delete( 'search_filter_departure_month_data', CACHE_GROUP );
		wp_cache_delete( 'search_filter_departure_duration_data', CACHE_GROUP );
		wp_cache_delete( 'search_filter_ship_data', CACHE_GROUP );
	}
}

/**
 * Get region and season search filter data.
 *
 * @return string[]
 */
function get_region_and_season_search_filter_data(): array {
	// Get from cache.
	$cache_key                   = 'search_filter_region_season_data';
	$region_season_search_filter = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $region_season_search_filter ) && is_array( $region_season_search_filter ) ) {
		return $region_season_search_filter;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids  = $search->search();
	$region_seasons = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $region_seasons;
	}

	// Get region and season data.
	foreach ( $departure_ids as $departure_id ) {
		$departure = get_departure( $departure_id );

		// Get post meta.
		if ( ! is_array( $departure['post_meta'] ) || empty( $departure['post_meta']['region_season'] ) ) {
			continue;
		}

		// Get region and season.
		$region_seasons[ $departure_id ] = $departure['post_meta']['region_season'];
	}

	// Get unique region and season.
	$region_seasons = array_unique( $region_seasons );

	// Prepare filter data.
	$filter_data = [];

	// Prepare region and season data.
	foreach ( $region_seasons as $region_season ) {
		// region_season - ANT-2024-23.
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

		// Prepare region and season data.
		$filter_data[ $region_season ] = sprintf( '%s %s', $region_term->name, $season_term->name );
	}

	// Set cache.
	wp_cache_set( $cache_key, $filter_data, CACHE_GROUP );

	// Return filter data.
	return $filter_data;
}

/**
 * Get Expedition search filter data.
 *
 * @return array{}|array{
 *     int: string
 * }
 */
function get_expedition_search_filter_data(): array {
	// Get from cache.
	$cache_key                = 'search_filter_expeditions_data';
	$expedition_search_filter = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $expedition_search_filter ) && is_array( $expedition_search_filter ) ) {
		return $expedition_search_filter;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids = $search->search();
	$expeditions   = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $expeditions;
	}

	// Get expedition data.
	foreach ( $departure_ids as $departure_id ) {
		$departure = get_departure( $departure_id );

		// Get post meta.
		if ( ! is_array( $departure['post_meta'] ) || empty( $departure['post_meta']['related_expedition'] ) ) {
			continue;
		}

		// Get expedition.
		$expedition_id = absint( $departure['post_meta']['related_expedition'] );

		// Validate expedition.
		if ( empty( $expedition_id ) ) {
			continue;
		}

		// Prepare expedition data.
		$expeditions[ $expedition_id ] = get_the_title( $expedition_id );
	}

	// Set cache.
	wp_cache_set( $cache_key, $expeditions, CACHE_GROUP );

	// Return expedition data.
	return $expeditions;
}

/**
 * Get Adventure Options search filter data.
 *
 * @return array{}|array{
 *     int: string
 * }
 */
function get_adventure_options_search_filter_data(): array {
	// Get from cache.
	$cache_key                     = 'search_filter_adventure_options_data';
	$adventure_options_search_data = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $adventure_options_search_data ) && is_array( $adventure_options_search_data ) ) {
		return $adventure_options_search_data;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids     = $search->search();
	$adventure_options = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $adventure_options;
	}

	// Get adventure options data.
	foreach ( $departure_ids as $departure_id ) {
		// Prepare Included Adventure Options details.
		$include_options = get_included_adventure_options( $departure_id );

		// Loop through include_options.
		foreach ( $include_options as $include_option ) {
			if ( empty( $include_option['term_id'] ) ) {
				continue;
			}

			// Add to adventure options.
			$adventure_options[ $include_option['term_id'] ] = $include_option['name'];
		}

		// Prepare Paid Adventure Options details.
		$adventure_options = array_replace( $adventure_options, get_paid_adventure_options( $departure_id ) );
	}

	// Set cache.
	wp_cache_set( $cache_key, $adventure_options, CACHE_GROUP );

	// Return adventure options data.
	return $adventure_options;
}

/**
 * Get Departure Month search filter data.
 *
 * @return array{}|array{
 *     string: string
 * }
 */
function get_month_search_filter_data(): array {
	// Get from cache.
	$cache_key           = 'search_filter_departure_month_data';
	$month_search_filter = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $month_search_filter ) && is_array( $month_search_filter ) ) {
		return $month_search_filter;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids = $search->search();
	$months        = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $months;
	}

	// Get month data.
	foreach ( $departure_ids as $departure_id ) {
		$departure = get_departure( $departure_id );

		// Get post meta.
		if ( ! is_array( $departure['post_meta'] ) || empty( $departure['post_meta']['start_date'] ) ) {
			continue;
		}

		// Get start date.
		$start_date = $departure['post_meta']['start_date'];

		// Prepare month data - 10-2024 => October 2024.
		$month_key   = gmdate( 'm-Y', strtotime( $start_date ) );
		$month_value = gmdate( 'F Y', strtotime( $start_date ) );

		// Prepare month data.
		$months[ $month_key ] = $month_value;
	}

	// Sort the months array by keys (dates).
	uksort(
		$months,
		function ( $a, $b ) {
			// Adding "01-" to ensure proper date format for strtotime comparison.
			return strtotime( '01-' . $a ) - strtotime( '01-' . $b );
		}
	);

	// Set cache.
	wp_cache_set( $cache_key, $months, CACHE_GROUP );

	// Return month data.
	return $months;
}

/**
 * Get Departure Duration search filter data.
 *
 * @return array{}|array{
 *     string: string
 * }
 */
function get_duration_search_filter_data(): array {
	// Get from cache.
	$cache_key              = 'search_filter_departure_duration_data';
	$duration_search_filter = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $duration_search_filter ) && is_array( $duration_search_filter ) ) {
		return $duration_search_filter;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids = $search->search();
	$durations     = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $durations;
	}

	// Get duration data.
	foreach ( $departure_ids as $departure_id ) {
		$departure = get_departure( $departure_id );

		// Get post meta.
		if ( ! is_array( $departure['post_meta'] ) || empty( $departure['post_meta']['duration'] ) ) {
			continue;
		}

		// Get duration.
		$duration = $departure['post_meta']['duration'];

		// Prepare duration data.
		$durations[ $duration ] = $duration;
	}

	// Sort the durations array by keys (dates).
	ksort( $durations );

	// Create a new array with range of 7 days using the durations.
	// i.e. - 1-7 => 1-7 days, 8-14 => 8-14 days, 15-21 => 15-21 days, 22-28 => 22-28 days.
	$range_durations = [];

	// Loop through durations.
	foreach ( $durations as $duration ) {
		// Get duration value.
		$duration_value = absint( $duration );

		// Get range.
		$range = ceil( $duration_value / 7 );

		// Prepare Key and value.
		$range_key   = sprintf( '%d-%d', ( $range * 7 ) - 6, $range * 7 );
		$range_value = sprintf( '%d-%d Days', ( $range * 7 ) - 6, $range * 7 );

		// Prepare range duration.
		$range_durations[ $range_key ] = $range_value;
	}

	// Set cache.
	wp_cache_set( $cache_key, $range_durations, CACHE_GROUP );

	// Return duration data.
	return $range_durations;
}

/**
 * Get Departure Ship search filter data.
 *
 * @return array{}|array{
 *     int: string
 * }
 */
function get_ship_search_filter_data(): array {
	// Get from cache.
	$cache_key          = 'search_filter_ship_data';
	$ship_search_filter = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $ship_search_filter ) && is_array( $ship_search_filter ) ) {
		return $ship_search_filter;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids = $search->search();
	$ships         = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $ships;
	}

	// Get ship data.
	foreach ( $departure_ids as $departure_id ) {
		$departure = get_departure( $departure_id );

		// Get post meta.
		if ( ! is_array( $departure['post_meta'] ) || empty( $departure['post_meta']['related_ship'] ) ) {
			continue;
		}

		// Get ship ID.
		$ship_id = absint( $departure['post_meta']['related_ship'] );

		// Get ship data.
		$ship = get_ship_post( $ship_id );

		// Validate ship.
		if ( ! $ship['post'] instanceof WP_Post ) {
			continue;
		}

		// Prepare ship data.
		$ships[ $ship_id ] = $ship['post']->post_title;
	}

	// Set cache.
	wp_cache_set( $cache_key, $ships, CACHE_GROUP );

	// Return ship data.
	return $ships;
}
