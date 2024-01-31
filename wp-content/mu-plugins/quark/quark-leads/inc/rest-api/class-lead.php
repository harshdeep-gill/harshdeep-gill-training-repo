<?php
/**
 * Rest API: Lead.
 *
 * @package quark-leads
 */

namespace Quark\Leads\RestApi;

use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

use function Quark\Leads\create_lead;
use function Travelopia\Security\validate_recaptcha;

use const Quark\Leads\REST_API_NAMESPACE;

/**
 * Class Lead.
 */
class Lead extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected $namespace = REST_API_NAMESPACE . '/leads';

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Register rest route.
		register_rest_route(
			$this->namespace,
			'/create',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_item' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'recaptcha_token'   => [
						'required'          => true,
						'type'              => 'string',
						'default'           => '',
						'description'       => esc_html__( 'reCAPTCHA Token', 'tcs' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'salesforce_object' => [
						'required'          => true,
						'type'              => 'string',
						'default'           => '',
						'description'       => esc_html__( 'Salesforce Object', 'tcs' ),
						'sanitize_callback' => 'sanitize_text_field',
					],
					'fields'            => [
						'required'          => true,
						'type'              => 'object',
						'description'       => esc_html__( 'Form Fields', 'tcs' ),
						'validate_callback' => fn( $param ) => is_array( $param ) && ! empty( $param ),
					],
				],
			]
		);
	}

	/**
	 * Create a lead.
	 *
	 * @param WP_REST_Request $request Request data.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_item( $request ): WP_REST_Response|WP_Error { // phpcs:ignore
		// Prepare reCAPTCHA data.
		$recaptcha_data = [];

		// Do we need to validate reCAPTCHA?
		if ( apply_filters( 'travelopia_leads_check_recaptcha', true ) && function_exists( 'Travelopia\Security\validate_recaptcha' ) ) {
			// Validate reCAPTCHA.
			$recaptcha_validation = validate_recaptcha( strval( $request->get_param( 'recaptcha_token' ) ), 'leads' );

			// Check validation.
			if ( $recaptcha_validation instanceof WP_Error ) {
				// Validation failed, trigger an action.
				do_action( 'travelopia_leads_recaptcha_failed', $recaptcha_validation, $request );

				// Check if we need to allow validation to fail.
				// Ex: Maybe just send an email notification, but let the validation go through.
				if ( apply_filters( 'travelopia_leads_allow_recaptcha_fail', true ) ) {
					return $recaptcha_validation;
				}

				// Add error data to response.
				$recaptcha_data = $recaptcha_validation->get_error_data();
			} else {
				// Add validation body to response.
				$recaptcha_data = $recaptcha_validation;
			}
		}

		// Prepare lead data.
		$lead_data = [
			'recaptcha'         => $recaptcha_data,
			'salesforce_object' => $request->get_param( 'salesforce_object' ),
			'fields'            => (array) $request->get_param( 'fields' ),
		];

		// Create lead.
		$response = create_lead( $lead_data );

		// Return response, if there is an error.
		if ( $response instanceof WP_Error ) {
			return $response;
		}

		// Return response.
		return rest_ensure_response( $response );
	}
}
