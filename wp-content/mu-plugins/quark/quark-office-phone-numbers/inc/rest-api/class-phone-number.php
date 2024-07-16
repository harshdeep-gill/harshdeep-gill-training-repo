<?php
/**
 * Phone Number class.
 *
 * @package quark-phone-numbers
 */

namespace Quark\OfficePhoneNumbers\RestApi;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use function Quark\Core\get_visitor_geo_country;
use function Quark\OfficePhoneNumbers\get_office_phone_number;

use const Quark\OfficePhoneNumbers\REST_API_NAMESPACE;

/**
 * Class Phone_Number.
 */
class Phone_Number extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace = '/phone-number';

	/**
	 * Register API routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Register rest route to get phone number.
		register_rest_route(
			REST_API_NAMESPACE,
			$this->namespace . '/get',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Get the trip details.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get( WP_REST_Request $request ): WP_REST_Response|WP_Error { // phpcs:ignore Travelopia.Functions.FunctionArgsDefaultValue.Missing
		// Get the visitor's country.
		$country = get_visitor_geo_country();

		// Return the matching rule.
		return rest_ensure_response( get_office_phone_number( $country ) );
	}
}
