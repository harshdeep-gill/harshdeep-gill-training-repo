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
use function Quark\Leads\process_job_application_input_data;

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
				'leads_api_endpoint' => get_rest_url( null, '/' . REST_API_NAMESPACE . '/leads/create' ),
			],
			front_end_data( [] )
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
		// Validate recaptcha token.
		$this->assertInstanceOf( WP_Error::class, validate_recaptcha_token() );
		$this->assertInstanceOf( WP_Error::class, validate_recaptcha_token( 'dummy_token' ) );

		// Allow recaptcha to fail for testing.
		update_option( 'options_allow_recaptcha_to_fail', true );

		// It will bypass the recaptcha validation.
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
	 * Test to process job application input data.
	 *
	 * @covers \Quark\Leads\process_job_application_input_data()
	 *
	 * @return void
	 */
	public function test_process_job_application_input_data(): void {
		// Initialize variables.
		$salesforce_object = 'WebForm_Job_Application__c';
		$fields            = [
			'UTM_Campaign__c'                        => '',
			'UTM_Content__c'                         => '',
			'UTM_Medium__c'                          => '',
			'UTM_Source__c'                          => '',
			'UTM_Term__c'                            => '',
			'GCLID__c'                               => '',
			'FBBID__c'                               => '',
			'FBCLID__c'                              => '',
			'MSCLID__c'                              => '',
			'Webform_URL__c'                         => '',
			'Job_Type__c'                            => '',
			'FirstName__c'                           => 'John',
			'LastName__c'                            => 'Doe',
			'Email__c'                               => 'johndoe@travelopia.com',
			'Phone__c'                               => '1234567890',
			'Address1__c'                            => '123 Main St',
			'City__c'                                => 'Los Angeles',
			'Postal_Code__c'                         => '90001',
			'Country_Code__c'                        => 'US',
			'State_Code__c'                          => 'CA',
			'Was_a_Passenger__c'                     => 'Yes',
			'Has_Worked_on_Cruise_Line_or_Vessel__c' => 'Yes',
			'Has_Worked_in_Polar_Regions_Before__c'  => 'Yes',
			'Expedition_Team_Roles__c'               => 'Naturalist',
			'Languages__c'                           => 'English',
			'Work_Areas__c'                          => 'Deck',
			'Certifications__c'                      => 'First Aid',
			'Degree_Areas__c'                        => 'Biology',
			'Season_Availability__c'                 => 'Antarctica',
			'Maximum_Contract_Length__c'             => '6_weeks',
			'Was_Referred__c'                        => 'Yes',
			'Referrer_Name__c'                       => 'Jane Doe',
		];

		// Process data.
		$processed_data = process_job_application_input_data( $fields, $salesforce_object );

		// Assert data.
		$this->assertArrayHasKey( 'WebForm_Submission_ID__c', $processed_data );
		$this->assertArrayNotHasKey( 'Webform_URL__c', $processed_data );
		$this->assertArrayNotHasKey( 'UTM_Campaign__c', $processed_data );
		$this->assertArrayNotHasKey( 'UTM_Content__c', $processed_data );
		$this->assertArrayNotHasKey( 'UTM_Medium__c', $processed_data );
		$this->assertArrayNotHasKey( 'UTM_Source__c', $processed_data );
		$this->assertArrayNotHasKey( 'UTM_Term__c', $processed_data );
		$this->assertArrayNotHasKey( 'GCLID__c', $processed_data );
		$this->assertArrayNotHasKey( 'FBBID__c', $processed_data );
		$this->assertArrayNotHasKey( 'FBCLID__c', $processed_data );
		$this->assertArrayNotHasKey( 'MSCLID__c', $processed_data );
		$this->assertArrayNotHasKey( 'Job_Type__c', $processed_data );
		$this->assertArrayHasKey( 'State_Province__c', $processed_data );
		$this->assertArrayNotHasKey( 'State_Code__c', $processed_data );
		$this->assertEquals( 'CA', $processed_data['State_Province__c'] );
		$this->assertEquals( 'John', $processed_data['FirstName__c'] );
		$this->assertEquals( 'Doe', $processed_data['LastName__c'] );
		$this->assertEquals( 'johndoe@travelopia.com', $processed_data['Email__c'] );
		$this->assertEquals( '1234567890', $processed_data['Phone__c'] );
		$this->assertEquals( '123 Main St', $processed_data['Address1__c'] );
		$this->assertEquals( 'Los Angeles', $processed_data['City__c'] );
		$this->assertEquals( '90001', $processed_data['Postal_Code__c'] );
		$this->assertEquals( 'US', $processed_data['Country_Code__c'] );
		$this->assertEquals( 'Yes', $processed_data['Was_a_Passenger__c'] );
		$this->assertEquals( 'Yes', $processed_data['Has_Worked_on_Cruise_Line_or_Vessel__c'] );
		$this->assertEquals( 'Yes', $processed_data['Has_Worked_in_Polar_Regions_Before__c'] );
		$this->assertEquals( 'Naturalist', $processed_data['Expedition_Team_Roles__c'] );
		$this->assertEquals( 'English', $processed_data['Languages__c'] );
		$this->assertEquals( 'Deck', $processed_data['Work_Areas__c'] );
		$this->assertEquals( 'First Aid', $processed_data['Certifications__c'] );
		$this->assertEquals( 'Biology', $processed_data['Degree_Areas__c'] );
		$this->assertEquals( 'Antarctica', $processed_data['Season_Availability__c'] );
		$this->assertEquals( '6_weeks', $processed_data['Maximum_Contract_Length__c'] );
		$this->assertEquals( 'Yes', $processed_data['Was_Referred__c'] );
		$this->assertEquals( 'Jane Doe', $processed_data['Referrer_Name__c'] );
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
