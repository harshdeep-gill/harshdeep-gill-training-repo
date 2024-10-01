<?php
/**
 * Namespace functions.
 *
 * @package quark-leads
 */

namespace Quark\Leads;

use WP_Error;

use function Quark\Core\doing_automated_test;
use function Travelopia\Salesforce\send_request;
use function Travelopia\Security\validate_recaptcha;
use function Travelopia\Security\get_recaptcha_settings;

const REST_API_NAMESPACE = 'quark-leads/v1';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Front-end data.
	add_action( 'quark_front_end_data', __NAMESPACE__ . '\\front_end_data' );

	// Rest API.
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_endpoints' );
	add_filter( 'travelopia_security_public_rest_api_routes', __NAMESPACE__ . '\\security_public_rest_api_routes' );

	// Admin stuff.
	add_action( 'admin_menu', __NAMESPACE__ . '\\setup_settings' );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/leads.php';
	}
}

/**
 * Front-end data.
 *
 * @param mixed[] $data Front-end data.
 *
 * @return mixed[]
 */
function front_end_data( array $data = [] ): array {
	// Add endpoint.
	$data['leads_api_endpoint'] = get_rest_url( null, '/' . REST_API_NAMESPACE . '/leads/create' );

	// Get $validate_recaptcha.
	$validate_recaptcha = absint( get_option( 'options_validate_recaptcha', 1 ) );

	// If validate_recaptcha is true, and function exists 'get_recaptcha_settings', get recaptcha_settings.
	if (
		1 === $validate_recaptcha
		&& function_exists( 'Travelopia\Security\get_recaptcha_settings' )
		&& ! doing_automated_test()
	) {
		$recaptcha_settings = get_recaptcha_settings();

		// If 'site_key' is not empty set the recaptcha_site_key.
		if ( ! empty( $recaptcha_settings['site_key'] ) ) {
			$data['recaptcha_site_key'] = $recaptcha_settings['site_key'];
		}
	}

	// Front-end data.
	return $data;
}

/**
 * Register REST API endpoints.
 *
 * @return void
 */
function register_endpoints(): void {
	// Include class-lead file.
	require_once __DIR__ . '/rest-api/class-lead.php';

	// Build endpoints.
	$endpoints = [
		new RestApi\Lead(),
	];

	// Loop through endpoints and register routes.
	foreach ( $endpoints as $endpoint ) {
		$endpoint->register_routes();
	}
}

/**
 * Register public REST API routes.
 *
 * @param string[] $routes Public routes.
 *
 * @return string[]
 */
function security_public_rest_api_routes( array $routes = [] ): array {
	// Add routes.
	$routes[] = '/' . REST_API_NAMESPACE . '/leads/create';

	// Return routes.
	return $routes;
}

/**
 * Site settings.
 *
 * @return void
 */
function setup_settings(): void {
	// If 'acf_add_options_page' does not exist, return.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		// Early return.
		return;
	}

	// Settings.
	acf_add_options_sub_page(
		[
			'page_title'  => 'Leads',
			'menu_title'  => 'Leads',
			'parent_slug' => 'site-settings',
		]
	);
}

/**
 * Create lead.
 *
 * @param mixed[] $lead_data Request data.
 *
 * @return WP_Error|mixed[]
 */
function create_lead( array $lead_data = [] ): array|WP_Error {
	// Get lead data.
	$lead_data = wp_parse_args(
		$lead_data,
		[
			'recaptcha'         => [],
			'salesforce_object' => '',
			'fields'            => [],
		]
	);

	// Validate data.
	if (
		$lead_data['recaptcha'] instanceof WP_Error
		|| empty( $lead_data['salesforce_object'] )
		|| empty( $lead_data['fields'] )
	) {
		do_action( 'quark_leads_invalid_data' );

		// Return an error.
		return new WP_Error( 'quark_leads_invalid_data', 'Invalid data for leads.' );
	}

	// Build request URL.
	$request_url = build_salesforce_request_uri( $lead_data['salesforce_object'] );

	// Build request data.
	$request_data = build_salesforce_request_data( $lead_data['fields'], $lead_data['salesforce_object'] );

	// Send data to Salesforce.
	$response = send_request( $request_url, $request_data );

	// Check for valid response.
	if ( $response instanceof WP_Error ) {
		// Only show error on non-production environments.
		if ( 'production' !== wp_get_environment_type() ) {
			$error = $response;
		} else {
			$error = $response->get_error_message();
		}

		// Return an error.
		return new WP_Error( 'quark_leads_salesforce_error', 'Salesforce error.', $error );
	}

	// Return response.
	return $response;
}

/**
 * Build Salesforce request URI.
 *
 * @param string $salesforce_object Salesforce object name.
 *
 * @return string
 */
function build_salesforce_request_uri( string $salesforce_object = '' ): string {
	// Build request URI based on object name.
	return sprintf( '/services/data/v51.0/sobjects/%s/', $salesforce_object );
}

/**
 * Build Salesforce request data from fields.
 *
 * @param mixed[] $fields            Fields.
 * @param string  $salesforce_object Salesforce object name.
 *
 * @return mixed[]
 *
 * @note This function is just a wrapper function for now,
 *       but can be used to build a more complicated request in the future,
 *       like composite requests, etc.
 */
function build_salesforce_request_data( array $fields = [], string $salesforce_object = '' ): array {
	// Add WebForm_Submission_ID__c field.
	$fields['WebForm_Submission_ID__c'] = uniqid( strval( time() ), true );

	// The fields are the only data required in the request.
	return (array) apply_filters(
		'quark_leads_input_data',
		$fields,
		$salesforce_object
	);
}

/**
 * Validate reCaptcha token.
 *
 * @param string $recaptcha_token reCaptcha Token.
 *
 * @return true|float|WP_Error
 */
function validate_recaptcha_token( string $recaptcha_token = '' ): true|float|WP_Error {
	// Validate reCAPTCHA.
	$validate_recaptcha = absint( get_option( 'options_validate_recaptcha', 1 ) );

	// If 'validate_recaptcha' is not true or 'validate_recaptcha' function does not exist then bail out.
	if ( 1 !== $validate_recaptcha || ! function_exists( 'Travelopia\Security\validate_recaptcha' ) ) {
		return true;
	}

	// Ignore if we are currently in an automated test.
	if ( doing_automated_test() ) {
		return true;
	}

	// Get the value of 'allow_recaptcha_to_fail' from options table.
	$allow_recaptcha_to_fail = absint( get_option( 'options_allow_recaptcha_to_fail', 0 ) );

	// Validate reCAPTCHA if 'allow_recaptcha_to_fail' is zero.
	// Where: 0 = No, 1 = Yes.
	if ( 0 === $allow_recaptcha_to_fail ) {
		// Validate recaptcha.
		$recaptcha_validation = validate_recaptcha( $recaptcha_token, 'leads' );

		// Handle errors.
		if ( $recaptcha_validation instanceof WP_Error ) {
			// If 'allow_recaptcha_to_fail' is zero, return error.
			return new WP_Error( 'quark_leads_recaptcha_failed', 'reCAPTCHA validation failed' );
		}

		// Check if the score is set in the validation response.
		if ( isset( $recaptcha_validation['score'] ) ) {
			return floatval( $recaptcha_validation['score'] );
		}
	}

	// Return true when allowing reCaptcha to fail or reCaptcha score is not available.
	return true;
}
