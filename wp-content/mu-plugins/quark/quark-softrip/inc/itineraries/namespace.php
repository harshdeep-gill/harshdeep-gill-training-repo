<?php
/**
 * Namespace for the Softrip itinerary data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Itineraries;

use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Departures\get_related_ship;
use function Quark\Softrip\Departures\get_start_date as get_departure_start_date;
use function Quark\Softrip\Departures\get_end_date as get_departure_end_date;

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

	// Allow overriding the related ships.
	$ships = apply_filters( 'qrk_override_related_ships', $ships, $post_id );

	// Return if ships are already set.
	if ( ! empty( $ships ) && is_array( $ships ) ) {
		return $ships;
	}

	// Get all departure posts for the itinerary.
	$departure_post_ids = get_departures_by_itinerary( $post_id );
	$ships              = [];

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

	// Remove duplicates.
	$ships = array_unique( $ships );

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
function get_start_date( int $post_id = 0 ): string {
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
		$departure_start_date = get_departure_start_date( $departure_post_id );

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
function get_end_date( int $post_id = 0 ): string {
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
		$departure_end_date = get_departure_end_date( $departure_post_id );

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
