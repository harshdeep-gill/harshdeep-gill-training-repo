<?php
/**
 * Landing Pages test suite.
 *
 * @package quark-landing-pages
 */

namespace Quark\Leads\Tests;

use WP_UnitTestCase;
use WP_Error;

use function Quark\Leads\front_end_data;
use function Quark\Leads\validate_recaptcha_token;
use function Quark\Leads\create_lead;
use function Quark\Leads\security_public_rest_api_routes;
use function Quark\Leads\build_salesforce_request_uri;
use function Quark\Leads\build_salesforce_request_data;
use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

use const Quark\Leads\REST_API_NAMESPACE;

/**
 * Class Test_Leads.
 */
class Test_Leads extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Leads\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Assert if functions exists.
		$this->assertTrue( function_exists( 'Quark\Leads\bootstrap' ) );

		// Test hooks.
		$this->assertEquals( 10, has_action( 'quark_front_end_data', 'Quark\Leads\front_end_data' ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', 'Quark\Leads\register_endpoints' ) );
		$this->assertEquals( 10, has_filter( 'travelopia_security_public_rest_api_routes', 'Quark\Leads\security_public_rest_api_routes' ) );
		$this->assertEquals( 10, has_action( 'admin_menu', 'Quark\Leads\setup_settings' ) );
	}

	/**
	 * Test Front End Data.
	 *
	 * @covers \Quark\Leads\front_end_data()
	 *
	 * @return void
	 */
	public function test_front_end_data(): void {
		// Test 1: Check if function return api endpoint or not.
		$this->assertEquals(
			[
				'data' => [
					'leads_api_endpoint' => get_rest_url( null, '/' . REST_API_NAMESPACE . '/leads/create' ),
				],
			],
			front_end_data( [ 'data' => [] ] )
		);
	}

	/**
	 * Test Front End Data.
	 *
	 * @covers \Quark\Leads\security_public_rest_api_routes()
	 *
	 * @return void
	 */
	public function test_security_public_rest_api_routes(): void {
		// Test 1: Check if function return api endpoint or not.
		$this->assertEquals(
			[
				'/quark-leads/v1/leads/create',
			],
			security_public_rest_api_routes( [] )
		);
	}

	/**
	 * Test to validate recaptcha token.
	 *
	 * @covers \Quark\Leads\validate_recaptcha_token()
	 *
	 * @return void
	 */
	public function test_validate_recaptcha_token(): void {
		// Assert data.
		$this->assertTrue( validate_recaptcha_token() );
	}

	/**
	 * Test for create_lead function.
	 *
	 * @covers \Quark\Leads\create_lead()
	 *
	 * @return void
	 */
	public function test_create_lead(): void {
		// Argument data.
		$data = [
			'recaptcha'         => [ 'test_recaptcha_token' ],
			'salesforce_object' => 'test_saleseforce_object',
			'fields'            => [],
		];

		// Test 1: When field data is not passed.
		$this->assertEquals(
			new WP_Error( 'quark_leads_invalid_data', 'Invalid data for leads.' ),
			create_lead( $data )
		);

		/**
		 * Test 2: Check for successful submission.
		 */
		// Add field data.
		$data['fields'] = [
			'FirstName' => 'dummy',
			'LastName'  => 'test',
			'email'     => 'dummy@email.test',
		];
		wp_cache_set(
			'travelopia_salesforce_access_token',
			[
				'access_token' => 'access_token',
				'scope'        => 'scope',
				'instance_url' => 'instance_url',
				'id'           => 'id',
				'token_type'   => 'token_type',
			],
			'travelopia_salesforce_access_token'
		);

		// Mock request.
		$request_callback = function () {
			return [
				'headers'  => [
					'location' => 'https://www.example.com/success/',
				],
				'response' => [
					'code' => 201,
				],
				'body'     => wp_json_encode(
					[
						'scope'        => 'scope',
						'instance_url' => 'instance_url',
						'id'           => 'id',
					]
				),
			];
		};
		add_filter( 'pre_http_request', $request_callback );

		// Assert data.
		$this->assertEquals(
			[
				'scope'        => 'scope',
				'instance_url' => 'instance_url',
				'id'           => 'id',
			],
			create_lead( $data )
		);

		// Remove callback from filter.
		remove_filter( 'pre_http_request', $request_callback );

		/**
		 * Test 3: Failed submission.
		 */
		$request_callback = function () {
			return [
				'headers'  => [
					'location' => 'https://www.example.com/success/',
				],
				'response' => [
					'code' => 404,
				],
				'body'     => wp_json_encode(
					[
						'scope'        => 'scope',
						'instance_url' => 'instance_url',
						'id'           => 'id',
					]
				),
			];
		};
		add_filter( 'pre_http_request', $request_callback );
		$response = create_lead( $data );

		// Assert data.
		$this->assertInstanceOf( 'WP_Error', $response );
		$this->assertEquals( 'quark_leads_salesforce_error', $response->get_error_code() );
		$this->assertEquals( 'Salesforce error.', $response->get_error_message() );

		// Remove callback from filter.
		remove_filter( 'pre_http_request', $request_callback );
	}

	/**
	 * Test Salesforce Request URI.
	 *
	 * @covers \Quark\Leads\build_salesforce_request_uri()
	 *
	 * @return void
	 */
	public function test_build_salesforce_request_uri(): void {
		// Test 1: Check if function return api endpoint or not.
		$this->assertEquals(
			'/services/data/v51.0/sobjects/test_data/',
			build_salesforce_request_uri( 'test_data' )
		);
	}

	/**
	 * Test to build salesforce request data.
	 *
	 * @covers \Quark\Leads\build_salesforce_request_data()
	 *
	 * @return void
	 */
	public function test_build_salesforce_request_data(): void {
		// build dummy data.
		$data = [
			'FirstName' => 'dummy',
			'LastName'  => 'test',
		];

		// Assert data.
		$this->assertEquals( $data, build_salesforce_request_data( $data ) );

		// Test 2: Check if function respects filter or not.
		$request_callback = function () {
			return [
				'email' => 'dummy-email@test.com',
			];
		};
		add_filter( 'quark_leads_input_data', $request_callback );

		// Assert data.
		$this->assertNotEquals( $data, build_salesforce_request_data( $data ) );
		$this->assertArrayHasKey( 'email', build_salesforce_request_data( $data ) );

		// Remove callback from filter.
		remove_filter( 'quark_leads_input_data', $request_callback );
	}

	/**
	 * Test Country list for forms.
	 *
	 * @covers \Quark\Leads\Forms\get_countries()
	 *
	 * @return void
	 */
	public function test_forms_countries_list(): void {
		// Test the count of countries.
		$this->assertEquals( 250, count( get_countries() ) );
	}

	/**
	 * Test States list for forms.
	 *
	 * @covers \Quark\Leads\Forms\get_states()
	 *
	 * @return void
	 */
	public function test_forms_states_list(): void {
		// Top level array keys.
		$keys_to_test = [ 'AU', 'US', 'CA' ];

		// Get the states.
		$states = get_states();

		// Check for keys.
		foreach ( $keys_to_test as $key ) {
			$this->assertArrayHasKey( $key, $states );
		}
	}
}
