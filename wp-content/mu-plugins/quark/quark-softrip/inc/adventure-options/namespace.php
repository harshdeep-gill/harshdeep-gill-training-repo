<?php
/**
 * Namespace for the Softrip adventure options data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\AdventureOptions;

use WP_Error;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\prefix_table_name;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Core\CURRENCIES;

const CACHE_KEY_PREFIX = 'qrk_softrip_adventure_options';
const CACHE_GROUP      = 'qrk_softrip_adventure_options';

/**
 * Get adventure option table name.
 *
 * @return string
 */
function get_table_name(): string {
	// Return the table name.
	return prefix_table_name( 'adventure_options' );
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
 * @return boolean
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

		// If existing, update the adventure option, else insert.
		if ( ! empty( $existing_adventure_option['id'] ) ) {
			$wpdb->update(
				$table_name,
				$formatted_adventure_option,
				[ 'id' => $existing_adventure_option['id'] ]
			);
		} else {
			$wpdb->insert(
				$table_name,
				$formatted_adventure_option
			);
		}
	}

	// Remove duplicates.
	$adventure_option_term_ids = array_unique( $adventure_option_term_ids );

	// Update the post meta.
	update_post_meta( $departure_post_id, 'adventure_options', $adventure_option_term_ids );

	// Return successful.
	return true;
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
 *   price_per_person_usd: float,
 *   price_per_person_cad: float,
 *   price_per_person_aud: float,
 *   price_per_person_gbp: float,
 *   price_per_person_eur: float
 * } | array{}
 */
function format_adventure_option_data( array $raw_adventure_option = [], int $departure_post_id = 0 ): array {
	// Bail out if empty departure post ID.
	if ( empty( $departure_post_id ) ) {
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

	// Initialize formatted data.
	$formatted_data = [
		'softrip_option_id'        => $raw_adventure_option['id'],
		'departure_post_id'        => $departure_post_id,
		'softrip_package_code'     => '',
		'spaces_available'         => absint( $raw_adventure_option['spacesAvailable'] ),
		'service_ids'              => '',
		'adventure_option_term_id' => 0,
		'price_per_person_usd'     => 0,
		'price_per_person_cad'     => 0,
		'price_per_person_aud'     => 0,
		'price_per_person_gbp'     => 0,
		'price_per_person_eur'     => 0,
	];

	// Get the package code.
	$softrip_package_code = get_post_meta( $departure_post_id, 'softrip_package_code', true );

	// Add package code to formatted data.
	if ( ! empty( $softrip_package_code ) && is_string( $softrip_package_code ) ) {
		$formatted_data['softrip_package_code'] = $softrip_package_code;
	}

	// Add service ids and term id to formatted data.
	if ( ! empty( $raw_adventure_option['serviceIds'] ) && is_array( $raw_adventure_option['serviceIds'] ) ) {
		// Initialize service IDs.
		$service_ids = array_map( 'sanitize_text_field', $raw_adventure_option['serviceIds'] );

		// Add adventure option term id.
		foreach ( $service_ids as $service_id ) {
			// Get the term IDs where the service ID is stored in the meta.
			$term_ids = get_adventure_option_taxonomy_term_by_service_id( $service_id );

			// Add first term to formatted data and break.
			if ( ! empty( $term_ids ) ) {
				$formatted_data['adventure_option_term_id'] = $term_ids[0];
				break;
			}
		}

		// Store service ids in formatted data.
		$formatted_data['service_ids'] = implode( ',', $service_ids );
	}

	// Check if price is set and is an array.
	if ( ! empty( $raw_adventure_option['price'] ) ) {
		// Loop through the currencies.
		foreach ( CURRENCIES as $currency ) {
			// Check if the currency is set and price per person exists.
			if ( empty( $raw_adventure_option['price'][ $currency ] ) || ! is_array( $raw_adventure_option['price'][ $currency ] ) || empty( $raw_adventure_option['price'][ $currency ]['pricePerPerson'] ) ) {
				continue;
			}

			// Add the price per person to the formatted data.
			$formatted_data[ 'price_per_person_' . strtolower( $currency ) ] = doubleval( $raw_adventure_option['price'][ $currency ]['pricePerPerson'] );
		}
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
 * @param boolean $direct            Whether to bypass the cache.
 *
 * @return mixed[][]
 */
function get_adventure_option_by_departure_post_id( int $departure_post_id = 0, bool $direct = false ): array {
	// Validate departure post ID.
	if ( empty( $departure_post_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . "_$departure_post_id";

	// If not direct, check for cached version.
	if ( empty( $direct ) ) {
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

	// Cache the value.
	wp_cache_set( $cache_key, $adventure_options, CACHE_GROUP );

	// Return the adventure options.
	return $adventure_options;
}

/**
 * Get adventure options by softrip option id.
 *
 * @param string  $softrip_option_id The Softrip option ID.
 * @param boolean $direct           Whether to bypass the cache.
 *
 * @return mixed[][]
 */
function get_adventure_option_by_softrip_option_id( string $softrip_option_id = '', bool $direct = false ): array {
	// Validate Softrip option ID.
	if ( empty( $softrip_option_id ) ) {
		return [];
	}

	// Cache key.
	$cache_key = CACHE_KEY_PREFIX . "_$softrip_option_id";

	// If not direct, check for cached version.
	if ( empty( $direct ) ) {
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

	// Cache the value.
	wp_cache_set( $cache_key, $adventure_options, CACHE_GROUP );

	// Return the adventure options.
	return $adventure_options;
}
