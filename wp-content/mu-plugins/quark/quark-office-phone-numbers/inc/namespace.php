<?php
/**
 * Namespace functions.
 *
 * @package quark-office-phone-numbers
 */

namespace Quark\OfficePhoneNumbers;

use Quark\OfficePhoneNumbers\RestApi\Phone_Number;

use function Quark\Core\get_visitor_geo_country;

const OFFICE_CACHE_KEY = 'office_phone_number_data';
const CACHE_GROUP      = 'qrk_options';

const REST_API_NAMESPACE = 'qrk-phone-numbers/v1';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Other hooks.
	add_filter( 'quark_front_end_data', __NAMESPACE__ . '\\office_phone_number_front_end_data' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\setup_phone_number_settings' );
	add_action( 'acf/options_page/save', __NAMESPACE__ . '\\purge_local_office_data_cache', 10, 2 );

	// REST API hooks.
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_rest_endpoints' );
	add_filter( 'travelopia_security_public_rest_api_routes', __NAMESPACE__ . '\\security_public_rest_api_routes' );

	// Replace [quark_default_phone_number] string with the default phone number.
	add_filter( 'quark_front_end_markup', __NAMESPACE__ . '\\replace_default_phone_number_shortcode' );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/options-office.php';
	}
}

/**
 * Site settings.
 *
 * @return void
 */
function setup_phone_number_settings(): void {
	// Check if ACF plugin is enabled.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	// Local Office details.
	acf_add_options_page(
		[
			'page_title'  => 'Office Phone Numbers',
			'menu_slug'   => 'office',
			'parent_slug' => 'site-settings',
			'redirect'    => false,
		]
	);
}

/**
 * Get Office phone number front-end data.
 *
 * @param mixed[] $data Template data.
 *
 * @return array<string,array<string,mixed>> Updated data.
 */
function office_phone_number_front_end_data( array $data = [] ): array {
	// Check for correct data.
	if ( ! is_array( $data ) ) {
		$data = [];
	}

	// Add dynamic phone number data.
	$data['dynamic_phone_number'] = [
		'api_endpoint'         => home_url( 'wp-json/' . REST_API_NAMESPACE . '/phone-number/get' ),
		'default_phone_number' => get_option( 'options_default_phone_number', '' ),
	];

	// Return updated data.
	return $data;
}

/**
 * Register API endpoints.
 *
 * @return void
 */
function register_rest_endpoints(): void {
	// Include endpoint classes.
	require_once __DIR__ . '/rest-api/class-phone-number.php';

	// Setup endpoints.
	$endpoints = [
		new Phone_Number(),
	];

	// Register routes.
	foreach ( $endpoints as $endpoint ) {
		$endpoint->register_routes();
	}
}

/**
 * Register public REST API routes.
 *
 * @param string[] $routes Public routes.
 *
 * @return string[]
 */
function security_public_rest_api_routes( array $routes = [] ): array {
	// Whitelist local phone number API routes.
	$routes[] = sprintf( '/%s/phone-number/get', REST_API_NAMESPACE );

	// Return routes.
	return $routes;
}

/**
 * Get local office data.
 *
 * @return array<int,array<string,mixed>>
 */
function get_local_office_data(): array {
	// Init Local office data.
	$local_office_data = [];

	// Check for cached version.
	$cached_value = wp_cache_get( OFFICE_CACHE_KEY, CACHE_GROUP );

	// Check for cached value.
	if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
		return $cached_value;
	}

	// Get local office count.
	$local_office_count = absint( get_option( 'options_country', '' ) );

	// Loop through local offices.
	for ( $index = 0; $index < $local_office_count; $index++ ) {
		$country = get_option( 'options_country_' . $index . '_name', '' );

		// Skip if country is empty.
		if ( empty( $country ) ) {
			continue;
		}

		// Add country data.
		$local_office_data[ $index ] = [
			'name'                => $country,
			'phone_number_prefix' => get_option( 'options_country_' . $index . '_phone_number_prefix', '' ),
			'phone'               => get_option( 'options_country_' . $index . '_phone_number', '' ),
			'is_corporate_office' => (bool) get_option( 'options_country_' . $index . '_is_corporate_office', false ),
			'coverage'            => get_option( 'options_country_' . $index . '_coverage', [] ),
		];
	}

	// Set cache and return data.
	wp_cache_set( OFFICE_CACHE_KEY, $local_office_data, CACHE_GROUP );

	// Set local office data.
	return $local_office_data;
}

/**
 * Get Corporate office phone details.
 *
 * @return array{}|array{
 *     phone_number: string,
 *     prefix: string,
 * }
 */
function get_corporate_office_phone_number(): array {
	// Cache key.
	$cache_key = OFFICE_CACHE_KEY . '_corporate';

	// Check for cached version.
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
		return $cached_value;
	}

	// Get local office data.
	$office_data = get_local_office_data();
	$data        = [];

	// Loop through local offices.
	foreach ( $office_data as $office ) {
		// Check if corporate office.
		if ( ! empty( $office['is_corporate_office'] ) ) {
			$data = [
				'phone_number' => strval( $office['phone'] ),
				'prefix'       => strval( $office['phone_number_prefix'] ),
			];
			break;
		}
	}

	// Check if phone number is set.
	if ( ! empty( $data ) ) {
		// Set cache and return data.
		wp_cache_set( $cache_key, $data, CACHE_GROUP );

		// Return phone number.
		return $data;
	}

	// Return empty array if corporate office is not set.
	return [];
}

/**
 * Get office phone number details by country code.
 *
 * @param string $country_code Country code.
 *
 * @return array{}|array{
 *    phone_number: string,
 *    prefix: string,
 * }
 */
function get_office_phone_number_by_country_code( string $country_code = 'US' ): array {
	// Cache key.
	$cache_key = OFFICE_CACHE_KEY . '_' . $country_code;

	// Check for cached version.
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( ! empty( $cached_value ) && is_array( $cached_value ) ) {
		return $cached_value;
	}

	// Get local office data.
	$office_data = get_local_office_data();
	$data        = [];

	// Loop through local offices.
	foreach ( $office_data as $office ) {
		// Check if country code matches.
		if ( in_array( strtoupper( $country_code ), (array) $office['coverage'], true ) ) {
			$data = [
				'phone_number' => strval( $office['phone'] ),
				'prefix'       => strval( $office['phone_number_prefix'] ),
			];
			break;
		}
	}

	// Check if phone number is set.
	if ( ! empty( $data ) ) {
		// Set cache and return data.
		wp_cache_set( $cache_key, $data, CACHE_GROUP );

		// Return phone number.
		return $data;
	}

	// Return empty array if country code is empty.
	return [];
}

/**
 * Get office phone number by geo location.
 *
 * @return array{}|array{
 *     phone_number: string,
 *     prefix: string,
 * }
 */
function get_office_phone_number(): array {
	// Get the visitor's country.
	$country = get_visitor_geo_country();

	// Get office phone number by country code.
	$office_phone_number = get_office_phone_number_by_country_code( $country );

	// Use corporate office phone number if no local office phone number is found.
	if ( empty( $office_phone_number ) ) {
		$office_phone_number = get_corporate_office_phone_number();
	}

	// Return the matching rule.
	return $office_phone_number;
}

/**
 * Purge local office data cache.
 * This function is hooked into `acf/options_page/save` action.
 *
 * @param string $page_id   Page ID.
 * @param string $page_slug Page Slug.
 *
 * @return void
 */
function purge_local_office_data_cache( string $page_id = '', string $page_slug = '' ): void {
	// Check for office options page.
	if ( 'options' !== $page_id || 'office' !== $page_slug ) {
		return;
	}

	// Purge cache.
	wp_cache_flush_group( CACHE_GROUP );

	// Trigger action to clear cache for office data.
	do_action( 'qe_office_data_cache_busted' );
}

/**
 * Replace shortcode with the default phone number.
 *
 * @param string $markup The current markup.
 *
 * @return string
 */
function replace_default_phone_number_shortcode( string $markup = '' ): string {
	// Replace shortcode with default phone number.
	return strval(
		str_replace(
			'[quark_default_phone_number]',
			strval( get_option( 'options_default_phone_number', '' ) ),
			$markup
		)
	);
}
