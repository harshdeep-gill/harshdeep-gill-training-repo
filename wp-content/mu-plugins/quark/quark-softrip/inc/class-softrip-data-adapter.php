<?php
/**
 * Softrip_Data_Adapter Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Error;

/**
 * Class Softrip_Data_Adapter.
 */
class Softrip_Data_Adapter {
	/**
	 * Do a request.
	 *
	 * @param string  $service The service to request from.
	 * @param mixed[] $params  The request params.
	 * @param string  $method  The request method.
	 *
	 * @return mixed[]|WP_Error
	 */
	public function do_request( string $service = '', array $params = [], string $method = 'GET' ): array|WP_Error {
		// Check Username and Password are set.
		if (
			! defined( 'QUARK_SOFTRIP_ADAPTER_BASE_URL' ) ||
			! defined( 'QUARK_SOFTRIP_ADAPTER_USERNAME' ) ||
			! defined( 'QUARK_SOFTRIP_ADAPTER_PASSWORD' )
		) {
			return new WP_Error( 'no_auth', __( 'Softrip credentials missing', 'qrk' ) );
		}

		// Create the URL.
		$url = trailingslashit( QUARK_SOFTRIP_ADAPTER_BASE_URL ) . $service;

		// Set the request args.
		$args = [
			'method'  => $method,
			'headers' => [
				'Authorization' => 'basic ' . base64_encode( QUARK_SOFTRIP_ADAPTER_USERNAME . ':' . QUARK_SOFTRIP_ADAPTER_PASSWORD ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			],
		];

		// Add body if present.
		if ( ! empty( $params ) ) {
			$args['body'] = $params;
		}

		// Do request.
		$request = wp_remote_request( $url, $args );

		// Return WP_Error if failed.
		if ( $request instanceof WP_Error ) {
			return $request;
		}

		// Return result array.
		return (array) json_decode( wp_remote_retrieve_body( $request ), true );
	}
}
