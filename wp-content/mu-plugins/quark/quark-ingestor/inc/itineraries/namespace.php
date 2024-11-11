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
use function Quark\InclusionSets\get as get_inclusion_set;
use function Quark\ExclusionSets\get as get_exclusion_set;
use function Quark\Ingestor\Departures\get_departures_data;
use function Quark\Itineraries\get as get_itinerary;
use function Quark\ItineraryDays\get as get_itinerary_day;
use function Quark\Ports\get as get_port;

use const Quark\InclusionSets\INCLUSION_EXCLUSION_CATEGORY;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\StaffMembers\SEASON_TAXONOMY;

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

		// Initialize season.
		$season = '';

		// Check for post taxonomies.
		if ( ! empty( $itinerary_post['post_taxonomies'] ) && ! empty( $itinerary_post['post_taxonomies'][ SEASON_TAXONOMY ] ) && is_array( $itinerary_post['post_taxonomies'][ SEASON_TAXONOMY ] ) ) {
			$season = $itinerary_post['post_taxonomies'][ SEASON_TAXONOMY ][0]['name'] ?? '';
		}

		// Initialize itinerary data.
		$itinerary_data = [
			'id'                     => $itinerary_post_id,
			'packageId'              => $softrip_package_code,
			'name'                   => get_raw_text_from_html( $itinerary_post['post']->post_title ),
			'published'              => 'publish' === $itinerary_post['post']->post_status,
			'durationInDays'         => $duration_in_days,
			'startLocation'          => '',
			'endLocation'            => '',
			'departures'             => [],
			'modified'               => $itinerary_post['post']->post_modified,
			'season'                 => $season,
			'embarkation'            => '',
			'embarkationPortCode'    => '',
			'disembarkation'         => '',
			'disembarkationPortCode' => '',
			'itineraryMap'           => [],
			'days'                   => [],
			'inclusions'             => [],
			'exclusions'             => [],
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

		// Get embarkation from meta.
		$embarkation_port_post_id = absint( $itinerary_post['post_meta']['embarkation_port'] ?? 0 );

		// Validate post ID.
		if ( ! empty( $embarkation_port_post_id ) ) {
			$embarkation_port_post = get_port( $embarkation_port_post_id );

			// Validate post.
			if ( ! empty( $embarkation_port_post['post'] ) && $embarkation_port_post['post'] instanceof WP_Post ) {
				$itinerary_data['embarkation']         = get_raw_text_from_html( $embarkation_port_post['post']->post_title );
				$itinerary_data['embarkationPortCode'] = $embarkation_port_post['post_meta']['port_code'] ?? '';
			}
		}

		// Get disembarkation from meta.
		$disembarkation_port_post_id = absint( $itinerary_post['post_meta']['disembarkation_port'] ?? 0 );

		// Validate post ID.
		if ( ! empty( $disembarkation_port_post_id ) ) {
			$disembarkation_port_post = get_port( $disembarkation_port_post_id );

			// Validate post.
			if ( ! empty( $disembarkation_port_post['post'] ) && $disembarkation_port_post['post'] instanceof WP_Post ) {
				$itinerary_data['disembarkation']         = get_raw_text_from_html( $disembarkation_port_post['post']->post_title );
				$itinerary_data['disembarkationPortCode'] = $disembarkation_port_post['post_meta']['port_code'] ?? '';
			}
		}

		// Get map from meta.
		$itinerary_map_image_id = absint( $itinerary_post['post_meta']['map'] ?? 0 );

		// Validate image ID.
		if ( ! empty( $itinerary_map_image_id ) ) {
			// Full size url.
			$full_size_url = wp_get_attachment_image_url( $itinerary_map_image_id, 'full' );

			// Validate full size url.
			if ( ! empty( $full_size_url ) ) {
				// Thumbnail url.
				$thumbnail_url = wp_get_attachment_image_url( $itinerary_map_image_id, 'thumbnail' );

				// Alt text.
				$alt_text = strval( get_post_meta( $itinerary_map_image_id, '_wp_attachment_image_alt', true ) );

				// Add image data.
				$itinerary_data['itineraryMap'] = [
					'id'           => $itinerary_map_image_id,
					'fullSizeUrl'  => $full_size_url,
					'thumbnailUrl' => $thumbnail_url,
					'alt'          => $alt_text,
				];
			}
		}

		// Add days data.
		$itinerary_data['days'] = get_itinerary_days( $itinerary_post_id );

		// Add inclusions data.
		$itinerary_data['inclusions'] = get_inclusions_data( $itinerary_post_id );

		// Add exclusions data.
		$itinerary_data['exclusions'] = get_exclusions_data( $itinerary_post_id );

		// Add departure data.
		$itinerary_data['departures'] = get_departures_data( $expedition_post_id, $itinerary_post_id );

		// Add itinerary data to itineraries.
		$itineraries_data[] = $itinerary_data;
	}

	// Itineraries data.
	return $itineraries_data;
}

/**
 * Get itinerary day data.
 *
 * @param int $itinerary_post_id Itinerary ID.
 *
 * @return mixed[]
 */
function get_itinerary_days( int $itinerary_post_id = 0 ): array {
	// Initialize itinerary days.
	$itinerary_days_data = [];

	// Early return if no itinerary post ID.
	if ( empty( $itinerary_post_id ) ) {
		return $itinerary_days_data;
	}

	// Get itinerary post.
	$itinerary_post = get_itinerary( $itinerary_post_id );

	// Check for post.
	if ( empty( $itinerary_post['post'] ) || ! $itinerary_post['post'] instanceof WP_Post ) {
		return $itinerary_days_data;
	}

	// Get days from meta.
	$itinerary_day_post_ids = $itinerary_post['post_meta']['itinerary_days'] ?? [];

	// Bail if no days.
	if ( empty( $itinerary_day_post_ids ) || ! is_array( $itinerary_day_post_ids ) ) {
		return $itinerary_days_data;
	}

	// Convert to integer.
	$itinerary_day_post_ids = array_map( 'absint', $itinerary_day_post_ids );

	// Loop through each day.
	foreach ( $itinerary_day_post_ids as $itinerary_day_post_id ) {
		// Skip if empty.
		if ( empty( $itinerary_day_post_id ) ) {
			continue;
		}

		// Get itinerary day post.
		$itinerary_day_post = get_itinerary_day( $itinerary_day_post_id );

		// Validate post.
		if ( empty( $itinerary_day_post['post'] ) || ! $itinerary_day_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Prepare itinerary day data.
		$itinerary_day_data = [
			'title'          => strval( $itinerary_day_post['post_meta']['day_title'] ?? '' ),
			'dayStartNumber' => absint( $itinerary_day_post['post_meta']['day_number_from'] ?? 0 ),
			'dayEndNumber'   => absint( $itinerary_day_post['post_meta']['day_number_to'] ?? 0 ),
			'location'       => strval( $itinerary_day_post['post_meta']['location'] ?? '' ),
			'portCode'       => '',
			'portLocation'   => '',
			'description'    => get_raw_text_from_html( $itinerary_day_post['post']->post_content ),
		];

		// Add port data.
		$port_post_id = absint( $itinerary_day_post['post_meta']['port'] ?? 0 );

		// Validate post ID.
		if ( ! empty( $port_post_id ) ) {
			// Get port post.
			$port_post = get_port( $port_post_id );

			// Validate post.
			if ( ! empty( $port_post['post'] ) && $port_post['post'] instanceof WP_Post ) {
				$itinerary_day_data['portCode']     = $port_post['post_meta']['port_code'] ?? '';
				$itinerary_day_data['portLocation'] = get_raw_text_from_html( $port_post['post']->post_title );
			}
		}

		// Add itinerary day data to itinerary days.
		$itinerary_days_data[] = $itinerary_day_data;
	}

	return $itinerary_days_data;
}

/**
 * Get inclusions data.
 *
 * @param int $itinerary_post_id Itinerary ID.
 *
 * @return mixed[]
 */
function get_inclusions_data( int $itinerary_post_id = 0 ): array {
	// Initialize inclusions.
	$inclusions_exclusions_data = [];

	// Early return if no itinerary post ID.
	if ( empty( $itinerary_post_id ) ) {
		return $inclusions_exclusions_data;
	}

	// Get itinerary post.
	$itinerary_post = get_itinerary( $itinerary_post_id );

	// Check for post.
	if ( empty( $itinerary_post['post'] ) || ! $itinerary_post['post'] instanceof WP_Post ) {
		return $inclusions_exclusions_data;
	}

	// Get inclusions from meta.
	$inclusion_post_ids = $itinerary_post['post_meta']['inclusions'] ?? [];

	// Bail if no inclusions.
	if ( empty( $inclusion_post_ids ) || ! is_array( $inclusion_post_ids ) ) {
		return $inclusions_exclusions_data;
	}

	// Convert to integer.
	$inclusion_post_ids = array_map( 'absint', $inclusion_post_ids );

	// Loop through each inclusion.
	foreach ( $inclusion_post_ids as $inclusion_post_id ) {
		// Skip if empty.
		if ( empty( $inclusion_post_id ) ) {
			continue;
		}

		// Get inclusion post.
		$inclusion_post = get_inclusion_set( $inclusion_post_id );

		// Validate post.
		if ( empty( $inclusion_post['post'] ) || ! $inclusion_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Initialize category id.
		$category_id   = 0;
		$category_name = '';

		// Check for post taxonomies.
		if ( ! empty( $inclusion_post['post_taxonomies'] ) && ! empty( $inclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ] ) && is_array( $inclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ] ) ) {
			$category_id   = $inclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ][0]['term_id'] ?? 0;
			$category_name = $inclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ][0]['name'] ?? '';
		}

		// Initialize inclusion items.
		$inclusion_items = [];

		// Loop through each meta.
		foreach ( $inclusion_post['post_meta'] as $key => $value ) {
			// Skip if not an item.
			if ( ! str_starts_with( $key, 'set_' ) || ! is_string( $value ) ) {
				continue;
			}

			// Add item.
			$inclusion_items[] = get_raw_text_from_html( $value );
		}

		// Prepare inclusion data.
		$inclusion_data = [
			'id'            => $inclusion_post_id,
			'title'         => $inclusion_post['post_meta']['display_title'] ?? '',
			'items'         => $inclusion_items,
			'category_id'   => $category_id,
			'category_name' => $category_name,
		];

		// Add inclusion data to inclusions.
		$inclusions_exclusions_data[] = $inclusion_data;
	}

	return $inclusions_exclusions_data;
}

/**
 * Get exclusions data.
 *
 * @param int $itinerary_post_id Itinerary ID.
 *
 * @return mixed[]
 */
function get_exclusions_data( int $itinerary_post_id = 0 ): array {
	// Initialize inclusions.
	$exclusions_data = [];

	// Early return if no itinerary post ID.
	if ( empty( $itinerary_post_id ) ) {
		return $exclusions_data;
	}

	// Get itinerary post.
	$itinerary_post = get_itinerary( $itinerary_post_id );

	// Check for post.
	if ( empty( $itinerary_post['post'] ) || ! $itinerary_post['post'] instanceof WP_Post ) {
		return $exclusions_data;
	}

	// Get exclusions from meta.
	$exclusion_post_ids = $itinerary_post['post_meta']['exclusions'] ?? [];

	// Bail if no exclusions.
	if ( empty( $exclusion_post_ids ) || ! is_array( $exclusion_post_ids ) ) {
		return $exclusions_data;
	}

	// Convert to integer.
	$exclusion_post_ids = array_map( 'absint', $exclusion_post_ids );

	// Loop through each inclusion.
	foreach ( $exclusion_post_ids as $exclusion_post_id ) {
		// Skip if empty.
		if ( empty( $exclusion_post_id ) ) {
			continue;
		}

		// Get inclusion post.
		$exclusion_post = get_exclusion_set( $exclusion_post_id );

		// Validate post.
		if ( empty( $exclusion_post['post'] ) || ! $exclusion_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Initialize category id.
		$category_id   = 0;
		$category_name = '';

		// Check for post taxonomies.
		if ( ! empty( $exclusion_post['post_taxonomies'] ) && ! empty( $exclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ] ) && is_array( $exclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ] ) ) {
			$category_id   = $exclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ][0]['term_id'] ?? 0;
			$category_name = $exclusion_post['post_taxonomies'][ INCLUSION_EXCLUSION_CATEGORY ][0]['name'] ?? '';
		}

		// Initialize inclusion items.
		$exclusion_items = [];

		// Loop through each meta.
		foreach ( $exclusion_post['post_meta'] as $key => $value ) {
			// Skip if not an item.
			if ( ! str_starts_with( $key, 'set_' ) || ! is_string( $value ) ) {
				continue;
			}

			// Add item.
			$exclusion_items[] = get_raw_text_from_html( $value );
		}

		// Prepare inclusion data.
		$exclusion_data = [
			'id'            => $exclusion_post_id,
			'title'         => $exclusion_post['post_meta']['display_title'] ?? '',
			'items'         => $exclusion_items,
			'category_id'   => $category_id,
			'category_name' => $category_name,
		];

		// Add inclusion data to inclusions.
		$exclusions_data[] = $exclusion_data;
	}

	return $exclusions_data;
}
