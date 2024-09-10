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
	// Add actions.
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
