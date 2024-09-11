<?php
/**
 * Namespace functions.
 *
 * @package quark-localization
 */

namespace Quark\Localization;

use function Quark\Core\doing_tests;

const USD_CURRENCY = 'USD';
const AUD_CURRENCY = 'AUD';
const CAD_CURRENCY = 'CAD';
const EUR_CURRENCY = 'EUR';
const GBP_CURRENCY = 'GBP';
const CURRENCIES   = [
	USD_CURRENCY,
	AUD_CURRENCY,
	CAD_CURRENCY,
	EUR_CURRENCY,
	GBP_CURRENCY,
];

// Default currency and cookie.
const DEFAULT_CURRENCY = USD_CURRENCY;
const CURRENCY_COOKIE  = 'STYXKEY_currency';

/**
 * Bootstrap the plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Frontend data.
	add_action( 'quark_front_end_data', __NAMESPACE__ . '\\front_end_data' );
}

/**
 * Get currencies.
 *
 * @return array{
 *   0: 'USD',
 *   1: 'AUD',
 *   2: 'CAD',
 *   3: 'EUR',
 *   4: 'GBP',
 * }
 */
function get_currencies(): array {
	// Return the currencies.
	return CURRENCIES;
}

/**
 * Get current currency.
 *
 * @return string
 */
function get_current_currency(): string {
	// Initialize the current currency.
	static $current_currency = null;

	// Is doing unit test.
	$is_doing_test = doing_tests();

	// Look for cached currency.
	if ( null !== $current_currency && ! $is_doing_test ) {
		return $current_currency;
	}

	// Default currency.
	$current_currency = DEFAULT_CURRENCY;

	// Read currency cookie.
	if ( isset( $_COOKIE[ CURRENCY_COOKIE ] ) && is_string( $_COOKIE[ CURRENCY_COOKIE ] ) ) {
		$currency = strtoupper( sanitize_text_field( $_COOKIE[ CURRENCY_COOKIE ] ) );

		// Validate currency.
		if ( in_array( $currency, get_currencies(), true ) ) {
			$current_currency = $currency;
		}
	}

	// Return the current currency.
	return $current_currency;
}

/**
 * Front-end data.
 *
 * @param mixed[] $data Front-end data.
 *
 * @return mixed[]
 */
function front_end_data( array $data = [] ): array {
	// Add currency.
	$data['currencies'] = [
		USD_CURRENCY => __( '$ USD', 'qrk' ),
		AUD_CURRENCY => __( '$ AUD', 'qrk' ),
		CAD_CURRENCY => __( '$ CAD', 'qrk' ),
		EUR_CURRENCY => __( '€ EUR', 'qrk' ),
		GBP_CURRENCY => __( '£ GBP', 'qrk' ),
	];

	// Return data.
	return $data;
}
