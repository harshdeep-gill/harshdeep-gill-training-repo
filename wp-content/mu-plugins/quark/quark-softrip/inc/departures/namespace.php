<?php
/**
 * Namespace for the Softrip departure data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Departures;

use WP_Error;
use WP_Query;

use function Quark\Ships\get_id_from_ship_code;
use function Quark\Softrip\AdventureOptions\update_adventure_options;
use function Quark\Softrip\Occupancies\update_occupancies;
use function Quark\Softrip\is_date_in_the_past;
use function Quark\Softrip\Occupancies\clear_occupancies_by_departure;
use function Quark\Softrip\Occupancies\get_lowest_price as get_occupancies_lowest_price;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\Core\CURRENCIES;
use const Quark\Departures\CACHE_GROUP as DEPARTURE_CACHE_GROUP;
use const Quark\Departures\CACHE_KEY as DEPARTURE_CACHE_KEY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;

/**
 * Update the departure data.
 *
 * @param mixed[] $raw_departures       Raw departures data from Softrip to update with.
 * @param string  $softrip_package_code Softrip package code.
 *
 * @return bool
 */
function update_departures( array $raw_departures = [], string $softrip_package_code = '' ): bool {
	// Bail out if empty softrip package code.
	if ( empty( $softrip_package_code ) ) {
		return false;
	}

	// Get itinerary post id.
	$itinerary_posts = new WP_Query(
		[
			'post_type'              => ITINERARY_POST_TYPE,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_term_meta_cache' => false,
			'post_status'            => [ 'draft', 'publish' ],
			'posts_per_page'         => -1,
			'ignore_sticky_posts'    => true,
			'meta_query'             => [
				[
					'key'   => 'softrip_package_code',
					'value' => $softrip_package_code,
				],
			],
		]
	);

	// Get the itinerary post IDs.
	$itinerary_post_ids = $itinerary_posts->posts;

	// Validate for empty or multiple itinerary post IDs.
	if ( empty( $itinerary_post_ids ) || 1 < count( $itinerary_post_ids ) ) {
		return false;
	}

	// Get the itinerary post ID.
	$itinerary_post_id = absint( $itinerary_post_ids[0] );

	// Get the expedition post ID.
	$expedition_post_id = absint( get_post_meta( $itinerary_post_id, 'related_expedition', true ) );

	// Get current departures with the package code.
	$departure_posts = new WP_Query(
		[
			'post_type'              => DEPARTURE_POST_TYPE,
			'fields'                 => 'ids',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'post_status'            => [ 'draft', 'publish' ],
			'meta_query'             => [
				[
					'key'   => 'softrip_package_code',
					'value' => $softrip_package_code,
				],
			],
		]
	);

	// Get the departure post IDs.
	$departure_post_ids = $departure_posts->posts;

	// Filter integer post IDs.
	$departure_post_ids = array_map( 'absint', $departure_post_ids );

	// Initialize existing departure codes.
	$existing_departure_codes = [];

	// Get each departure details.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// If not an integer, skip.
		if ( ! is_int( $departure_post_id ) ) {
			continue;
		}

		// Get departure code.
		$departure_code = get_post_meta( $departure_post_id, 'softrip_id', true );

		// Skip if empty.
		if ( empty( $departure_code ) ) {
			continue;
		}

		// Add to existing departure codes.
		$existing_departure_codes[ $departure_code ] = $departure_post_id;
	}

	// Initialize updated departure unique codes.
	$updated_departure_codes = [];

	// Loop through raw departures and update/create the departure posts, cabins, prices and promotions.
	foreach ( $raw_departures as $raw_departure ) {
		// Validate if not array or empty array or no id.
		if ( ! is_array( $raw_departure ) || empty( $raw_departure ) || empty( $raw_departure['id'] ) ) {
			continue;
		}

		// Find in existing departure codes.
		$is_existing = in_array( $raw_departure['id'], array_keys( $existing_departure_codes ), true );

		// Format raw departure data.
		$formatted_data = format_raw_departure_data( $raw_departure, $itinerary_post_id, $expedition_post_id );

		// If empty formatted data, skip.
		if ( empty( $formatted_data ) ) {
			continue;
		}

		// Initialized is saved flag.
		$updated_post_id = false;

		// If existing, update the post.
		if ( $is_existing ) {
			// Add post ID to formatted data.
			$formatted_data['ID'] = $existing_departure_codes[ $raw_departure['id'] ];

			// Update the post.
			$updated_post_id = wp_update_post( $formatted_data, true );
		} else {
			// Set post status to publish.
			$formatted_data['post_status'] = 'publish';

			// Insert the post.
			$updated_post_id = wp_insert_post( $formatted_data );
		}

		// Skip if error or empty post ID.
		if ( $updated_post_id instanceof WP_Error || empty( $updated_post_id ) ) {
			continue;
		} else {
			// Add to updated departure codes.
			$updated_departure_codes[] = $raw_departure['id'];

			// Set spoken language for newly created departure.
			if ( ! $is_existing ) {
				// Set english as spoken language.
				wp_set_object_terms( $updated_post_id, [ 'english' ], SPOKEN_LANGUAGE_TAXONOMY );
			}
		}

		// Update adventure options.
		if ( ! empty( $raw_departure['adventureOptions'] ) ) {
			update_adventure_options( $raw_departure['adventureOptions'], $updated_post_id );
		}

		// Update promotions. This is done before cabins to get the promotion IDs.
		if ( ! empty( $raw_departure['promotions'] ) ) {
			update_promotions( $raw_departure['promotions'] );
		}

		// Update Cabins.
		if ( ! empty( $raw_departure['cabins'] ) ) {
			update_occupancies( $raw_departure['cabins'], $updated_post_id );
		}
	}

	// Delete all departure posts not in updated departure codes and has expired.
	foreach ( $existing_departure_codes as $departure_code => $departure_post_id ) {
		// Skip if departure code is in updated departure codes.
		if ( in_array( $departure_code, $updated_departure_codes, true ) ) {
			continue;
		}

		// Get start date meta.
		$start_date = get_post_meta( $departure_post_id, 'start_date', true );

		// If start date is in the past, skip.
		if ( ! is_string( $start_date ) || empty( $start_date ) || ! is_date_in_the_past( $start_date ) ) {
			continue;
		}

		/**
		 * Deletion should happen as current departure has expired.
		 * 1. Delete all the occupancies which in-turn deletes all the occupancy promotions.
		 * 2. On successful deletion of occupancies, delete the departure post.
		 */

		// Delete the occupancies.
		$is_cleared = clear_occupancies_by_departure( $departure_post_id );

		// Skip if not cleared.
		if ( ! $is_cleared ) {
			continue;
		}

		// Delete the post.
		wp_delete_post( $departure_post_id, true );

		// Bust the departure module cache.
		wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure_post_id", DEPARTURE_CACHE_GROUP );
	}

	// Return successful.
	return true;
}

/**
 * Format raw departure data.
 *
 * @param mixed[] $raw_departure_data Raw departure data.
 * @param int     $itinerary_post_id  Itinerary post ID.
 * @param int     $expedition_post_id Expedition post ID.
 *
 * @return array{
 *   post_title: string,
 *   post_type: string,
 *   post_parent: int,
 *   meta_input: array{
 *     related_expedition: int,
 *     itinerary: int,
 *     related_ship: int,
 *     softrip_package_code: string,
 *     softrip_id: string,
 *     softrip_code: string,
 *     start_date: string,
 *     end_date: string,
 *     duration: int,
 *     ship_code: string,
 *     softrip_market_code: string,
 *    }
 * } | array{}
 */
function format_raw_departure_data( array $raw_departure_data = [], int $itinerary_post_id = 0, int $expedition_post_id = 0 ): array {
	// Return empty if no itinerary post ID.
	if (
		empty( $raw_departure_data ) ||
		empty( $itinerary_post_id ) ||
		empty( $expedition_post_id )
	) {
		return [];
	}

	// Default values.
	$default = [
		'id'          => '',
		'code'        => '',
		'packageCode' => '',
		'startDate'   => '',
		'endDate'     => '',
		'duration'    => 0,
		'shipCode'    => '',
		'marketCode'  => '',
	];

	// Apply default values.
	$raw_departure_data = wp_parse_args( $raw_departure_data, $default );

	// Validate for empty values.
	if (
		empty( $raw_departure_data['id'] ) ||
		empty( $raw_departure_data['code'] ) ||
		empty( $raw_departure_data['packageCode'] ) ||
		empty( $raw_departure_data['startDate'] ) ||
		empty( $raw_departure_data['endDate'] ) ||
		empty( $raw_departure_data['shipCode'] ) ||
		empty( $raw_departure_data['marketCode'] )
	) {
		return [];
	}

	// Prepare formatted data.
	$formatted_data = [
		'post_title'  => strval( $raw_departure_data['id'] ),
		'post_type'   => DEPARTURE_POST_TYPE,
		'post_parent' => $itinerary_post_id,
		'meta_input'  => [
			'related_expedition'   => $expedition_post_id,
			'itinerary'            => $itinerary_post_id,
			'related_ship'         => get_id_from_ship_code( $raw_departure_data['shipCode'] ),
			'softrip_package_code' => strval( $raw_departure_data['packageCode'] ),
			'softrip_id'           => strval( $raw_departure_data['id'] ),
			'softrip_code'         => strval( $raw_departure_data['code'] ),
			'start_date'           => strval( $raw_departure_data['startDate'] ),
			'end_date'             => strval( $raw_departure_data['endDate'] ),
			'duration'             => absint( $raw_departure_data['duration'] ),
			'ship_code'            => strval( $raw_departure_data['shipCode'] ),
			'softrip_market_code'  => strval( $raw_departure_data['marketCode'] ),
		],
	];

	// Return formatted data.
	return $formatted_data;
}

/**
 * Get lowest price for a departure.
 *
 * @param int    $post_id  Departure post ID.
 * @param string $currency The currency code to get.
 *
 * @return array{
 *   original: int,
 *   discounted: int,
 * }
 */
function get_lowest_price( int $post_id = 0, string $currency = 'USD' ): array {
	// Uppercase currency.
	$currency = strtoupper( $currency );

	// Setup default return values.
	$lowest_price = [
		'original'   => 0,
		'discounted' => 0,
	];

	// Return default values if no post ID.
	if ( empty( $post_id ) || ! in_array( $currency, CURRENCIES, true ) ) {
		return $lowest_price;
	}

	// Get occupancy lowest price for the departure.
	$departure_price = get_occupancies_lowest_price( $post_id, $currency );

	// Return lowest price.
	return $departure_price;
}

/**
 * Get departure post ids by itinerary post id.
 *
 * @param int $itinerary_post_id Itinerary post ID.
 *
 * @return int[]
 */
function get_departures_by_itinerary( int $itinerary_post_id = 0 ): array {
	// Return empty if no itinerary post ID.
	if ( empty( $itinerary_post_id ) ) {
		return [];
	}

	// Prepare args.
	$args = [
		'post_type'              => DEPARTURE_POST_TYPE,
		'posts_per_page'         => 100, // @todo Change to -1 if departures are more than 100.
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'post_parent'            => $itinerary_post_id,
		'fields'                 => 'ids',
	];

	// Get departure posts.
	$posts         = new WP_Query( $args );
	$departure_ids = array_map( 'absint', $posts->posts );

	// Return departure post IDs.
	return $departure_ids;
}

/**
 * Get ship posts by departure post id.
 *
 * @param int $departure_post_id Departure post ID.
 *
 * @return int
 */
function get_related_ship( int $departure_post_id = 0 ): int {
	// Return empty if no departure post ID.
	if ( empty( $departure_post_id ) ) {
		return 0;
	}

	// Get ship post ID.
	$ship_post_id = get_post_meta( $departure_post_id, 'related_ship', true );
	$ship_code    = get_post_meta( $departure_post_id, 'ship_code', true );

	// Return empty if no ship post ID.
	if ( empty( $ship_post_id ) || is_array( $ship_post_id ) ) {
		// Check if ship code is available.
		if ( empty( $ship_code ) || ! is_string( $ship_code ) ) {
			return 0;
		}

		// Get ship post ID from ship code.
		$ship_post_id = get_id_from_ship_code( $ship_code );

		// Return empty if no ship post ID.
		if ( empty( $ship_post_id ) ) {
			return 0;
		}
	}

	// Return the ship post ID.
	return absint( $ship_post_id );
}

/**
 * Get starting date.
 *
 * @param int $departure_post_id Departure Post ID.
 *
 * @return string
 */
function get_start_date( int $departure_post_id = 0 ): string {
	// Setup default.
	$default_start_date = '';

	// Bail out if empty.
	if ( empty( $departure_post_id ) ) {
		return $default_start_date;
	}

	// Get start date from meta.
	$start_date = get_post_meta( $departure_post_id, 'start_date', true );

	// Validate.
	if ( empty( $start_date ) || ! is_string( $start_date ) ) {
		return $default_start_date;
	}

	// Return start date.
	return $start_date;
}

/**
 * Get ending date.
 *
 * @param int $departure_post_id Departure Post ID.
 *
 * @return string
 */
function get_end_date( int $departure_post_id = 0 ): string {
	// Setup default.
	$default_end_date = '';

	// Bail out if empty.
	if ( empty( $departure_post_id ) ) {
		return $default_end_date;
	}

	// Get end date from meta.
	$end_date = get_post_meta( $departure_post_id, 'end_date', true );

	// Validate.
	if ( empty( $end_date ) || ! is_string( $end_date ) ) {
		return $default_end_date;
	}

	// Return start date.
	return $end_date;
}
