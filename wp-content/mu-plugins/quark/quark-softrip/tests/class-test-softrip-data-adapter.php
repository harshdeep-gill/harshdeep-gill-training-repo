<?php
/**
 * Test suite for Softrip Data Adapter.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Softrip_Data_Adapter;
use WP_Error;
use WP_UnitTestCase;

/**
 * Test_Softrip_Data_Adapter class.
 */
class Test_Softrip_Data_Adapter extends WP_UnitTestCase {

	/**
	 * Class instance to use for the test.
	 *
	 * @var Softrip_Data_Adapter
	 */
	protected $instance;

	/**
	 * Set up the class which will be tested.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->instance = new Softrip_Data_Adapter();
	}

	/**
	 * Test case for do_request.
	 *
	 * @covers \Quark\Softrip\Softrip_Data_Adapter::do_request()
	 *
	 * @return void
	 */
	public function test_do_request(): void {
		// Test case 1: Check for API credentials.
		$this->assertTrue( defined( 'QUARK_SOFTRIP_ADAPTER_BASE_URL' ) );
		$this->assertTrue( defined( 'QUARK_SOFTRIP_ADAPTER_USERNAME' ) );
		$this->assertTrue( defined( 'QUARK_SOFTRIP_ADAPTER_PASSWORD' ) );
		$this->assertNotEmpty( QUARK_SOFTRIP_ADAPTER_BASE_URL );
		$this->assertNotEmpty( QUARK_SOFTRIP_ADAPTER_USERNAME );
		$this->assertNotEmpty( QUARK_SOFTRIP_ADAPTER_PASSWORD );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Test case 2: Passing no service or any param - should return WP_Error.
		$actual = $this->instance->do_request();
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertEquals( 'qrk_softrip_invalid_response', $actual->get_error_code() );

		// Test case 3: Passing invalid service - should return WP_Error.
		$actual = $this->instance->do_request( 'invalid-service' );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_invalid_response', $actual->get_error_code() );

		// Test case 4: Passing valid service but no departure code.
		$actual = $this->instance->do_request( 'departures' );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_invalid_response', $actual->get_error_code() );

		// Test case 5: Passing valid service but invalid departure code.
		$actual = $this->instance->do_request( 'departures', [ 'productCodes' => 'invalid-123' ] );
		$this->assertIsArray( $actual );
		$this->assertArrayHasKey( 'invalid-123', $actual );
		$this->assertIsArray( $actual['invalid-123'] );
		$this->assertArrayHasKey( 'departures', $actual['invalid-123'] );
		$this->assertEmpty( $actual['invalid-123']['departures'] );

		// Test case 6: Passing valid service and valid departure code.
		$actual = $this->instance->do_request( 'departures', [ 'productCodes' => 'ABC-123' ] );
		$this->assertIsArray( $actual );
		$this->assertArrayHasKey( 'ABC-123', $actual );
		$this->assertIsArray( $actual['ABC-123'] );
		$this->assertArrayHasKey( 'departures', $actual['ABC-123'] );
		$this->assertNotEmpty( $actual['ABC-123']['departures'] );

		// Test case 7: Passing valid service and multiple departure codes.
		$actual = $this->instance->do_request( 'departures', [ 'productCodes' => 'ABC-123,DEF-456,PQR-345' ] );
		$this->assertIsArray( $actual );
		$this->assertArrayHasKey( 'ABC-123', $actual );
		$this->assertArrayHasKey( 'DEF-456', $actual );
		$this->assertArrayHasKey( 'PQR-345', $actual );

		// Valid departure codes.
		$this->assertIsArray( $actual['ABC-123'] );
		$this->assertArrayHasKey( 'departures', $actual['ABC-123'] );
		$this->assertNotEmpty( $actual['ABC-123']['departures'] );

		// Invalid departure codes.
		$this->assertIsArray( $actual['DEF-456'] );
		$this->assertArrayHasKey( 'departures', $actual['DEF-456'] );
		$this->assertEmpty( $actual['DEF-456']['departures'] );

		// Test case 8: Passing valid service but more than 5 departure codes.
		$actual = $this->instance->do_request( 'departures', [ 'productCodes' => 'ABC-123,DEF-456,PQR-345,XYZ-789,LMN-456,OPQ-123' ] );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_invalid_response', $actual->get_error_code() );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );
	}
}
