<?php
/**
 * Namespace file for promotions data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Promotions;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\prefix_table_name;

const CACHE_KEY_PREFIX = 'quark_softrip_promotion';
const CACHE_GROUP      = 'quark_softrip_promotions';

/**
 * Get table name.
 *
 * @return string
 */
function get_table_name(): string {
	// Return table name.
	return prefix_table_name( 'promotions' );
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

	// Build the SQL query.
	$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			code VARCHAR(255) NOT NULL UNIQUE,
			start_date DATETIME NOT NULL,
			end_date DATETIME NOT NULL,
			description VARCHAR(255) NOT NULL,
			discount_type VARCHAR(255) NOT NULL,
			discount_value VARCHAR(255) NOT NULL,
			is_pif TINYINT(1) NOT NULL
		) $engine_collate";

	// Return the SQL.
	return $sql;
}

/**
 * Update promotions data.
 *
 * @param mixed[] $raw_promotions_data Raw promotions data from Softrip.
 *
 * @return boolean
 */
function update_promotions( array $raw_promotions_data = [] ): bool {
	// Bail out if no data.
	if ( empty( $raw_promotions_data ) || ! is_array( $raw_promotions_data ) ) {
		return false;
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Loop through each raw promotion data.
	foreach ( $raw_promotions_data as $raw_promotion_data ) {
		// Validate the raw promotion data.
		if ( ! is_array( $raw_promotion_data ) ) {
			continue;
		}

		// Format the promotion data.
		$formatted_data = format_data( $raw_promotion_data );

		// Skip if empty.
		if ( empty( $formatted_data ) ) {
			continue;
		}

		// Get existing promotion data by the code.
		$existing_promotions_data = get_promotions_by_code( $formatted_data['code'] );

		// Get the first item.
		$existing_promotion_data = ! empty( $existing_promotions_data ) ? $existing_promotions_data[0] : [];

		// Initialize updated id.
		$updated_id = 0;

		// If the promotion exists, update it.
		if ( ! empty( $existing_promotion_data['id'] ) ) {
			// Update the promotion.
			$updated_id = $wpdb->update(
				$table_name,
				$formatted_data,
				[ 'id' => $existing_promotion_data['id'] ]
			);
		} else {
			// Insert the promotion.
			$wpdb->insert(
				$table_name,
				$formatted_data
			);

			// Get the inserted ID.
			$updated_id = $wpdb->insert_id;
		}

		// Skip if no updated ID.
		if ( empty( $updated_id ) ) {
			continue;
		}

		// Bust the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_' . $formatted_data['code'], CACHE_GROUP );
	}

	// Return success.
	return true;
}

/**
 * Format the raw promotion data.
 *
 * @param mixed[] $raw_promotion_data Raw promotion data from Softrip.
 *
 * @return array{}|array{
 *   end_date: string,
 *   start_date: string,
 *   description: string,
 *   discount_type: string,
 *   discount_value: string,
 *   code: string,
 *   is_pif: int,
 * }
 */
function format_data( array $raw_promotion_data = [] ): array {
	// Bail out if no data.
	if ( ! is_array( $raw_promotion_data ) || empty( $raw_promotion_data ) ) {
		return [];
	}

	// Setup the defaults.
	$default = [
		'endDate'       => '',
		'startDate'     => '',
		'description'   => '',
		'discountType'  => '',
		'discountValue' => '',
		'promotionCode' => '',
		'isPIF'         => 0,
	];

	// Apply the defaults.
	$raw_promotion_data = wp_parse_args( $raw_promotion_data, $default );

	// Validate the data.
	if (
		empty( $raw_promotion_data['endDate'] ) ||
		empty( $raw_promotion_data['startDate'] ) ||
		empty( $raw_promotion_data['description'] ) ||
		empty( $raw_promotion_data['discountType'] ) ||
		empty( $raw_promotion_data['discountValue'] ) ||
		empty( $raw_promotion_data['promotionCode'] )
	) {
		return [];
	}

	// Initialize the formatted data.
	$formatted_data = [
		'end_date'       => strval( $raw_promotion_data['endDate'] ),
		'start_date'     => strval( $raw_promotion_data['startDate'] ),
		'description'    => strval( $raw_promotion_data['description'] ),
		'discount_type'  => strval( $raw_promotion_data['discountType'] ),
		'discount_value' => strval( $raw_promotion_data['discountValue'] ),
		'code'           => strval( $raw_promotion_data['promotionCode'] ),
		'is_pif'         => absint( $raw_promotion_data['isPIF'] ),
	];

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Get promotion by code.
 *
 * @param string $code   The promotion code.
 * @param bool   $direct Whether to bypass the cache.
 *
 * @return mixed[][]
 */
function get_promotions_by_code( string $code = '', bool $direct = false ): array {
	// Bail out if no code.
	if ( empty( $code ) ) {
		return [];
	}

	// Get the cache key.
	$cache_key = CACHE_KEY_PREFIX . "_$code";

	// If not direct, check the cache.
	if ( empty( $direct ) ) {
		// Get from cache.
		$cached_value = wp_cache_get( $cache_key );

		// Check if we have the data.
		if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the promotion data.
	$promotion_data = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT
                *
            FROM
                %i
            WHERE
                code = %s
            ',
			[
				$table_name,
				$code,
			]
		),
		ARRAY_A
	);

	// Cache the value.
	wp_cache_set( $cache_key, $promotion_data, CACHE_GROUP );

	// Return the promotion data.
	return $promotion_data;
}

/**
 * Get promotions by promotion id.
 *
 * @param int  $promotion_id The promotion ID.
 * @param bool $direct       Whether to bypass the cache.
 *
 * @return mixed[][]
 */
function get_promotions_by_id( int $promotion_id = 0, bool $direct = false ): array {
	// Bail out if no ID.
	if ( empty( $promotion_id ) ) {
		return [];
	}

	// Get the cache key.
	$cache_key = CACHE_KEY_PREFIX . "_promotion_id_$promotion_id";

	// If not direct, check the cache.
	if ( empty( $direct ) ) {
		// Get from cache.
		$cached_value = wp_cache_get( $cache_key );

		// Check if we have the data.
		if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the promotion data.
	$promotion_data = $wpdb->get_results(
		$wpdb->prepare(
			'SELECT
				*
			FROM
				%i
			WHERE
				id = %d
			',
			[
				$table_name,
				$promotion_id,
			]
		),
		ARRAY_A
	);

	// Cache the value.
	wp_cache_set( $cache_key, $promotion_data, CACHE_GROUP );

	// Return the promotion data.
	return $promotion_data;
}
