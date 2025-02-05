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

use function Quark\Departures\get_included_adventure_options;
use function Quark\Departures\get_paid_adventure_options;
use function Quark\Expeditions\get as get_expedition_post;
use function Quark\Itineraries\get_season;
use function Quark\Localization\get_currencies;
use function Quark\Localization\get_current_currency;
use function Quark\Search\update_post_in_index;
use function Quark\Softrip\Departures\get_lowest_price;
use function Quark\Softrip\get_initiated_via;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Search\Filters\ADVENTURE_OPTION_FILTER_KEY;
use const Quark\Search\Filters\CURRENCY_FILTER_KEY;
use const Quark\Search\Filters\DESTINATION_FILTER_KEY;
use const Quark\Search\Filters\DURATION_FILTER_KEY;
use const Quark\Search\Filters\EXPEDITION_FILTER_KEY;
use const Quark\Search\Filters\ITINERARY_LENGTH_FILTER_KEY;
use const Quark\Search\Filters\LANGUAGE_FILTER_KEY;
use const Quark\Search\Filters\MONTH_FILTER_KEY;
use const Quark\Search\Filters\PAGE_FILTER_KEY;
use const Quark\Search\Filters\PER_PAGE_FILTER_KEY;
use const Quark\Search\Filters\SEASON_FILTER_KEY;
use const Quark\Search\Filters\SHIP_FILTER_KEY;
use const Quark\Search\Filters\SORT_FILTER_KEY;

const CACHE_GROUP                 = 'quark_search';
const SCHEDULE_REINDEX_HOOK       = 'qrk_search_reindex_departures';
const REINDEX_POST_IDS_OPTION_KEY = 'qrk_search_posts_to_be_reindexed';

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Filers.
	add_filter( 'solr_index_custom_fields', __NAMESPACE__ . '\\solr_index_custom_fields' );
	add_filter( 'solr_build_document', __NAMESPACE__ . '\\filter_solr_build_document', 10, 2 );

	/**
	 * Hooks for re-indexing departures on update.
	 * This is required as some expedition(destination taxonomy) and itinerary(season taxonomy) data is indexed on Solr.
	 */

	// Track posts to be reindexed. Assigning lower priority so that it runs at the end.
	add_action( 'save_post', __NAMESPACE__ . '\\track_posts_to_be_reindexed', 999, 3 );
	add_action( SCHEDULE_REINDEX_HOOK, __NAMESPACE__ . '\\reindex_departures' );
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );
	add_action( 'quark_softrip_sync_departure_updated', __NAMESPACE__ . '\\reindex_synced_departure' );
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

	// Currencies.
	$currencies = get_currencies();

	// Price.
	foreach ( $currencies as $currency ) {
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

	/**
	 * Set adventure options in Solr index.
	 */

	// Get included adventure options for departure.
	$included_options    = get_included_adventure_options( $post->ID );
	$included_option_ids = array_column( $included_options, 'term_id' );

	// Get paid adventure options for departure.
	$paid_options    = get_paid_adventure_options( $post->ID );
	$paid_option_ids = array_keys( $paid_options );

	// Merge included and paid options.
	$adventure_option_ids = array_merge( $included_option_ids, $paid_option_ids );

	// Set adventure options in Solr index.
	$document->setField( ADVENTURE_OPTION_CATEGORY . '_taxonomy_id', $adventure_option_ids );

	/**
	 * Set start date in Date format in Solr index.
	 */

	// Get start date.
	$start_date = get_post_meta( $post->ID, 'start_date', true );

	// Validate start date.
	if ( ! empty( $start_date ) && is_string( $start_date ) ) {
		// Format start date.
		$timestamp = strtotime( $start_date );

		// Set start date in date format.
		if ( ! empty( $timestamp ) ) {
			// Set start date in date format.
			$document->setField( 'start_date_dt', gmdate( 'Y-m-d\TH:i:s\Z', $timestamp ) );
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
		SEASON_FILTER_KEY           => isset( $_GET[SEASON_FILTER_KEY] ) ? strval( $_GET[SEASON_FILTER_KEY] ) : '', // phpcs:ignore
		EXPEDITION_FILTER_KEY       => isset( $_GET[EXPEDITION_FILTER_KEY] ) ? strval( $_GET[EXPEDITION_FILTER_KEY] ) : '', // phpcs:ignore
		ADVENTURE_OPTION_FILTER_KEY => isset( $_GET[ADVENTURE_OPTION_FILTER_KEY] ) ? strval( $_GET[ADVENTURE_OPTION_FILTER_KEY] ) : '', // phpcs:ignore
		MONTH_FILTER_KEY            => isset( $_GET[MONTH_FILTER_KEY] ) ? strval( $_GET[MONTH_FILTER_KEY] ) : '', // phpcs:ignore
		DURATION_FILTER_KEY         => isset( $_GET[DURATION_FILTER_KEY] ) ? strval( $_GET[DURATION_FILTER_KEY] ) : '', // phpcs:ignore
		SHIP_FILTER_KEY             => isset( $_GET[SHIP_FILTER_KEY] ) ? strval( $_GET[SHIP_FILTER_KEY] ) : '', // phpcs:ignore
		PAGE_FILTER_KEY             => isset( $_GET[PAGE_FILTER_KEY] ) ? strval( $_GET[PAGE_FILTER_KEY] ) : '1', // phpcs:ignore
		SORT_FILTER_KEY             => isset( $_GET[SORT_FILTER_KEY] ) ? strval( $_GET[SORT_FILTER_KEY] ) : 'date-now', // phpcs:ignore
		LANGUAGE_FILTER_KEY         => isset( $_GET[LANGUAGE_FILTER_KEY] ) ? strval( $_GET[LANGUAGE_FILTER_KEY] ) : '', // phpcs:ignore
		DESTINATION_FILTER_KEY      => isset( $_GET[DESTINATION_FILTER_KEY] ) ? strval( $_GET[DESTINATION_FILTER_KEY] ) : '', // phpcs:ignore
		ITINERARY_LENGTH_FILTER_KEY => isset( $_GET[ITINERARY_LENGTH_FILTER_KEY] ) ? strval( $_GET[ITINERARY_LENGTH_FILTER_KEY] ) : '', // phpcs:ignore
	];
}

/**
 * Parse the filters.
 *
 * @param mixed[] $filters Filters.
 *
 * @return array{
 *     seasons: string[],
 *     expeditions: string[],
 *     adventure_options: string[],
 *     months: string[],
 *     durations: array<int, string[]>,
 *     ships: string[],
 *     sort: string[],
 *     page: int,
 *     posts_per_load: int,
 *     currency: string,
 *     destinations: string[],
 *     languages: string[],
 *     itinerary_lengths: string[],
 * }
 */
function parse_filters( array $filters = [] ): array {
	// Prepare filter default values.
	$filters = wp_parse_args(
		$filters,
		[
			CURRENCY_FILTER_KEY         => get_current_currency(),
			SEASON_FILTER_KEY           => [],
			EXPEDITION_FILTER_KEY       => [],
			ADVENTURE_OPTION_FILTER_KEY => [],
			MONTH_FILTER_KEY            => [],
			DURATION_FILTER_KEY         => [],
			SHIP_FILTER_KEY             => [],
			SORT_FILTER_KEY             => [ 'date-now' ],
			PAGE_FILTER_KEY             => 1,
			PER_PAGE_FILTER_KEY         => 10,
			DESTINATION_FILTER_KEY      => [],
			LANGUAGE_FILTER_KEY         => [],
			ITINERARY_LENGTH_FILTER_KEY => [],
		]
	);

	// Parse expeditions.
	if ( is_string( $filters[ EXPEDITION_FILTER_KEY ] ) || is_int( $filters[ EXPEDITION_FILTER_KEY ] ) ) {
		$filters[ EXPEDITION_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', strval( $filters[ EXPEDITION_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ EXPEDITION_FILTER_KEY ] ) ) {
		$filters[ EXPEDITION_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ EXPEDITION_FILTER_KEY ] ) );
	}

	// Parse months.
	if ( is_string( $filters[ MONTH_FILTER_KEY ] ) ) {
		$filters[ MONTH_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', $filters[ MONTH_FILTER_KEY ] ) ) );
	} elseif ( is_array( $filters[ MONTH_FILTER_KEY ] ) ) {
		$filters[ MONTH_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ MONTH_FILTER_KEY ] ) );
	}

	// Parse adventure_options.
	if ( is_string( $filters[ ADVENTURE_OPTION_FILTER_KEY ] ) || is_int( $filters[ ADVENTURE_OPTION_FILTER_KEY ] ) ) {
		$filters[ ADVENTURE_OPTION_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', strval( $filters[ ADVENTURE_OPTION_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ ADVENTURE_OPTION_FILTER_KEY ] ) ) {
		$filters[ ADVENTURE_OPTION_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ ADVENTURE_OPTION_FILTER_KEY ] ) );
	}

	// Parse duration.
	if ( is_string( $filters[ DURATION_FILTER_KEY ] ) || is_int( $filters[ DURATION_FILTER_KEY ] ) ) {
		$filters[ DURATION_FILTER_KEY ] = array_filter( array_map( 'trim', explode( '-', strval( $filters[ DURATION_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ DURATION_FILTER_KEY ] ) ) {
		$filters[ DURATION_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ DURATION_FILTER_KEY ] ) );
		$filters[ DURATION_FILTER_KEY ] = array_map(
			function ( $duration ) {
				$duration = explode( '-', $duration );

				// Return duration.
				return $duration;
			},
			$filters[ DURATION_FILTER_KEY ]
		);
	}

	// Parse seasons.
	if ( is_string( $filters[ SEASON_FILTER_KEY ] ) ) {
		$filters[ SEASON_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', $filters[ SEASON_FILTER_KEY ] ) ) );
	} elseif ( is_array( $filters[ SEASON_FILTER_KEY ] ) ) {
		$filters[ SEASON_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ SEASON_FILTER_KEY ] ) );
	}

	// Parse ships.
	if ( is_string( $filters[ SHIP_FILTER_KEY ] ) || is_int( $filters[ SHIP_FILTER_KEY ] ) ) {
		$filters[ SHIP_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', strval( $filters[ SHIP_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ SHIP_FILTER_KEY ] ) ) {
		$filters[ SHIP_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ SHIP_FILTER_KEY ] ) );
	}

	// Parse destinations.
	if ( is_string( $filters[ DESTINATION_FILTER_KEY ] ) || is_int( $filters[ DESTINATION_FILTER_KEY ] ) ) {
		$filters[ DESTINATION_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', strval( $filters[ DESTINATION_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ DESTINATION_FILTER_KEY ] ) ) {
		$filters[ DESTINATION_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ DESTINATION_FILTER_KEY ] ) );
	}

	// Parse languages.
	if ( is_string( $filters[ LANGUAGE_FILTER_KEY ] ) || is_int( $filters[ LANGUAGE_FILTER_KEY ] ) ) {
		$filters[ LANGUAGE_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', strval( $filters[ LANGUAGE_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ LANGUAGE_FILTER_KEY ] ) ) {
		$filters[ LANGUAGE_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ LANGUAGE_FILTER_KEY ] ) );
	}

	// Parse itinerary lengths.
	if ( is_string( $filters[ ITINERARY_LENGTH_FILTER_KEY ] ) || is_int( $filters[ ITINERARY_LENGTH_FILTER_KEY ] ) ) {
		$filters[ ITINERARY_LENGTH_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', strval( $filters[ ITINERARY_LENGTH_FILTER_KEY ] ) ) ) );
	} elseif ( is_array( $filters[ ITINERARY_LENGTH_FILTER_KEY ] ) ) {
		$filters[ ITINERARY_LENGTH_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ ITINERARY_LENGTH_FILTER_KEY ] ) );
	}

	// Parse sort.
	if ( is_string( $filters[ SORT_FILTER_KEY ] ) ) {
		$filters[ SORT_FILTER_KEY ] = array_filter( array_map( 'trim', explode( ',', $filters[ SORT_FILTER_KEY ] ) ) );
	} elseif ( is_array( $filters[ SORT_FILTER_KEY ] ) ) {
		$filters[ SORT_FILTER_KEY ] = array_filter( array_map( 'trim', $filters[ SORT_FILTER_KEY ] ) );
	}

	// Return parsed filters.
	return [
		SEASON_FILTER_KEY           => (array) $filters[ SEASON_FILTER_KEY ],
		EXPEDITION_FILTER_KEY       => (array) $filters[ EXPEDITION_FILTER_KEY ],
		MONTH_FILTER_KEY            => (array) $filters[ MONTH_FILTER_KEY ],
		ADVENTURE_OPTION_FILTER_KEY => (array) $filters[ ADVENTURE_OPTION_FILTER_KEY ],
		DURATION_FILTER_KEY         => (array) $filters[ DURATION_FILTER_KEY ],
		SHIP_FILTER_KEY             => (array) $filters[ SHIP_FILTER_KEY ],
		PAGE_FILTER_KEY             => absint( $filters[ PAGE_FILTER_KEY ] ),
		SORT_FILTER_KEY             => (array) $filters[ SORT_FILTER_KEY ],
		PER_PAGE_FILTER_KEY         => absint( $filters[ PER_PAGE_FILTER_KEY ] ),
		CURRENCY_FILTER_KEY         => $filters[ CURRENCY_FILTER_KEY ],
		DESTINATION_FILTER_KEY      => (array) $filters[ DESTINATION_FILTER_KEY ],
		LANGUAGE_FILTER_KEY         => (array) $filters[ LANGUAGE_FILTER_KEY ],
		ITINERARY_LENGTH_FILTER_KEY => (array) $filters[ ITINERARY_LENGTH_FILTER_KEY ],
	];
}

/**
 * Fetch Departure as per the filters provided.
 *
 * @param mixed[] $filters      Filters.
 * @param mixed[] $facets       Facets.
 * @param bool    $retrieve_all Retrieve all.
 *
 * @return array{
 *     ids: int[],
 *     current_page: int,
 *     next_page: int,
 *     result_count: int,
 *     remaining_count: int,
 *     facet_results: mixed[],
 * }
 */
function search( array $filters = [], array $facets = [], bool $retrieve_all = false ): array {
	// Parse filters.
	$filters = parse_filters( $filters );

	// Get the filters.
	$sorts             = array_map( 'strval', (array) $filters[ SORT_FILTER_KEY ] );
	$seasons           = array_map( 'strval', (array) $filters[ SEASON_FILTER_KEY ] );
	$months            = array_map( 'strval', (array) $filters[ MONTH_FILTER_KEY ] );
	$expeditions       = array_map( 'absint', (array) $filters[ EXPEDITION_FILTER_KEY ] );
	$adventure_options = array_map( 'absint', (array) $filters[ ADVENTURE_OPTION_FILTER_KEY ] );
	$ships             = array_map( 'absint', (array) $filters[ SHIP_FILTER_KEY ] );
	$destinations      = array_map( 'absint', (array) $filters[ DESTINATION_FILTER_KEY ] );
	$languages         = array_map( 'absint', (array) $filters[ LANGUAGE_FILTER_KEY ] );
	$itinerary_lengths = array_map( 'absint', (array) $filters[ ITINERARY_LENGTH_FILTER_KEY ] );

	// Validate durations.
	$durations = array_map(
		fn ( array $duration = [] ) => array_map( 'absint', $duration ),
		$filters[ DURATION_FILTER_KEY ]
	);

	// Prepare search object.
	$search = new Search();
	$search->set_months( $months );
	$search->set_seasons( $seasons );
	$search->set_expeditions( $expeditions );
	$search->set_adventure_options( $adventure_options );
	$search->set_durations( $durations );
	$search->set_ships( $ships );
	$search->set_destinations( $destinations );
	$search->set_languages( $languages );
	$search->set_itinerary_lengths( $itinerary_lengths );
	$search->set_sorts( $sorts, $filters[ CURRENCY_FILTER_KEY ] );

	// Set page and posts per page.
	if ( empty( $retrieve_all ) ) {
		$search->set_page( absint( $filters[ PAGE_FILTER_KEY ] ) );
		$search->set_posts_per_page( absint( $filters[ PER_PAGE_FILTER_KEY ] ?: 5 ) );
	} else {
		$search->set_posts_per_page( -1 );
	}

	// Set facets.
	$search->set_facets( $facets );

	// Returned filtered trips.
	return [
		'ids'             => $search->search(),
		'current_page'    => $search->current_page,
		'next_page'       => $search->next_page,
		'result_count'    => $search->result_count,
		'remaining_count' => $search->remaining_count,
		'facet_results'   => $search->facet_results,
	];
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
	if ( empty( $post_id ) || empty( $update ) ) {
		return;
	}

	// Get post type.
	$post_type = get_post_type( $post_id );

	// Return if not a supported post type.
	if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE, ITINERARY_POST_TYPE ], true ) ) {
		return;
	}

	// Get option.
	$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );

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
	update_option( REINDEX_POST_IDS_OPTION_KEY, $option );

	// Schedule reindex if not yet.
	if ( ! wp_next_scheduled( SCHEDULE_REINDEX_HOOK ) ) {
		wp_schedule_single_event( strtotime( '+1 hour' ), SCHEDULE_REINDEX_HOOK );
	}
}

/**
 * Reindex departures on itinerary or expedition post update.
 * As some expedition(destination taxonomy) and itinerary(season taxonomy) data is indexed on Solr, we need to reindex such that Solr has the lates data.
 *
 * @return void
 */
function reindex_departures(): void {
	// Get option.
	$post_ids = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );

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
					'error'   => 'Invalid post.',
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
					'error'   => 'Unsupported post type. Neither a expedition, nor a itinerary post.',
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
	update_option( REINDEX_POST_IDS_OPTION_KEY, [] );

	// Clear the cron.
	wp_clear_scheduled_hook( SCHEDULE_REINDEX_HOOK );
}

/**
 * Get departures by expeditions and months.
 *
 * @param int      $expedition_id Expedition ID.
 * @param string[] $months       Months.
 *
 * @return int[]
 */
function get_departures_by_expeditions_and_months( int $expedition_id = 0, array $months = [] ): array {
	// Search for departures.
	$departures_search = new Search();

	// Set expedition.
	$departures_search->set_expeditions( [ $expedition_id ] );

	// Set months.
	$departures_search->set_months( $months );

	// Sort by date.
	$departures_search->set_sorts( [ 'date-now' ] );

	// Return search results.
	return $departures_search->search();
}

/**
 * Reindex synced departure.
 *
 * @param mixed[] $data Data passed to the action.
 *
 * @return void
 */
function reindex_synced_departure( array $data = [] ): void {
	// Validate data.
	if ( empty( $data ) || empty( $data['post_id'] ) || empty( $data['updated_fields'] ) || ! is_array( $data['updated_fields'] ) ) {
		return;
	}

	// Skip if occupancies are not updated or departure post is updated.
	if ( empty( $data['updated_fields']['occupancies'] ) || ! empty( $data['updated_fields']['departure_post'] ) ) {
		return;
	}

	// Departure post id.
	$departure_post_id = absint( $data['post_id'] );

	// Validate departure post id.
	if ( empty( $departure_post_id ) ) {
		return;
	}

	// Get initiated via.
	$initiated_via = get_initiated_via();

	// Add itinerary to reindex list if initiated manually.
	if ( 'manually' === $initiated_via ) {
		// Get itinerary.
		$itinerary_post_id = absint( get_post_meta( $departure_post_id, 'itinerary', true ) );

		// Skip if no itinerary.
		if ( empty( $itinerary_post_id ) ) {
			return;
		}

		// Add to reindex list.
		track_posts_to_be_reindexed( $itinerary_post_id, null, true );

		// Done.
		return;
	}

	// Trigger reindex.
	update_post_in_index( $departure_post_id );
}
