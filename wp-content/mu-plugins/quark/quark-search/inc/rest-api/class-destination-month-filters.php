<?php
/**
 * REST API: Destination Month Filters
 *
 * @package quark-search
 */

namespace Quark\Search\REST_API;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use function Quark\Search\Departures\get_destination_and_month_search_filter_data;

use const Quark\Search\REST_API_NAMESPACE;

/**
 * Destination Month Filters class.
 */
class Destination_Month_Filters {
	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Register the route for getting the filter options.
		register_rest_route(
			REST_API_NAMESPACE,
			'/filter-options/by-destination-and-month',
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_the_options' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'destination_term_id' => [
						'required'          => false,
						'type'              => 'string',
						'description'       => __( 'Destination Term ID', 'qrk' ),
						'sanitize_callback' => 'absint',
					],
					'month'               => [
						'required'          => false,
						'type'              => 'string',
						'description'       => __( 'Month', 'qrk' ),
						'sanitize_callback' => 'sanitize_text_field',
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
	public function get_the_options( ?WP_REST_Request $request = null ): WP_REST_Response|WP_Error {
		// Validate the request.
		if ( ! $request instanceof WP_REST_Request ) {
			return new WP_Error( 'invalid_request', __( 'Invalid request.', 'qrk' ), [ 'status' => 400 ] );
		}

		// Get the destination term id.
		$destination_term_id = $request->get_param( 'destination_term_id' );
		$destination_term_id = ! empty( $destination_term_id ) ? absint( $destination_term_id ) : 0;

		// Get the month.
		$month = $request->get_param( 'month' );
		$month = ! empty( $month ) ? strval( $month ) : '';

		// Get filter options by terms.
		$filter_options = get_destination_and_month_search_filter_data( $destination_term_id, $month );

		// Return the response.
		return rest_ensure_response( $filter_options );
	}
}
