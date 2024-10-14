<?php
/**
 * REST API: Expedition Month Filters
 *
 * @package quark-search
 */

namespace Quark\Search\REST_API;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use function Quark\Search\Filters\get_expeditions_and_month_options_by_expedition;

use const Quark\Search\REST_API_NAMESPACE;

/**
 * Expedition Month Filters class.
 */
class Expedition_Month_Filters {
	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Register the route for getting the filter options.
		register_rest_route(
			REST_API_NAMESPACE,
			'/filter-options/by-expedition',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_the_options' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'expedition_id' => [
						'required'          => true,
						'type'              => 'string',
						'description'       => __( 'Expedition ID', 'qrk' ),
						'sanitize_callback' => 'absint',
					],
				],
			]
		);
	}

	/**
	 * Get the filter options.
	 *
	 * @param WP_REST_Request|null $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_the_options( WP_REST_Request $request = null ): WP_REST_Response|WP_Error {
		// Check for the request.
		if ( ! $request instanceof WP_REST_Request ) {
			return new WP_Error( 'invalid_request', __( 'Invalid request.', 'qrk' ), [ 'status' => 400 ] );
		}

		// Get the destination and month filter options.
		$options = get_expeditions_and_month_options_by_expedition( absint( $request->get_param( 'expedition_id' ) ) );

		// Return the response.
		return rest_ensure_response( $options );
	}
}
