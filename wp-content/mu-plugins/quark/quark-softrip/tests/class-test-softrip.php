<?php
/**
 * Softrip tests.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;
use WP_Error;

/**
 * Class Test_Softrip.
 */
class Test_Softrip extends WP_UnitTestCase {
	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		// Run parent and include setup.
		parent::set_up_before_class();
		include_once 'setup.php';

		// Mock the response for the POST request.
		add_filter( 'pre_http_request', 'Quark\Softrip\mock_http_request', 10, 3 );
	}

	/**
	 * Tear down after class.
	 *
	 * @return void
	 */
	public static function tear_down_after_class(): void {
		// Run parent.
		parent::tear_down_after_class();

		// Remove the filter.
		remove_filter( 'pre_http_request', 'Quark\Softrip\mock_http_request' );
	}

	/**
	 * Test for request_departures.
	 *
	 * @covers \Quark\Softrip\request_departures
	 * @covers \Quark\Softrip\Softrip_Data_Adapter::do_request
	 *
	 * @return void
	 */
	public function test_request_departures(): void {
		// Prepare data.
		$raw_data = [
			'ABC-123',
			'HIJ-567',
			'MNO-456',
			'QWE-567',
			'PQR-345',
			'XYZ-234',
		];

		// Request departures.
		$result = request_departures( $raw_data );

		// Check the result.
		$this->assertTrue( $result instanceof WP_Error );

		// Request departures.
		$result = request_departures( [ 'ABC-123' ] );

		// assert the result.
		$this->assertTrue( is_array( $result ) );
		$this->assertArrayHasKey( 'ABC-123', $result );

		// Request departures.
		$result = request_departures( [ 'ABC-123', 'PQR-345' ] );

		// assert the result.
		$this->assertTrue( is_array( $result ) );
		$this->assertArrayHasKey( 'ABC-123', $result );
		$this->assertArrayHasKey( 'PQR-345', $result );

		// Request departures.
		$result = request_departures( [ 'NO-DATA-123' ] );

		// assert the result.
		$this->assertTrue( $result instanceof WP_Error );
	}

	/**
	 * Test for get_departures.
	 *
	 * @covers \Quark\Softrip\get_db_tables
	 *
	 * @return void
	 */
	public function test_get_db_tables(): void {
		// Create an instance.
		$instance = new Softrip_DB();

		// Get the tables.
		$tables = $instance->get_db_tables();

		// Check the result.
		$this->assertTrue( 5 === count( $tables ) );
		$this->assertArrayHasKey( 'adventure_options', $tables );
		$this->assertArrayHasKey( 'cabin_categories', $tables );
		$this->assertArrayHasKey( 'occupancies', $tables );
		$this->assertArrayHasKey( 'occupancy_prices', $tables );
		$this->assertArrayHasKey( 'promos', $tables );
	}
}
