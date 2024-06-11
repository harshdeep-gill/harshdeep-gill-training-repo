<?php
/**
 * Namespace functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_CLI;
use WP_Error;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-softrip db', __NAMESPACE__ . '\\WP_CLI\\DB' );
	}
}

/**
 * Request departures for an array of Softrip IDs.
 *
 * @param array<int, mixed> $codes Softrip ID array, max 5.
 *
 * @return array<string, array<string, array<string, array<string, mixed>>>>|WP_Error
 */
function request_departures( array $codes = [] ): array|WP_Error {
	// Strip out duplicates.
	$codes = array_unique( $codes );

	// Check if less than 5 IDs.
	if ( empty( $codes ) || 5 <= count( $codes ) ) {
		return new WP_Error( 'qrk_softrip_departures_limit', 'The maximum number of codes allowed is 5' );
	}

	// Get API.
	$softrip = new Softrip_Data_Adapter();

	// Implode IDs into a string.
	$code_string = implode( ',', $codes );

	// Do request and return the result.
	return $softrip->do_request( 'departures', [ 'productCodes' => $code_string ] );
}
