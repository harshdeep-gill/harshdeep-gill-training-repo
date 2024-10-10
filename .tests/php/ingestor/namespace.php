<?php
/**
 * Ingestor test functions.
 *
 * @package quark
 */

namespace Quark\Tests\Ingestor;

/**
 * Setup Ingestor integration.
 *
 * @return void
 */
function setup_ingestor_integration(): void {
	// Define constants.
	if ( function_exists( 'getenv' ) ) {
		define( 'QUARK_INGESTOR_BASE_URL', getenv( 'QUARK_INGESTOR_BASE_URL' ) );
		define( 'QUARK_INGESTOR_API_KEY', getenv( 'QUARK_INGESTOR_API_KEY' ) );
	}
}

/**
 * Mock the HTTP request.
 *
 * @param mixed[]|false $response    The response.
 * @param mixed[]       $parsed_args The parsed args.
 * @param string|null   $url         The URL.
 *
 * @return false|array{}|array{
 *     body: string|false,
 *     response: array{
 *          code: int,
 *          message: string,
 *     },
 *     headers: array{},
 * }
 */
function mock_ingestor_http_request( array|false $response = [], array $parsed_args = [], string $url = null ): false|array {
	// Check if the URL is the one we want to mock.
	if ( ! str_contains( $url, QUARK_INGESTOR_BASE_URL ) ) {
		return $response;
	}

	// Check if the request is a PUT request.
	if ( 'POST' !== $parsed_args['method'] ) {
		// Return the response if it is not a PUT request.
		return $response;
	}

	// Check if the request has the correct headers.
	if ( QUARK_INGESTOR_API_KEY !== $parsed_args['headers']['x-api-key'] ) {
		// Return the response if the headers are incorrect.
		return $response;
	}

	// Return a successful response.
	return [
		'body'     => wp_json_encode( [ 'status' => 'success' ] ),
		'response' => [
			'code'    => 200,
			'message' => 'OK',
		],
		'headers'  => [],
	];
}
