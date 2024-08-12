<?php
/**
 * Namespace file for occupancies data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Occupancies;

use WP_Query;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_occupancy_id;
use function Quark\Softrip\OccupancyPromotions\get_lowest_price as get_occupancy_promotion_lowest_price;
use function Quark\Softrip\OccupancyPromotions\update_occupancy_promotions;
use function Quark\Softrip\add_prefix_to_table_name;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Core\CURRENCIES;

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
	if ( empty( $raw_cabins_data ) || ! is_array( $raw_cabins_data ) || empty( $departure_post_id ) ) {
		return false;
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

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
				$wpdb->update(
					$table_name,
					$formatted_data,
					[ 'id' => $existing_cabin_data['id'] ]
				);

				// Set the updated ID.
				$updated_id = $existing_cabin_data['id'];
			} else {
				// Insert the cabin.
				$wpdb->insert(
					$table_name,
					$formatted_data
				);

				// Set the updated ID.
				$updated_id = $wpdb->insert_id;
			}

			// Set occupancy promotions data.
			if ( ! empty( $updated_id ) && ! empty( $raw_cabin_occupancy_data['prices'] ) && is_array( $raw_cabin_occupancy_data['prices'] ) ) {
				// Update the occupancy promotions.
				update_occupancy_promotions( array_values( $raw_cabin_occupancy_data['prices'] ), $updated_id );

				// Bust caches.
				wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_id_' . $formatted_data['softrip_id'], CACHE_GROUP );
				wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
			}
		}
	}

	// Return success.
	return true;
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
		'id'                      => '',
		'name'                    => '',
		'mask'                    => '',
		'availabilityStatus'      => '',
		'availabilityDescription' => '',
		'spacesAvailable'         => 0,
		'prices'                  => [],
	];

	// Apply defaults.
	$raw_occupancy_data = wp_parse_args( $raw_occupancy_data, $default );

	// Validate for empty values.
	if (
		empty( $raw_occupancy_data['id'] ) ||
		empty( $raw_occupancy_data['name'] ) ||
		empty( $raw_occupancy_data['mask'] ) ||
		empty( $raw_occupancy_data['availabilityStatus'] ) ||
		empty( $raw_occupancy_data['availabilityDescription'] ) ||
		empty( $raw_occupancy_data['prices'] )
	) {
		return [];
	}

	// Initialize the formatted data.
	$formatted_data = [
		'softrip_id'               => strval( $raw_occupancy_data['id'] ),
		'softrip_name'             => strval( $raw_occupancy_data['name'] ),
		'mask'                     => strval( $raw_occupancy_data['mask'] ),
		'departure_post_id'        => absint( $departure_post_id ),
		'cabin_category_post_id'   => absint( $cabin_category_post_id ),
		'spaces_available'         => absint( $raw_occupancy_data['spacesAvailable'] ),
		'availability_description' => strval( $raw_occupancy_data['availabilityDescription'] ),
		'availability_status'      => strval( $raw_occupancy_data['availabilityStatus'] ),
		'price_per_person_usd'     => 0,
		'price_per_person_cad'     => 0,
		'price_per_person_aud'     => 0,
		'price_per_person_gbp'     => 0,
		'price_per_person_eur'     => 0,
	];

	// Loop through the currencies.
	foreach ( CURRENCIES as $currency ) {
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
			'post_type'      => CABIN_CATEGORY_POST_TYPE,
			'fields'         => 'ids',
			'posts_per_page' => 1,
			'meta_query'     => [
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
 * @return mixed[][]
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
		if ( is_array( $cached_data ) && ! empty( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global wpdb.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the cabin data.
	$cabin_data = $wpdb->get_results(
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

	// Cache the data.
	wp_cache_set( $cache_key, $cabin_data, CACHE_GROUP );

	// Return the cabin data.
	return $cabin_data;
}

/**
 * Get cabin data by departure post ID.
 *
 * @param int  $departure_post_id The departure post ID.
 * @param bool $force Direct query.
 *
 * @return mixed[][]
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
		if ( is_array( $cached_data ) && ! empty( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global wpdb.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the cabin data.
	$cabin_data = $wpdb->get_results(
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

	// Cache the data.
	wp_cache_set( $cache_key, $cabin_data, CACHE_GROUP );

	// Return the cabin data.
	return $cabin_data;
}

/**
 * Get occupancy data by ID.
 *
 * @param int  $occupancy_id The occupancy ID.
 * @param bool $force Direct query.
 *
 * @return mixed[]
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
		if ( is_array( $cached_data ) && ! empty( $cached_data ) ) {
			return $cached_data;
		}
	}

	// Get the global wpdb.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the cabin data.
	$cabin_data = $wpdb->get_results(
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

	// Cache the data.
	wp_cache_set( $cache_key, $cabin_data, CACHE_GROUP );

	// Return the cabin data.
	return $cabin_data;
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
function get_lowest_price( int $post_id = 0, string $currency = 'USD' ): array {
	// Upper case currency.
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

	// Get all occupancies by current departure.
	$occupancies = get_occupancies_by_departure( $post_id );

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Construct the price per person key.
		$price_per_person_key = 'price_per_person_' . strtolower( $currency );

		// Validate the price per person.
		if ( ! is_array( $occupancy ) || empty( $occupancy[ $price_per_person_key ] ) || empty( $occupancy['id'] ) ) {
			continue;
		}

		// Get lowest price for occupancy promotions.
		$promotion_lowest_price = get_occupancy_promotion_lowest_price( $occupancy['id'], $currency );

		/**
		 * If the promotion price is less than the current lowest price, update the discounted as well as the original price.
		 * For example, if the lowest promotion price is $100 and the corresponding original price is $200, the discounted price will be $100 and the original price will be $200.
		 */
		if ( empty( $lowest_price['discounted'] ) || $promotion_lowest_price < $lowest_price['discounted'] ) {
			$lowest_price['discounted'] = $promotion_lowest_price;
			$lowest_price['original']   = absint( $occupancy[ $price_per_person_key ] );
		}
	}

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
	$occupancies = get_occupancies_by_departure( $departure_post_id );

	// Flag for if all occupancies are deleted.
	$all_deleted = true;

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Bail if not array or empty.
		if ( ! is_array( $occupancy ) || empty( $occupancy ) || empty( $occupancy['id'] ) || empty( $occupancy['softrip_id'] ) ) {
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
	}

	// Return failure if not all deleted.
	if ( ! $all_deleted ) {
		return false;
	}

	// Bust caches.
	wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );

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
	$occupancy_data = get_occupancy_data_by_id( $occupancy_id );

	// Bail if empty.
	if ( empty( $occupancy_data ) || ! is_array( $occupancy_data ) ) {
		return false;
	}

	// First item.
	$occupancy = $occupancy_data[0];

	// Bail if empty.
	if ( empty( $occupancy ) || ! is_array( $occupancy ) || empty( $occupancy['softrip_id'] ) ) {
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

	// Return success.
	return true;
}
