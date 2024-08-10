<?php
/**
 * Test Suite for Itineraries.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Itineraries;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Departures\get_ending_date as get_departure_ending_date;
use function Quark\Softrip\Departures\get_related_ship as get_departure_related_ship;
use function Quark\Softrip\Departures\get_starting_date as get_departure_starting_date;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\Itineraries\get_ending_date;
use function Quark\Softrip\Itineraries\get_lowest_price;
use function Quark\Softrip\Itineraries\get_related_ships;
use function Quark\Softrip\Itineraries\get_starting_date;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;

/**
 * Class Test_Itineraries
 */
class Test_Itineraries extends Softrip_TestCase {
	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Softrip\Itineraries\get_lowest_price
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Invalid post ID.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price() );

		// Invalid currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( 1, 'INVALID' ) );

		// Create an itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Itinerary with no departure.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get itinerary post with package code ABC-123.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get lowest price for itinerary with package code ABC-123 with USD currency.
		$expected = [
			'original'   => 34895,
			'discounted' => 26171,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );

		// Get lowest price for itinerary with package code ABC-123 with AUD currency.
		$expected = [
			'original'   => 54795,
			'discounted' => 41096,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'AUD' ) );

		// Get lowest price for itinerary with package code ABC-123 with EUR currency.
		$expected = [
			'original'   => 32495,
			'discounted' => 24371,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'EUR' ) );

		// Get lowest price for itinerary with package code ABC-123 with GBP currency.
		$expected = [
			'original'   => 27995,
			'discounted' => 20996,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'GBP' ) );

		// Get lowest price for itinerary with package code ABC-123 with invalid currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'INVALID' ) );

		// Get itinerary with package code DEF-456.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'DEF-456',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get lowest price for itinerary with package code DEF-456 with USD currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );

		// Get lowest price for itinerary with package code DEF-456 with AUD currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'AUD' ) );

		// Get lowest price for itinerary with package code DEF-456 with EUR currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'EUR' ) );

		// Get lowest price for itinerary with package code DEF-456 with GBP currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'GBP' ) );

		// Get lowest price for itinerary with package code DEF-456 with invalid currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'INVALID' ) );

		// Get itinerary with package code HIJ-456.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'HIJ-456',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get lowest price for itinerary with package code HIJ-456 with USD currency.
		$expected = [
			'original'   => 12795,
			'discounted' => 10236,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );

		// Get lowest price for itinerary with package code HIJ-456 with AUD currency.
		$expected = [
			'original'   => 20100,
			'discounted' => 16080,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'AUD' ) );

		// Get lowest price for itinerary with package code HIJ-456 with EUR currency.
		$expected = [
			'original'   => 11900,
			'discounted' => 9520,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'EUR' ) );

		// Get lowest price for itinerary with package code HIJ-456 with GBP currency.
		$expected = [
			'original'   => 10300,
			'discounted' => 8240,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'GBP' ) );

		// Get lowest price for itinerary with package code HIJ-456 with invalid currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'INVALID' ) );
	}

	/**
	 * Test get related ships.
	 *
	 * @covers \Quark\Softrip\Itineraries\get_related_ships
	 *
	 * @return void
	 */
	public function test_get_related_ships(): void {
		// Create an itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Itinerary with no departure.
		$expected = [];
		$this->assertEquals( $expected, get_related_ships( $itinerary_id ) );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get itinerary post with package code ABC-123.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get all departures.
		$departure_post_ids = get_departures_by_itinerary( $itinerary_id );
		$this->assertNotEmpty( $departure_post_ids );

		// Expected related ships.
		$expected = [];

		// Get related ships for each departure.
		foreach ( $departure_post_ids as $departure_post_id ) {
			$related_ship = get_departure_related_ship( $departure_post_id );
			$this->assertNotEmpty( $related_ship );
			$this->assertIsInt( $related_ship );

			// Add related ship to expected.
			$expected[] = $related_ship;

		}

		// Get related ships for itinerary with package code ABC-123.
		$this->assertEquals( $expected, get_related_ships( $itinerary_id ) );

		// Get itinerary with package code DEF-456.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'DEF-456',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get related ships for itinerary with package code DEF-456.
		$expected = [];
		$this->assertEquals( $expected, get_related_ships( $itinerary_id ) );
	}

	/**
	 * Test get ending date.
	 *
	 * @covers \Quark\Softrip\Itineraries\get_ending_date
	 *
	 * @return void
	 */
	public function test_get_ending_date(): void {
		// Create an itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Itinerary with no departure.
		$expected = '';
		$this->assertEquals( $expected, get_ending_date( $itinerary_id ) );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get itinerary post with package code ABC-123.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get all departures.
		$departure_post_ids = get_departures_by_itinerary( $itinerary_id );

		// Ending dates to compare.
		$ending_dates = [];

		// Get ending date for each departure.
		foreach ( $departure_post_ids as $departure_post_id ) {
			$ending_date = get_departure_ending_date( $departure_post_id );
			$this->assertNotEmpty( $ending_date );
			$this->assertIsString( $ending_date );

			// Add ending date to expected.
			$ending_dates[] = $ending_date;
		}

		// Get ending date for itinerary with package code ABC-123.
		$this->assertEquals( max( $ending_dates ), get_ending_date( $itinerary_id ) );

		// Let's test with a new itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Create some departures and assign to the itinerary.
		$departure_post_ids = [];

		// Departure 1.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_id,
			]
		);
		$this->assertIsInt( $departure_post_id );
		$departure_post_ids[] = $departure_post_id;

		// Set starting date.
		update_post_meta( $departure_post_id, 'end_date', '2021-01-01' );

		// Departure 2.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_id,
			]
		);
		$this->assertIsInt( $departure_post_id );
		$departure_post_ids[] = $departure_post_id;

		// Set starting date.
		update_post_meta( $departure_post_id, 'end_date', '2021-01-02' );

		// Get starting date for itinerary.
		$this->assertEquals( '2021-01-02', get_ending_date( $itinerary_id ) );
	}

	/**
	 * Test starting date.
	 *
	 * @covers \Quark\Softrip\Itineraries\get_starting_date
	 *
	 * @return void
	 */
	public function test_get_starting_date(): void {
		// Create an itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Itinerary with no departure.
		$expected = '';
		$this->assertEquals( $expected, get_starting_date( $itinerary_id ) );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get itinerary post with package code ABC-123.
		$itinerary_posts = get_posts(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get all departures.
		$departure_post_ids = get_departures_by_itinerary( $itinerary_id );

		// Starting dates to compare.
		$starting_dates = [];

		// Get starting date for each departure.
		foreach ( $departure_post_ids as $departure_post_id ) {
			$starting_date = get_departure_starting_date( $departure_post_id );
			$this->assertNotEmpty( $starting_date );
			$this->assertIsString( $starting_date );

			// Add starting date to expected.
			$starting_dates[] = $starting_date;
		}

		// Get starting date for itinerary with package code ABC-123.
		$this->assertEquals( min( $starting_dates ), get_starting_date( $itinerary_id ) );

		// Let's test with a new itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Create some departures and assign to the itinerary.
		$departure_post_ids = [];

		// Departure 1.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_id,
			]
		);
		$this->assertIsInt( $departure_post_id );
		$departure_post_ids[] = $departure_post_id;

		// Set starting date.
		update_post_meta( $departure_post_id, 'start_date', '2021-01-01' );

		// Departure 2.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_id,
			]
		);
		$this->assertIsInt( $departure_post_id );
		$departure_post_ids[] = $departure_post_id;

		// Set starting date.
		update_post_meta( $departure_post_id, 'start_date', '2021-01-02' );

		// Get starting date for itinerary.
		$this->assertEquals( '2021-01-01', get_starting_date( $itinerary_id ) );
	}
}
