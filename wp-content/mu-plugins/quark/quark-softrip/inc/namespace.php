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

	add_action('admin_menu', function(){
		echo '<pre>';
		var_dump( get_departures(['ANT-ECR-11D2025']) );
		die;
	});
}

/**
 * Get departures for an array of Softrip IDs.
 *
 * @param string[] $codes Softrip ID array, max 5.
 *
 * @return mixed[]
 */
function get_departures( array $codes = [] ): array {
	// Get API.
	$softrip = new API();

	// Strip out duplicates.
	$codes = array_unique( $codes );

	// Check if less than 5 IDs.
	if ( empty( $codes ) || 5 <= count( $codes ) ) {
		return [
			'success' => false,
			'message' => __( "No ID's or Too many ID's requested", 'tcs' ),
		];
	}

	// Implode IDs into a string.
	$code_string = implode( ',', $codes );
	$result      = $softrip->do_request( 'departures', [ 'productCodes' => $code_string ] );

	// Check if request was successful.
	if ( $result instanceof WP_Error ) {
		return [
			'success' => false,
			'message' => $result->get_error_message(),
		];
	}

	// Return successful result.
	return [
		'success' => true,
		'data'    => $result,
	];
}
