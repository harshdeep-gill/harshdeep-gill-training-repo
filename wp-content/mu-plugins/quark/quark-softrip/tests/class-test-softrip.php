<?php
/**
 * Softrip test suite.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Departure;
use Quark\Softrip\Itinerary;
use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Error;
use WP_Post;

use function Quark\Softrip\cron_add_schedule;
use function Quark\Softrip\cron_is_scheduled;
use function Quark\Softrip\cron_schedule_sync;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\synchronize_itinerary_departures;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Softrip\ITINERARY_SYNC_BATCH_SIZE;
use const Quark\Softrip\SCHEDULE_HOOK;
use const Quark\Softrip\SCHEDULE_RECURRENCE;

/**
 * Class Test_Softrip.
 */
class Test_Softrip1 extends Softrip_TestCase {
	/**
	 * Test case for syncing itinerary departures with Softrip.
	 *
	 * @covers \Quark\Softrip\synchronize_itinerary_departures()
	 *
	 * @return void
	 */
	public function test_synchronize_itinerary_departures(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Test case 1: No argument passed - empty softrip codes.
		$result = synchronize_itinerary_departures();
		$this->assertTrue( $result instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_no_codes', $result->get_error_code() );
		$this->assertSame( 'No Softrip codes provided', $result->get_error_message() );

		// Test case 2: Empty array passed.
		$result = synchronize_itinerary_departures( [] );
		$this->assertTrue( $result instanceof WP_Error );
		$this->assertSame( 'qrk_softrip_no_codes', $result->get_error_code() );
		$this->assertSame( 'No Softrip codes provided', $result->get_error_message() );

		// Test case 3: Test code array with more than 5 elements.
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
		$this->assertSame( sprintf( 'The maximum number of codes allowed is %d', ITINERARY_SYNC_BATCH_SIZE ), $result->get_error_message() );

		// Test case 4: Test code array with one element.
		$test_codes = [ 'ABC-123' ];
		$result     = synchronize_itinerary_departures( $test_codes );
		$this->assertIsArray( $result );
		$this->assertArrayHasKey( 'ABC-123', $result );

		// Test case 5: Test code array with five elements with only a few valid.
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
	 * Test for do_sync.
	 *
	 * @covers \Quark\Softrip\do_sync
	 * @covers \Quark\Softrip\batch_request
	 * @covers \Quark\Softrip\Softrip_Sync::sync_softrip_code
	 * @covers \Quark\Softrip\Softrip_Sync::get_lowest_price
	 * @covers \Quark\Softrip\Softrip_Sync::get_starting_date
	 * @covers \Quark\Softrip\Softrip_Sync::get_ending_date
	 *
	 * @return void
	 */
	public function test_do_sync(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Do the sync.
		do_sync();

		// Assert Departures are created.
		$departures = get_posts(
			[
				'post_type'      => DEPARTURE_POST_TYPE,
				'posts_per_page' => -1,
			]
		);

		// Check the count.
		$this->assertCount( 3, $departures );

		// Check the $departures post title.
		$departure_titles = wp_list_pluck( $departures, 'post_title' );
		$expected_data    = [
			'ABC-123:2026-02-28',
			'JKL-012:2025-01-09',
			'JKL-012:2026-01-16',
		];

		// Check the departure titles.
		$this->assertEqualSets( $expected_data, $departure_titles );

		// Cabin data.
		$cabins      = [];
		$cabin_codes = [];

		// Loop through the departures and get the cabins.
		foreach ( $departures as $departure ) {
			$departure_object = new Departure();

			// Assert the departure object.
			$this->assertTrue( $departure instanceof WP_Post );

			// Load the departure.
			$departure_object->load( $departure->ID );
			$departure_cabins = $departure_object->get_cabins();
			$cabin_codes      = array_merge( $cabin_codes, array_keys( $departure_cabins ) );

			// merge the cabins without keys.
			$cabins = array_merge( $cabins, array_values( $departure_cabins ) );
		}

		// Check the count.
		$this->assertCount( 3, array_unique( $cabin_codes ) );

		// Get cabins keys.
		$expected_data = [
			'OEX-SGL',
			'ULT-SGL',
			'ULT-DBL',
		];

		// Assert the cabin keys.
		$this->assertEqualSets( $expected_data, array_unique( $cabin_codes ) );

		// Load Occupancies.
		$occupancies = [];

		// Loop through the cabins and get the occupancies.
		foreach ( $cabins as $cabin ) {
			$occupancies = array_merge( $occupancies, $cabin->get_occupancies() );
		}

		// Check the count.
		$this->assertCount( 5, $occupancies );

		// Get occupancy keys.
		$occupancy_keys = array_keys( $occupancies );
		$expected_data  = [
			'ABC-123:2026-02-28:OEX-SGL:A',
			'JKL-012:2025-01-09:ULT-SGL:A',
			'JKL-012:2025-01-09:ULT-DBL:A',
			'JKL-012:2025-01-09:ULT-DBL:AA',
			'JKL-012:2026-01-16:ULT-SGL:A',
		];

		// Check the occupancy keys.
		$this->assertEqualSets( $expected_data, array_unique( $occupancy_keys ) );

		// get the prices for the occupancy - JKL-012:2025-01-09:ULT-DBL:AA.
		$occupancy_prices = $occupancies['JKL-012:2025-01-09:ULT-DBL:AA']->get_occupancy_prices();

		// Assert prices.
		$this->assertCount( 5, $occupancy_prices );
		$this->assertEquals( '34600.00', $occupancy_prices['USD']->get_entry_data( 'price_per_person' ) );
		$this->assertEquals( '54200.00', $occupancy_prices['AUD']->get_entry_data( 'price_per_person' ) );
		$this->assertEquals( '47000.00', $occupancy_prices['CAD']->get_entry_data( 'price_per_person' ) );
		$this->assertEquals( '32200.00', $occupancy_prices['EUR']->get_entry_data( 'price_per_person' ) );
		$this->assertEquals( '27600.00', $occupancy_prices['GBP']->get_entry_data( 'price_per_person' ) );

		/**
		 * Assert the promo related data.
		 *
		 * $this->assertEquals( '23460.00', $occupancy_prices['GBP']->get_entry_data( 'promo_price_per_person' ) );
		 * $this->assertEquals( '15PROMO', $occupancy_prices['GBP']->get_entry_data( 'promotion_code' ) );
		 */

		// Assert 3rd of itinerary_ids is int.
		$this->assertIsInt( self::$itinerary_ids[3] );

		// Get the lowest price for the itinerary.
		$itinerary = new Itinerary();
		$itinerary->load( absint( self::$itinerary_ids[3] ) );

		// Assert the lowest price.
		$this->assertEquals( '27600.00', $itinerary->get_lowest_price( 'GBP' ) );
		$this->assertEquals( '34600.00', $itinerary->get_lowest_price() );

		// Assert the starting date.
		$this->assertEquals( '2025-01-09', $itinerary->get_starting_date() );

		// Assert the ending date.
		$this->assertEquals( '2026-02-01', $itinerary->get_ending_date() );

		// Get the related Ship.
		$related_ships = $itinerary->get_related_ships();

		// Assert the related ships.
		$this->assertCount( 1, $related_ships );

		// Get the ship.
		$ship = array_shift( $related_ships );

		// Assert the ship post.
		$this->assertIsArray( $ship );
		$this->assertIsArray( $ship['post_meta'] );

		// Assert the ship code.
		$this->assertEquals( 'ULT', $ship['post_meta']['ship_id'] );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
	}
}
