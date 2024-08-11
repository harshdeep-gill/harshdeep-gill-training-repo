<?php
/**
 * Namespace file for Cabin Occupancy Promotions
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\OccupancyPromotions;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\prefix_table_name;
use function Quark\Softrip\Promotions\get_promotions_by_code;

use const Quark\Core\CURRENCIES;

const CACHE_KEY_PREFIX = 'qrk_softrip_occupancy_promotion';
const CACHE_GROUP      = 'qrk_softrip_occupancy_promotions';

/**
 * Get table name.
 *
 * @return string
 */
function get_table_name(): string {
	// Return the table name.
	return prefix_table_name( 'occupancy_promotions' );
}

/**
 * Get table create SQL statement.
 *
 * @return string
 */
function get_table_sql(): string {
	// Get table name.
	$table_name = get_table_name();

	// Get engine and collate.
	$engine_collate = get_engine_collate();

	// SQL statement.
	$sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        occupancy_id BIGINT NOT NULL,
        promotion_id BIGINT NOT NULL,
        price_per_person_usd BIGINT NOT NULL,
        price_per_person_cad BIGINT NOT NULL,
        price_per_person_aud BIGINT NOT NULL,
        price_per_person_gbp BIGINT NOT NULL,
        price_per_person_eur BIGINT NOT NULL
    ) $engine_collate;";

	// Return the SQL statement.
	return $sql;
}

/**
 * Update the cabin occupancy promotions data.
 *
 * @param mixed[] $raw_occupancy_promotions The raw cabin occupancy promotions data.
 * @param int     $occupancy_id                 The occupancy ID.
 *
 * @return boolean
 */
function update_occupancy_promotions( array $raw_occupancy_promotions = [], int $occupancy_id = 0 ): bool {
	// Bail out if empty.
	if ( empty( $raw_occupancy_promotions ) || empty( $occupancy_id ) ) {
		return false;
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Initialize the formatted data.
	$promos_data = [];

	// Loop through the raw occupancy promotions.
	foreach ( $raw_occupancy_promotions as $raw_occupancy_promotion ) {
		// Skip if not array.
		if ( ! is_array( $raw_occupancy_promotion ) ) {
			continue;
		}

		// Setup defaults.
		$defaults = [
			'currencyCode'   => '',
			'pricePerPerson' => 0,
			'promos'         => [],
		];

		// Merge defaults with raw data.
		$raw_occupancy_promotion = wp_parse_args( $raw_occupancy_promotion, $defaults );

		// Continue out if no promos.
		if ( empty( $raw_occupancy_promotion['currencyCode'] ) || ! in_array( $raw_occupancy_promotion['currencyCode'], CURRENCIES, true ) || empty( $raw_occupancy_promotion['promos'] ) || ! is_array( $raw_occupancy_promotion['promos'] ) ) {
			continue;
		}

		// Loop through promos.
		foreach ( $raw_occupancy_promotion['promos'] as $promotion_code => $value ) {
			// Skip if empty.
			if ( empty( $promotion_code ) || ! is_array( $value ) || empty( $value ) || empty( $value['promoPricePerPerson'] ) ) {
				continue;
			}

			// Add to promos data.
			$promos_data[ $promotion_code ][ 'price_per_person_' . strtolower( $raw_occupancy_promotion['currencyCode'] ) ] = doubleval( $value['promoPricePerPerson'] );
		}
	}

	// Setup defaults.
	$defaults = [
		'occupancy_id'         => $occupancy_id,
		'promotion_id'         => 0,
		'price_per_person_usd' => 0,
		'price_per_person_cad' => 0,
		'price_per_person_aud' => 0,
		'price_per_person_gbp' => 0,
		'price_per_person_eur' => 0,
	];

	// Loop through the promos data.
	foreach ( $promos_data as $promo_code => $promo_data ) {
		// Merge defaults with promo data.
		$promo_data = wp_parse_args( $promo_data, $defaults );

		// Get existing promotions by code.
		$existing_promotions = get_promotions_by_code( $promo_code, true );

		// Get the first item.
		$existing_promotion = ! empty( $existing_promotions ) ? $existing_promotions[0] : [];

		// Skip if no promotion ID.
		if ( empty( $existing_promotion ) || ! is_array( $existing_promotion ) || empty( $existing_promotion['id'] ) ) {
			continue;
		}

		// Add promotion ID to promo data.
		$promo_data['promotion_id'] = $existing_promotion['id'];

		// Get the occupancy promotions by occupancy ID and promotion ID.
		$existing_occupancy_promotions = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, $existing_promotion['id'], true );

		// Get the first item.
		$existing_occupancy_promotion = ! empty( $existing_occupancy_promotions ) ? $existing_occupancy_promotions[0] : [];

		// Initialize updated ID.
		$updated_id = 0;

		// If the occupancy promotion exists, update it.
		if ( ! empty( $existing_occupancy_promotion ) && is_array( $existing_occupancy_promotion ) && ! empty( $existing_occupancy_promotion['id'] ) ) {
			// Update the occupancy promotion.
			$updated_id = $wpdb->update(
				$table_name,
				$promo_data,
				[ 'id' => $existing_occupancy_promotion['id'] ]
			);
		} else {
			// Insert the occupancy promotion.
			$wpdb->insert(
				$table_name,
				$promo_data
			);

			// Get the inserted ID.
			$updated_id = $wpdb->insert_id;
		}

		// Skip if no updated ID.
		if ( empty( $updated_id ) ) {
			continue;
		}

		// Bust caches.
		wp_cache_delete( CACHE_KEY_PREFIX . '_' . $occupancy_id . '_' . $existing_promotion['id'], CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_occupancy_' . $occupancy_id, CACHE_GROUP );
	}

	// Return success.
	return true;
}

/**
 * Get occupancy promotions by occupancy ID and promotion ID.
 *
 * @param int  $occupancy_id  The occupancy ID.
 * @param int  $promotion_id  The promotion ID.
 * @param bool $direct       Whether to bypass the cache.
 *
 * @return mixed[]
 */
function get_occupancy_promotions_by_occupancy_id_and_promotion_id( int $occupancy_id = 0, int $promotion_id = 0, bool $direct = false ): array {
	// Bail out if empty.
	if ( empty( $occupancy_id ) || empty( $promotion_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_' . $occupancy_id . '_' . $promotion_id;

	// If not direct, get from cache.
	if ( empty( $direct ) ) {
		// Check cache.
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Return cached value if exists.
		if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the SQL statement.
	$occupancy_promotions = $wpdb->get_results(
		$wpdb->prepare(
			'
            SELECT
                *
            FROM
                %i
            WHERE
                occupancy_id = %d
            AND
                promotion_id = %d
            ',
			[
				$table_name,
				$occupancy_id,
				$promotion_id,
			]
		),
		ARRAY_A
	);

	// Set cache.
	wp_cache_set( $cache_key, $occupancy_promotions, CACHE_GROUP );

	// Return the results.
	return $occupancy_promotions;
}

/**
 * Get occupancy promotions by occupancy ID.
 *
 * @param int  $occupancy_id  The occupancy ID.
 * @param bool $direct       Whether to bypass the cache.
 *
 * @return mixed[][]
 */
function get_occupancy_promotions_by_occupancy( int $occupancy_id = 0, bool $direct = false ): array {
	// Bail out if empty.
	if ( empty( $occupancy_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_occupancy_' . $occupancy_id;

	// If not direct, get from cache.
	if ( empty( $direct ) ) {
		// Check cache.
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Return cached value if exists.
		if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Prepare the SQL statement.
	$occupancy_promotions = $wpdb->get_results(
		$wpdb->prepare(
			'
            SELECT
                *
            FROM
                %i
            WHERE
                occupancy_id = %d
            ',
			[
				$table_name,
				$occupancy_id,
			]
		),
		ARRAY_A
	);

	// Set cache.
	wp_cache_set( $cache_key, $occupancy_promotions, CACHE_GROUP );

	// Return the results.
	return $occupancy_promotions;
}

/**
 * Get lowest price by occupancy.
 *
 * @param int    $occupancy_id The occupancy ID.
 * @param string $currency The currency code.
 *
 * @return int
 */
function get_lowest_price( int $occupancy_id = 0, string $currency = 'USD' ): int {
	// Uppercase the currency.
	$currency = strtoupper( $currency );

	// Setup default return value.
	$lowest_price = 0;

	// Bail out if empty or invalid currency.
	if ( empty( $occupancy_id ) || ! in_array( $currency, CURRENCIES, true ) ) {
		return $lowest_price;
	}

	// Get the occupancy promotions by occupancy ID.
	$occupancy_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id );

	// Loop through the occupancy promotions.
	foreach ( $occupancy_promotions as $occupancy_promotion ) {
		// Skip if empty.
		if ( empty( $occupancy_promotion ) || ! is_array( $occupancy_promotion ) ) {
			continue;
		}

		// Get the price per person key.
		$price_per_person_key = 'price_per_person_' . strtolower( $currency );

		// Validate the price per person.
		if ( empty( $occupancy_promotion[ $price_per_person_key ] ) ) {
			continue;
		}

		// Get the price per person.
		$price_per_person = absint( $occupancy_promotion[ $price_per_person_key ] );

		// Check if lowest is set and is lower than the previous price.
		if ( empty( $lowest_price ) || $lowest_price > $price_per_person ) {
			// Use the price as it's lower.
			$lowest_price = $price_per_person;
		}
	}

	// Return the lowest price.
	return $lowest_price;
}
