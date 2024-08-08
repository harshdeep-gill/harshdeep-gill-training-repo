<?php
/**
 * Namespace file for cabins data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Cabins;

use WP_Query;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Core\CURRENCIES;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\prefix_table_name;

const CACHE_KEY_PREFIX = 'quark_softrip_cabin';
const CACHE_GROUP      = 'quark_softrip_cabins';

/**
 * Get table name.
 *
 * @return string
 */
function get_table_name(): string {
    // Return table name.
    return prefix_table_name( 'cabins' );
}

/**
 * Get table SQL.
 *
 * @return string
 */
function get_table_sql(): string {
    // Get table name.
    $table_name = get_table_name();

    // Get engine and collate.
    $engine_collate = get_engine_collate();

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
 * @param int $departure_post_id The departure post ID.
 *
 * @return boolean
 */
function update_cabins( array $raw_cabins_data = [], int $departure_post_id = 0 ): bool {
    // Bail if empty.
    if ( empty( $raw_cabins_data ) || ! is_array( $raw_cabins_data ) || empty( $departure_post_id ) ) {
        return false;
    }

    // Get the global $wpdb object.
    global $wpdb;

    // Get the table name.
    $table_name = get_table_name();

    foreach ( $raw_cabins_data as $raw_cabin_data ) {
        // Validate the raw cabin data.
        if ( ! is_array( $raw_cabin_data ) || empty( $raw_cabin_data ) || empty( $raw_cabin_data['code'] ) || empty( $raw_cabin_data['occupancies'] ) || ! is_array( $raw_cabin_data['occupancies'] ) ) {
            continue;
        }

        // Get cabin category post id by cabin code.
        $cabin_category_post_id = get_cabin_category_post_id( $raw_cabin_data['code'] );

        // Bail if no cabin category post ID.
        if ( empty( $cabin_category_post_id ) ) {
            continue;
        }

        // Iterate through the cabin occupancies.
        foreach ( $raw_cabin_data['occupancies'] as $raw_cabin_occupancy_data ) {
            // Format the data.
            $formatted_data = format_data( $raw_cabin_occupancy_data, $cabin_category_post_id, $departure_post_id );

            // Bail if empty.
            if ( empty( $formatted_data ) ) {
                continue;
            }

            // Get existing cabin data by the Softrip ID.
            $existing_cabins_data = get_cabin_data_by_softrip_id( $formatted_data['softrip_id'] );

            // Get the first item.
            $existing_cabin_data = ! empty( $existing_cabins_data ) ? $existing_cabins_data[0] : [];

            // If the cabin exists, update it.
            if ( ! empty( $existing_cabin_data['id'] ) ) {
                // Update the cabin.
                $wpdb->update(
                    $table_name,
                    $formatted_data,
                    [ 'id' => $existing_cabin_data['id'] ]
                );
            } else {
                // Insert the cabin.
                $wpdb->insert(
                    $table_name,
                    $formatted_data
                );
            }
        }
    }

    return true;
    
}

/**
 * Format occupancy data.
 *
 * @param mixed[] $raw_occupancy_data Raw occupancy data from Softrip.
 * @param int $cabin_category_post_id The cabin category post ID.
 * @param int $cabin_category_post_id The cabin category post ID.
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
 *    price_per_person_usd: float,
 *    price_per_person_cad: float,
 *    price_per_person_aud: float,
 *    price_per_person_gbp: float,
 *    price_per_person_eur: float,
 * }
 */
function format_data( array $raw_occupancy_data = [], int $cabin_category_post_id = 0, int $departure_post_id = 0 ): array {
    // Bail if empty.
    if ( empty( $raw_occupancy_data ) || ! is_array( $raw_occupancy_data ) || empty( $cabin_category_post_id ) || empty( $departure_post_id ) ) {
        return [];
    }

    // Setup the defaults.
    $default = [
        'id' => '',
        'name' => '',
        'mask' => '',
        'availabilityStatus' => '',
        'availabilityDescription' => '',
        'spacesAvailable' => 0,
        'price' => [],
    ];

    // Apply defaults.
    $raw_occupancy_data = wp_parse_args( $raw_occupancy_data, $default );

    // Initialize the formatted data.
    $formatted_data = [
        'softrip_id' => strval( $raw_occupancy_data['id'] ),
        'softrip_name' => strval( $raw_occupancy_data['name'] ),
        'mask' => strval( $raw_occupancy_data['mask'] ),
        'departure_post_id' => absint( $departure_post_id ),
        'cabin_category_post_id' => absint( $cabin_category_post_id ),
        'spaces_available' => absint( $raw_occupancy_data['spacesAvailable'] ),
        'availability_description' => strval( $raw_occupancy_data['availabilityDescription'] ),
        'availability_status' => strval( $raw_occupancy_data['availabilityStatus'] ),
        'price_per_person_usd' => 0,
        'price_per_person_cad' => 0,
        'price_per_person_aud' => 0,
        'price_per_person_gbp' => 0,
        'price_per_person_eur' => 0,
    ];

    // Check if the price exists.
    if ( ! empty( $raw_occupancy_data['prices'] ) ) {
        // Loop through the currencies.
        foreach ( CURRENCIES as $currency ) {
            // Check if the currency is set and price per person exists.
			if ( empty( $raw_occupancy_data['prices'][ $currency ] ) || ! is_array( $raw_occupancy_data['prices'][ $currency ] ) || empty( $raw_occupancy_data['prices'][ $currency ]['pricePerPerson'] ) ) {
				continue;
			}

            // Set the price per person.
            $formatted_data[ 'price_per_person_' . strtolower( $currency ) ] = doubleval( $raw_occupancy_data['prices'][ $currency ]['pricePerPerson'] );
        }
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
function get_cabin_category_post_id( string $cabin_code = '' ): int {
    // Bail if empty.
    if (empty($cabin_code)) {
        return 0;
    }

    // Run the query.
    $query = new WP_Query( [
        'post_type' => CABIN_CATEGORY_POST_TYPE,
        'fields' => 'ids',
        'posts_per_page' => 1,
        'meta_query' => [
            [
                'key' => 'cabin_category_id',
                'value' => $cabin_code,
            ],
        ],
    ] );

    // Get the cabin category post ID.
    $cabin_category_post_id = ! empty( $query->posts ) ? absint( $query->posts[0] ) : 0;

    // Return the cabin category post ID.
    return $cabin_category_post_id;
}

/**
 * Get cabin data by Softrip ID.
 *
 * @param string $softrip_id The Softrip ID.
 *
 * @return mixed[][]
 */
function get_cabin_data_by_softrip_id( string $softrip_id = '', bool $direct = false ): array {
    // Bail if empty.
    if ( empty( $softrip_id ) ) {
        return [];
    }

    // Cache key.
    $cache_key = CACHE_KEY_PREFIX . '_data_' . $softrip_id;

    // If not direct, check the cache.
    if ( ! $direct ) {
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
