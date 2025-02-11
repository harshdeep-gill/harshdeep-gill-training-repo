<?php
/**
 * Namespace for the Softrip adventure options data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\AdventureOptions;

use WP_Error;

use function Quark\Localization\get_currencies;
use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\add_prefix_to_table_name;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;

const CACHE_KEY_PREFIX = 'qrk_softrip_adventure_options';
const CACHE_GROUP      = 'qrk_softrip_adventure_options';

/**
 * Get adventure option table name.
 *
 * @return string
 */
function get_table_name(): string {
	// Return the table name.
	return add_prefix_to_table_name( 'adventure_options' );
}

/**
 * Get the adventure_options table create SQL.
 *
 * @return string
 */
function get_table_sql(): string {
	// Get the table name.
	$table_name = get_table_name();

	// Get the engine collate.
	$engine_collate = get_engine_collate();

	// Build the SQL query.
	$sql = "CREATE TABLE $table_name (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		softrip_option_id VARCHAR(255) NOT NULL UNIQUE,
		departure_post_id BIGINT NOT NULL,
		softrip_package_code VARCHAR(20) NOT NULL,
		service_ids VARCHAR(255) NOT NULL,
		spaces_available BIGINT NOT NULL,
		adventure_option_term_id BIGINT NOT NULL,
		price_per_person_usd BIGINT NOT NULL,
		price_per_person_cad BIGINT NOT NULL,
		price_per_person_aud BIGINT NOT NULL,
		price_per_person_gbp BIGINT NOT NULL,
		price_per_person_eur BIGINT NOT NULL
	) $engine_collate";

	// return the SQL.
	return $sql;
}

/**
 * Update the adventure options data.
 *
 * @param mixed[] $raw_adventure_options Raw adventure options data from Softrip to update with.
 * @param int     $departure_post_id     Departure post ID.
 *
 * @return boolean Whether any adventure options were updated/inserted.
 */
function update_adventure_options( array $raw_adventure_options = [], int $departure_post_id = 0 ): bool {
	// Bail out if empty departure post ID.
	if ( empty( $departure_post_id ) ) {
		return false;
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Initialize adventure option term ids.
	$adventure_option_term_ids = [];

	// Existing adventure options by departure post ID.
	$existing_adventure_options = get_adventure_option_by_departure_post_id( $departure_post_id, true );

	// Map the existing adventure options to Softrip option ID.
	$existing_adventure_options_by_softrip_option_id = [];

	// Loop through the existing adventure options.
	foreach ( $existing_adventure_options as $existing_adventure_option ) {
		// Skip if empty.
		if ( empty( $existing_adventure_option['softrip_option_id'] ) || empty( $existing_adventure_option['id'] ) ) {
			continue;
		}

		// Add to the map.
		$existing_adventure_options_by_softrip_option_id[ $existing_adventure_option['softrip_option_id'] ] = absint( $existing_adventure_option['id'] );
	}

	// Initialize if any updated.
	$any_updated = false;

	// Initialize updated option ids.
	$updated_option_ids = [];

	// What to do with empty raw adventure options?
	foreach ( $raw_adventure_options as $raw_adventure_option ) {
		// Validate the raw adventure option.
		if ( ! is_array( $raw_adventure_option ) || empty( $raw_adventure_option ) ) {
			continue;
		}

		// Format raw adventure option.
		$formatted_adventure_option = format_adventure_option_data( $raw_adventure_option, $departure_post_id );

		// Skip if empty.
		if ( empty( $formatted_adventure_option ) ) {
			continue;
		}

		// Add term ID to the list.
		if ( ! empty( $formatted_adventure_option['adventure_option_term_id'] ) ) {
			$adventure_option_term_ids[] = $formatted_adventure_option['adventure_option_term_id'];
		}

		// Get existing adventure option by Softrip option ID.
		$existing_adventure_options = get_adventure_option_by_softrip_option_id( $formatted_adventure_option['softrip_option_id'], true );

		// Get the first existing adventure option.
		$existing_adventure_option = ! empty( $existing_adventure_options ) ? $existing_adventure_options[0] : [];

		// Initialize updated ID.
		$updated_id = 0;

		// If existing, update the adventure option, else insert.
		if ( ! empty( $existing_adventure_option['id'] ) ) {
			$is_updated = $wpdb->update(
				$table_name,
				$formatted_adventure_option,
				[ 'id' => $existing_adventure_option['id'] ]
			);

			// Check if updated.
			if ( $is_updated > 0 ) {
				$any_updated = true;
			}

			// Get the updated ID.
			$updated_id = $existing_adventure_option['id'];
		} else {
			$wpdb->insert(
				$table_name,
				$formatted_adventure_option
			);

			// Get the inserted ID.
			$updated_id = $wpdb->insert_id;

			// Set any updated to true.
			$any_updated = true;
		}

		// Skip if no updated ID.
		if ( empty( $updated_id ) ) {
			continue;
		}

		// Add to the updated option IDs.
		$updated_option_ids[] = $formatted_adventure_option['softrip_option_id'];

		// Bust caches.
		wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_option_id_' . $formatted_adventure_option['softrip_option_id'], CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
	}

	// Delete the non-updated adventure options - redundant.
	$non_updated_option_ids = array_diff( array_keys( $existing_adventure_options_by_softrip_option_id ), $updated_option_ids );

	// Loop through the non-updated option IDs and remove them.
	foreach ( $non_updated_option_ids as $non_updated_option_id ) {
		// Get the ID.
		$non_updated_option_id = absint( $existing_adventure_options_by_softrip_option_id[ $non_updated_option_id ] );

		// Skip if empty.
		if ( empty( $non_updated_option_id ) ) {
			continue;
		}

		// Delete the non-updated option.
		$is_deleted = $wpdb->delete(
			$table_name,
			[ 'id' => $non_updated_option_id ]
		);

		// Skip if not deleted.
		if ( empty( $is_deleted ) ) {
			continue;
		}

		// Set any updated to true.
		$any_updated = true;

		// Bust caches.
		wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_option_id_' . $non_updated_option_id, CACHE_GROUP );
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
	}

	// Remove duplicates.
	$adventure_option_term_ids = array_unique( $adventure_option_term_ids );

	// Update the post meta.
	update_post_meta( $departure_post_id, 'adventure_options', $adventure_option_term_ids );

	// Bust the cache.
	foreach ( $adventure_option_term_ids as $adventure_option_term_id ) {
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_adventure_option_term_id_' . $adventure_option_term_id, CACHE_GROUP );
	}

	// Return if any updated.
	return $any_updated;
}

/**
 * Format the adventure options raw data.
 *
 * @param mixed[] $raw_adventure_option Raw adventure options data from Softrip.
 * @param int     $departure_post_id    Departure post ID.
 *
 * @return array{
 *   softrip_option_id: string,
 *   departure_post_id: int,
 *   softrip_package_code: string,
 *   spaces_available: int,
 *   service_ids: string,
 *   adventure_option_term_id: int,
 *   price_per_person_usd: int,
 *   price_per_person_cad: int,
 *   price_per_person_aud: int,
 *   price_per_person_gbp: int,
 *   price_per_person_eur: int
 * } | array{}
 */
function format_adventure_option_data( array $raw_adventure_option = [], int $departure_post_id = 0 ): array {
	// Bail out if empty departure post ID.
	if ( empty( $departure_post_id ) || empty( $raw_adventure_option ) ) {
		return [];
	}

	// Setup default.
	$default = [
		'id'              => '',
		'spacesAvailable' => 0,
		'serviceIds'      => [],
		'price'           => [],
	];

	// Apply default values.
	$raw_adventure_option = wp_parse_args( $raw_adventure_option, $default );

	// Validate empty values.
	if ( empty( $raw_adventure_option['id'] ) || empty( $raw_adventure_option['price'] ) ) {
		return [];
	}

	// Service Ids.
	$service_ids = '';

	// Adventure Option term id.
	$adventure_option_term_id = 0;

	// Add service ids and term id to formatted data.
	if ( ! empty( $raw_adventure_option['serviceIds'] ) && is_array( $raw_adventure_option['serviceIds'] ) ) {
		// Initialize service IDs by sanitizing.
		$service_ids = array_map( 'strval', $raw_adventure_option['serviceIds'] );
		$service_ids = array_map( 'sanitize_text_field', $service_ids );

		// Add adventure option term id.
		foreach ( $service_ids as $service_id ) {
			// Get the term IDs where the service ID is stored in the meta.
			$term_ids = get_adventure_option_taxonomy_term_by_service_id( $service_id );

			// Add first term to formatted data and break.
			if ( ! empty( $term_ids ) ) {
				$adventure_option_term_id = $term_ids[0];
				break;
			}
		}

		// Store service ids in formatted data.
		$service_ids = implode( ',', $service_ids );
	}

	// Validate term ID.
	if ( empty( $adventure_option_term_id ) ) {
		return [];
	}

	// Spaces available.
	$spaces_available = intval( $raw_adventure_option['spacesAvailable'] ); // phpcs:ignore Travelopia.PHP.PreferAbsintOverIntval.UseAbsInt

	// Ensure spaces available is not negative.
	if ( $spaces_available < 0 ) {
		$spaces_available = 0;
	}

	// Initialize formatted data.
	$formatted_data = [
		'softrip_option_id'        => sanitize_text_field( strval( $raw_adventure_option['id'] ) ),
		'departure_post_id'        => $departure_post_id,
		'softrip_package_code'     => '',
		'spaces_available'         => $spaces_available,
		'service_ids'              => $service_ids,
		'adventure_option_term_id' => $adventure_option_term_id,
		'price_per_person_usd'     => 0,
		'price_per_person_cad'     => 0,
		'price_per_person_aud'     => 0,
		'price_per_person_gbp'     => 0,
		'price_per_person_eur'     => 0,
	];

	// Get the package code.
	$softrip_package_code = get_post_meta( $departure_post_id, 'softrip_package_code', true );

	// Add package code to formatted data.
	if ( ! empty( $softrip_package_code ) ) {
		$formatted_data['softrip_package_code'] = strval( $softrip_package_code );
	}

	// Loop through the currencies.
	foreach ( get_currencies() as $currency ) {
		// Check if the currency is set and price per person exists.
		if ( empty( $raw_adventure_option['price'][ $currency ] ) || ! is_array( $raw_adventure_option['price'][ $currency ] ) || empty( $raw_adventure_option['price'][ $currency ]['pricePerPerson'] ) ) {
			continue;
		}

		// Add the price per person to the formatted data.
		$formatted_data[ 'price_per_person_' . strtolower( $currency ) ] = absint( $raw_adventure_option['price'][ $currency ]['pricePerPerson'] );
	}

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Get adventure option taxonomy term by service ID.
 *
 * @param string $service_id The service ID to get the term for.
 *
 * @return int[]
 */
function get_adventure_option_taxonomy_term_by_service_id( string $service_id = '' ): array {
	// Check for valid service ID.
	if ( empty( $service_id ) ) {
		return [];
	}

	// Get the terms associated with the service ID. Service ID is stored in the term meta with the key softrip_{index}_id.
	$terms = get_terms(
		[
			'taxonomy'   => ADVENTURE_OPTION_CATEGORY,
			'hide_empty' => false,
			'fields'     => 'ids',
			'meta_query' => [
				[
					'key'         => 'softrip_.+_id',
					'compare_key' => 'REGEXP',
					'value'       => $service_id,
				],
			],
		]
	);

	// Check if terms is valid array.
	if ( $terms instanceof WP_Error || ! is_array( $terms ) ) {
		return [];
	}

	// Map the term IDs to an array.
	$terms = array_map( 'absint', $terms );

	// Return the term.
	return $terms;
}

/**
 * Get adventure options by departure post ID.
 *
 * @param integer $departure_post_id The departure post ID.
 * @param boolean $force            Whether to bypass the cache.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_option_id: string,
 *     departure_post_id: int,
 *     softrip_package_code: string,
 *     service_ids: string,
 *     spaces_available: int,
 *     adventure_option_term_id: int,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int
 *   }
 * >
 */
function get_adventure_option_by_departure_post_id( int $departure_post_id = 0, bool $force = false ): array {
	// Validate departure post ID.
	if ( empty( $departure_post_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . "_departure_post_id_$departure_post_id";

	// If not direct, check for cached version.
	if ( empty( $force ) ) {
		// Check for cached version.
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Check for cached value.
		if ( is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the adventure options.
	$adventure_options = $wpdb->get_results(
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

	// Return if not array.
	if ( ! is_array( $adventure_options ) ) {
		return [];
	}

	// Format the rows data.
	$formatted_rows = format_rows_data_from_db( $adventure_options );

	// Cache the value.
	wp_cache_set( $cache_key, $formatted_rows, CACHE_GROUP );

	// Return the adventure options.
	return $formatted_rows;
}

/**
 * Get adventure options by softrip option id.
 *
 * @param string  $softrip_option_id The Softrip option ID.
 * @param boolean $force           Whether to bypass the cache.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_option_id: string,
 *     departure_post_id: int,
 *     softrip_package_code: string,
 *     service_ids: string,
 *     spaces_available: int,
 *     adventure_option_term_id: int,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int
 *   }
 * >
 */
function get_adventure_option_by_softrip_option_id( string $softrip_option_id = '', bool $force = false ): array {
	// Validate Softrip option ID.
	if ( empty( $softrip_option_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . "_softrip_option_id_$softrip_option_id";

	// If not direct, check for cached version.
	if ( empty( $force ) ) {
		// Check for cached version.
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Check for cached value.
		if ( is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the adventure options.
	$adventure_options = $wpdb->get_results(
		$wpdb->prepare(
			'
			SELECT
				*
			FROM
				%i
			WHERE
				softrip_option_id = %s
			',
			[
				$table_name,
				$softrip_option_id,
			]
		),
		ARRAY_A
	);

	// Return if not array.
	if ( ! is_array( $adventure_options ) ) {
		return [];
	}

	// Format the rows data.
	$formatted_rows = format_rows_data_from_db( $adventure_options );

	// Cache the value.
	wp_cache_set( $cache_key, $formatted_rows, CACHE_GROUP );

	// Return the adventure options.
	return $formatted_rows;
}

/**
 * Format the adventure option row data coming from the database.
 *
 * @param mixed[] $row_data The adventure option row data.
 *
 * @return array{}|array{
 *   id: int,
 *   softrip_option_id: string,
 *   departure_post_id: int,
 *   softrip_package_code: string,
 *   service_ids: string,
 *   spaces_available: int,
 *   adventure_option_term_id: int,
 *   price_per_person_usd: int,
 *   price_per_person_cad: int,
 *   price_per_person_aud: int,
 *   price_per_person_gbp: int,
 *   price_per_person_eur: int
 * }
 */
function format_row_data_from_db( array $row_data = [] ): array {
	// Bail out if empty row data.
	if ( empty( $row_data ) || ! is_array( $row_data ) ) {
		return [];
	}

	// Validate required columns.
	$required_fields = [
		'id',
		'softrip_option_id',
		'departure_post_id',
		'softrip_package_code',
		'adventure_option_term_id',
	];

	// Check for required fields.
	foreach ( $required_fields as $required_field ) {
		if ( empty( $row_data[ $required_field ] ) ) {
			return [];
		}
	}

	// Initialize formatted data.
	$formatted_data = [
		'id'                       => absint( $row_data['id'] ),
		'softrip_option_id'        => sanitize_text_field( strval( $row_data['softrip_option_id'] ) ),
		'departure_post_id'        => absint( $row_data['departure_post_id'] ),
		'softrip_package_code'     => sanitize_text_field( strval( $row_data['softrip_package_code'] ) ),
		'service_ids'              => sanitize_text_field( strval( $row_data['service_ids'] ?? '' ) ),
		'spaces_available'         => absint( $row_data['spaces_available'] ?? 0 ),
		'adventure_option_term_id' => absint( $row_data['adventure_option_term_id'] ),
		'price_per_person_usd'     => absint( $row_data['price_per_person_usd'] ?? 0 ),
		'price_per_person_cad'     => absint( $row_data['price_per_person_cad'] ?? 0 ),
		'price_per_person_aud'     => absint( $row_data['price_per_person_aud'] ?? 0 ),
		'price_per_person_gbp'     => absint( $row_data['price_per_person_gbp'] ?? 0 ),
		'price_per_person_eur'     => absint( $row_data['price_per_person_eur'] ?? 0 ),
	];

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Format rows data from the database.
 *
 * @param array<int, string[]> $rows_data The rows data.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_option_id: string,
 *     departure_post_id: int,
 *     softrip_package_code: string,
 *     service_ids: string,
 *     spaces_available: int,
 *     adventure_option_term_id: int,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int
 *   }
 * >
 */
function format_rows_data_from_db( array $rows_data = [] ): array {
	// Bail out if empty rows data.
	if ( empty( $rows_data ) || ! is_array( $rows_data ) ) {
		return [];
	}

	// Initialize formatted data.
	$formatted_data = [];

	// Format the data.
	foreach ( $rows_data as $row_data ) {
		$formatted_row_data = format_row_data_from_db( $row_data );

		// Skip if empty.
		if ( empty( $formatted_row_data ) ) {
			continue;
		}

		// Add formatted row data to formatted data.
		$formatted_data[] = $formatted_row_data;
	}

	// Return the formatted data.
	return $formatted_data;
}

/**
 * Get departures by adventure option term id.
 *
 * @param integer $adventure_option_term_id The adventure option term ID.
 * @param boolean $force                    Whether to bypass the cache.
 *
 * @return int[]
 */
function get_departures_by_adventure_option_term_id( int $adventure_option_term_id = 0, bool $force = false ): array {
	// Validate adventure option term ID.
	if ( empty( $adventure_option_term_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . "_departure_adventure_option_term_id_$adventure_option_term_id";

	// If not direct, check for cached version.
	if ( empty( $force ) ) {
		// Check for cached version.
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Check for cached value.
		if ( is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the departures.
	$departure_ids = $wpdb->get_col(
		$wpdb->prepare(
			'
			SELECT
				departure_post_id
			FROM
				%i
			WHERE
				adventure_option_term_id = %d
			GROUP BY
				departure_post_id
			',
			[
				$table_name,
				$adventure_option_term_id,
			]
		)
	);

	// Convert to int.
	$departure_ids = array_map( 'absint', $departure_ids );

	// Cache the value.
	wp_cache_set( $cache_key, $departure_ids, CACHE_GROUP );

	// Return the departure IDs.
	return $departure_ids;
}

/**
 * Get adventure option by ID.
 *
 * @param int  $id    The adventure option ID.
 * @param bool $force Whether to bypass the cache.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     softrip_option_id: string,
 *     departure_post_id: int,
 *     softrip_package_code: string,
 *     service_ids: string,
 *     spaces_available: int,
 *     adventure_option_term_id: int,
 *     price_per_person_usd: int,
 *     price_per_person_cad: int,
 *     price_per_person_aud: int,
 *     price_per_person_gbp: int,
 *     price_per_person_eur: int
 *   }
 * >
 */
function get_adventure_options_by_id( int $id = 0, bool $force = false ): array {
	// Bail out if empty ID.
	if ( empty( $id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . '_id_' . $id;

	// If not direct, get from cache.
	if ( empty( $force ) ) {
		// Check cache.
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Return cached value if exists.
		if ( is_array( $cached_value ) ) {
			return $cached_value;
		}
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Load the adventure option.
	$adventure_option = $wpdb->get_results(
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
				$id,
			]
		),
		ARRAY_A
	);

	// Bail out if not array.
	if ( ! is_array( $adventure_option ) ) {
		return [];
	}

	// Format the row data.
	$formatted_data = format_rows_data_from_db( $adventure_option );

	// Cache the value.
	wp_cache_set( $cache_key, $formatted_data, CACHE_GROUP );

	// Return the adventure option.
	return $formatted_data;
}

/**
 * Delete adventure option by id.
 *
 * @param int $id The adventure option ID.
 *
 * @return boolean Whether the adventure option was deleted.
 */
function delete_adventure_option_by_id( int $id = 0 ): bool {
	// Bail out if empty ID.
	if ( empty( $id ) ) {
		return false;
	}

	// Get global DB object.
	global $wpdb;

	// Get the table name.
	$table_name = get_table_name();

	// Get the adventure option.
	$adventure_options = get_adventure_options_by_id( $id );

	// Bail out if empty.
	if ( empty( $adventure_options ) || 1 < count( $adventure_options ) ) {
		return false;
	}

	// Get first item.
	$adventure_option = $adventure_options[0];

	// Bail out if empty.
	if ( empty( $adventure_option['id'] ) ) {
		return false;
	}

	// Delete the adventure option.
	$is_deleted = $wpdb->delete(
		$table_name,
		[ 'id' => $adventure_option['id'] ]
	);

	// Return false if not deleted.
	if ( empty( $is_deleted ) ) {
		return false;
	}

	// Bust caches.
	wp_cache_delete( CACHE_KEY_PREFIX . '_id_' . $id, CACHE_GROUP );
	wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_option_id_' . $adventure_option['softrip_option_id'], CACHE_GROUP );
	wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $adventure_option['departure_post_id'], CACHE_GROUP );
	wp_cache_delete( CACHE_KEY_PREFIX . '_departure_adventure_option_term_id_' . $adventure_option['adventure_option_term_id'], CACHE_GROUP );

	// Return success.
	return true;
}
