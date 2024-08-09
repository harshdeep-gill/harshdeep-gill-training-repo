<?php
/**
 * Namespace for the Softrip itinerary data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Itineraries;

use function Quark\Softrip\Departure\get_departures_by_itinerary;
use function Quark\Softrip\Departure\get_lowest_price as get_departure_lowest_price;
use function Quark\Softrip\Departure\get_related_ship;
use function Quark\Softrip\Departure\get_starting_date as get_departure_starting_date;
use function Quark\Softrip\Departure\get_ending_date as get_departure_ending_date;

use const Quark\Core\CURRENCIES;

/**
 * Get lowest price for itinerary.
 *
 * @param int    $post_id Itinerary post ID.
 * @param string $currency Currency code.
 *
 * @return array{
 *  original: float,
 *  discounted: float,
 * }
 */
function get_lowest_price( int $post_id = 0, string $currency = 'USD' ): array {
	// Uppercase the currency code.
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

	// Get all departure posts for the itinerary.
	$departure_post_ids = get_departures_by_itinerary( $post_id );

	// Loop through each departure post.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Get the lowest price for the departure.
		$departure_price = get_departure_lowest_price( $departure_post_id, $currency );

		// If the lowest price is less than the current lowest price, update it.
		if ( empty( $lowest_price['original'] ) || $departure_price['original'] < $lowest_price['original'] ) {
			$lowest_price['original'] = $departure_price['original'];
		}

		// If the discounted price is less than the current discounted price, update it.
		if ( empty( $lowest_price['discounted'] ) || $departure_price['discounted'] < $lowest_price['discounted'] ) {
			$lowest_price['discounted'] = $departure_price['discounted'];
		}
	}

	// Return the lowest price.
	return $lowest_price;
}

/**
 * Get related ships for itinerary.
 *
 * @param int $post_id Itinerary post ID.
 *
 * @return int[] Ship post IDs.
 */
function get_related_ships( int $post_id = 0 ): array {
	// Setup default return value.
	$ships = [];

	// Return default value if no post ID.
	if ( empty( $post_id ) ) {
		return $ships;
	}

	// Get all departure posts for the itinerary.
	$departure_post_ids = get_departures_by_itinerary( $post_id );

	// Loop through each departure post.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Get the ship post for the departure.
		$ship_post_id = get_related_ship( $departure_post_id );

		// Skip if ship is empty.
		if ( empty( $ship_post_id ) ) {
			continue;
		}

		// Add the ship post ID to the list.
		$ships[] = $ship_post_id;
	}

	// Return the ships.
	return $ships;
}

/**
 * Get earliest start date for itinerary.
 *
 * @param int $post_id Itinerary post ID.
 *
 * @return string
 */
function get_starting_date( int $post_id = 0 ): string {
	// Set up default.
	$start_date = '';

	// Bail out if empty.
	if ( empty( $post_id ) ) {
		return $start_date;
	}

	// Get all departure posts for the itinerary.
	$departure_post_ids = get_departures_by_itinerary( $post_id );

	// Loop through each departure post.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Get starting date.
		$departure_start_date = get_departure_starting_date( $departure_post_id );

		// Validate.
		if ( empty( $departure_start_date ) ) {
			continue;
		}

		// Compare.
		if ( empty( $start_date ) || strtotime( $start_date ) > strtotime( $departure_start_date ) ) {
			// Update start date.
			$start_date = $departure_start_date;
		}
	}

	// Return the start date.
	return $start_date;
}

/**
 * Get farthest ending date for itinerary.
 *
 * @param int $post_id Itinerary post ID.
 *
 * @return string
 */
function get_ending_date( int $post_id = 0 ): string {
	// Set up default.
	$end_date = '';

	// Bail out if empty.
	if ( empty( $post_id ) ) {
		return $end_date;
	}

	// Get all departure posts for the itinerary.
	$departure_post_ids = get_departures_by_itinerary( $post_id );

	// Loop through each departure post.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Get ending date.
		$departure_end_date = get_departure_ending_date( $departure_post_id );

		// Validate.
		if ( empty( $departure_end_date ) ) {
			continue;
		}

		// Compare.
		if ( empty( $end_date ) || strtotime( $end_date ) < strtotime( $departure_end_date ) ) {
			// Update end date.
			$end_date = $departure_end_date;
		}
	}

	// Return the end date.
	return $end_date;
}
