<?php
/**
 * Departure search namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search\Departures;

use Quark\Search\Stream_Connector;
use WP_Post;
use WP_Term;
use Solarium\QueryType\Update\Query\Document\Document;

use function Quark\Departures\get as get_departure;
use function Quark\Departures\get_included_adventure_options;
use function Quark\Departures\get_paid_adventure_options;
use function Quark\Expeditions\get as get_expedition_post;
use function Quark\Expeditions\get_destination_term_by_code;
use function Quark\Itineraries\get_season;
use function Quark\Search\update_post_in_index;
use function Quark\Ships\get as get_ship_post;
use function Quark\Softrip\Departures\get_lowest_price;

use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\Core\CURRENCIES;
use const Quark\Core\USD_CURRENCY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;

const CACHE_GROUP           = 'quark_search';
const SCHEDULE_REINDEX_HOOK = 'qrk_search_reindex_departures';

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

	// Trigger reindex on post update.
	add_action( 'save_post', __NAMESPACE__ . '\\track_posts_to_be_reindexed', 999, 3 );

	// Schedule reindex departures.
	add_action( 'init', __NAMESPACE__ . '\\schedule_reindex_departures' );

	// Register reindex departures cron.
	add_action( SCHEDULE_REINDEX_HOOK, __NAMESPACE__ . '\\reindex_departures' );

	// Register Stream log connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );

	// Load search class.
	require_once __DIR__ . '/class-search.php';
}

/**
 * Schedule reindex departures.
 *
 * @return void
 */
function schedule_reindex_departures(): void {
	// Check if scheduled reindex departures.
	if ( wp_next_scheduled( SCHEDULE_REINDEX_HOOK ) ) {
		return;
	}

	// Set a time + 1 hour to schedule reindex departures.
	$next_hour = strtotime( '+1 hour' );

	// Schedule reindex departures.
	wp_schedule_event( $next_hour, 'hourly', SCHEDULE_REINDEX_HOOK );
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

	// Price.
	foreach ( CURRENCIES as $currency ) {
		// Lowercase currency.
		$currency = strtolower( $currency );

		// Price key.
		$key = 'lowest_price_' . $currency . '_i';

		// Get original and discounted price.
		$prices = get_lowest_price( $post->ID, $currency );

		// Index the discounted price.
		$document->setField( $key, $prices['discounted'] );
	}

	/**
	 * Populate destinations field from expedition on each departure.
	 */
	$expedition_id = absint( get_post_meta( $post->ID, 'related_expedition', true ) );

	// Get expedition post.
	$expedition = get_expedition_post( $expedition_id );

	// Validate post.
	if ( $expedition['post'] instanceof WP_Post && ! empty( $expedition['post_taxonomies'] ) && ! empty( $expedition['post_taxonomies'][ DESTINATION_TAXONOMY ] ) && is_array( $expedition['post_taxonomies'][ DESTINATION_TAXONOMY ] ) ) {
		// Get taxonomies.
		$destination_terms = [
			'ids'   => [],
			'slugs' => [],
		];

		// Loop through destination terms.
		foreach ( $expedition['post_taxonomies'][ DESTINATION_TAXONOMY ] as $destination_term ) {
			// Validate term.
			if ( ! is_array( $destination_term ) || empty( $destination_term['term_id'] ) || empty( $destination_term['slug'] ) ) {
				continue;
			}

			// Set destination field.
			$destination_terms['ids'][]   = $destination_term['term_id'];
			$destination_terms['slugs'][] = $destination_term['slug'];
		}

		// Set destination field.
		$document->setField( DESTINATION_TAXONOMY . '_taxonomy_id', $destination_terms['ids'] );
		$document->setField( DESTINATION_TAXONOMY . '_taxonomy_slug_str', $destination_terms['slugs'] );
	}

	/**
	 * Populate the region from departure and season from itinerary on each departure in Solr.
	 */
	$itinerary_post_id = absint( get_post_meta( $post->ID, 'itinerary', true ) );

	// Validate itinerary.
	if ( ! empty( $itinerary_post_id ) ) {
		// Get season.
		$season = get_season( $itinerary_post_id );

		// Get market code for current departure from meta.
		$market_code = strval( get_post_meta( $post->ID, 'softrip_market_code', true ) );

		// Validate market code.
		if ( ! empty( $market_code ) && ! empty( $season ) ) {
			$region_season = $market_code . '-' . $season['slug'];

			// Add region and season to formatted data.
			$document->setField( 'region_season_s', $region_season );
			$document->setField( 'region_season_str', [ $region_season ] );
		}
	}

	// Return document.
	return $document;
}

/**
 * Register custom stream connectors for Solr reindex.
 *
 * @param array<string, mixed> $connectors Connectors.
 *
 * @return array<string, mixed>
 */
function setup_stream_connectors( array $connectors = [] ): array {
	// Load Stream connector file.
	require_once __DIR__ . '/class-stream-connector.php';

	// Add our connector.
	$connectors['quark_search_solr_reindex'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
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
		'currency'          => isset( $_GET['currency'] ) ? strval( $_GET['currency'] ) : USD_CURRENCY, // phpcs:ignore
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
 *     currency: string,
 *     destinations: string[],
 * }
 */
function parse_filters( array $filters = [] ): array {
	// Prepare filter default values.
	$filters = wp_parse_args(
		$filters,
		[
			'currency'          => USD_CURRENCY, // @todo https://tuispecialist.atlassian.net/browse/QE-326 Modify this to global currency switcher when implemented.
			'seasons'           => '',
			'expeditions'       => '',
			'adventure_options' => '',
			'months'            => '',
			'durations'         => '',
			'ships'             => '',
			'sort'              => 'date-now',
			'page'              => 1,
			'posts_per_load'    => 10,
			'destinations'      => '',
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

	// Validate currency.
	if ( is_string( $filters['currency'] ) && in_array( $filters['currency'], CURRENCIES, true ) ) {
		$filters['currency'] = trim( $filters['currency'] );
	} else {
		$filters['currency'] = USD_CURRENCY;
	}

	// Parse destinations.
	if ( is_string( $filters['destinations'] ) || is_int( $filters['destinations'] ) ) {
		$filters['destinations'] = array_filter( array_map( 'trim', explode( ',', strval( $filters['destinations'] ) ) ) );
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
		'currency'          => $filters['currency'],
		'destinations'      => (array) $filters['destinations'],
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
	$destinations      = array_map( 'absint', (array) $filters['destinations'] );

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
	$search->set_sort( $sort, $filters['currency'] );
	$search->set_destinations( $destinations );

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
		wp_cache_delete( 'search_filter_itinerary_length_data', CACHE_GROUP );
	}
}

/**
 * Track posts to be reindexed.
 *
 * @param int          $post_id Post ID.
 * @param WP_Post|null $post    WP Post.
 * @param bool         $update  Is update.
 *
 * @return void
 */
function track_posts_to_be_reindexed( int $post_id = 0, ?WP_Post $post = null, bool $update = false ): void {
	// Validate post. Reindex only on update.
	if ( empty( $post ) || ! $post instanceof WP_Post || empty( $update ) ) {
		return;
	}

	// Get post type.
	$post_type = $post->post_type;

	// Return if not a supported post type.
	if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE, ITINERARY_POST_TYPE ], true ) ) {
		return;
	}

	// Get option.
	$option = get_option( 'qrk_search_posts_to_be_reindexed', [] );

	// Validate option.
	if ( ! is_array( $option ) ) {
		$option = [];
	}

	// Back-off if post already in the list.
	if ( in_array( $post_id, $option, true ) ) {
		return;
	}

	// Add post to the list.
	$option[] = $post_id;

	// Update the option.
	update_option( 'qrk_search_posts_to_be_reindexed', $option );
}

/**
 * Reindex departures on itinerary or expedition post update.
 * As some expedition(destination taxonomy) and itinerary(season taxonomy) data is indexed on Solr, we need to reindex such that Solr has the lates data.
 *
 * @return void
 */
function reindex_departures(): void {
	// Get option.
	$post_ids = get_option( 'qrk_search_posts_to_be_reindexed', [] );

	// Validate option.
	if ( ! is_array( $post_ids ) ) {
		$post_ids = [];
	}

	// Back-off if no posts to reindex.
	if ( empty( $post_ids ) ) {
		return;
	}

	// Log action.
	do_action(
		'quark_search_reindex_initiated',
		[
			'total' => count( $post_ids ),
		]
	);

	// Success count.
	$success_count = 0;

	// Loop through post ids.
	foreach ( $post_ids as $post_id ) {
		// Get post.
		$post = get_post( $post_id );

		// Validate post.
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			// Log the action.
			do_action(
				'quark_search_reindex_failed',
				[
					'post_id' => $post_id,
					'error'   => __( 'Invalid post.', 'qrk' ),
				]
			);

			// Continue to next post.
			continue;
		}

		// Get post type.
		$post_type = $post->post_type;

		// Return if not a supported post type.
		if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE, ITINERARY_POST_TYPE ], true ) ) {
			// Log the action.
			do_action(
				'quark_search_reindex_failed',
				[
					'post_id' => $post_id,
					'error'   => __( 'Unsupported post type. Neither a expedition, nor a itinerary post.', 'qrk' ),
				]
			);

			// Continue to next post.
			continue;
		}

		// Initialize itinerary ids.
		$itinerary_ids = [];

		// Get itinerary ids.
		if ( EXPEDITION_POST_TYPE === $post_type ) {
			// Get itinerary ids.
			$related_itineraries = get_post_meta( $post_id, 'related_itineraries', true );

			// Validate related itineraries.
			if ( ! empty( $related_itineraries ) && is_array( $related_itineraries ) ) {
				$itinerary_ids = array_map( 'absint', $related_itineraries );
			}
		} elseif ( ITINERARY_POST_TYPE === $post_type ) {
			// Add itinerary id.
			$itinerary_ids[] = $post_id;
		}

		// Initialize departure ids.
		$departure_post_ids = [];

		// Fetch all the departures for each itinerary and trigger reindex.
		foreach ( $itinerary_ids as $itinerary_id ) {
			// Get departure ids.
			$departure_posts = get_posts(
				[
					'post_type'              => DEPARTURE_POST_TYPE,
					'posts_per_page'         => -1,
					'parent'                 => $itinerary_id,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'cache_results'          => false,
					'ignore_sticky_posts'    => true,
					'suppress_filters'       => false,
				]
			);

			// Validate departure ids.
			if ( empty( $departure_posts ) ) {
				continue;
			}

			// Convert to integer.
			$departure_posts = array_map( 'absint', $departure_posts );

			// Merge departure ids.
			$departure_post_ids = array_merge( $departure_post_ids, $departure_posts );
		}

		// Unique departure ids.
		$departure_post_ids = array_unique( $departure_post_ids );

		// Update departures in index.
		foreach ( $departure_post_ids as $departure_post_id ) {
			update_post_in_index( $departure_post_id );
		}

		// Increment success count.
		++$success_count;
	}

	// Log the action.
	do_action(
		'quark_search_reindex_completed',
		[
			'total'   => count( $post_ids ),
			'success' => $success_count,
			'failed'  => count( $post_ids ) - $success_count,
		]
	);

	// Reset the option.
	update_option( 'qrk_search_posts_to_be_reindexed', [] );
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

/**
 * Get departure language search filter data.
 *
 * @return string[]
 */
function get_language_search_filter_data(): array {
	// Get terms.
	$the_terms = get_terms(
		[
			'taxonomy'   => SPOKEN_LANGUAGE_TAXONOMY,
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
		$filter_data[ $term->term_id ] = $term->name;
	}

	// Return filter data.
	return $filter_data;
}

/**
 * Get departure cabin class search filter data.
 *
 * @return string[]
 */
function get_cabin_class_search_filter_data(): array {
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
		$filter_data[ $term->term_id ] = $term->name;
	}

	// Return filter data.
	return $filter_data;
}

/**
 * Get itinerary length search filter data.
 *
 * @return string[]
 */
function get_itinerary_length_search_filter_data(): array {
	// Cache key.
	$cache_key = 'search_filter_itinerary_length_data';

	// Get from cache.
	$itinerary_length_search_filter = wp_cache_get( $cache_key, CACHE_GROUP );

	// Return cache data.
	if ( ! empty( $itinerary_length_search_filter ) && is_array( $itinerary_length_search_filter ) ) {
		return $itinerary_length_search_filter;
	}

	// Prepare search object.
	$search = new Search();
	$search->set_posts_per_page( -1 );

	// Get departure ids.
	$departure_ids     = $search->search();
	$itinerary_lengths = [];

	// Validate departure ids.
	if ( empty( $departure_ids ) ) {
		return $itinerary_lengths;
	}

	// Get itinerary length data.
	foreach ( $departure_ids as $departure_id ) {
		$departure = get_departure( $departure_id );

		// Get post meta.
		if ( ! is_array( $departure['post_meta'] ) || empty( $departure['post_meta']['duration'] ) ) {
			continue;
		}

		// Get itinerary length.
		$itinerary_length = absint( $departure['post_meta']['duration'] );

		// Validate itinerary length.
		if ( empty( $itinerary_length ) || ! empty( $itinerary_lengths[ $itinerary_length ] ) ) {
			continue;
		}

		// Prepare itinerary length data.
		$itinerary_lengths[ $itinerary_length ] = $itinerary_length;
	}

	// Sort the itinerary lengths array by keys (dates).
	ksort( $itinerary_lengths );

	// Create a new array with key as length and value as label.
	$itinerary_length_data = [];

	// Loop through itinerary lengths.
	foreach ( $itinerary_lengths as $itinerary_length ) {
		// Prepare itinerary length data.
		$itinerary_length_data[ $itinerary_length ] = sprintf(
			// translators: %d: Itinerary length/duration.
			__( '%d Days', 'qrk' ),
			absint( $itinerary_length )
		);
	}

	// Set cache.
	wp_cache_set( $cache_key, $itinerary_length_data, CACHE_GROUP );

	// Return itinerary length data.
	return $itinerary_length_data;
}

/**
 * Get destinations search filter data.
 *
 * @return array{}|array<int, array{
 *   id: int,
 *   slug: string,
 *   name: string,
 *   children: array<int, array{
 *      id: int,
 *      slug: string,
 *      name: string,
 *      parent_id: int,
 *   }>,
 * }>
 */
function get_destination_search_filter_data(): array {
	// Get parent terms.
	$parent_terms = get_terms(
		[
			'taxonomy'   => DESTINATION_TAXONOMY,
			'hide_empty' => true,
			'parent'     => 0,
		]
	);

	// Validate terms.
	if ( empty( $parent_terms ) || ! is_array( $parent_terms ) ) {
		return [];
	}

	// Initialize returned data.
	$terms_data = [];

	// Get child terms.
	foreach ( $parent_terms as $parent_term ) {
		// Validate parent term.
		if ( ! $parent_term instanceof WP_Term ) {
			continue;
		}

		// Get parent term id.
		$parent_id = absint( $parent_term->term_id );

		// Prepare parent term.
		$parent_term = [
			'id'       => $parent_id,
			'slug'     => $parent_term->slug,
			'name'     => $parent_term->name,
			'children' => [],
		];

		// Get child terms.
		$child_terms = get_terms(
			[
				'taxonomy'   => DESTINATION_TAXONOMY,
				'hide_empty' => true,
				'parent'     => $parent_id,
			]
		);

		// Validate child terms.
		if ( empty( $child_terms ) || ! is_array( $child_terms ) ) {
			$terms_data[] = $parent_term;
			continue;
		}

		// Loop through child terms.
		foreach ( $child_terms as $child_term ) {
			// Validate child term.
			if ( ! $child_term instanceof WP_Term ) {
				continue;
			}

			// Prepare child term.
			$parent_term['children'][] = [
				'id'        => absint( $child_term->term_id ),
				'slug'      => $child_term->slug,
				'name'      => $child_term->name,
				'parent_id' => $parent_id,
			];
		}

		// Add parent term.
		$terms_data[] = $parent_term;
	}

	// Return expedition data.
	return $terms_data;
}
