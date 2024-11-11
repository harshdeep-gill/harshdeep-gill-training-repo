<?php
/**
 * Namespace for the Itinerary functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Itineraries;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_Post;
use WP_Query;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Ingestor\Departures\get_departures_data;
use function Quark\Itineraries\get as get_itinerary;

use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;

/**
 * Get itineraries for the expedition.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     packageId: string,
 *     name: string,
 *     published: bool,
 *     startLocation: string,
 *     endLocation: string,
 *     departures: mixed[],
 *   }
 * >
 */
function get_itineraries( int $expedition_post_id = 0 ): array {
	// Initialize itineraries.
	$itineraries_data = [];

	// Early return if no expedition post ID.
	if ( empty( $expedition_post_id ) ) {
		return $itineraries_data;
	}

	// Get expedition post.
	$expedition_post = get_expedition( $expedition_post_id );

	// Check for post.
	if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
		return $itineraries_data;
	}

	// Check for itineraries.
	if ( ! array_key_exists( 'related_itineraries', $expedition_post['post_meta'] ) ||
	! is_array( $expedition_post['post_meta']['related_itineraries'] )
	) {
		return $itineraries_data;
	}

	// Validate itineraries.
	$itinerary_post_ids = array_map( 'absint', $expedition_post['post_meta']['related_itineraries'] );

	// Loop through each itinerary.
	foreach ( $itinerary_post_ids as $itinerary_post_id ) {
		// Get itinerary post.
		$itinerary_post = get_itinerary( $itinerary_post_id );

		// Check for post.
		if ( empty( $itinerary_post['post'] ) || ! $itinerary_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Check for published or draft.
		if ( ! in_array( $itinerary_post['post']->post_status, [ 'publish', 'draft' ], true ) ) {
			continue;
		}

		// Get duration in days.
		$duration_in_days = absint( $itinerary_post['post_meta']['duration_in_days'] ?? '' );

		// Validate softrip_package_code.
		if ( ! array_key_exists( 'softrip_package_code', $itinerary_post['post_meta'] ) ) {
			continue;
		}

		// Initialize softrip_package_code.
		$softrip_package_code = strval( $itinerary_post['post_meta']['softrip_package_code'] );

		// Bail if no softrip_package_code.
		if ( empty( $softrip_package_code ) ) {
			continue;
		}

		// Initialize itinerary data.
		$itinerary_data = [
			'id'             => $itinerary_post_id,
			'packageId'      => $softrip_package_code,
			'name'           => get_raw_text_from_html( $itinerary_post['post']->post_title ),
			'published'      => 'publish' === $itinerary_post['post']->post_status,
			'durationInDays' => $duration_in_days,
			'startLocation'  => '',
			'endLocation'    => '',
			'departures'     => [],
		];

		// Get start location from meta.
		$start_location_id = absint( get_post_meta( $itinerary_post_id, 'start_location', true ) );

		// Get term name.
		$start_location_term = get_term( $start_location_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );

		// Check for term.
		if ( ! empty( $start_location_term ) && is_array( $start_location_term ) ) {
			$itinerary_data['startLocation'] = $start_location_term['name'];
		}

		// Get end location from meta.
		$end_location_id = absint( get_post_meta( $itinerary_post_id, 'end_location', true ) );

		// Get term name.
		$end_location_term = get_term( $end_location_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );

		// Check for term.
		if ( ! empty( $end_location_term ) && is_array( $end_location_term ) ) {
			$itinerary_data['endLocation'] = $end_location_term['name'];
		}

		// Add departure data.
		$itinerary_data['departures'] = get_departures_data( $expedition_post_id, $itinerary_post_id );

		// Add itinerary data to itineraries.
		$itineraries_data[] = $itinerary_data;
	}

	// Itineraries data.
	return $itineraries_data;
}
