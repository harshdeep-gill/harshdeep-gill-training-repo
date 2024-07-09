<?php
/**
 * Office Phone Numbers test suite.
 *
 * @package quark-office-phone-numbers
 */

namespace Quark\PhoneNumbers\Tests;

use WP_UnitTestCase;

use function Quark\OfficePhoneNumbers\office_phone_number_front_end_data;

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
	}

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\OfficePhoneNumbers\get_local_office_data()
	 * @covers \Quark\OfficePhoneNumbers\office_phone_number_front_end_data()
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
			],
			[
				'name'                => 'Office 2',
				'phone'               => '18001234566',
				'phone_number_prefix' => '+12',
				'coverage'            => [],
			],
		];

		// Get data.
		$data = office_phone_number_front_end_data();

		// Test data.
		$this->assertEquals( $expected_data, $data['data']['office_phone_numbers'] );
	}
}
