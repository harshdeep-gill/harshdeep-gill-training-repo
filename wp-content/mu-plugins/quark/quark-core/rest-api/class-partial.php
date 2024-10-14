<?php
/**
 * Get Partial API.
 *
 * @package Quark-core
 */

namespace Quark\Core\RestApi;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

use function Quark\Tracking\add_infinity_tracking_class;
use const Quark\Core\REST_API_NAMESPACE;

/**
 * Class Partial.
 */
class Partial extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace = REST_API_NAMESPACE . '/partial';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Register the rest route.
		register_rest_route(
			$this->namespace,
			'get',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_partial' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'name' => [
						'required'          => true,
						'type'              => 'string',
						'description'       => 'Partial Name',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
					],
					'data' => [
						'required'    => false,
						'type'        => 'object',
						'description' => 'Partial Data',
						'default'     => [],
					],
				],
			]
		);
	}

	/**
	 * Get partial.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_partial( WP_REST_Request $request ): WP_Error|WP_REST_Response { // phpcs:ignore
		// Parse parameters.
		$name = sanitize_text_field( strval( $request->get_param( 'name' ) ?? '' ) );
		$data = (array) ( $request->get_param( 'data' ) ?? [] );

		// Build partial.
		$response = (array) apply_filters(
			'qrk_get_partial',
			[
				'markup' => '',
				'data'   => [],
			],
			$name,
			$data
		);

		// Add infinity tracking class to phone numbers.
		$response['markup'] = add_infinity_tracking_class( strval( $response['markup'] ) );

		// Return response.
		return rest_ensure_response( $response );
	}
}
