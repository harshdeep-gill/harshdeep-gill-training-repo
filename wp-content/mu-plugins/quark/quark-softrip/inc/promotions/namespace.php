<?php
/**
 * Namespace file for promotions data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Promotions;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\add_prefix_to_table_name;

const CACHE_KEY_PREFIX = 'quark_softrip_promotion';
const CACHE_GROUP      = 'quark_softrip_promotions';

/**
 * Get table name.
 *
 * @return string
 */
function get_table_name(): string {
	// Return table name.
	return add_prefix_to_table_name( 'promotions' );
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
			start_date VARCHAR(20) NOT NULL,
			end_date VARCHAR(20) NOT NULL,
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
 * @param int     $departure_post_id   The departure post ID.
 *
 * @return boolean
 */
function update_promotions( array $raw_promotions_data = [], int $departure_post_id = 0 ): bool {
	// Bail out if no data.
	if ( ! is_array( $raw_promotions_data ) || empty( $departure_post_id ) ) {
		return false;
	}

	// If empty promotions data, delete all promotions.
	if ( empty( $raw_promotions_data ) ) {
		// Delete from meta.
		delete_post_meta( $departure_post_id, 'promotion_codes' );

		// Return success.
		return true;
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Updated promotion codes.
	$updated_promotion_codes = [];

	// Initialize if any updated.
	$any_updated = false;

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
			$is_saved = $wpdb->update(
				$table_name,
				$formatted_data,
				[ 'id' => $existing_promotion_data['id'] ]
			);

			// Fire the action on update.
			if ( $is_saved > 0 ) {
				$any_updated = true;
			}

			// Get the updated ID.
			$updated_id = $is_saved ? $existing_promotion_data['id'] : 0;
		} else {
			// Insert the promotion.
			$wpdb->insert(
				$table_name,
				$formatted_data
			);

			// Get the inserted ID.
			$updated_id  = $wpdb->insert_id;
			$any_updated = true;
		}

		// Add the updated promotion code.
		$updated_promotion_codes[] = $formatted_data['code'];

		// Skip if no updated ID.
		if ( empty( $updated_id ) ) {
			continue;
		}

		// Bust the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_promotion_code_' . $formatted_data['code'], CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_promotion_id_' . $updated_id, CACHE_GROUP );
	}

	// Update promotion code on departure meta.
	$is_saved = update_post_meta( $departure_post_id, 'promotion_codes', $updated_promotion_codes );

	// If meta saved, set any updated to true.
	if ( ! empty( $is_saved ) ) {
		$any_updated = true;
	}

	// Return if any updated.
	return $any_updated;
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
 *   currency: string|null,
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
		'end_date'       => sanitize_text_field( strval( $raw_promotion_data['endDate'] ) ),
		'start_date'     => sanitize_text_field( strval( $raw_promotion_data['startDate'] ) ),
		'description'    => sanitize_text_field( strval( $raw_promotion_data['description'] ) ),
		'discount_type'  => sanitize_text_field( strval( $raw_promotion_data['discountType'] ) ),
		'discount_value' => sanitize_text_field( strval( $raw_promotion_data['discountValue'] ) ),
		'code'           => sanitize_text_field( strval( $raw_promotion_data['promotionCode'] ) ),
		'currency'       => ! empty( $raw_promotion_data['currency'] ) ? sanitize_text_field( strval( $raw_promotion_data['currency'] ) ) : null,
		'is_pif'         => absint( $raw_promotion_data['isPIF'] ),
	];

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Get promotion by code.
 *
 * @param string $code   The promotion code.
 * @param bool   $force Whether to bypass the cache.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     code: string,
 *     start_date: string,
 *     end_date: string,
 *     description: string,
 *     discount_type: string,
 *     discount_value: string,
 *     currency: string|null,
 *     is_pif: int,
 *   }
 * >
 */
function get_promotions_by_code( string $code = '', bool $force = false ): array {
	// Bail out if no code.
	if ( empty( $code ) ) {
		return [];
	}

	// Get the cache key.
	$cache_key = CACHE_KEY_PREFIX . "_promotion_code_$code";

	// If not direct, check the cache.
	if ( empty( $force ) ) {
		// Get from cache.
		$cached_value = wp_cache_get( $cache_key );

		// Check if we have the data.
		if ( is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get the global $wpdb object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the promotion data.
	$promotions_data = $wpdb->get_results(
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

	// Bail out if not an array.
	if ( ! is_array( $promotions_data ) ) {
		return [];
	}

	// Format the data.
	$formatted_promotions_data = format_rows_data_from_db( $promotions_data );

	// Cache the value.
	wp_cache_set( $cache_key, $formatted_promotions_data, CACHE_GROUP );

	// Return the promotion data.
	return $formatted_promotions_data;
}

/**
 * Format promotion row from database.
 *
 * @param string[] $row_data Row data from database.
 *
 * @return array{}|array{
 *   id: int,
 *   code: string,
 *   start_date: string,
 *   end_date: string,
 *   description: string,
 *   discount_type: string,
 *   discount_value: string,
 *   currency: string|null,
 *   is_pif: int,
 * }
 */
function format_row_data_from_db( array $row_data = [] ): array {
	// Bail out if no data.
	if ( empty( $row_data ) || ! is_array( $row_data ) ) {
		return [];
	}

	// Required fields.
	$required_fields = [
		'id',
		'code',
		'start_date',
		'end_date',
		'description',
		'discount_type',
		'discount_value',
		'currency',
		'is_pif',
	];

	// Check if required fields are present.
	foreach ( $required_fields as $required_field ) {
		if ( ! array_key_exists( $required_field, $row_data ) ) {
			return [];
		}
	}

	// Format the data.
	$formatted_data = [
		'id'             => absint( $row_data['id'] ),
		'code'           => sanitize_text_field( strval( $row_data['code'] ) ),
		'start_date'     => sanitize_text_field( strval( $row_data['start_date'] ) ),
		'end_date'       => sanitize_text_field( strval( $row_data['end_date'] ) ),
		'description'    => sanitize_text_field( strval( $row_data['description'] ) ),
		'discount_type'  => sanitize_text_field( strval( $row_data['discount_type'] ) ),
		'discount_value' => sanitize_text_field( strval( $row_data['discount_value'] ) ),
		'currency'       => ! empty( $row_data['currency'] ) ? sanitize_text_field( strval( $row_data['currency'] ) ) : null,
		'is_pif'         => absint( $row_data['is_pif'] ),
	];

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Format rows data from database.
 *
 * @param array<int, string[]> $rows_data Rows data from database.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     code: string,
 *     start_date: string,
 *     end_date: string,
 *     description: string,
 *     discount_type: string,
 *     discount_value: string,
 *     currency: string|null,
 *     is_pif: int,
 *   }
 * >
 */
function format_rows_data_from_db( array $rows_data = [] ): array {
	// Bail out if no data.
	if ( empty( $rows_data ) || ! is_array( $rows_data ) ) {
		return [];
	}

	// Initialize the formatted data.
	$formatted_data = [];

	// Loop through each row data.
	foreach ( $rows_data as $row_data ) {
		// Format the row data.
		$formatted_row_data = format_row_data_from_db( $row_data );

		// Skip if empty.
		if ( empty( $formatted_row_data ) ) {
			continue;
		}

		// Add to the formatted data.
		$formatted_data[] = $formatted_row_data;
	}

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Get promotions by promotion id.
 *
 * @param int  $promotion_id The promotion ID.
 * @param bool $direct       Whether to bypass the cache.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     code: string,
 *     start_date: string,
 *     end_date: string,
 *     description: string,
 *     discount_type: string,
 *     discount_value: string,
 *     currency: string|null,
 *     is_pif: int,
 *   }
 * >
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
		if ( is_array( $cached_value ) ) {
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

	// Bail out if not an array.
	if ( ! is_array( $promotion_data ) ) {
		return [];
	}

	// Format the data.
	$formatted_promotion_data = format_rows_data_from_db( $promotion_data );

	// Cache the value.
	wp_cache_set( $cache_key, $formatted_promotion_data, CACHE_GROUP );

	// Return the promotion data.
	return $formatted_promotion_data;
}
