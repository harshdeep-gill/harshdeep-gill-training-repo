<?php
/**
 * Test for sync.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;
use WP_Post;
use WP_Error;
use WP_Query;

use function Quark\Tests\Softrip\truncate_softrip_db_tables;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Softrip_Sync.
 */
class Test_Softrip_Sync extends WP_UnitTestCase {

	/**
	 * Itinerary posts.
	 *
	 * @var array<int|WP_Error>
	 */
	protected static array $itinerary_ids = [];

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		// Run parent and include setup.
		parent::set_up_before_class();
		include_once 'setup.php';

		// Create a test itinerary post.
		self::$itinerary_ids = self::factory()->post->create_many(
			20,
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
			]
		);

		// Write above code in loop.
		$softrip_package_ids = [
			'ABC-123',
			'DEF-456',
			'GHI-789',
			'JKL-012',
			'MNO-678',
			'PQR-345',
			'STU-901',
			'VWX-234',
			'YZA-567',
			'BCD-890',
			'EFG-123',
			'HIJ-456',
			'KLM-789',
			'NOP-012',
			'QRS-345',
			'TUV-678',
			'WXY-901',
			'ZAB-234',
			'CDE-567',
			'FGH-890',
		];

		// Loop through the itineraries and set meta.
		foreach ( self::$itinerary_ids as $index => $itinerary_id ) {
			update_post_meta( absint( $itinerary_id ), 'softrip_package_id', $softrip_package_ids[ $index ] );
			wp_cache_delete( ITINERARY_POST_TYPE . '_' . absint( $itinerary_id ), ITINERARY_POST_TYPE );
		}

		// Create Cabin Category posts.
		$cabin_ids = self::factory()->post->create_many(
			5,
			[
				'post_title'   => 'Test Cabin',
				'post_content' => 'Cabin content',
				'post_status'  => 'publish',
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
			]
		);

		// List the Cabin softrip codes.
		$softrip_cabin_ids = [
			'OEX-SGL',
			'OEX-DBL',
			'ULT-SGL',
			'ULT-SGL',
			'ULT-DBL',
			'ULT-DBL',
		];

		// Loop through the cabins and set meta.
		foreach ( $cabin_ids as $index => $cabin_id ) {
			update_post_meta( absint( $cabin_id ), 'cabin_category_id', $softrip_cabin_ids[ $index ] );
			wp_cache_delete( CABIN_CATEGORY_POST_TYPE . '_' . absint( $cabin_id ), CABIN_CATEGORY_POST_TYPE );
		}

		// Create ship posts.
		$ship_ids = self::factory()->post->create_many(
			5,
			[
				'post_title'   => 'Test Ship',
				'post_content' => 'Ship content',
				'post_status'  => 'publish',
				'post_type'    => SHIP_POST_TYPE,
			]
		);

		// List the Ship softrip codes.
		$softrip_ship_ids = [
			'OEX',
			'GHI',
			'JKL',
			'ULT',
			'MNO',
		];

		// Loop through the ships and set meta.
		foreach ( $ship_ids as $index => $ship_id ) {
			update_post_meta( absint( $ship_id ), 'ship_id', $softrip_ship_ids[ $index ] );
			wp_cache_delete( SHIP_POST_TYPE . '_' . absint( $ship_id ), SHIP_POST_TYPE );
		}
	}

	/**
	 * Tear down after class.
	 *
	 * @return void
	 */
	public static function tear_down_after_class(): void {
		// Run parent.
		parent::tear_down_after_class();
		truncate_softrip_db_tables();

		// Delete the test itinerary posts.
		foreach ( self::$itinerary_ids as $itinerary_id ) {
			if ( ! $itinerary_id instanceof WP_Error ) {
				wp_delete_post( $itinerary_id, true );
			}
		}

		// Reset the itinerary posts.
		self::$itinerary_ids = [];

		// Delete the test cabin posts.
		$cabin_query = new WP_Query(
			[
				'post_type'              => CABIN_CATEGORY_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
			]
		);

		// Loop through the cabin posts.
		foreach ( $cabin_query->posts as $cabin_id ) {
			wp_delete_post( is_int( $cabin_id ) ? $cabin_id : $cabin_id->ID, true );
		}

		// Delete the test ship posts.
		$ship_query = new WP_Query(
			[
				'post_type'              => SHIP_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
			]
		);

		// Loop through the ship posts.
		foreach ( $ship_query->posts as $ship_id ) {
			wp_delete_post( is_int( $ship_id ) ? $ship_id : $ship_id->ID, true );
		}
	}

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// Run parent.
		parent::set_up();

		// Mock the response for the POST request.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );
	}

	/**
	 * Tear down after tests.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// Run parent.
		parent::tear_down();

		// Remove the filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
	}

	/**
	 * Test for prepare_batch_ids.
	 *
	 * @covers \Quark\Softrip\Sync::prepare_batch_ids
	 * @covers \Quark\Softrip\Sync::get_all_itinerary_ids
	 *
	 * @return void
	 */
	public function test_prepare_batch_ids(): void {
		// Create the sync object.
		$sync = new Softrip_Sync();

		// Get the itinerary IDs.
		$ids = $sync->get_all_itinerary_ids();

		// Assert the IDs.
		$this->assertIsArray( $ids );
		$this->assertCount( 20, $ids );

		// Prepare the batches.
		$batches = $sync->prepare_batch_ids( $ids );

		// Check the batches.
		$this->assertIsArray( $batches );
		$this->assertCount( 4, $batches );
		$this->assertCount( 5, $batches[0] );
		$this->assertCount( 5, $batches[1] );
		$this->assertCount( 5, $batches[2] );
		$this->assertCount( 5, $batches[3] );
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
	}
}
