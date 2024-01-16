<?php
/**
 * Landing Pages test suite.
 *
 * @package quark-landing-pages
 */

namespace Quark\Leads\Tests;

use WP_UnitTestCase;

use function Quark\Leads\front_end_data;
use function Quark\Leads\validate_recaptcha_token;

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
}
