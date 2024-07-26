<?php
/**
 * Core test suite.
 *
 * @package quark-core
 */

namespace Quark\Core;

use WP_UnitTestCase;

use function Quark\Core\get_front_end_data;

/**
 * Class Test_Core.
 */
class Test_Core extends WP_UnitTestCase {

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\Core\get_front_end_data()
	 *
	 * @return void
	 */
	public function test_get_front_end_data(): void {
		// No data.
		$original_data = [
			'header'               => [
				'logo_url' => 'http://test.quarkexpeditions.com',
				'nav_menu' => "<div></div>\n",
			],
			'social_links'         => [
				'facebook'  => '',
				'twitter'   => '',
				'instagram' => '',
				'pinterest' => '',
				'youtube'   => '',
			],
			'leads_api_endpoint'   => 'http://test.quarkexpeditions.com/wp-json/quark-leads/v1/leads/create',
			'current_url'          => false,
			'dynamic_phone_number' => [
				'api_endpoint' => 'http://test.quarkexpeditions.com/wp-json/qrk-phone-numbers/v1/phone-number/get',
			],
		];

		// Test front-end data.
		$this->assertEquals(
			$original_data,
			\Quark\Core\get_front_end_data()
		);

		// Test layout and data.
		$test_data = [
			'key' => 'value',
		];
		add_filter( 'quark_front_end_data', fn () => $test_data );

		// Original data without force.
		$this->assertEquals(
			$original_data,
			\Quark\Core\get_front_end_data()
		);

		// Test data with force.
		$this->assertEquals(
			$test_data,
			\Quark\Core\get_front_end_data( true )
		);
	}

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\Core\get_front_end_data()
	 * @covers \Quark\Core\core_front_end_data()
	 *
	 * @return void
	 */
	public function test_core_front_end_data(): void {
		// Prepare data.
		update_option( 'options_facebook_url', 'https://facebook.com' );
		update_option( 'options_twitter_url', 'https://twitter.com' );
		update_option( 'options_instagram_url', 'https://instagram.com' );
		update_option( 'options_pinterest_url', 'https://pinterest.com' );
		update_option( 'options_youtube_url', 'https://youtube.com' );

		// Get data.
		$data = get_front_end_data( true );

		// Test data.
		$this->assertEquals(
			[
				'logo_url' => 'http://test.quarkexpeditions.com',
				'nav_menu' => "<div></div>\n",
			],
			$data['header']
		);

		// Assert expected social links and actual social links are equal.
		$this->assertEquals(
			[
				'facebook'  => 'https://facebook.com',
				'twitter'   => 'https://twitter.com',
				'instagram' => 'https://instagram.com',
				'pinterest' => 'https://pinterest.com',
				'youtube'   => 'https://youtube.com',
			],
			$data['social_links'] ?? []
		);
	}

	/**
	 * Test nav menus.
	 *
	 * @covers \Quark\Core\nav_menus()
	 *
	 * @return void
	 */
	public function test_nav_menus(): void {
		// Test navigation menus.
		$this->assertEquals(
			[
				'main' => 'Main Menu',
			],
			get_registered_nav_menus()
		);
	}

	/**
	 * Test doing automated tests.
	 *
	 * @covers \Quark\Core\doing_automated_test()
	 *
	 * @return void
	 */
	public function test_doing_automated_test(): void {
		// Test default state.
		$this->assertFalse( doing_automated_test() );

		// Define config.
		$_SERVER['HTTP_USER_AGENT'] = 'TEST_USER_AGENT';
		define( 'QUARK_AUTOMATED_TEST_USER_AGENT', 'TEST_USER_AGENT' );

		// Test user agent.
		$this->assertTrue( doing_automated_test() );

		// Clean up.
		$_SERVER['HTTP_USER_AGENT'] = '';
	}

	/**
	 * Test format_price.
	 *
	 * @covers \Quark\Core\format_price()
	 *
	 * @return void
	 */
	public function test_format_price(): void {
		// Test price formatting.
		$this->assertEquals(
			'$1,000 USD',
			format_price( 1000 )
		);

		// Test price formatting with custom currency.
		$this->assertEquals(
			'€10,000 EUR',
			format_price( 10000, 'eur' )
		);

		// Test price formatting with custom currency.
		$this->assertEquals(
			'£100,000 GBP',
			format_price( 100000, 'GBP' )
		);
	}
}
