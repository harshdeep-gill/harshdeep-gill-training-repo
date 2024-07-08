<?php
/**
 * Namespace functions.
 *
 * @package quark-office-phone-numbers
 */

namespace Quark\OfficePhoneNumbers;

const OFFICE_CACHE_KEY = 'office_phone_number_data';
const CACHE_GROUP      = 'qrk_options';

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
	if ( ! (
		is_array( $data )
		&& array_key_exists( 'data', $data )
		&& is_array( $data['data'] )
	) ) {
		$data['data'] = [];
	}

	// Get local office data.
	$office_data = get_local_office_data();

	// Check for empty data.
	if ( empty( $office_data ) ) {
		return $data;
	}

	// Add data.
	$data['data'] = array_merge(
		$data['data'],
		[
			'office_phone_numbers' => $office_data,
		]
	);

	// Return updated data.
	return $data;
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
		];

		// Get coverage count for current office.
		$local_office_data[ $index ]['coverage'] = get_option( 'options_country_' . $index . '_coverage', [] );
	}

	// Set cache and return data.
	wp_cache_set( OFFICE_CACHE_KEY, $local_office_data, CACHE_GROUP );

	// Set local office data.
	return $local_office_data;
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
	wp_cache_delete( OFFICE_CACHE_KEY, CACHE_GROUP );

	// Trigger action to clear cache for office data.
	do_action( 'qe_office_data_cache_busted' );
}
