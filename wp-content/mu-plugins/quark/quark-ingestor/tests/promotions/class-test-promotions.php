<?php
/**
 * Test suite for the Promotions namespace.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Tests\Promotions;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Ingestor\Promotions\get_promotions_data;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\Promotions\get_promotions_by_code;

use const Quark\Departures\POST_TYPE;

/**
 * Class Test_Promotions
 */
class Test_Promotions extends Softrip_TestCase {
	/**
	 * Test get_promotions_data.
	 *
	 * @covers \Quark\Ingestor\Promotions\get_promotions_data
	 *
	 * @return void
	 */
	public function test_get_promotions_data(): void {
		// Test with empty departure post ID.
		$promotions_data = get_promotions_data();
		$this->assertEmpty( $promotions_data );

		// Test with default departure post ID.
		$promotions_data = get_promotions_data( 0 );
		$this->assertEmpty( $promotions_data );

		// Test with non-existent departure post ID.
		$promotions_data = get_promotions_data( 999999 );
		$this->assertEmpty( $promotions_data );

		// Setup mock response.
		add_filter( 'pre_http_request', '\Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync all data.
		do_sync();

		// Flush cache.
		wp_cache_flush();

		// Remove Softrip mock response.
		remove_filter( 'pre_http_request', '\Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get departure post.
		$departure_posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
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
		$this->assertNotEmpty( $departure_posts );

		// Get first departure post ID.
		$departure_post_id = $departure_posts[0];
		$this->assertIsInt( $departure_post_id );

		// Test with valid departure post ID.
		$promotions_data = get_promotions_data( $departure_post_id );
		$this->assertNotEmpty( $promotions_data );
		$this->assertCount( 2, $promotions_data );

		// Get first promotion data.
		$promotion_data = $promotions_data[0];
		$this->assertIsArray( $promotion_data );
		$this->assertArrayHasKey( 'code', $promotion_data );

		// Promotion code.
		$promotion_code = $promotion_data['code'];

		// Get promotion by code.
		$expected_promotion_data = get_promotions_by_code( $promotion_code );
		$this->assertNotEmpty( $expected_promotion_data );
		$expected_promotion = $expected_promotion_data[0];
		$this->assertIsArray( $expected_promotion );

		// Get second promotion data.
		$promotion_data = $promotions_data[1];
		$this->assertIsArray( $promotion_data );
		$this->assertArrayHasKey( 'code', $promotion_data );

		// Promotion code.
		$promotion_code = $promotion_data['code'];

		// Get promotion by code.
		$expected_promotion_data = get_promotions_by_code( $promotion_code );
		$this->assertNotEmpty( $expected_promotion_data );
		$expected_promotion2 = $expected_promotion_data[0];
		$this->assertIsArray( $expected_promotion2 );

		// Expected promotions data.
		$expected = [
			[
				'id'            => $expected_promotion['id'],
				'code'          => $expected_promotion['code'],
				'startDate'     => $expected_promotion['start_date'],
				'endDate'       => $expected_promotion['end_date'],
				'description'   => $expected_promotion['description'],
				'discountType'  => $expected_promotion['discount_type'],
				'discountValue' => $expected_promotion['discount_value'],
				'isPIF'         => $expected_promotion['is_pif'],
			],
			[
				'id'            => $expected_promotion2['id'],
				'code'          => $expected_promotion2['code'],
				'startDate'     => $expected_promotion2['start_date'],
				'endDate'       => $expected_promotion2['end_date'],
				'description'   => $expected_promotion2['description'],
				'discountType'  => $expected_promotion2['discount_type'],
				'discountValue' => $expected_promotion2['discount_value'],
				'isPIF'         => $expected_promotion2['is_pif'],
			],
		];
		$this->assertEquals( $expected, $promotions_data );
	}
}
