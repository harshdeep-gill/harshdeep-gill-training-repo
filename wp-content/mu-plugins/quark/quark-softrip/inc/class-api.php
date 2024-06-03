<?php
/**
 * API Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Error;

use const Quark\Softrip\BASE_URL as BASE_URL;

/**
 * Class API.
 */
class API {

	/**
	 * Holds the username.
	 *
	 * @var string
	 */
	private string $username = '';

	/**
	 * Holds the password.
	 *
	 * @var string
	 */
	private string $password = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Populate the auth.
		$this->username = strval( get_field( 'softrip_username', 'options' ) );
		$this->password = strval( get_field( 'softrip_password', 'options' ) );
	}

	/**
	 * Get an API instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		// Declare a static var.
		static $instance;

		// IF not set, create new instance.
		if ( ! $instance ) {
			$instance = new self();
		}

		// Return instance.
		return $instance;
	}

	/**
	 * Do a request.
	 *
	 * @param string       $service The service to request from.
	 * @param array<mixed> $params  The request params.
	 * @param string       $method  The request method.
	 *
	 * @return array<mixed>|WP_Error
	 */
	private function do_request( string $service = '', array $params = [], string $method = 'GET' ): array|WP_Error {
		// Check Username and Password are set.
		if ( empty( $this->username ) || empty( $this->password ) ) {
			return new WP_Error( 'no_auth', __( 'Softrip credentials missing', 'tcs' ) );
		}

		// Create the URL.
		$url = trailingslashit( BASE_URL ) . $service;

		// Set the request args.
		$args = [
			/** This filter is documented in wp-includes/class-wp-http-streams.php */
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			'method'    => $method,
			'headers'   => [
				'Authorization' => 'basic ' . base64_encode( $this->username . ':' . $this->password ), // phpcs:ignore
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

	/**
	 * Get departures for an array of Softrip IDs.
	 *
	 * @param array<string> $codes Softrip ID array, max 5.
	 *
	 * @return array<mixed>
	 */
	public function get_departures( array $codes = [] ): array {
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
		$result      = $this->do_request( 'departures', [ 'productCodes' => $code_string ] );

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
}
