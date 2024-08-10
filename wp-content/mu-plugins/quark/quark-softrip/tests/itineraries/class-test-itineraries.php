<?php
/**
 * Test Suite for Itineraries.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Itineraries;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\do_sync;
use function Quark\Softrip\Itineraries\get_lowest_price;

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

		// Sync softrip with exising posts.
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
}
