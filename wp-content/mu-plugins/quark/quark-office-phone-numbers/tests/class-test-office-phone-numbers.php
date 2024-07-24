<?php
/**
 * Office Phone Numbers test suite.
 *
 * @package quark-office-phone-numbers
 */

namespace Quark\PhoneNumbers\Tests;

use WP_UnitTestCase;

use function Quark\OfficePhoneNumbers\office_phone_number_front_end_data;
use function Quark\OfficePhoneNumbers\get_corporate_office_phone_number;
use function Quark\OfficePhoneNumbers\get_office_phone_number;
use function Quark\OfficePhoneNumbers\get_local_office_data;
use function Quark\OfficePhoneNumbers\security_public_rest_api_routes;

/**
 * Class Test_Office_Phone_Numbers.
 */
class Test_Office_Phone_Numbers extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\OfficePhoneNumbers\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_action( 'acf/options_page/save', 'Quark\OfficePhoneNumbers\purge_local_office_data_cache' ) );
		$this->assertEquals( 10, has_action( 'admin_menu', 'Quark\OfficePhoneNumbers\setup_phone_number_settings' ) );
		$this->assertEquals( 10, has_action( 'rest_api_init', 'Quark\OfficePhoneNumbers\register_rest_endpoints' ) );
		$this->assertEquals( 10, has_filter( 'travelopia_security_public_rest_api_routes', 'Quark\OfficePhoneNumbers\security_public_rest_api_routes' ) );
	}

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\OfficePhoneNumbers\get_local_office_data()
	 *
	 * @return void
	 */
	public function test_core_front_end_data(): void {
		// Prepare data.
		update_option( 'options_country', '2' );
		update_option( 'options_country_0_name', 'Office 1' );
		update_option( 'options_country_0_phone_number_prefix', '+1' );
		update_option( 'options_country_0_phone_number', '18007654321' );
		update_option(
			'options_country_0_coverage',
			[
				'US',
				'CA',
				'WI',
			]
		);
		update_option( 'options_country_1_name', 'Office 2' );
		update_option( 'options_country_1_phone_number_prefix', '+12' );
		update_option( 'options_country_1_phone_number', '18001234566' );

		// Prepare expected data.
		$expected_data = [
			[
				'name'                => 'Office 1',
				'phone'               => '18007654321',
				'phone_number_prefix' => '+1',
				'coverage'            => [
					'US',
					'CA',
					'WI',
				],
				'is_corporate_office' => false,
			],
			[
				'name'                => 'Office 2',
				'phone'               => '18001234566',
				'phone_number_prefix' => '+12',
				'coverage'            => [],
				'is_corporate_office' => false,
			],
		];

		// Get data.
		$data = get_local_office_data();

		// Test data.
		$this->assertEquals( $expected_data, $data );

		// clean up.
		delete_option( 'options_country' );
		delete_option( 'options_country_0_name' );
		delete_option( 'options_country_0_phone_number_prefix' );
		delete_option( 'options_country_0_phone_number' );
		delete_option( 'options_country_0_coverage' );
		delete_option( 'options_country_1_name' );
		delete_option( 'options_country_1_phone_number_prefix' );
		delete_option( 'options_country_1_phone_number' );
	}

	/**
	 * Test getting front-end data with no data.
	 *
	 * @covers \Quark\OfficePhoneNumbers\office_phone_number_front_end_data()
	 *
	 * @return void
	 */
	public function test_office_phone_number_front_end_data(): void {
		// Get data.
		$data = office_phone_number_front_end_data();

		// Prepare expected data.
		$expected_data = [
			'api_endpoint' => 'http://test.quarkexpeditions.com/wp-json/qrk-phone-numbers/v1/phone-number/get',
		];

		// Test data.
		$this->assertEquals( $expected_data, $data['dynamic_phone_number'] );
	}

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\OfficePhoneNumbers\get_corporate_office_phone_number()
	 *
	 * @return void
	 */
	public function test_get_corporate_office_phone_number(): void {
		// Prepare data.
		update_option( 'options_country', '2' );
		update_option( 'options_country_0_name', 'Office 1' );
		update_option( 'options_country_0_phone_number', '+11800123456' );
		update_option( 'options_country_0_phone_number_prefix', 'Call Us To Book' );
		update_option(
			'options_country_0_coverage',
			[
				'US',
				'CA',
			]
		);
		update_option( 'options_country_1_name', 'Office 2' );
		update_option( 'options_country_1_phone_number_prefix', 'Call Us' );
		update_option( 'options_country_1_phone_number', '+12 18001234566' );
		update_option( 'options_country_1_is_corporate_office', '1' );
		update_option(
			'options_country_1_coverage',
			[
				'WI',
				'IN',
			]
		);

		// Prepare expected data.
		$expected_data = [
			'phone_number' => '+12 18001234566',
			'prefix'       => 'Call Us',
		];

		// Get data.
		$data = get_corporate_office_phone_number();

		// Test data.
		$this->assertEquals( $expected_data, $data );

		// Get Office phone number by Country code.
		$data = get_office_phone_number( 'ca' );

		// Prepare expected data.
		$expected_data = [
			'phone'  => '+11800123456',
			'prefix' => 'Call Us To Book',
		];

		// Test data.
		$this->assertEquals( $expected_data, $data );

		// clean up.
		delete_option( 'options_country' );
		delete_option( 'options_country_0_name' );
		delete_option( 'options_country_0_phone_number_prefix' );
		delete_option( 'options_country_0_phone_number' );
		delete_option( 'options_country_0_coverage' );
		delete_option( 'options_country_1_name' );
		delete_option( 'options_country_1_phone_number_prefix' );
		delete_option( 'options_country_1_phone_number' );
		delete_option( 'options_country_1_is_corporate_office' );
		delete_option( 'options_country_1_coverage' );
	}

	/**
	 * Test API Whitelist routes.
	 *
	 * @covers \Quark\OfficePhoneNumbers\security_public_rest_api_routes()
	 *
	 * @return void
	 */
	public function test_security_public_rest_api_routes(): void {
		// Prepare data.
		$routes = [];

		// Get data.
		$data = security_public_rest_api_routes( $routes );

		// Prepare expected data.
		$expected_data = [
			'/qrk-phone-numbers/v1/phone-number/get',
		];

		// Test data.
		$this->assertEquals( $expected_data, $data );
	}
}
