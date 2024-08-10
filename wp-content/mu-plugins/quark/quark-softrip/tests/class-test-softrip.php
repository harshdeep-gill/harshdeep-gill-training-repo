<?php
/**
 * Softrip test suite.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Error;

use function Quark\Softrip\cron_add_schedule;
use function Quark\Softrip\cron_is_scheduled;
use function Quark\Softrip\cron_schedule_sync;
use function Quark\Softrip\get_custom_db_table_mapping;
use function Quark\Softrip\synchronize_itinerary_departures;
use function Quark\Softrip\AdventureOptions\get_table_sql as get_adventure_options_table_sql;
use function Quark\Softrip\Occupancies\get_table_sql as get_occupancies_table_sql;
use function Quark\Softrip\Promotions\get_table_sql as get_promotions_table_sql;
use function Quark\Softrip\OccupancyPromotions\get_table_sql as get_occupancy_promotions_table_sql;
use function Quark\Softrip\AdventureOptions\get_table_name as get_adventure_options_table_name;
use function Quark\Softrip\create_custom_db_tables;
use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\is_expired;
use function Quark\Softrip\Occupancies\get_table_name as get_occupancies_table_name;
use function Quark\Softrip\OccupancyPromotions\get_table_name as get_occupancy_promotions_table_name;
use function Quark\Softrip\prefix_table_name;
use function Quark\Softrip\Promotions\get_table_name as get_promotions_table_name;
use function Quark\Tests\Softrip\drop_softrip_db_tables;

use const Quark\Softrip\ITINERARY_SYNC_BATCH_SIZE;
use const Quark\Softrip\SCHEDULE_HOOK;
use const Quark\Softrip\SCHEDULE_RECURRENCE;
use const Quark\Softrip\TABLE_PREFIX_NAME;

/**
 * Class Test Softrip.
 */
class Test_Softrip extends Softrip_TestCase {
	/**
	 * Test for getting custom db table creation mapping.
	 *
	 * @covers \Quark\Softrip\get_custom_db_table_mapping()
	 *
	 * @return void
	 */
	public function test_get_custom_db_table_mapping(): void {
		// Test case 1: Test if custom db table mapping is returned.
		$mapping = get_custom_db_table_mapping();
		$this->assertIsArray( $mapping );

		// Check for adventure options table.
		$adventure_option_table_name = get_adventure_options_table_name();
		$this->assertArrayHasKey( $adventure_option_table_name, $mapping );
		$this->assertIsString( $mapping[ $adventure_option_table_name ] );
		$this->assertStringStartsWith( get_adventure_options_table_sql(), $mapping[ $adventure_option_table_name ] );

		// Check for promotions table.
		$promotions_table_name = get_promotions_table_name();
		$this->assertArrayHasKey( $promotions_table_name, $mapping );
		$this->assertIsString( $mapping[ $promotions_table_name ] );
		$this->assertStringStartsWith( get_promotions_table_sql(), $mapping[ $promotions_table_name ] );

		// Check for occupancies table.
		$occupancies_table_name = get_occupancies_table_name();
		$this->assertArrayHasKey( $occupancies_table_name, $mapping );
		$this->assertIsString( $mapping[ $occupancies_table_name ] );
		$this->assertStringStartsWith( get_occupancies_table_sql(), $mapping[ $occupancies_table_name ] );

		// Check for occupancy promotions table.
		$occupancy_promotions_table_name = get_occupancy_promotions_table_name();
		$this->assertArrayHasKey( $occupancy_promotions_table_name, $mapping );
		$this->assertIsString( $mapping[ $occupancy_promotions_table_name ] );
		$this->assertStringStartsWith( get_occupancy_promotions_table_sql(), $mapping[ $occupancy_promotions_table_name ] );
	}

	/**
	 * Test for creating DB table.
	 *
	 * @covers \Quark\Softrip\create_custom_db_tables()
	 *
	 * @return void
	 */
	public function test_create_custom_db_tables(): void {
		// Remove existing custom DB tables.
		drop_softrip_db_tables();

		// Get wpdb.
		global $wpdb;

		// Get all tables.
		$tables = $wpdb->get_results( 'SHOW TABLES', ARRAY_N );
		$this->assertIsArray( $tables );

		// Flatten the array.
		$tables = array_map( 'current', $tables );

		// Initialize table names.
		$adventure_option_table_name     = get_adventure_options_table_name();
		$promotions_table_name           = get_promotions_table_name();
		$occupancies_table_name          = get_occupancies_table_name();
		$occupancy_promotions_table_name = get_occupancy_promotions_table_name();

		// Check if tables are present.
		$this->assertContains( $adventure_option_table_name, $tables );
		$this->assertContains( $promotions_table_name, $tables );
		$this->assertContains( $occupancies_table_name, $tables );
		$this->assertContains( $occupancy_promotions_table_name, $tables );

		// Create the tables again.
		create_custom_db_tables();
	}

	/**
	 * Test case for synchronizing departure from middleware.
	 *
	 * @covers \Quark\Softrip\synchronize_itinerary_departures()
	 *
	 * @return void
	 */
	public function test_synchronize_itinerary_departures(): void {
		// Test case 1: Test if request fails.
		$result = synchronize_itinerary_departures( [ 'ABC-123' ] );
		$this->assertTrue( $result instanceof WP_Error );
		$this->assertSame( 'http_request_failed', $result->get_error_code() );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Test case 2: No argument passed.
		$result = synchronize_itinerary_departures();
		$this->assertTrue( $result instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_no_codes', $result->get_error_code() );
		$this->assertSame( 'No Softrip codes provided', $result->get_error_message() );

		// Test case 3: Empty array passed.
		$result = synchronize_itinerary_departures( [] );
		$this->assertTrue( $result instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_no_codes', $result->get_error_code() );
		$this->assertSame( 'No Softrip codes provided', $result->get_error_message() );

		// Test case 4: Test code array with more than the limit - ITINERARY_SYNC_BATCH_SIZE.
		$test_codes = [
			'ABC-123',
			'DEF-456',
			'GHI-789',
			'JKL-012',
			'MNO-345',
			'PQR-678',
		];
		$result     = synchronize_itinerary_departures( $test_codes );
		$this->assertTrue( $result instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_departures_limit', $result->get_error_code() );
		$this->assertSame( 'The maximum number of codes allowed is ' . ITINERARY_SYNC_BATCH_SIZE, $result->get_error_message() );

		// Test case 5: Test code array with one element.
		$test_codes = [ 'ABC-123' ];
		$result     = synchronize_itinerary_departures( $test_codes );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'ABC-123', $result );

		// Test case 6: Test code array with five elements with only a few valid.
		$test_codes = [
			'ABC-123',
			'DEF-456',
			'GHI-789',
			'JKL-012',
			'MNO-345',
		];
		$result     = synchronize_itinerary_departures( $test_codes );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'ABC-123', $result );
		$this->assertIsArray( $result['ABC-123'] );
		$this->assertArrayHasKey( 'departures', $result['ABC-123'] );
		$this->assertNotEmpty( $result['ABC-123']['departures'] );

		// Check for DEF-456.
		$this->assertArrayHasKey( 'DEF-456', $result ); // Invalid code.
		$this->assertIsArray( $result['DEF-456'] );
		$this->assertArrayHasKey( 'departures', $result['DEF-456'] );
		$this->assertEmpty( $result['DEF-456']['departures'] );

		// Check for GHI-789.
		$this->assertArrayHasKey( 'GHI-789', $result ); // Invalid code.
		$this->assertIsArray( $result['GHI-789'] );
		$this->assertArrayHasKey( 'departures', $result['GHI-789'] );
		$this->assertEmpty( $result['GHI-789']['departures'] );

		// Check for JKL-012.
		$this->assertArrayHasKey( 'JKL-012', $result );
		$this->assertIsArray( $result['JKL-012'] );
		$this->assertArrayHasKey( 'departures', $result['JKL-012'] );
		$this->assertNotEmpty( $result['JKL-012']['departures'] );

		// Check for MNO-345.
		$this->assertArrayHasKey( 'MNO-345', $result ); // Invalid code.
		$this->assertIsArray( $result['MNO-345'] );
		$this->assertArrayHasKey( 'departures', $result['MNO-345'] );
		$this->assertEmpty( $result['MNO-345']['departures'] );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
	}

	/**
	 * Test case for adding custom schedule.
	 *
	 * @covers \Quark\Softrip\cron_add_schedule()
	 *
	 * @return void
	 */
	public function test_cron_add_schedule(): void {
		// Test case 1: Test adding custom schedule.
		$schedules = cron_add_schedule();
		$this->assertIsArray( $schedules );
		$this->assertArrayHasKey( SCHEDULE_RECURRENCE, $schedules );
		$this->assertArrayHasKey( 'interval', $schedules[ SCHEDULE_RECURRENCE ] );
		$this->assertArrayHasKey( 'display', $schedules[ SCHEDULE_RECURRENCE ] );
		$this->assertSame( 'Once every 4 hours', $schedules[ SCHEDULE_RECURRENCE ]['display'] );

		// Test case 2: Test if custom schedule added via hook.
		$schedules = apply_filters( 'cron_schedules', [] );
		$this->assertIsArray( $schedules );
		$this->assertArrayHasKey( SCHEDULE_RECURRENCE, $schedules );
		$this->assertArrayHasKey( 'interval', $schedules[ SCHEDULE_RECURRENCE ] );
		$this->assertArrayHasKey( 'display', $schedules[ SCHEDULE_RECURRENCE ] );
		$this->assertSame( 'Once every 4 hours', $schedules[ SCHEDULE_RECURRENCE ]['display'] );
	}

	/**
	 * Test for cron_is_scheduled.
	 *
	 * @covers \Quark\Softrip\cron_is_scheduled()
	 *
	 * @return void
	 */
	public function test_cron_is_scheduled(): void {
		// Clear any existing scheduled event for testing.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );

		// Test case 1: Test if cron is not scheduled.
		$result = cron_is_scheduled();
		$this->assertFalse( $result );

		// Test case 2: Test if cron is scheduled.
		cron_schedule_sync();
		$result = cron_is_scheduled();
		$this->assertTrue( $result );
	}

	/**
	 * Test for scheduling sync cron task.
	 *
	 * @covers \Quark\Softrip\cron_schedule_sync()
	 *
	 * @return void
	 */
	public function test_cron_schedule_sync(): void {
		// Clear any existing scheduled event for testing.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );
		$timestamp = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertFalse( $timestamp );

		// Test case 1: Test scheduling sync cron task.
		cron_schedule_sync();
		$timestamp = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertNotFalse( $timestamp );

		// Verify the schedule interval.
		$schedule = wp_get_schedule( SCHEDULE_HOOK );
		$this->assertSame( SCHEDULE_RECURRENCE, $schedule );

		// Test case 2: Test if repeated call should not schedule again.
		cron_schedule_sync();
		$timestamp2 = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertNotFalse( $timestamp2 );
		$this->assertSame( $timestamp, $timestamp2 );

		// Cleanup.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );
	}

	/**
	 * Test is expired.
	 *
	 * @covers \Quark\Softrip\is_expired()
	 *
	 * @return void
	 */
	public function test_is_expired(): void {
		// Test with empty arg.
		$result = is_expired();
		$this->assertFalse( $result );

		// Test with invalid arg.
		$result = is_expired( 'abc' );
		$this->assertFalse( $result );

		// Past date.
		$past_date = gmdate( 'Y-m-d', strtotime( '-1 day' ) );

		// Test with past date.
		$result = is_expired( $past_date );
		$this->assertTrue( $result );

		// Future date.
		$future_date = gmdate( 'Y-m-d', strtotime( '+1 day' ) );

		// Test with future date.
		$result = is_expired( $future_date );
		$this->assertFalse( $result );
	}

	/**
	 * Test prefix table name.
	 *
	 * @covers \Quark\Softrip\prefix_table_name()
	 *
	 * @return void
	 */
	public function test_prefix_table_name(): void {
		// Test with empty arg.
		$result = prefix_table_name();
		$this->assertEmpty( $result );

		// Test with invalid arg.
		$result = prefix_table_name( 'abc' );
		$this->assertSame( TABLE_PREFIX_NAME . 'abc', $result );
	}

	/**
	 * Test get engine collate.
	 *
	 * @covers \Quark\Softrip\get_engine_collate()
	 *
	 * @return void
	 */
	public function test_get_engine_collate(): void {
		// Get charset collate.
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// Set the engine and collate.
		$engine_collate = 'ENGINE=InnoDB';

		// If the charset_collate is not empty, add it to the engine_collate.
		if ( ! empty( $charset_collate ) ) {
			$engine_collate .= " $charset_collate";
		}

		// Test get engine collate.
		$result = get_engine_collate();
		$this->assertSame( $engine_collate, $result );
	}
}
