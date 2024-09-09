<?php
/**
 * Namespace functions.
 *
 * @package quark-localization
 */

namespace Quark\Localization;

use function Quark\Core\doing_tests;

use const Quark\Core\CURRENCIES;
use const Quark\Core\USD_CURRENCY;

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
		if ( in_array( $currency, CURRENCIES, true ) ) {
			$current_currency = $currency;
		}
	}

	// Return the current currency.
	return $current_currency;
}
