<?php
/**
 * Namespace file for occupancies data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Occupancies;

use WP_Query;

use function Quark\Departures\bust_post_cache as bust_departure_post_cache;
use function Quark\Itineraries\get_mandatory_transfer_price;
use function Quark\Itineraries\get_supplemental_price;
use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_occupancy_id;
use function Quark\Softrip\OccupancyPromotions\get_lowest_price as get_occupancy_promotion_lowest_price;
use function Quark\Softrip\OccupancyPromotions\update_occupancy_promotions;
use function Quark\Softrip\add_prefix_to_table_name;
use function Quark\Localization\get_currencies;
use function Quark\Softrip\get_initiated_via;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Localization\USD_CURRENCY;

const CACHE_KEY_PREFIX = 'quark_softrip_occupancy';
const CACHE_GROUP      = 'quark_softrip_occupancies';

/**
 * Get table name.
 *
 * @return string
 */
function get_table_name(): string {
	// Return table name.
	return add_prefix_to_table_name( 'occupancies' );
}

/**
 * Get create table SQL.
 *
 * @return string
 */
function get_table_sql(): string {
	// Get table name.
	$table_name = get_table_name();

	// Get engine and collate.
	$engine_collate = get_engine_collate();

	// Prepare SQL statement.
	$sql = "CREATE TABLE $table_name (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		softrip_id VARCHAR(255) NOT NULL UNIQUE,
		softrip_name VARCHAR(255) NOT NULL,
		mask VARCHAR(12) NOT NULL,
		departure_post_id BIGINT NOT NULL,
		cabin_category_post_id BIGINT NOT NULL,
		spaces_available INT NOT NULL,
		availability_description VARCHAR(255) NOT NULL,
		availability_status VARCHAR(4) NOT NULL,
		price_per_person_usd BIGINT NOT NULL,
		price_per_person_cad BIGINT NOT NULL,
		price_per_person_aud BIGINT NOT NULL,
		price_per_person_gbp BIGINT NOT NULL,
		price_per_person_eur BIGINT NOT NULL
	) $engine_collate";

	// Return the SQL.
	return $sql;
}

/**
 * Update cabins data.
 *
 * @param mixed[] $raw_cabins_data Raw cabins data from Softrip.
 * @param int     $departure_post_id The departure post ID.
 *
 * @return boolean
 */
function update_occupancies( array $raw_cabins_data = [], int $departure_post_id = 0 ): bool {
	// Bail if empty.
	if ( empty( $departure_post_id ) ) {
		return false;
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get existing occupancies by departure and cabin category.
	$existing_occupancies = get_occupancies_by_departure( $departure_post_id, true );

	// Initialize occupancies by Softrip ID.
	$existing_occupancies_by_softrip_id = [];

	// Loop through the existing occupancies.
	foreach ( $existing_occupancies as $existing_occupancy ) {
		// Continue if not array or empty.
		if ( ! is_array( $existing_occupancy ) || empty( $existing_occupancy['softrip_id'] ) || empty( $existing_occupancy['id'] ) ) {
			continue;
		}

		// Add to the existing occupancies by Softrip ID.
		$existing_occupancies_by_softrip_id[ strval( $existing_occupancy['softrip_id'] ) ] = absint( $existing_occupancy['id'] );
	}

	// Initialize updated softrip ids.
	$updated_softrip_ids = [];

	// Initialize any updated.
	$any_updated = false;

	// Loop through the raw cabin data.
	foreach ( $raw_cabins_data as $raw_cabin_data ) {
		// Validate the raw cabin data.
		if ( ! is_array( $raw_cabin_data ) || empty( $raw_cabin_data ) || empty( $raw_cabin_data['code'] ) || empty( $raw_cabin_data['occupancies'] ) || ! is_array( $raw_cabin_data['occupancies'] ) ) {
			continue;
		}

		// Get cabin category post id by cabin code.
		$cabin_category_post_id = get_cabin_category_post_by_cabin_code( $raw_cabin_data['code'] );

		// Bail if no cabin category post ID.
		if ( empty( $cabin_category_post_id ) ) {
			do_action(
				'quark_softrip_sync_error',
				[
					'error' => 'Cabin category post not found with cabin code: ' . $raw_cabin_data['code'] . ' for departure post ID: ' . $departure_post_id,
					'via'   => get_initiated_via(),
				]
			);

			// Continue to next cabin.
			continue;
		}

		// Iterate through the cabin occupancies.
		foreach ( $raw_cabin_data['occupancies'] as $raw_cabin_occupancy_data ) {
			// Continue if not array or empty.
			if ( ! is_array( $raw_cabin_occupancy_data ) || empty( $raw_cabin_occupancy_data ) ) {
				continue;
			}

			// Format the data.
			$formatted_data = format_data( $raw_cabin_occupancy_data, $cabin_category_post_id, $departure_post_id );

			// Bail if empty.
			if ( empty( $formatted_data ) ) {
				continue;
			}

			// Get existing cabin data by the Softrip ID.
			$existing_cabins_data = get_occupancy_data_by_softrip_id( $formatted_data['softrip_id'] );

			// Get the first item.
			$existing_cabin_data = ! empty( $existing_cabins_data ) ? $existing_cabins_data[0] : [];

			// Initialize id.
			$updated_id = 0;

			// If the cabin exists, update it.
			if ( ! empty( $existing_cabin_data['id'] ) ) {
				// Update the cabin.
				$is_updated = $wpdb->update(
					$table_name,
					$formatted_data,
					[ 'id' => $existing_cabin_data['id'] ]
				);

				// Fire the occupancy updated action.
				if ( $is_updated > 0 ) {
					$any_updated = true;
				}

				// Set the updated ID.
				$updated_id = $existing_cabin_data['id'];
			} else {
				// Insert the cabin.
				$wpdb->insert(
					$table_name,
					$formatted_data
				);

				// Set the updated ID.
				$updated_id  = $wpdb->insert_id;
				$any_updated = true;
			}

			// Bail if empty.
			if ( empty( $updated_id ) ) {
				continue;
			}

			// Add to the updated occupancies by Softrip ID.
			$updated_softrip_ids[] = $formatted_data['softrip_id'];

			// Set occupancy promotions data.
			if ( ! empty( $raw_cabin_occupancy_data['prices'] ) && is_array( $raw_cabin_occupancy_data['prices'] ) ) {
				// Update the occupancy promotions.
				$is_occupancy_promotions_updated = update_occupancy_promotions( array_values( $raw_cabin_occupancy_data['prices'] ), $updated_id );

				// Set flag to true.
				if ( $is_occupancy_promotions_updated ) {
					$any_updated = true;
				}
			}

			// Bust caches at occupancy level.
			wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_id_' . $formatted_data['softrip_id'], CACHE_GROUP );
			wp_cache_delete( CACHE_KEY_PREFIX . '_occupancy_id_' . $updated_id, CACHE_GROUP );
		}

		// Initialize cabin spaces available.
		$cabin_spaces_available = 0;

		// Set spaces available if exists after all occupancies are updated.
		if ( ! empty( $raw_cabin_data['spacesAvailable'] ) ) {
			$cabin_spaces_available = absint( $raw_cabin_data['spacesAvailable'] );
		}

		// Store cabin spaces available on departure meta.
		$is_cabin_spaces_updated = update_post_meta( $departure_post_id, 'cabin_spaces_available_' . $cabin_category_post_id, $cabin_spaces_available );

		// Set flag to true.
		if ( $is_cabin_spaces_updated ) {
			$any_updated = true;
		}

		// Bust caches at cabin category level.
		wp_cache_delete( CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_cabin_category_post_id_' . $cabin_category_post_id, CACHE_GROUP );
	}

	/**
	 * Get the difference between existing and updated occupancies by Softrip ID
	 * that's non-updated occupancies which need to be deleted as no more present in the raw data.
	 */

	// Get the difference between existing and updated occupancies by Softrip ID - that's non-updated occupancies which need to be deleted as no more present in the raw data.
	$non_updated_softrip_ids = array_diff( array_keys( $existing_occupancies_by_softrip_id ), $updated_softrip_ids );

	// Loop through the non-updated Softrip IDs.
	foreach ( $non_updated_softrip_ids as $non_updated_softrip_id ) {
		// Get the occupancy ID.
		$occupancy_id = absint( $existing_occupancies_by_softrip_id[ $non_updated_softrip_id ] );

		// Get occupancy by id.
		$occupancy_data = get_occupancy_data_by_id( $occupancy_id, true );

		// Bail if empty.
		if ( empty( $occupancy_data ) || ! is_array( $occupancy_data ) ) {
			continue;
		}

		// First item.
		$occupancy = $occupancy_data[0];

		// Bail if empty.
		if ( ! is_array( $occupancy ) || empty( $occupancy['softrip_id'] ) || empty( $occupancy['cabin_category_post_id'] ) ) {
			continue;
		}

		// Get the cabin category post ID.
		$cabin_category_post_id = absint( $occupancy['cabin_category_post_id'] );

		// Delete the occupancy promotions.
		$is_occupancy_promotions_deleted = delete_occupancy_promotions_by_occupancy_id( $occupancy_id );

		// Skip if not deleted.
		if ( ! $is_occupancy_promotions_deleted ) {
			continue;
		}

		// Delete the occupancy.
		$is_occupancy_deleted = delete_occupancy_by_id( $occupancy_id );

		// Skip if not deleted.
		if ( ! $is_occupancy_deleted ) {
			continue;
		}

		// Bust departure cache as meta has been deleted.
		bust_departure_post_cache( $departure_post_id );
		wp_cache_delete( CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_cabin_category_post_id_' . $cabin_category_post_id, CACHE_GROUP );

		// Set flag to true.
		$any_updated = true;
	}

	// Bust caches at departure level.
	wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
	wp_cache_delete( CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id, CACHE_GROUP );

	// Return success.
	return $any_updated;
}

/**
 * Format occupancy data.
 *
 * @param mixed[] $raw_occupancy_data Raw occupancy data from Softrip.
 * @param int     $cabin_category_post_id The cabin category post ID.
 * @param int     $departure_post_id The departure post ID.
 *
 * @return array{}|array{
 *    softrip_id: string,
 *    softrip_name: string,
 *    mask: string,
 *    departure_post_id: int,
 *    cabin_category_post_id: int,
 *    spaces_available: int,
 *    availability_description: string,
 *    availability_status: string,
 *    price_per_person_usd: int,
 *    price_per_person_cad: int,
 *    price_per_person_aud: int,
 *    price_per_person_gbp: int,
 *    price_per_person_eur: int,
 * }
 */
function format_data( array $raw_occupancy_data = [], int $cabin_category_post_id = 0, int $departure_post_id = 0 ): array {
	// Bail if empty.
	if ( empty( $raw_occupancy_data ) || ! is_array( $raw_occupancy_data ) || empty( $cabin_category_post_id ) || empty( $departure_post_id ) ) {
		return [];
	}

	// Setup the defaults.
	$default = [
		'id'              => '',
		'name'            => '',
		'mask'            => '',
		'saleStatusCode'  => '',
		'saleStatus'      => '',
		'spacesAvailable' => 0,
		'prices'          => [],
	];

	// Apply defaults.
	$raw_occupancy_data = wp_parse_args( $raw_occupancy_data, $default );

	// Validate for empty values.
	if (
		empty( $raw_occupancy_data['id'] ) ||
		empty( $raw_occupancy_data['name'] ) ||
		empty( $raw_occupancy_data['mask'] ) ||
		empty( $raw_occupancy_data['saleStatusCode'] ) ||
		empty( $raw_occupancy_data['saleStatus'] ) ||
		empty( $raw_occupancy_data['prices'] )
	) {
		return [];
	}

	// Get is occupancy available.
	$is_occupancy_available = is_occupancy_available( $raw_occupancy_data['saleStatusCode'] );

	// Bail if occupancy is not available.
	if ( ! $is_occupancy_available ) {
		return [];
	}

	// Initialize the formatted data.
	$formatted_data = [
		'softrip_id'               => sanitize_text_field( strval( $raw_occupancy_data['id'] ) ),
		'softrip_name'             => sanitize_text_field( strval( $raw_occupancy_data['name'] ) ),
		'mask'                     => sanitize_text_field( strval( $raw_occupancy_data['mask'] ) ),
		'departure_post_id'        => absint( $departure_post_id ),
		'cabin_category_post_id'   => absint( $cabin_category_post_id ),
		'spaces_available'         => absint( $raw_occupancy_data['spacesAvailable'] ),
		'availability_description' => sanitize_text_field( strval( $raw_occupancy_data['saleStatus'] ) ),
		'availability_status'      => sanitize_text_field( strval( $raw_occupancy_data['saleStatusCode'] ) ),
		'price_per_person_usd'     => 0,
		'price_per_person_cad'     => 0,
		'price_per_person_aud'     => 0,
		'price_per_person_gbp'     => 0,
		'price_per_person_eur'     => 0,
	];

	// Currencies.
	$currencies = get_currencies();

	// Loop through the currencies.
	foreach ( $currencies as $currency ) {
		// Check if the currency is set and price per person exists.
		if ( empty( $raw_occupancy_data['prices'][ $currency ] ) || ! is_array( $raw_occupancy_data['prices'][ $currency ] ) || empty( $raw_occupancy_data['prices'][ $currency ]['pricePerPerson'] ) ) {
			continue;
		}

		// Set the price per person.
		$formatted_data[ 'price_per_person_' . strtolower( $currency ) ] = absint( $raw_occupancy_data['prices'][ $currency ]['pricePerPerson'] );
	}

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Get cabin category post ID by cabin code.
 *
 * @param string $cabin_code The cabin code.
 *
 * @return int
 */
function get_cabin_category_post_by_cabin_code( string $cabin_code = '' ): int {
	// Bail if empty.
	if ( empty( $cabin_code ) ) {
		return 0;
	}

	// Run the query.
	$query = new WP_Query(
		[
			'post_type'              => CABIN_CATEGORY_POST_TYPE,
			'fields'                 => 'ids',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'ignore_sticky_posts'    => true,
			'meta_query'             => [
				[
					'key'   => 'cabin_category_id',
					'value' => $cabin_code,
				],
			],
		]
	);

	// Get the cabin category post ID.
	$cabin_category_post_id = ! empty( $query->posts ) ? absint( $query->posts[0] ) : 0;

	// Return the cabin category post ID.
	return $cabin_category_post_id;
}

/**
 * Get cabin data by Softrip ID.
 *
 * @param string $softrip_id The Softrip ID.
 * @param bool   $force     Bypass cache.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_id: string,
 *     softrip_name: string,
 *     mask: string,
 *     departure_post_id: int,
 *     cabin_category_post_id: int,
 *     spaces_available: int,
 *     availability_description: string,
 *     availability_status: string,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int,
 *   }
 * >
 */
function get_occupancy_data_by_softrip_id( string $softrip_id = '', bool $force = false ): array {
	// Bail if empty.
	if ( empty( $softrip_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_softrip_id_' . $softrip_id;

	// If not direct, check the cache.
	if ( ! $force ) {
		// Check for cached version.
		$cached_data = wp_cache_get( $cache_key, CACHE_GROUP );

		// If cached data, return it.
		if ( is_array( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global wpdb.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the cabin data.
	$occupancies_data = $wpdb->get_results(
		$wpdb->prepare(
			'
			SELECT
				*
			FROM
				%i
			WHERE
				softrip_id = %s
			',
			[
				$table_name,
				$softrip_id,
			]
		),
		ARRAY_A
	);

	// Bail if not array.
	if ( ! is_array( $occupancies_data ) ) {
		return [];
	}

	// Format the rows data.
	$formatted_rows = format_rows_data_from_db( $occupancies_data );

	// Cache the data.
	wp_cache_set( $cache_key, $formatted_rows, CACHE_GROUP );

	// Return the cabin data.
	return $formatted_rows;
}

/**
 * Get cabin data by departure post ID.
 *
 * @param int  $departure_post_id The departure post ID.
 * @param bool $force Direct query.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_id: string,
 *     softrip_name: string,
 *     mask: string,
 *     departure_post_id: int,
 *     cabin_category_post_id: int,
 *     spaces_available: int,
 *     availability_description: string,
 *     availability_status: string,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int,
 *   }
 * >
 */
function get_occupancies_by_departure( int $departure_post_id = 0, bool $force = false ): array {
	// Bail if empty.
	if ( empty( $departure_post_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id;

	// If not direct, check the cache.
	if ( ! $force ) {
		// Check for cached version.
		$cached_data = wp_cache_get( $cache_key, CACHE_GROUP );

		// If cached data, return it.
		if ( is_array( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global wpdb.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the occupancies data.
	$occupancies_data = $wpdb->get_results(
		$wpdb->prepare(
			'
			SELECT
				*
			FROM
				%i
			WHERE
				departure_post_id = %d
			',
			[
				$table_name,
				$departure_post_id,
			]
		),
		ARRAY_A
	);

	// Bail if empty.
	if ( ! is_array( $occupancies_data ) ) {
		return [];
	}

	// Format the rows data.
	$formatted_rows = format_rows_data_from_db( $occupancies_data );

	// Cache the data.
	wp_cache_set( $cache_key, $formatted_rows, CACHE_GROUP );

	// Return the cabin data.
	return $formatted_rows;
}

/**
 * Get occupancy data by ID.
 *
 * @param int  $occupancy_id The occupancy ID.
 * @param bool $force Direct query.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_id: string,
 *     softrip_name: string,
 *     mask: string,
 *     departure_post_id: int,
 *     cabin_category_post_id: int,
 *     spaces_available: int,
 *     availability_description: string,
 *     availability_status: string,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int,
 *   }
 * >
 */
function get_occupancy_data_by_id( int $occupancy_id = 0, bool $force = false ): array {
	// Bail if empty.
	if ( empty( $occupancy_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id;

	// If not direct, check the cache.
	if ( ! $force ) {
		// Check for cached version.
		$cached_data = wp_cache_get( $cache_key, CACHE_GROUP );

		// If cached data, return it.
		if ( is_array( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global wpdb.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the cabin data.
	$occupancies_data = $wpdb->get_results(
		$wpdb->prepare(
			'
			SELECT
				*
			FROM
				%i
			WHERE
				id = %d
			',
			[
				$table_name,
				$occupancy_id,
			]
		),
		ARRAY_A
	);

	// Bail if not array.
	if ( ! is_array( $occupancies_data ) ) {
		return [];
	}

	// Format the rows data.
	$formatted_rows = format_rows_data_from_db( $occupancies_data );

	// Cache the data.
	wp_cache_set( $cache_key, $formatted_rows, CACHE_GROUP );

	// Return the occupancies data.
	return $formatted_rows;
}

/**
 * Get lowest price for a departure.
 *
 * @param int    $post_id Departure post ID.
 * @param string $currency Currency code.
 *
 * @return array{
 *  original: int,
 *  discounted: int,
 * }
 */
function get_lowest_price( int $post_id = 0, string $currency = USD_CURRENCY ): array {
	// Upper case currency.
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

	// Get all occupancies by current departure.
	$occupancies = get_occupancies_by_departure( $post_id );

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Construct the price per person key.
		$price_per_person_key = 'price_per_person_' . strtolower( $currency );

		// Validate the price per person.
		if ( ! is_array( $occupancy ) || empty( $occupancy[ $price_per_person_key ] ) || empty( $occupancy['id'] ) || empty( $occupancy['availability_status'] ) ) {
			continue;
		}

		// Get lowest price for occupancy promotions.
		$promotion_lowest_price = get_occupancy_promotion_lowest_price( $occupancy['id'], $currency );

		// Set original price if promotion price is empty.
		if ( empty( $promotion_lowest_price ) ) {
			$promotion_lowest_price = absint( $occupancy[ $price_per_person_key ] );
		}

		/**
		 * If the promotion price is less than the current lowest price, update the discounted as well as the original price.
		 * For example, if the lowest promotion price is $100 and the corresponding original price is $200, the discounted price will be $100 and the original price will be $200.
		 * Or if the promotion price is equal to the current lowest price, but the original price is less than the current original price, update the original price.
		 */
		if (
			empty( $lowest_price['discounted'] ) ||
			( ! empty( $promotion_lowest_price ) &&
				( $promotion_lowest_price < $lowest_price['discounted'] ||
				( $promotion_lowest_price === $lowest_price['discounted'] && absint( $occupancy[ $price_per_person_key ] < $lowest_price['original'] ) )
				)
			)
		) {
			$lowest_price['discounted'] = $promotion_lowest_price;
			$lowest_price['original']   = absint( $occupancy[ $price_per_person_key ] );
		}
	}

	// Add mandatory transfer price and supplemental price.
	$lowest_price = add_supplemental_and_mandatory_price( $lowest_price, $post_id, $currency );

	// Return the lowest price.
	return $lowest_price;
}

/**
 * Clear occupancy data based on departure post id.
 *
 * @param int $departure_post_id The departure post ID.
 *
 * @return boolean
 */
function clear_occupancies_by_departure( int $departure_post_id = 0 ): bool {
	// Bail if empty.
	if ( empty( $departure_post_id ) ) {
		return false;
	}

	// Get all occupancies by departure.
	$occupancies = get_occupancies_by_departure( $departure_post_id, true );

	// Bail if empty.
	if ( empty( $occupancies ) || ! is_array( $occupancies ) ) {
		return false;
	}

	// Flag for if all occupancies are deleted.
	$all_deleted = true;

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Bail if not array or empty.
		if ( ! is_array( $occupancy ) || empty( $occupancy['id'] ) || empty( $occupancy['softrip_id'] ) || empty( $occupancy['cabin_category_post_id'] ) ) {
			continue;
		}

		// Get the occupancy ID.
		$occupancy_id = absint( $occupancy['id'] );

		// Delete the occupancy promotions.
		$is_deleted = delete_occupancy_promotions_by_occupancy_id( $occupancy['id'] );

		// Skip if not deleted.
		if ( ! $is_deleted ) {
			// Set flag to false.
			$all_deleted = false;

			// Continue to next occupancy.
			continue;
		}

		// Delete the occupancy.
		delete_occupancy_by_id( $occupancy_id );

		// Bust cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $occupancy['cabin_category_post_id'] . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_cabin_category_post_id_' . $occupancy['cabin_category_post_id'], CACHE_GROUP );
	}

	// Return failure if not all deleted.
	if ( ! $all_deleted ) {
		return false;
	}

	// Bust caches.
	wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
	wp_cache_delete( CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id, CACHE_GROUP );

	// Return success.
	return true;
}

/**
 * Delete occupancy by occupancy id.
 *
 * @param int $occupancy_id The occupancy ID.
 *
 * @return boolean
 */
function delete_occupancy_by_id( int $occupancy_id = 0 ): bool {
	// Bail if empty.
	if ( empty( $occupancy_id ) ) {
		return false;
	}

	// Get softrip id.
	$occupancy_data = get_occupancy_data_by_id( $occupancy_id, true );

	// Bail if empty.
	if ( empty( $occupancy_data ) || ! is_array( $occupancy_data ) ) {
		return false;
	}

	// First item.
	$occupancy = $occupancy_data[0];

	// Bail if empty.
	if ( ! is_array( $occupancy ) || empty( $occupancy['softrip_id'] ) ) {
		return false;
	}

	// Get the Softrip ID.
	$softrip_id = $occupancy['softrip_id'];

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Delete the occupancy.
	$deleted = $wpdb->delete(
		$table_name,
		[ 'id' => $occupancy_id ]
	);

	// Return failure if not deleted.
	if ( empty( $deleted ) ) {
		return false;
	}

	// Bust caches.
	wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_id_' . $softrip_id, CACHE_GROUP );
	wp_cache_delete( CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id, CACHE_GROUP );

	// Return success.
	return true;
}

/**
 * Format occupancy row data from database.
 *
 * @param string[] $occupancy_data Occupancy data from database.
 *
 * @return array{}|array{
 *   id: int,
 *   softrip_id: string,
 *   softrip_name: string,
 *   mask: string,
 *   departure_post_id: int,
 *   cabin_category_post_id: int,
 *   spaces_available: int,
 *   availability_description: string,
 *   availability_status: string,
 *   price_per_person_usd: int,
 *   price_per_person_cad: int,
 *   price_per_person_aud: int,
 *   price_per_person_gbp: int,
 *   price_per_person_eur: int,
 * }
 */
function format_row_data_from_db( array $occupancy_data = [] ): array {
	// Bail if empty.
	if ( empty( $occupancy_data ) || ! is_array( $occupancy_data ) ) {
		return [];
	}

	// Required columns.
	$required_columns = [
		'id',
		'softrip_id',
		'softrip_name',
		'mask',
		'departure_post_id',
		'cabin_category_post_id',
		'availability_description',
		'availability_status',
	];

	// Check if required columns are present.
	foreach ( $required_columns as $column ) {
		if ( empty( $occupancy_data[ $column ] ) ) {
			return [];
		}
	}

	// Initialize the formatted data.
	$formatted_data = [
		'id'                       => absint( $occupancy_data['id'] ),
		'softrip_id'               => sanitize_text_field( $occupancy_data['softrip_id'] ),
		'softrip_name'             => sanitize_text_field( $occupancy_data['softrip_name'] ),
		'mask'                     => sanitize_text_field( $occupancy_data['mask'] ),
		'departure_post_id'        => absint( $occupancy_data['departure_post_id'] ),
		'cabin_category_post_id'   => absint( $occupancy_data['cabin_category_post_id'] ),
		'spaces_available'         => absint( $occupancy_data['spaces_available'] ?? 0 ),
		'availability_description' => sanitize_text_field( $occupancy_data['availability_description'] ),
		'availability_status'      => sanitize_text_field( $occupancy_data['availability_status'] ),
		'price_per_person_usd'     => absint( $occupancy_data['price_per_person_usd'] ?? 0 ),
		'price_per_person_cad'     => absint( $occupancy_data['price_per_person_cad'] ?? 0 ),
		'price_per_person_aud'     => absint( $occupancy_data['price_per_person_aud'] ?? 0 ),
		'price_per_person_gbp'     => absint( $occupancy_data['price_per_person_gbp'] ?? 0 ),
		'price_per_person_eur'     => absint( $occupancy_data['price_per_person_eur'] ?? 0 ),
	];

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Format rows data from database.
 *
 * @param array<int, string[]> $rows_data The rows data.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_id: string,
 *     softrip_name: string,
 *     mask: string,
 *     departure_post_id: int,
 *     cabin_category_post_id: int,
 *     spaces_available: int,
 *     availability_description: string,
 *     availability_status: string,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int,
 *   }
 * >
 */
function format_rows_data_from_db( array $rows_data = [] ): array {
	// Bail if empty.
	if ( empty( $rows_data ) || ! is_array( $rows_data ) ) {
		return [];
	}

	// Initialize the formatted rows.
	$formatted_rows = [];

	// Loop through each row.
	foreach ( $rows_data as $row_data ) {
		// Format the row data.
		$formatted_row = format_row_data_from_db( $row_data );

		// Bail if empty.
		if ( empty( $formatted_row ) ) {
			continue;
		}

		// Add the formatted row to the rows.
		$formatted_rows[] = $formatted_row;
	}

	// Return the formatted rows.
	return $formatted_rows;
}

/**
 * Get cabin category post ids by departure post id from occupancy table.
 *
 * @param int  $departure_post_id The departure post ID.
 * @param bool $force             Whether to bypass cache.
 *
 * @return int[]
 */
function get_cabin_category_post_ids_by_departure( int $departure_post_id = 0, bool $force = false ): array {
	// Bail if empty.
	if ( empty( $departure_post_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id;

	// Check the cache.
	if ( ! $force ) {
		// Get the cached data.
		$cached_data = wp_cache_get( $cache_key, CACHE_GROUP );

		// If cached data, return it.
		if ( is_array( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the cabin category post IDs.
	$cabin_category_post_ids = $wpdb->get_col(
		$wpdb->prepare(
			'
			SELECT
				cabin_category_post_id
			FROM
				%i
			WHERE
				departure_post_id = %d
			GROUP BY
				cabin_category_post_id
			',
			[
				$table_name,
				$departure_post_id,
			]
		)
	);

	// Convert to integers.
	$ids = array_map( 'absint', $cabin_category_post_ids );

	// Cache the data.
	wp_cache_set( $cache_key, $ids, CACHE_GROUP );

	// Return the cabin category post IDs.
	return $ids;
}

/**
 * Get lowest price by cabin category post id.
 *
 * @param int    $cabin_category_post_id The cabin category post ID.
 * @param int    $departure_post_id      The departure post ID.
 * @param string $currency               Currency code.
 *
 * @return array{
 *   original: int,
 *   discounted: int,
 * }
 */
function get_lowest_price_by_cabin_category_and_departure( int $cabin_category_post_id = 0, int $departure_post_id = 0, string $currency = USD_CURRENCY ): array {
	// Upper case currency.
	$currency = strtoupper( $currency );

	// Setup default return values.
	$lowest_price = [
		'original'   => 0,
		'discounted' => 0,
	];

	// Return default values if no post ID.
	if ( empty( $cabin_category_post_id ) || ! in_array( $currency, get_currencies(), true ) ) {
		return $lowest_price;
	}

	// Get all occupancies by cabin category.
	$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Construct the price per person key.
		$price_per_person_key = 'price_per_person_' . strtolower( $currency );

		// Validate the price per person.
		if ( ! is_array( $occupancy ) || empty( $occupancy[ $price_per_person_key ] ) || empty( $occupancy['id'] ) || empty( $occupancy['availability_status'] ) ) {
			continue;
		}

		// Get lowest price for occupancy promotions.
		$promotion_lowest_price = get_occupancy_promotion_lowest_price( $occupancy['id'], $currency );

		// If promotion price is empty, set it to the original price.
		if ( empty( $promotion_lowest_price ) ) {
			$promotion_lowest_price = absint( $occupancy[ $price_per_person_key ] );
		}

		/**
		 * If the promotion price is less than the current lowest price, update the discounted as well as the original price.
		 * For example, if the lowest promotion price is $100 and the corresponding original price is $200, the discounted price will be $100 and the original price will be $200.
		 * Or if the promotion price is equal to the current lowest price, but the original price is less than the current original price, update the original price.
		 */
		if (
			empty( $lowest_price['discounted'] ) ||
			( ! empty( $promotion_lowest_price ) &&
				( $promotion_lowest_price < $lowest_price['discounted'] ||
				( $promotion_lowest_price === $lowest_price['discounted'] && absint( $occupancy[ $price_per_person_key ] < $lowest_price['original'] ) )
				)
			)
		) {
			$lowest_price['discounted'] = $promotion_lowest_price;
			$lowest_price['original']   = absint( $occupancy[ $price_per_person_key ] );
		}
	}

	// Bail if empty lowest price.
	if ( empty( $lowest_price['original'] ) ) {
		return $lowest_price;
	}

	// Add mandatory transfer price and supplemental price.
	$lowest_price = add_supplemental_and_mandatory_price( $lowest_price, $departure_post_id, $currency );

	// Return the lowest price.
	return $lowest_price;
}

/**
 * Get lowest price by cabin category post id, departure post id and promotion code.
 *
 * @param int    $cabin_category_post_id The cabin category post ID.
 * @param int    $departure_post_id The departure post ID.
 * @param string $promotion_code The promotion code.
 * @param string $currency Currency code.
 *
 * @return int
 */
function get_lowest_price_by_cabin_category_and_departure_and_promotion_code( int $cabin_category_post_id = 0, int $departure_post_id = 0, string $promotion_code = '', string $currency = USD_CURRENCY ): int {
	// Upper case currency.
	$currency = strtoupper( $currency );

	// Setup default return values.
	$lowest_price = 0;

	// Return default values if no post ID.
	if ( empty( $cabin_category_post_id ) || empty( $departure_post_id ) || empty( $promotion_code ) || ! in_array( $currency, get_currencies(), true ) ) {
		return $lowest_price;
	}

	// Get all occupancies by cabin category for the current departure.
	$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Construct the price per person key.
		$price_per_person_key = 'price_per_person_' . strtolower( $currency );

		// Validate the price per person.
		if ( ! is_array( $occupancy ) || empty( $occupancy[ $price_per_person_key ] ) || empty( $occupancy['id'] ) || empty( $occupancy['availability_status'] ) ) {
			continue;
		}

		// Get lowest price for occupancy promotions.
		$promotion_lowest_price = get_occupancy_promotion_lowest_price( $occupancy['id'], $currency, $promotion_code );

		/**
		 * If the promotion price is less than the current lowest price, update the lowest price.
		 */
		if (
			empty( $lowest_price ) ||
			( ! empty( $promotion_lowest_price ) && ( $promotion_lowest_price < $lowest_price ) )
		) {
			$lowest_price = $promotion_lowest_price;
		}
	}

	// Bail if empty lowest price.
	if ( empty( $lowest_price ) ) {
		return $lowest_price;
	}

	// Add supplemental and mandatory price.
	$updated_price = add_supplemental_and_mandatory_price(
		[
			'discounted' => $lowest_price,
			'original'   => 0,
		],
		$departure_post_id,
		$currency
	);
	$lowest_price  = $updated_price['discounted'];

	// Return the lowest price.
	return $lowest_price;
}

/**
 * Get occupancies by cabin category post ID and departure post ID.
 *
 * @param int  $cabin_category_post_id The cabin category post ID.
 * @param int  $departure_post_id      The departure post ID.
 * @param bool $force                  Bypass cache.
 *
 * @return array{}|array<int,
 *  array{
 *    id: int,
 *    softrip_id: string,
 *    softrip_name: string,
 *    mask: string,
 *    departure_post_id: int,
 *    cabin_category_post_id: int,
 *    spaces_available: int,
 *    availability_description: string,
 *    availability_status: string,
 *    price_per_person_usd: int,
 *    price_per_person_cad: int,
 *    price_per_person_aud: int,
 *    price_per_person_gbp: int,
 *    price_per_person_eur: int,
 *  }
 * >
 */
function get_occupancies_by_cabin_category_and_departure( int $cabin_category_post_id = 0, int $departure_post_id = 0, bool $force = false ): array {
	// Bail if empty.
	if ( empty( $cabin_category_post_id ) || empty( $departure_post_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id;

	// Check the cache.
	if ( ! $force ) {
		// Check for cached version.
		$cached_data = wp_cache_get( $cache_key, CACHE_GROUP );

		// If cached data, return it.
		if ( is_array( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get occupancies data.
	$occupancies_data = $wpdb->get_results(
		$wpdb->prepare(
			'
			SELECT
				*
			FROM
				%i
			WHERE
				cabin_category_post_id = %d
			AND
				departure_post_id = %d
			',
			[
				$table_name,
				$cabin_category_post_id,
				$departure_post_id,
			]
		),
		ARRAY_A
	);

	// Bail if not array.
	if ( ! is_array( $occupancies_data ) ) {
		return [];
	}

	// Format the rows data.
	$formatted_rows = format_rows_data_from_db( $occupancies_data );

	// Cache the data.
	wp_cache_set( $cache_key, $formatted_rows, CACHE_GROUP );

	// Return the cabin data.
	return $formatted_rows;
}

/**
 * Get description and pax count by mask.
 *
 * @param string $mask The mask.
 *
 * @return array{
 *  description: string,
 *  pax_count: int,
 * }
 */
function get_description_and_pax_count_by_mask( string $mask = '' ): array {
	// Setup default return values.
	$description_and_pax_count = [
		'description' => '',
		'pax_count'   => 0,
	];

	// Bail if empty.
	if ( empty( $mask ) ) {
		return $description_and_pax_count;
	}

	// The mask mapping.
	$mask_mapping = get_masks_mapping();

	// Return the description and pax count.
	return $mask_mapping[ $mask ] ?? $description_and_pax_count;
}

/**
 * Add supplemental and mandatory price to the lowest price.
 *
 * @param array{discounted: int, original: int} $lowest_price The lowest price.
 * @param int                                   $departure_post_id The departure post ID.
 * @param string                                $currency The currency code.
 *
 * @return array{
 *   discounted: int,
 *   original: int,
 * }
 */
function add_supplemental_and_mandatory_price( array $lowest_price = [ 'discounted' => 0, 'original' => 0 ], int $departure_post_id = 0, string $currency = USD_CURRENCY ): array { // phpcs:ignore WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	// Setup default return values.
	$lowest_price_with_supplemental = [
		'discounted' => 0,
		'original'   => 0,
	];

	// Bail if empty.
	if ( ! is_array( $lowest_price ) || empty( $departure_post_id ) || ! in_array( $currency, get_currencies(), true ) ) {
		return $lowest_price_with_supplemental;
	}

	// Upper case currency.
	$currency = strtoupper( $currency );

	// Get itinerary post ID.
	$itinerary_post_id = absint( get_post_meta( $departure_post_id, 'itinerary', true ) );

	// Initialize supplemental and mandatory price.
	$supplemental_price = 0;
	$mandatory_price    = 0;

	// Get supplemental price.
	if ( ! empty( $itinerary_post_id ) ) {
		$supplemental_price = get_supplemental_price( $itinerary_post_id, $currency );
		$mandatory_price    = get_mandatory_transfer_price( $itinerary_post_id, $currency );
	}

	// Add supplemental and mandatory price to the lowest price.
	$lowest_price_with_supplemental['discounted'] = $lowest_price['discounted'] + $supplemental_price + $mandatory_price;
	$lowest_price_with_supplemental['original']   = $lowest_price['original'] + $supplemental_price + $mandatory_price;

	// Return the lowest price.
	return $lowest_price_with_supplemental;
}

/**
 * Get masks mapping.
 *
 * @return array{
 *   A: array{
 *        description: string,
 *        pax_count: int,
 *   },
 *   AA: array{
 *         description: string,
 *         pax_count: int,
 *   },
 *   SAA: array{
 *          description: string,
 *          pax_count: int,
 *   },
 *   SMAA: array{
 *           description: string,
 *           pax_count: int,
 *   },
 *   SFAA: array{
 *           description: string,
 *           pax_count: int,
 *   },
 *   AAA: array{
 *          description: string,
 *          pax_count: int,
 *   },
 *   SAAA: array{
 *           description: string,
 *           pax_count: int,
 *   },
 *   SMAAA: array{
 *            description: string,
 *            pax_count: int,
 *   },
 *   SFAAA: array{
 *            description: string,
 *            pax_count: int,
 *   },
 *   AAAA: array{
 *           description: string,
 *           pax_count: int,
 *   },
 * }
 */
function get_masks_mapping(): array {
	// The mask mapping.
	$mask_mapping = [
		'A'     => [
			'description' => 'Single Room',
			'pax_count'   => 1,
		],
		'AA'    => [
			'description' => 'Double Room',
			'pax_count'   => 2,
		],
		'SAA'   => [
			'description' => 'Double Room Shared',
			'pax_count'   => 1,
		],
		'SMAA'  => [
			'description' => 'Double Room Shared (Male)',
			'pax_count'   => 1,
		],
		'SFAA'  => [
			'description' => 'Double Room Shared (Female)',
			'pax_count'   => 1,
		],
		'AAA'   => [
			'description' => 'Triple Room',
			'pax_count'   => 3,
		],
		'SAAA'  => [
			'description' => 'Triple Room Shared',
			'pax_count'   => 1,
		],
		'SMAAA' => [
			'description' => 'Triple Room Shared (Male)',
			'pax_count'   => 1,
		],
		'SFAAA' => [
			'description' => 'Triple Room Shared (Female)',
			'pax_count'   => 1,
		],
		'AAAA'  => [
			'description' => 'Quad Room',
			'pax_count'   => 4,
		],
	];

	// Return the mask mapping.
	return $mask_mapping;
}

/**
 * Check if occupancy is on sale(open) by sale status.
 *
 * @param string $status Sale status.
 *
 * @return bool
 */
function is_occupancy_on_sale( string $status = '' ): bool {
	// Check status.
	switch ( $status ) {
		// Open.
		case 'O':
		case 'ON':
		case 'W':
			return true;

		// Default.
		default:
			return false;
	}
}

/**
 * Is occupancy available.
 *
 * @param string $sale_status Sale status.
 *
 * @return bool
 */
function is_occupancy_available( string $sale_status = '' ): bool {
	/**
	 * Sale Status mapping.
	 *
	 * O - Open -> Available
	 * ON - Open -> Available
	 * S - Sold Out -> Available
	 * C - Closed -> Unavailable
	 * N - No Display -> Unavailable
	 * NO - No Display -> Unavailable
	 * I - Internal -> Unavailable
	 * W - Waitlisted -> Available
	 */

	// Check status.
	switch ( $sale_status ) {
		// Open.
		case 'O':
		case 'ON':
		case 'S':
		case 'W':
			return true;

		// Default.
		default:
			return false;
	}
}

/**
 * Get departures by cabin category post id.
 *
 * @param int  $cabin_category_post_id The cabin category post ID.
 * @param bool $force                  Bypass cache.
 *
 * @return int[]
 */
function get_departures_by_cabin_category_id( int $cabin_category_post_id = 0, bool $force = false ): array {
	// Bail if empty.
	if ( empty( $cabin_category_post_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_departure_cabin_category_post_id_' . $cabin_category_post_id;

	// Check the cache.
	if ( ! $force ) {
		// Check for cached version.
		$cached_data = wp_cache_get( $cache_key, CACHE_GROUP );

		// If cached data, return it.
		if ( is_array( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get departures ids.
	$departure_ids = $wpdb->get_col(
		$wpdb->prepare(
			'
			SELECT
				departure_post_id
			FROM
				%i
			WHERE
				cabin_category_post_id = %d
			GROUP BY
				departure_post_id
			',
			[
				$table_name,
				$cabin_category_post_id,
			]
		)
	);

	// Convert to integers.
	$ids = array_map( 'absint', $departure_ids );

	// Cache the data.
	wp_cache_set( $cache_key, $ids, CACHE_GROUP );

	// Return the departure post IDs.
	return $ids;
}
