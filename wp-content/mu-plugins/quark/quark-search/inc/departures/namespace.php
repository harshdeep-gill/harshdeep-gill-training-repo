<?php
/**
 * Departure search namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search\Departures;

use WP_Post;
use Solarium\QueryType\Update\Query\Document\Document;

use function Quark\Departures\get_season;
use function Quark\Departures\get_region_and_season;
use function Quark\Departures\get as get_departure;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Filers.
	add_filter( 'solr_index_custom_fields', __NAMESPACE__ . '\\solr_index_custom_fields' );
	add_filter( 'solr_build_document', __NAMESPACE__ . '\\filter_solr_build_document', 10, 2 );
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

	// Get departure data.
	$departure_data = get_departure( $post->ID );

	// Set post title for sorting.
	$document->setField( 'post_title_s', get_the_title( $post->ID ) );

	// Set Region.
	$document->setField( 'region_s', $departure_data['post_meta']['region'] ?? '' );

	// set Season.
	$document->setField( 'season_s', get_season( $post->ID ) );

	// Set Region & season.
	$document->setField( 'region_season_s', get_region_and_season( $post->ID ) );

	// Set Expeditions.
	$document->setField( 'expedition_i', absint( $departure_data['post_meta']['related_expedition'] ?? 0 ) );

	// Set Ship.
	$document->setField( 'ship_i', absint( $departure_data['post_meta']['related_ship'] ?? 0 ) );

	// Set duration.
	$document->setField( 'duration_i', absint( $departure_data['post_meta']['duration'] ?? 0 ) );

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
			'departure_start_date',
			'departure_end_date',
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
		'sort'              => isset( $_GET['sort'] ) ? strval( $_GET['sort'] ) : 'start_date', // phpcs:ignore
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
 *     sort: array{}|array<string, string>,
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
			'sort'              => '',
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
		$filters['durations'] = array_filter( array_map( 'trim', explode( ',', strval( $filters['durations'] ) ) ) );
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
		'sort'              => (array) $filters['sort'],
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
 *     result_count: int
 * }
 */
function search( array $filters = [] ): array {
	// Load search class.
	require_once __DIR__ . '/class-search.php';

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

	// Set sort.
	foreach ( $sort as $order_by => $order ) {
		$search->set_sort( $order_by, $order );
	}

	// Returned filtered trips.
	return [
		'ids'          => $search->search(),
		'current_page' => $search->current_page,
		'next_page'    => $search->next_page,
		'result_count' => $search->result_count,
	];
}
