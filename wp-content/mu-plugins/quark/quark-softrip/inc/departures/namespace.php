<?php
/**
 * Namespace for the Softrip departure data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Departures;

use WP_Error;
use WP_Query;

use function Quark\Departures\bust_post_cache as bust_departure_post_cache;
use function Quark\Localization\get_currencies;
use function Quark\Ships\get_id_from_ship_code;
use function Quark\Softrip\AdventureOptions\update_adventure_options;
use function Quark\Softrip\get_initiated_via;
use function Quark\Softrip\Occupancies\update_occupancies;
use function Quark\Softrip\is_date_in_the_past;
use function Quark\Softrip\Occupancies\get_lowest_price as get_occupancies_lowest_price;
use function Quark\Softrip\Occupancies\is_occupancy_available;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Localization\DEFAULT_CURRENCY;

const DATA_HASH_KEY = '_formatted_data_hash';

/**
 * Update the departure data.
 *
 * @param mixed[] $raw_departures              Raw departures data from Softrip to update with.
 * @param string  $softrip_package_code        Softrip package code.
 * @param int[]   $specific_departure_post_ids Specific Departure post IDs to update. Default is empty.
 *
 * @return bool Whether the departures are updated or not.
 */
function update_departures( array $raw_departures = [], string $softrip_package_code = '', array $specific_departure_post_ids = [] ): bool {
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
	$expedition_post_ids = get_post_meta( $itinerary_post_id, 'related_expedition', true );

	// Validate for empty or multiple expedition post IDs.
	if ( empty( $expedition_post_ids ) ) {
		return false;
	} elseif ( is_array( $expedition_post_ids ) ) {
		// Validate for multiple expedition post IDs.
		if ( 1 < count( $expedition_post_ids ) ) {
			return false;
		}

		// Get the expedition post ID.
		$expedition_post_id = absint( $expedition_post_ids[0] );
	} else {
		// Get the expedition post ID.
		$expedition_post_id = absint( $expedition_post_ids );
	}

	// Bail out if empty expedition post ID.
	if ( empty( $expedition_post_id ) ) {
		return false;
	}

	// Get current departures with the package code.
	$departure_posts = new WP_Query(
		[
			'post_type'              => DEPARTURE_POST_TYPE,
			'fields'                 => 'ids',
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'ignore_sticky_posts'    => true,
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
		$departure_code = sanitize_text_field( strval( get_post_meta( $departure_post_id, 'softrip_id', true ) ) );

		// Skip if empty.
		if ( empty( $departure_code ) ) {
			continue;
		}

		// Add to existing departure codes.
		$existing_departure_codes[ $departure_code ] = $departure_post_id;
	}

	// If no raw departures and no specific departure post IDs, then unpublish expired departures.
	if ( empty( $raw_departures ) && empty( $specific_departure_post_ids ) ) {
		// Loop through existing departure codes.
		foreach ( $existing_departure_codes as $departure_code => $departure_post_id ) {
			// Skip if draft already.
			if ( 'draft' === get_post_status( $departure_post_id ) ) {
				continue;
			}

			// Get start date.
			$start_date = get_start_date( $departure_post_id );

			// If start date is in the past, unpublish the post.
			if ( ! empty( $start_date ) && is_date_in_the_past( $start_date ) ) {
				// Unpublish the post.
				wp_update_post(
					[
						'ID'          => $departure_post_id,
						'post_status' => 'draft',
					]
				);

				// Bust the departure module cache.
				bust_departure_post_cache( $departure_post_id );
			}
		}

		// Return successful.
		return true;
	}

	/**
	 * Update the departures.
	 * 1. Loop through raw departures.
	 * 2. Update the departure posts - create if not existing, update if existing and different.
	 * 3. Update the adventure options - create if not existing, update if existing and different.
	 * 4. Update the promotions - create if not existing, update if existing and different.
	 * 5. Update the cabin occupancies - create if not existing, update if existing and different.
	 * 6. Update the occupancy prices - create if not existing, update if existing and different.
	 * 7. For all existing departures, if start date is in past, then unpublish.
	 */

	// Initialize departure codes which are updated.
	$updated_departure_codes = [];

	// Loop through raw departures and update/create the departure posts, cabins, prices and promotions.
	foreach ( $raw_departures as $raw_departure ) {
		// Validate if not array or empty array or no id.
		if ( ! is_array( $raw_departure ) || empty( $raw_departure ) || empty( $raw_departure['id'] ) ) {
			continue;
		}

		// Initialize softrip id.
		$departure_softrip_id = sanitize_text_field( strval( $raw_departure['id'] ) );

		// Find in existing departure codes.
		$is_existing = in_array( $departure_softrip_id, array_keys( $existing_departure_codes ), true );

		// If specific departure post IDs are set, skip if not in the list.
		if ( ! empty( $specific_departure_post_ids ) && ! in_array( $existing_departure_codes[ $departure_softrip_id ], $specific_departure_post_ids, true ) ) {
			continue;
		}

		// Format raw departure data.
		$formatted_data = format_raw_departure_data( $raw_departure, $itinerary_post_id, $expedition_post_id );

		// If empty formatted data, skip.
		if ( empty( $formatted_data ) ) {
			continue;
		}

		// Has valid occupancies.
		$has_valid_occupancies = has_raw_departure_valid_occupancies( $raw_departure );

		// If no valid occupancies.
		if ( ! $has_valid_occupancies ) {
			// Skip if not existing.
			if ( ! $is_existing ) {
				$message = 'No valid occupancies found for new departure.';

				// Log error.
				do_action(
					'quark_softrip_sync_error',
					[
						'error' => $message,
						'via'   => get_initiated_via(),
						'codes' => [ $departure_softrip_id ],
					]
				);

				// Continue to next departure - no need to create.
				continue;
			} else {
				// Check if already draft.
				if ( 'draft' === get_post_status( $existing_departure_codes[ $departure_softrip_id ] ) ) {
					// Prepare message.
					$message = 'No valid occupancies found for existing draft departure.';

					// Log error.
					do_action(
						'quark_softrip_sync_error',
						[
							'error' => $message,
							'via'   => get_initiated_via(),
							'codes' => [ $departure_softrip_id ],
						]
					);

					// Continue to next departure - no need to update.
					continue;
				}

				// Prepare message.
				$message = 'No valid occupancies found for existing departure. So, converting to draft.';

				// Unpublish the post.
				wp_update_post(
					[
						'ID'          => $existing_departure_codes[ $departure_softrip_id ],
						'post_status' => 'draft',
					]
				);

				// Bust the departure module cache.
				bust_departure_post_cache( $existing_departure_codes[ $departure_softrip_id ] );

				// Log error.
				do_action(
					'quark_softrip_sync_error',
					[
						'error' => $message,
						'via'   => get_initiated_via(),
						'codes' => [ $departure_softrip_id ],
					]
				);

				// Not bailing out as departure's other data (cabins, occupancies) might be updated.
			}
		}

		// Hashed formatted data.
		$formatted_data_hash = md5( strval( wp_json_encode( $formatted_data ) ) );

		// Initialize post id.
		$updated_post_id = 0;

		// Initialize is updated flag.
		$is_adventure_options_updated = false;
		$is_promotions_updated        = false;
		$is_occupancies_updated       = false;
		$is_departure_post_updated    = false;

		// If existing, update the post.
		if ( $is_existing ) {
			// Get hash of existing formatted data.
			$existing_formatted_data_hash = get_post_meta( $existing_departure_codes[ $departure_softrip_id ], DATA_HASH_KEY, true );

			// Update the post if formatted data hash is different.
			if ( $formatted_data_hash !== $existing_formatted_data_hash ) {
				// Add post ID to formatted data.
				$formatted_data['ID'] = $existing_departure_codes[ $departure_softrip_id ];

				// Update the post.
				$updated_post_id           = wp_update_post( $formatted_data, true );
				$is_departure_post_updated = true;
			} else {
				// Set updated post ID.
				$updated_post_id = $existing_departure_codes[ $departure_softrip_id ];
			}
		} else {
			// Set post status to publish and insert the post.
			$formatted_data['post_status'] = 'publish';
			$updated_post_id               = wp_insert_post( $formatted_data );
			$is_departure_post_updated     = true;
		}

		// Skip if error or empty post ID.
		if ( $updated_post_id instanceof WP_Error || empty( $updated_post_id ) ) {
			continue;
		} else {
			// Set formatted data hash.
			update_post_meta( $updated_post_id, DATA_HASH_KEY, $formatted_data_hash );

			// Add to updated departure codes.
			$updated_departure_codes[] = $departure_softrip_id;

			// Set spoken language for newly created departure.
			if ( ! $is_existing ) {
				// Set english as spoken language.
				wp_set_object_terms( $updated_post_id, [ 'english' ], SPOKEN_LANGUAGE_TAXONOMY );
			}
		}

		// Update adventure options.
		if ( isset( $raw_departure['adventureOptions'] ) && is_array( $raw_departure['adventureOptions'] ) ) {
			$is_adventure_options_updated = update_adventure_options( $raw_departure['adventureOptions'], $updated_post_id );
		}

		// Update promotions. This is done before cabins to get the promotion IDs.
		if ( isset( $raw_departure['promotions'] ) && is_array( $raw_departure['promotions'] ) ) {
			$is_promotions_updated = update_promotions( $raw_departure['promotions'], $updated_post_id );
		}

		// Update Cabins.
		if ( isset( $raw_departure['cabins'] ) && is_array( $raw_departure['cabins'] ) ) {
			$is_occupancies_updated = update_occupancies( $raw_departure['cabins'], $updated_post_id );
		}

		// Validate if any of the updates are successful.
		if ( $is_adventure_options_updated || $is_promotions_updated || $is_occupancies_updated || $is_departure_post_updated ) {
			// Bust the departure module cache.
			bust_departure_post_cache( $updated_post_id );

			// Updated fields.
			$updated_fields = [
				'adventure_options' => $is_adventure_options_updated,
				'promotions'        => $is_promotions_updated,
				'occupancies'       => $is_occupancies_updated,
				'departure_post'    => $is_departure_post_updated,
			];

			/**
			 * Fires after a departure is updated.
			 *
			 * @param int   $updated_post_id Updated departure post ID.
			 * @param array $updated_fields  Updated fields.
			 */
			do_action(
				'quark_softrip_sync_departure_updated',
				[
					'post_id'        => $updated_post_id,
					'softrip_id'     => $departure_softrip_id,
					'updated_fields' => $updated_fields,
				]
			);
		} else {
			// Fire action if no updates.
			do_action(
				'quark_softrip_sync_departure_no_updates',
				[
					'post_id'    => $updated_post_id,
					'softrip_id' => $departure_softrip_id,
				]
			);
		}
	}

	// Unpublish departure posts that have expired.
	foreach ( $existing_departure_codes as $departure_code => $departure_post_id ) {
		// Skip if specific departure post IDs are set and not in the list.
		if ( ! empty( $specific_departure_post_ids ) && ! in_array( $departure_post_id, $specific_departure_post_ids, true ) ) {
			continue;
		}

		// Skip if already draft.
		if ( 'draft' === get_post_status( $departure_post_id ) ) {
			continue;
		}

		// Get start date meta.
		$start_date = get_start_date( $departure_post_id );

		// If empty start date or not in the past, skip.
		if ( empty( $start_date ) || ! is_date_in_the_past( $start_date ) ) {
			continue;
		}

		// Draft the post.
		$is_unpublished = wp_update_post(
			[
				'ID'          => $departure_post_id,
				'post_status' => 'draft',
			]
		);

		// Skip if error.
		if ( $is_unpublished instanceof WP_Error || empty( $is_unpublished ) ) {
			continue;
		}

		// Bust the departure module cache.
		bust_departure_post_cache( $departure_post_id );

		/**
		 * Fires after a departure is expired and unpublished.
		 *
		 * @param int $departure_post_id Departure post ID.
		 */
		do_action(
			'quark_softrip_sync_departure_expired',
			[
				'post_id'    => $departure_post_id,
				'softrip_id' => $departure_code,
			]
		);
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
		'post_title'  => sanitize_text_field( strval( $raw_departure_data['id'] ) ),
		'post_type'   => DEPARTURE_POST_TYPE,
		'post_parent' => $itinerary_post_id,
		'meta_input'  => [
			'related_expedition'   => $expedition_post_id,
			'itinerary'            => $itinerary_post_id,
			'related_ship'         => get_id_from_ship_code( strval( $raw_departure_data['shipCode'] ) ),
			'softrip_package_code' => sanitize_text_field( strval( $raw_departure_data['packageCode'] ) ),
			'softrip_id'           => sanitize_text_field( strval( $raw_departure_data['id'] ) ),
			'softrip_code'         => sanitize_text_field( strval( $raw_departure_data['code'] ) ),
			'start_date'           => sanitize_text_field( strval( $raw_departure_data['startDate'] ) ),
			'end_date'             => sanitize_text_field( strval( $raw_departure_data['endDate'] ) ),
			'duration'             => absint( $raw_departure_data['duration'] ),
			'ship_code'            => sanitize_text_field( strval( $raw_departure_data['shipCode'] ) ),
			'softrip_market_code'  => sanitize_text_field( strval( $raw_departure_data['marketCode'] ) ),
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
function get_lowest_price( int $post_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// Uppercase currency.
	$currency = strtoupper( $currency );

	// Setup default return values.
	$lowest_price = [
		'original'   => 0,
		'discounted' => 0,
	];

	// Return default values if no post ID.
	if ( empty( $post_id ) || ! in_array( $currency, get_currencies(), true ) ) {
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
		'posts_per_page'         => -1,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
		'ignore_sticky_posts'    => true,
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

/**
 * Has raw departure the valid occupancies.
 *
 * @param mixed[] $occupancy_data Occupancy data.
 *
 * @return bool
 */
function has_raw_departure_valid_occupancies( array $occupancy_data = [] ): bool {
	// Check for cabins.
	if ( empty( $occupancy_data['cabins'] ) || ! is_array( $occupancy_data['cabins'] ) ) {
		return false;
	}

	// Check if any of cabin have valid occupancies.
	$has_valid_occupancies = false;

	// Loop through cabins.
	foreach ( $occupancy_data['cabins'] as $cabin ) {
		// Skip if not an array or empty occupancies.
		if ( ! is_array( $cabin ) || empty( $cabin['occupancies'] ) || ! is_array( $cabin['occupancies'] ) ) {
			continue;
		}

		// Loop through occupancies.
		foreach ( $cabin['occupancies'] as $occupancy ) {
			// Skip if not an array or empty occupancy.
			if ( ! is_array( $occupancy ) || empty( $occupancy ) || empty( $occupancy['saleStatusCode'] ) ) {
				continue;
			}

			// Skip if sale status code is not available.
			if ( ! is_occupancy_available( $occupancy['saleStatusCode'] ) ) {
				continue;
			} else {
				// Valid.
				$has_valid_occupancies = true;
				break;
			}
		}

		// Break if valid occupancies.
		if ( $has_valid_occupancies ) {
			break;
		}
	}

	// Return if valid occupancies.
	return $has_valid_occupancies;
}
