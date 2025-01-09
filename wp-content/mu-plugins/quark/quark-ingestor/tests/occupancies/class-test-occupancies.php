<?php
/**
 * Test suite for Occupancies.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Occupancies;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Ingestor\Occupancies\get_occupancies_data;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\update_occupancies;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Occupancies
 */
class Test_Occupancies extends Softrip_TestCase {
		/**
		 * Test get occupancies data.
		 *
		 * @covers \Quark\Ingestor\get_occupancies_data
		 *
		 * @return void
		 */
	public function test_get_occupancies_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancies_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_occupancies_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_occupancies_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Update related itinerary.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-123:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'start_date'         => '2025-01-01',
					'end_date'           => '2025-01-02',
					'duration'           => 2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Create ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'POQ',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Create a cabin post without softrip id.
		$cabin_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORY_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'POQ-SGL',
				],
			]
		);
		$this->assertIsInt( $cabin_post_id1 );

		// Insert some promotions.
		$raw_promotions = [
			[
				'endDate'       => '2025-03-01',
				'startDate'     => '2025-01-01',
				'description'   => 'Promotion 1',
				'discountType'  => 'percentage',
				'discountValue' => '0.1',
				'promotionCode' => '10PROMO',
				'isPIF'         => false,
			],
			[
				'endDate'       => '2025-04-01',
				'startDate'     => '2025-02-01',
				'description'   => 'Promotion 2',
				'discountType'  => 'fixed',
				'discountValue' => '0.1',
				'promotionCode' => '10PIF',
				'isPIF'         => true,
			],
			[
				'endDate'       => '2025-03-22',
				'startDate'     => '2025-01-12',
				'description'   => 'Promotion 3',
				'discountType'  => 'percentage',
				'discountValue' => '0.2',
				'promotionCode' => '20PROMO',
				'isPIF'         => false,
			],
		];
		$is_success     = update_promotions( $raw_promotions, $departure_post_id );
		$this->assertTrue( $is_success );

		// Get promotion by code - 10PROMO.
		$promotions = get_promotions_by_code( '10PROMO' );
		$this->assertIsArray( $promotions );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );
		$promotion1 = $promotions[0];
		$this->assertIsArray( $promotion1 );
		$this->assertArrayHasKey( 'id', $promotion1 );
		$promotion_id1 = $promotion1['id'];

		// Get promotion by code - 20PROMO.
		$promotions = get_promotions_by_code( '20PROMO' );
		$this->assertIsArray( $promotions );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );
		$promotion2 = $promotions[0];
		$this->assertIsArray( $promotion2 );
		$this->assertArrayHasKey( 'id', $promotion2 );
		$promotion_id2 = $promotion2['id'];

		// Get promotion by code - 10PIF.
		$promotions = get_promotions_by_code( '10PIF' );
		$this->assertIsArray( $promotions );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );
		$promotion3 = $promotions[0];
		$this->assertIsArray( $promotion3 );
		$this->assertArrayHasKey( 'id', $promotion3 );
		$promotion_id3 = $promotion3['id'];

		// Insert occupancies for this cabin.
		$raw_cabins_data = [
			[
				'id'          => 'UNQ-123:2025-01-01:POQ-SGL',
				'code'        => 'POQ-SGL',
				'name'        => 'Explorer Single',
				'departureId' => 'UNQ-123:2025-01-01',
				'occupancies' => [
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'mask'           => 'A',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [
							'AUD' => [
								'currencyCode'   => 'AUD',
								'pricePerPerson' => 1000,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 900,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 800,
									],
								],
							],
							'USD' => [
								'currencyCode'   => 'USD',
								'pricePerPerson' => 8176,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 7360,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 6544,
									],
									'10PROMO' => [
										'promoPricePerPerson' => 5360,
									],
								],
							],
							'CAD' => [
								'currencyCode'   => 'CAD',
								'pricePerPerson' => 1000,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 900,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 800,
									],
								],
							],
							'EUR' => [
								'currencyCode'   => 'EUR',
								'pricePerPerson' => 780,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 900,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 800,
									],
									'10PROMO' => [
										'promoPricePerPerson' => 800,
									],
								],
							],
							'GBP' => [
								'currencyCode'   => 'GBP',
								'pricePerPerson' => 18722,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 16850,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 14978,
									],
									'10PROMO' => [
										'promoPricePerPerson' => 12300,
									],
								],
							],
						],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'mask'           => 'AA',
						'saleStatusCode' => 'S',
						'saleStatus'     => 'Sold Out',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'mask'           => 'SA',
						'saleStatusCode' => 'N',
						'saleStatus'     => 'No display',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'           => 'SAA',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [
							'AUD' => [
								'currencyCode'   => 'AUD',
								'pricePerPerson' => 1000,
							],
						],
					],
				],
			],
		];

		// Insert occupancies.
		$is_updated = update_occupancies( $raw_cabins_data, $departure_post_id );
		$this->assertTrue( $is_updated );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned occupancy that has no softrip id.
		$actual   = get_occupancies_data( $itinerary_post_id, $departure_post_id, $cabin_post_id1 );
		$expected = [
			[
				'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:A',
				'mask'                    => 'A',
				'description'             => get_description_and_pax_count_by_mask( 'A' )['description'],
				'availabilityStatus'      => 'O',
				'availabilityDescription' => 'Open',
				'spacesAvailable'         => 0,
				'prices'                  => [
					'AUD' => [
						'currencyCode'                    => 'AUD',
						'pricePerPerson'                  => 1000,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 900,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 800,
							],
						],
					],
					'USD' => [
						'currencyCode'                    => 'USD',
						'pricePerPerson'                  => 8176,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 7360,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 6544,
							],
							[
								'id'                  => $promotion_id1,
								'promotionCode'       => '10PROMO',
								'promoPricePerPerson' => 5360,
							],
						],
					],
					'CAD' => [
						'currencyCode'                    => 'CAD',
						'pricePerPerson'                  => 1000,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 900,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 800,
							],
						],
					],
					'EUR' => [
						'currencyCode'                    => 'EUR',
						'pricePerPerson'                  => 780,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 900,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 800,
							],
							[
								'id'                  => $promotion_id1,
								'promotionCode'       => '10PROMO',
								'promoPricePerPerson' => 800,
							],
						],
					],
					'GBP' => [
						'currencyCode'                    => 'GBP',
						'pricePerPerson'                  => 18722,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 16850,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 14978,
							],
							[
								'id'                  => $promotion_id1,
								'promotionCode'       => '10PROMO',
								'promoPricePerPerson' => 12300,
							],
						],
					],
				],
			],
			[
				'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
				'mask'                    => 'SAA',
				'description'             => get_description_and_pax_count_by_mask( 'SAA' )['description'],
				'availabilityStatus'      => 'O',
				'availabilityDescription' => 'Open',
				'spacesAvailable'         => 0,
				'prices'                  => [
					'AUD' => [
						'currencyCode'                    => 'AUD',
						'pricePerPerson'                  => 1000,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'USD' => [
						'currencyCode'                    => 'USD',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'EUR' => [
						'currencyCode'                    => 'EUR',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'GBP' => [
						'currencyCode'                    => 'GBP',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'CAD' => [
						'currencyCode'                    => 'CAD',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}
}
