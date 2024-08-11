<?php
/**
 * Test suite for departures.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Departures;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Query;

use function Quark\Ships\get_id_from_ship_code;
use function Quark\Softrip\Departures\format_raw_departure_data;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Departures\get_ending_date;
use function Quark\Softrip\Departures\get_lowest_price;
use function Quark\Softrip\Departures\get_related_ship;
use function Quark\Softrip\Departures\get_starting_date;
use function Quark\Softrip\do_sync;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Departures.
 */
class Test_Departures extends Softrip_TestCase {
	/**
	 * Test format raw departure data.
	 *
	 * @covers \Quark\Softrip\Departures\format_raw_departure_data
	 *
	 * @return void
	 */
	public function test_format_raw_departure_data(): void {
		// Test with no argument.
		$expected = [];
		$actual   = format_raw_departure_data();
		$this->assertEquals( $expected, $actual );

		// Test with empty array of raw departure data.
		$expected = [];
		$actual   = format_raw_departure_data( [] );
		$this->assertEquals( $expected, $actual );

		// Test with non-empty array of raw departure data but no itinerary post or expedition post id.
		$raw_departure_data = [
			'id'          => '123',
			'code'        => 'ABC',
			'packageCode' => 'DEF',
			'startDate'   => '2021-01-01',
			'endDate'     => '2021-01-02',
			'duration'    => '2',
			'shipCode'    => 'GHI',
			'marketCode'  => 'JKL',
		];
		$expected           = [];
		$actual             = format_raw_departure_data( $raw_departure_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-empty array of raw departure data, non-empty itinerary post id, but empty expedition post id.
		$expected = [];
		$actual   = format_raw_departure_data( $raw_departure_data, 123 );
		$this->assertEquals( $expected, $actual );

		// Test with default values.
		$actual = [];
		$actual = format_raw_departure_data( [], 0, 0 );
		$this->assertEquals( $expected, $actual );

		/**
		 * Test with empty value for each required field in raw departure data.
		 * Should return an empty array.
		 */
		$raw_departure_data = [];

		// Add empty id.
		$raw_departure_data['id'] = '';
		$expected                 = [];
		$actual                   = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		// Add empty code.
		$raw_departure_data['code'] = '';
		$expected                   = [];
		$actual                     = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		// Add empty package code.
		$raw_departure_data['packageCode'] = '';
		$expected                          = [];
		$actual                            = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		// Add empty start date.
		$raw_departure_data['startDate'] = '';
		$expected                        = [];
		$actual                          = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		// Add empty end date.
		$raw_departure_data['endDate'] = '';
		$expected                      = [];
		$actual                        = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		// Add empty ship code.
		$raw_departure_data['shipCode'] = '';
		$expected                       = [];
		$actual                         = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		// Add empty market code.
		$raw_departure_data['marketCode'] = '';
		$expected                         = [];
		$actual                           = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );

		/**
		 * Test with valid raw departure data.
		 * Should return an array with the formatted departure data.
		 */
		$raw_departure_data = [
			'id'          => '123',
			'code'        => 'ABC',
			'packageCode' => 'DEF',
			'startDate'   => '2021-01-01',
			'endDate'     => '2021-01-02',
			'duration'    => '2',
			'shipCode'    => 'GHI',
			'marketCode'  => 'JKL',
		];

		// Related ship post id.
		$related_ship_id = get_id_from_ship_code( $raw_departure_data['shipCode'] );

		// Test with valid raw departure data, itinerary post id, and expedition post id.
		$expected = [
			'post_title'  => '123',
			'post_type'   => DEPARTURE_POST_TYPE,
			'post_parent' => 123,
			'meta_input'  => [
				'related_expedition'   => 456,
				'itinerary'            => 123,
				'related_ship'         => $related_ship_id,
				'softrip_package_code' => 'DEF',
				'softrip_id'           => '123',
				'softrip_code'         => 'ABC',
				'start_date'           => '2021-01-01',
				'end_date'             => '2021-01-02',
				'duration'             => 2,
				'ship_code'            => 'GHI',
				'softrip_market_code'  => 'JKL',
			],
		];
		$actual   = format_raw_departure_data( $raw_departure_data, 123, 456 );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get starting date.
	 *
	 * @covers \Quark\Softrip\Departures\get_starting_date
	 *
	 * @return void
	 */
	public function test_get_starting_date(): void {
		// Test with no argument.
		$expected = '';
		$actual   = get_starting_date();
		$this->assertSame( $expected, $actual );

		// Test with default values.
		$expected = '';
		$actual   = get_starting_date( 0 );
		$this->assertSame( $expected, $actual );

		// Test with a non-existent departure post id.
		$expected = '';
		$actual   = get_starting_date( 123 );
		$this->assertSame( $expected, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// Test with a departure post id without a starting date.
		$expected = '';
		$actual   = get_starting_date( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set empty string starting date for the departure post.
		update_post_meta( $departure_post_id, 'start_date', '' );

		// Test with a departure post id with an empty string starting date.
		$expected = '';
		$actual   = get_starting_date( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set a non string starting date for the departure post.
		update_post_meta( $departure_post_id, 'start_date', [ 123 ] );

		// Test with a departure post id with a non string starting date.
		$expected = '';
		$actual   = get_starting_date( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set a valid starting date for the departure post.
		$starting_date = '2021-01-01';
		update_post_meta( $departure_post_id, 'start_date', $starting_date );

		// Test with a departure post id with a valid starting date.
		$expected = $starting_date;
		$actual   = get_starting_date( $departure_post_id );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get ending date.
	 *
	 * @covers \Quark\Softrip\Departures\get_ending_date
	 *
	 * @return void
	 */
	public function test_get_ending_date(): void {
		// Test with no argument.
		$expected = '';
		$actual   = get_ending_date();
		$this->assertSame( $expected, $actual );

		// Test with default values.
		$expected = '';
		$actual   = get_ending_date( 0 );
		$this->assertSame( $expected, $actual );

		// Test with a non-existent departure post id.
		$expected = '';
		$actual   = get_ending_date( 123 );
		$this->assertSame( $expected, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// Test with a departure post id without an ending date.
		$expected = '';
		$actual   = get_ending_date( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set empty string ending date for the departure post.
		update_post_meta( $departure_post_id, 'end_date', '' );

		// Test with a departure post id with an empty string ending date.
		$expected = '';
		$actual   = get_ending_date( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set a non string ending date for the departure post.
		update_post_meta( $departure_post_id, 'end_date', [ 123 ] );

		// Test with a departure post id with a non string ending date.
		$expected = '';
		$actual   = get_ending_date( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set a valid ending date for the departure post.
		$ending_date = '2021-01-02';
		update_post_meta( $departure_post_id, 'end_date', $ending_date );

		// Test with a departure post id with a valid ending date.
		$expected = $ending_date;
		$actual   = get_ending_date( $departure_post_id );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Softrip\Departures\get_lowest_price
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Test with no argument.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price();
		$this->assertSame( $expected, $actual );

		// Test with default values.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( 0 );
		$this->assertSame( $expected, $actual );

		// Test with a non-existent departure post id.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( 123 );
		$this->assertSame( $expected, $actual );

		// Test with invalid currency code.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( 123, 'INVALID' );
		$this->assertSame( $expected, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// Test with a departure post id without any occupancies.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get departure post with softrip id = ABC-123:2026-02-28.
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'fields'                 => 'ids',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'   => 'softrip_id',
						'value' => 'ABC-123:2026-02-28',
					],
				],
			]
		);
		$this->assertNotEmpty( $departure_posts->posts );

		// Get the departure post id.
		$departure_post_id = $departure_posts->posts[0];
		$this->assertIsInt( $departure_post_id );

		// Test with a departure post id which has single occupancy.
		$expected = [
			'original'   => 34895,
			'discounted' => 26171,
		];
		$actual   = get_lowest_price( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id which has single occupancy but with invalid currency code.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'INVALID' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having single occupancy, with AUD currency code.
		$expected = [
			'original'   => 54795,
			'discounted' => 41096,
		];
		$actual   = get_lowest_price( $departure_post_id, 'AUD' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having single occupancy, with CAD currency code.
		$expected = [
			'original'   => 47495,
			'discounted' => 35621,
		];
		$actual   = get_lowest_price( $departure_post_id, 'cad' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having single occupancy, with EUR currency code.
		$expected = [
			'original'   => 32495,
			'discounted' => 24371,
		];
		$actual   = get_lowest_price( $departure_post_id, 'eur' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having single occupancy, with GBP currency code.
		$expected = [
			'original'   => 27995,
			'discounted' => 20996,
		];
		$actual   = get_lowest_price( $departure_post_id, 'GBP' );
		$this->assertSame( $expected, $actual );

		/**
		 * Test with departure that has multiple occupancies with various promos.
		 */

		// Get departure post with softrip id = HIJ-456:2025-08-26.
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'fields'                 => 'ids',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'   => 'softrip_id',
						'value' => 'HIJ-456:2025-08-26',
					],
				],
			]
		);
		$this->assertNotEmpty( $departure_posts->posts );

		// Get the departure post id.
		$departure_post_id = $departure_posts->posts[0];
		$this->assertIsInt( $departure_post_id );

		// Test with a departure post id which has multiple occupancies.
		$expected = [
			'original'   => 12795,
			'discounted' => 11516,
		];
		$actual   = get_lowest_price( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id which has multiple occupancies but with invalid currency code.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'INVALID' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having multiple occupancies, with AUD currency code.
		$expected = [
			'original'   => 20100,
			'discounted' => 18090,
		];
		$actual   = get_lowest_price( $departure_post_id, 'AUD' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having multiple occupancies, with CAD currency code.
		$expected = [
			'original'   => 17500,
			'discounted' => 15750,
		];
		$actual   = get_lowest_price( $departure_post_id, 'cad' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having multiple occupancies, with EUR currency code.
		$expected = [
			'original'   => 11900,
			'discounted' => 10710,
		];
		$actual   = get_lowest_price( $departure_post_id, 'eur' );
		$this->assertSame( $expected, $actual );

		// Test with a departure post id, having multiple occupancies, with GBP currency code.
		$expected = [
			'original'   => 10300,
			'discounted' => 9270,
		];
		$actual   = get_lowest_price( $departure_post_id, 'GBP' );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get departures by itinerary.
	 *
	 * @covers \Quark\Softrip\Departures\get_departures_by_itinerary
	 *
	 * @return void
	 */
	public function test_get_departures_by_itinerary(): void {
		// Test with no argument.
		$expected = [];
		$actual   = get_departures_by_itinerary();
		$this->assertSame( $expected, $actual );

		// Test with default values.
		$expected = [];
		$actual   = get_departures_by_itinerary( 0 );
		$this->assertSame( $expected, $actual );

		// Test with a non-existent itinerary post id.
		$expected = [];
		$actual   = get_departures_by_itinerary( 123 );
		$this->assertSame( $expected, $actual );

		// Create an itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Test with an itinerary post id without any departure.
		$expected = [];
		$actual   = get_departures_by_itinerary( $itinerary_post_id );
		$this->assertSame( $expected, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Test with an itinerary post id with a departure.
		$expected = [ $departure_post_id ];
		$actual   = get_departures_by_itinerary( $itinerary_post_id );
		$this->assertSame( $expected, $actual );

		// Create another departure post.
		$another_departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
			]
		);
		$this->assertIsInt( $another_departure_post_id );

		// Test with an itinerary post id with multiple departures.
		$expected = [ $another_departure_post_id, $departure_post_id ];
		$actual   = get_departures_by_itinerary( $itinerary_post_id );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get related ship.
	 *
	 * @covers \Quark\Softrip\Departures\get_related_ship
	 *
	 * @return void
	 */
	public function test_get_related_ship(): void {
		// Test with no argument.
		$expected = 0;
		$actual   = get_related_ship();
		$this->assertSame( $expected, $actual );

		// Test with default values.
		$expected = 0;
		$actual   = get_related_ship( 0 );
		$this->assertSame( $expected, $actual );

		// Test with a non-existent departure post id.
		$expected = 0;
		$actual   = get_related_ship( 123 );
		$this->assertSame( $expected, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// Test with a departure post id without a related ship.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set empty string related ship for the departure post.
		update_post_meta( $departure_post_id, 'related_ship', '' );

		// Test with a departure post id with an empty string related ship.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set a non string related ship for the departure post.
		update_post_meta( $departure_post_id, 'related_ship', [ 123 ] );

		// Test with a departure post id with a non string related ship.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set a valid related ship for the departure post.
		$related_ship = wp_rand( 50, 100 );
		update_post_meta( $departure_post_id, 'related_ship', $related_ship );

		// Test with a departure post id with a valid related ship.
		$expected = $related_ship;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set an invalid string related ship for the departure post.
		update_post_meta( $departure_post_id, 'related_ship', 'INVALID' );

		// Test with a departure post id with an invalid string related ship.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set an invalid integer related ship for the departure post.
		update_post_meta( $departure_post_id, 'related_ship', 0 );

		// Test with a departure post id with an invalid integer related ship.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set an invalid float related ship for the departure post.
		update_post_meta( $departure_post_id, 'related_ship', 0.0 );

		// Test with a departure post id with an invalid float related ship.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Remove related ship meta for the departure post.
		delete_post_meta( $departure_post_id, 'related_ship' );

		// Create a ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'POQ',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// No ship has been set yet.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set the related ship via code.
		update_post_meta( $departure_post_id, 'ship_code', 'POQ' );

		// Test with a departure post id with a valid related ship.
		$expected = $ship_post_id;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Set an invalid ship code for the departure post.
		update_post_meta( $departure_post_id, 'ship_code', 'INVALID' );

		// Test with a departure post id with an invalid ship code.
		$expected = 0;
		$actual   = get_related_ship( $departure_post_id );
		$this->assertSame( $expected, $actual );
	}
}
