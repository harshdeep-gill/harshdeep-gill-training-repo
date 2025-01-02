<?php
/**
 * Namespace for the Departure functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Departures;

use WP_Post;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Departures\get as get_departure;
use function Quark\Ingestor\AdventureOptions\get_included_adventure_options_data;
use function Quark\Ingestor\AdventureOptions\get_paid_adventure_options_data;
use function Quark\Ingestor\Cabins\get_cabins_data;
use function Quark\Ingestor\get_id;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ingestor\Promotions\get_promotions_data;
use function Quark\Ingestor\Ships\get_ship_data;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;

/**
 * Get departures for an itinerary.
 *
 * @param int $expedition_post_id Expedition post ID.
 * @param int $itinerary_post_id  Itinerary post ID.
 *
 * @return array{}|array<int,
 *   array{
 *    id: int,
 *    name: string,
 *    published: bool,
 *    startDate: string,
 *    endDate: string,
 *    durationInDays: int,
 *    ship: array{}|array{
 *      id: int,
 *      code: string,
 *      name: string,
 *    },
 *    languages: string,
 *    cabins: mixed[],
 *    adventureOptions: array{
 *       includedOptions: mixed[],
 *       paidOptions: mixed[],
 *    }
 *  }
 * >
 */
function get_departures_data( int $expedition_post_id = 0, int $itinerary_post_id = 0 ): array {
	// Initialize departures data.
	$departures_data = [];

	// Early return if no expedition or itinerary post ID.
	if ( empty( $itinerary_post_id ) || empty( $expedition_post_id ) ) {
		return $departures_data;
	}

	// Get departure post IDs by itinerary.
	$departure_post_ids = get_children(
		[
			'post_parent'            => $itinerary_post_id,
			'post_type'              => DEPARTURE_POST_TYPE,
			'post_status'            => [ 'publish', 'draft' ],
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'ID',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		],
		ARRAY_N
	);

	// Validate departure post IDs.
	$departure_post_ids = array_map( 'absint', $departure_post_ids );

	// Loop through each departure.
	foreach ( $departure_post_ids as $departure_post_id ) {
		$departure_post = get_departure( $departure_post_id );

		// Check for post.
		if ( empty( $departure_post['post'] ) || ! $departure_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Get softrip_id meta.
		if ( ! array_key_exists( 'softrip_id', $departure_post['post_meta'] ) ) {
			continue;
		}

		// Initialize softrip_id.
		$softrip_id = strval( $departure_post['post_meta']['softrip_id'] );

		// Initialize departure data.
		$departure_data = [
			'id'               => get_id( $departure_post_id ),
			'name'             => get_raw_text_from_html( $departure_post['post']->post_title ),
			'published'        => 'publish' === $departure_post['post']->post_status,
			'url'              => '',
			'startDate'        => $departure_post['post_meta']['start_date'] ?? '',
			'endDate'          => $departure_post['post_meta']['end_date'] ?? '',
			'durationInDays'   => absint( $departure_post['post_meta']['duration'] ?? '' ),
			'softripId'        => $softrip_id,
			'code'             => $departure_post['post_meta']['softrip_code'] ?? '',
			'modified'         => get_post_modified_time( $departure_post['post'] ),
			'languages'        => '',
			'ship'             => [],
			'cabins'           => [],
			'adventureOptions' => [
				'includedOptions' => [],
				'paidOptions'     => [],
			],
			'promotions'       => [],
		];

		// Add ship data.
		$departure_data['ship'] = get_ship_data( $departure_post_id );

		// Get languages.
		if ( array_key_exists( SPOKEN_LANGUAGE_TAXONOMY, $departure_post['post_taxonomies'] ) && is_array( $departure_post['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] ) && ! empty( $departure_post['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] ) ) {
			// Initialize languages.
			$departure_languages = [];

			// Iterate through languages.
			foreach ( $departure_post['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] as $language_term ) {
				// Get language term ID.
				$language_term_id = absint( $language_term['term_id'] );

				// Get language code from meta.
				$language_code = strval( get_term_meta( $language_term_id, 'language_code', true ) );

				// Check for language code.
				if ( ! empty( $language_code ) ) {
					$departure_languages[] = $language_code;
				}
			}

			// Set languages.
			$departure_data['languages'] = implode( ', ', $departure_languages );
		}

		// Add cabins data.
		$departure_data['cabins'] = get_cabins_data( $expedition_post_id, $itinerary_post_id, $departure_post_id );

		// Add included adventure options data.
		$departure_data['adventureOptions']['includedOptions'] = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$departure_data['adventureOptions']['paidOptions']     = get_paid_adventure_options_data( $departure_post_id );

		// Add promotions data.
		$departure_data['promotions'] = get_promotions_data( $departure_post_id );

		// Add departure data.
		$departures_data[] = $departure_data;
	}

	// Return departures data.
	return $departures_data;
}
