<?php
/**
 * Namespace functions.
 *
 * @package quark-checkout
 */

namespace Quark\Checkout;

use WP_Post;

use function Quark\CabinCategories\get as get_cabin_category_post;
use function Quark\Departures\get as get_departure_post;

use const Quark\Core\CURRENCIES;
use const Quark\Core\USD_CURRENCY;

/**
 * Bootstrap the plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Add code here.
}

/**
 * Get Checkout URL
 *
 * @param int    $departure_post_id Departure post ID.
 * @param int    $cabin_post_id     Cabin post ID.
 * @param string $currency          Currency.
 *
 * @return string
 */
function get_checkout_url( int $departure_post_id = 0, int $cabin_post_id = 0, string $currency = USD_CURRENCY ): string {
	// Check base URL.
	if ( ! defined( 'QUARK_CHECKOUT_BASE_URL' ) ) {
		return '';
	}

	// Get base URL.
	$url = QUARK_CHECKOUT_BASE_URL;

	// Validate departure post ID, cabin post ID and currency.
	if ( empty( $departure_post_id ) || empty( $cabin_post_id ) || empty( $currency ) ) {
		return $url;
	}

	// Uppercase currency.
	$currency = strtoupper( $currency );

	// Validate currency.
	if ( ! in_array( $currency, CURRENCIES, true ) ) {
		return $url;
	}

	// Get departure post.
	$departure_post = get_departure_post( $departure_post_id );

	// Validate departure post.
	if ( empty( $departure_post['post'] ) || ! $departure_post['post'] instanceof WP_Post || empty( $departure_post['post_meta'] ) ) {
		return $url;
	}

	// Get cabin post.
	$cabin_post = get_cabin_category_post( $cabin_post_id );

	// Validate cabin post.
	if ( empty( $cabin_post['post'] ) || ! $cabin_post['post'] instanceof WP_Post || empty( $cabin_post['post_meta'] ) ) {
		return $url;
	}

	// Check if softrip package code is set on meta.
	if ( empty( $departure_post['post_meta']['softrip_package_code'] ) ) {
		return $url;
	}

	// Get package code.
	$package_code = strval( $departure_post['post_meta']['softrip_package_code'] );

	// Check if departure date is set on meta.
	if ( empty( $departure_post['post_meta']['start_date'] ) ) {
		return $url;
	}

	// Get start date.
	$start_date = strval( $departure_post['post_meta']['start_date'] );

	// Check if cabin code is set on meta.
	if ( empty( $cabin_post['post_meta']['cabin_category_id'] ) ) {
		return $url;
	}

	// Get cabin code.
	$cabin_code = strval( $cabin_post['post_meta']['cabin_category_id'] );

	// Build checkout URL.
	$checkout_url = add_query_arg(
		[
			'package_id'     => $package_code,
			'departure_date' => $start_date,
			'cabin_code'     => $cabin_code,
			'currency'       => $currency,
		],
		$url
	);

	// Return checkout URL.
	return $checkout_url;
}
