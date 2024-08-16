<?php
/**
 * Test suite for Occupancy Promotions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Occupancy_Promotions;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\Occupancies\format_data as format_raw_occupancy_data;
use function Quark\Softrip\Occupancies\get_table_name as get_occupancies_table_name;
use function Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_id;
use function Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_occupancy_id;
use function Quark\Softrip\OccupancyPromotions\format_row_data_from_db;
use function Quark\Softrip\OccupancyPromotions\format_rows_data_from_db;
use function Quark\Softrip\OccupancyPromotions\get_lowest_price;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promo_by_id;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy_id_and_promotion_id;
use function Quark\Softrip\OccupancyPromotions\get_table_name;
use function Quark\Softrip\OccupancyPromotions\get_table_sql;
use function Quark\Softrip\OccupancyPromotions\update_occupancy_promotions;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\Softrip\OccupancyPromotions\CACHE_GROUP;
use const Quark\Softrip\OccupancyPromotions\CACHE_KEY_PREFIX;
use const Quark\Softrip\TABLE_PREFIX_NAME;

/**
 * Class Test_Occupancy_Promotions
 */
class Test_Occupancy_Promotions extends Softrip_TestCase {
	/**
	 * Test get table name.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_table_name
	 *
	 * @return void
	 */
	public function test_get_table_name(): void {
		// Test table name.
		$expected = TABLE_PREFIX_NAME . 'occupancy_promotions';
		$actual   = get_table_name();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get table sql.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_table_sql
	 *
	 * @return void
	 */
	public function test_get_table_sql(): void {
		// Get table name.
		$table_name = get_table_name();

		// Get engine collate.
		$engine_collate = get_engine_collate();

		// Assert that SQL is correct.
		$expected_sql = "CREATE TABLE $table_name (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            occupancy_id BIGINT NOT NULL,
            promotion_id BIGINT NOT NULL,
            price_per_person_usd BIGINT NOT NULL,
            price_per_person_cad BIGINT NOT NULL,
            price_per_person_aud BIGINT NOT NULL,
            price_per_person_gbp BIGINT NOT NULL,
            price_per_person_eur BIGINT NOT NULL
        ) $engine_collate";
		$actual       = get_table_sql();

		// Replace \n\r\s with empty string.
		$expected_sql = preg_replace( '/\r|\n|\s+/', '', $expected_sql );
		$actual       = preg_replace( '/\r|\n|\s+/', '', $actual );
		$this->assertEquals( $expected_sql, $actual );
	}

	/**
	 * Test update occupancy promotions.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\update_occupancy_promotions
	 *
	 * @return void
	 */
	public function test_update_occupancy_promotions(): void {
		// Test with no arguments.
		$actual = update_occupancy_promotions();
		$this->assertFalse( $actual );

		// Test with empty array.
		$actual = update_occupancy_promotions( [] );
		$this->assertFalse( $actual );

		// Test with default occupancy id.
		$actual = update_occupancy_promotions( [], 0 );
		$this->assertFalse( $actual );

		// Test with non-existing occupancy id.
		$actual = update_occupancy_promotions(
			[
				[
					'USD' => [
						'currencyCode'   => 'USD',
						'pricePerPerson' => 100,
						'promos'         => [
							'10PROMO' => 90,
						],
					],
				],
			],
			999
		);
		$this->assertFalse( $actual );

		// Create an occupancy.
		$raw_occupancy_data     = [
			'id'                      => 'OCC1',
			'name'                    => 'Single',
			'mask'                    => 'A',
			'availabilityStatus'      => 'O',
			'availabilityDescription' => 'Available',
			'spacesAvailable'         => 11,
			'prices'                  => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
			],
		];
		$departure_post_id      = 3483;
		$cabin_category_post_id = 9343;

		// Format occupancy data.
		$formatted_occupancy_data = format_raw_occupancy_data( $raw_occupancy_data, $cabin_category_post_id, $departure_post_id );
		$this->assertNotEmpty( $formatted_occupancy_data );

		// Global db.
		global $wpdb;

		// Table name.
		$table_name = get_occupancies_table_name();

		// Insert occupancy.
		$wpdb->insert( $table_name, $formatted_occupancy_data );

		// Get occupancy id.
		$occupancy_id = $wpdb->insert_id;
		$this->assertNotEmpty( $occupancy_id );

		// Test with valid occupancy id, but invalid promotions raw data.
		$raw_promotions_data = [
			'USD' => [
				'currencyCode'   => 'USD',
				'pricePerPerson' => 100,
				'promos'         => [
					'10PROMO' => 90,
				],
			],
		];
		$actual              = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
		$this->assertTrue( $actual );

		// Get occupancy promotions by occupancy id - should be empty.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertEmpty( $occ_promotions );

		// Test with valid occupancy id and promotions raw data.
		$raw_promotions_data = [
			'USD' => [
				'currencyCode'   => 'USD',
				'pricePerPerson' => 100,
				'promos'         => [
					'10PROMO' => [
						'pricePerPerson' => 90,
					],
					'20PROMO' => [
						'pricePerPerson' => 80,
					],
				],
			],
		];
		$actual              = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
		$this->assertTrue( $actual );

		// Get occupancy promotions by occupancy id - still empty. Because promotions are not created yet.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertEmpty( $occ_promotions );

		// Create promotions.
		$promotions = [
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-19T00:00:00',
				'description'   => 'Save 10%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.1',
				'promotionCode' => '10PROMO',
				'isPIF'         => true,
			],
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-28T00:00:00',
				'description'   => 'Save 20%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.2',
				'promotionCode' => '20PROMO',
				'isPIF'         => false,
			],
		];
		$is_saved   = update_promotions( $promotions );
		$this->assertTrue( $is_saved );

		// Get promotions by code.
		$promotions = get_promotions_by_code( '10PROMO' );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );

		// Get promotions by code.
		$promotions = get_promotions_by_code( '20PROMO' );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );

		// Try again saving occupancy promotions.
		$raw_promotions_data = [
			'USD' => [
				'currencyCode'   => 'USD',
				'pricePerPerson' => 100,
				'promos'         => [
					'10PROMO' => [
						'promoPricePerPerson' => 90,
					],
					'20PROMO' => [
						'promoPricePerPerson' => 80,
					],
				],
			],
		];
		$actual              = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
		$this->assertTrue( $actual );

		// Get occupancy promotions by occupancy id.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertIsArray( $occ_promotions );
		$this->assertNotEmpty( $occ_promotions );
		$this->assertCount( 2, $occ_promotions );

		// Verify each field.
		$this->assertEquals( 90, $occ_promotions[0]['price_per_person_usd'] );
		$this->assertEquals( 80, $occ_promotions[1]['price_per_person_usd'] );
		$this->assertEquals( 0, $occ_promotions[0]['price_per_person_cad'] );
		$this->assertEquals( 0, $occ_promotions[1]['price_per_person_cad'] );
		$this->assertEquals( 0, $occ_promotions[0]['price_per_person_aud'] );
		$this->assertEquals( 0, $occ_promotions[1]['price_per_person_aud'] );
		$this->assertEquals( 0, $occ_promotions[0]['price_per_person_gbp'] );
		$this->assertEquals( 0, $occ_promotions[1]['price_per_person_gbp'] );
		$this->assertEquals( 0, $occ_promotions[0]['price_per_person_eur'] );
		$this->assertEquals( 0, $occ_promotions[1]['price_per_person_eur'] );

		// Get promotion id by code.
		$promos = get_promotions_by_code( '10PROMO' );
		$this->assertNotEmpty( $promos );
		$this->assertIsArray( $promos );
		$this->assertCount( 1, $promos );
		$promo = $promos[0];
		$this->assertIsArray( $promo );
		$this->assertNotEmpty( $promo );
		$this->assertArrayHasKey( 'id', $promo );
		$promo_id1 = $promo['id'];

		// Get promotion id by code.
		$promos = get_promotions_by_code( '20PROMO' );
		$this->assertNotEmpty( $promos );
		$this->assertIsArray( $promos );
		$this->assertCount( 1, $promos );
		$promo = $promos[0];
		$this->assertIsArray( $promo );
		$this->assertNotEmpty( $promo );
		$this->assertArrayHasKey( 'id', $promo );
		$promo_id2 = $promo['id'];

		// Verify promo code.
		$this->assertEquals( $promo_id1, $occ_promotions[0]['promotion_id'] );
		$this->assertEquals( $promo_id2, $occ_promotions[1]['promotion_id'] );

		// Update occupancy promotions with new data.
		$raw_promotions_data = [
			'USD' => [
				'currencyCode'   => 'USD',
				'pricePerPerson' => 100,
				'promos'         => [
					'10PROMO' => [
						'promoPricePerPerson' => 90, // old.
					],
					'20PROMO' => [
						'promoPricePerPerson' => 85,
					],
				],
			],
			'CAD' => [
				'currencyCode'   => 'CAD',
				'pricePerPerson' => 120,
				'promos'         => [
					'10PROMO' => [
						'promoPricePerPerson' => 110,
					],
					'20PROMO' => [
						'promoPricePerPerson' => 105,
					],
				],
			],
			'AUD' => [
				'currencyCode'   => 'AUD',
				'pricePerPerson' => 130,
				'promos'         => [
					'10PROMO' => [
						'promoPricePerPerson' => 120,
					],
					'20PROMO' => [
						'promoPricePerPerson' => 115,
					],
				],
			],
			'GBP' => [
				'currencyCode'   => 'GBP',
				'pricePerPerson' => 140,
				'promos'         => [
					'10PROMO' => [
						'promoPricePerPerson' => 130,
					],
					'20PROMO' => [
						'promoPricePerPerson' => 125,
					],
				],
			],
			'EUR' => [
				'currencyCode'   => 'EUR',
				'pricePerPerson' => 150,
				'promos'         => [
					'10PROMO' => [
						'promoPricePerPerson' => 140,
					],
					'20PROMO' => [
						'promoPricePerPerson' => 135,
					],
				],
			],
		];

		// Update occupancy promotions.
		$actual = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
		$this->assertTrue( $actual );

		// Get occupancy promotions by occupancy id.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertIsArray( $occ_promotions );
		$this->assertNotEmpty( $occ_promotions );
		$this->assertCount( 2, $occ_promotions );

		// Get first occupancy promotion.
		$occ_promotion1 = $occ_promotions[0];
		$this->assertIsArray( $occ_promotion1 );
		$this->assertNotEmpty( $occ_promotion1 );

		// Verify each field.
		$this->assertEquals( 90, $occ_promotion1['price_per_person_usd'] );
		$this->assertEquals( 110, $occ_promotion1['price_per_person_cad'] );
		$this->assertEquals( 120, $occ_promotion1['price_per_person_aud'] );
		$this->assertEquals( 130, $occ_promotion1['price_per_person_gbp'] );
		$this->assertEquals( 140, $occ_promotion1['price_per_person_eur'] );

		// Get second occupancy promotion.
		$occ_promotion2 = $occ_promotions[1];
		$this->assertIsArray( $occ_promotion2 );
		$this->assertNotEmpty( $occ_promotion2 );

		// Verify each field.
		$this->assertEquals( 85, $occ_promotion2['price_per_person_usd'] );
		$this->assertEquals( 105, $occ_promotion2['price_per_person_cad'] );
		$this->assertEquals( 115, $occ_promotion2['price_per_person_aud'] );
		$this->assertEquals( 125, $occ_promotion2['price_per_person_gbp'] );
		$this->assertEquals( 135, $occ_promotion2['price_per_person_eur'] );
	}

	/**
	 * Test get occupancy promotions by occupancy id and promotion id.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy_id_and_promotion_id
	 *
	 * @return void
	 */
	public function test_get_occupancy_promotions_by_occupancy_id_and_promotion_id(): void {
		// Test with no arguments.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id();
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with default occupancy id.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 0 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with default promotion id.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with non-existing occupancy id.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 999, 1 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with non-existing promotion id.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1, 999 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Create promotions.
		$promotions = [
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-19T00:00:00',
				'description'   => 'Save 10%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.1',
				'promotionCode' => '10PROMO',
				'isPIF'         => true,
			],
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-28T00:00:00',
				'description'   => 'Save 20%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.2',
				'promotionCode' => '20PROMO',
				'isPIF'         => false,
			],
		];
		$is_saved   = update_promotions( $promotions );
		$this->assertTrue( $is_saved );

		// Get promotions by code.
		$promotions = get_promotions_by_code( '10PROMO' );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );

		// Get first promotion id.
		$promo = $promotions[0];
		$this->assertIsArray( $promo );
		$this->assertNotEmpty( $promo );
		$this->assertArrayHasKey( 'id', $promo );
		$promo_id1 = $promo['id'];
		$this->assertIsInt( $promo_id1 );

		// Get promotions by code.
		$promotions = get_promotions_by_code( '20PROMO' );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );

		// Get second promotion id.
		$promo = $promotions[0];
		$this->assertIsArray( $promo );
		$this->assertNotEmpty( $promo );
		$this->assertArrayHasKey( 'id', $promo );
		$promo_id2 = $promo['id'];
		$this->assertIsInt( $promo_id2 );

		// Create an occupancy.
		$raw_occupancy_data     = [
			'id'                      => 'OCC1',
			'name'                    => 'Single',
			'mask'                    => 'A',
			'availabilityStatus'      => 'O',
			'availabilityDescription' => 'Available',
			'spacesAvailable'         => 11,
			'prices'                  => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 90,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 80,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 70,
						],
					],
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 120,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 110,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 100,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 90,
						],
					],
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 130,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 120,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 110,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 100,
						],
					],
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 140,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 130,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 120,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 110,
						],
					],
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 140,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 130,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 120,
						],
					],
				],
			],
		];
		$departure_post_id      = 3483;
		$cabin_category_post_id = 9343;

		// Format occupancy data.
		$formatted_occupancy_data = format_raw_occupancy_data( $raw_occupancy_data, $cabin_category_post_id, $departure_post_id );
		$this->assertNotEmpty( $formatted_occupancy_data );

		// Global db.
		global $wpdb;

		// Table name.
		$table_name = get_occupancies_table_name();

		// Insert occupancy.
		$wpdb->insert( $table_name, $formatted_occupancy_data );

		// Get occupancy id.
		$occupancy_id = $wpdb->insert_id;
		$this->assertNotEmpty( $occupancy_id );

		// Test with valid occupancy id, but invalid promotion id.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, 999 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with valid occupancy id and promotion id.
		$actual   = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, $promo_id1 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Flush cache.
		wp_cache_flush();

		// Update occupancy promotions.
		$is_updated = update_occupancy_promotions( $raw_occupancy_data['prices'], $occupancy_id );
		$this->assertTrue( $is_updated );

		// Cache keys.
		$cache_key_1 = CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id;
		$cache_key_2 = CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id . '_promotion_id_' . $promo_id1;
		$cache_key_3 = CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id . '_promotion_id_' . $promo_id2;

		// There should be no cache.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_3, CACHE_GROUP ) );

		// Test with valid occupancy id and promotion id.
		$actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, $promo_id1 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been warmed.
		$this->assertNotEmpty( wp_cache_get( $cache_key_2, CACHE_GROUP ) );

		// Get occupancy promotions by occupancy id.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertIsArray( $occ_promotions );
		$this->assertNotEmpty( $occ_promotions );
		$this->assertCount( 2, $occ_promotions );

		// Cache should have been set.
		$this->assertNotEmpty( wp_cache_get( $cache_key_1, CACHE_GROUP ) );

		// Get first occupancy promotion.
		$occ_promotion1 = $actual[0];
		$this->assertIsArray( $occ_promotion1 );
		$this->assertNotEmpty( $occ_promotion1 );

		// Verify each field.
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => $promo_id1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$this->assertEquals( $expected, $occ_promotion1 );

		// Test with valid occupancy id and second promotion id.
		$actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, $promo_id2 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been set.
		$actual_from_cache = wp_cache_get( $cache_key_3, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get second occupancy promotion.
		$occ_promotion2 = $actual[0];
		$this->assertIsArray( $occ_promotion2 );
		$this->assertNotEmpty( $occ_promotion2 );

		// Verify each field.
		$expected = [
			'id'                   => 2,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => $promo_id2,
			'price_per_person_usd' => 80,
			'price_per_person_cad' => 100,
			'price_per_person_aud' => 110,
			'price_per_person_gbp' => 120,
			'price_per_person_eur' => 130,
		];
		$this->assertEquals( $expected, $occ_promotion2 );

		// Create a new promo - 30PROMO.
		$promotions = [
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-28T00:00:00',
				'description'   => 'Save 30%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.3',
				'promotionCode' => '30PROMO',
				'isPIF'         => false,
			],
		];
		$is_saved   = update_promotions( $promotions );
		$this->assertTrue( $is_saved );

		// Update occupancy promotions with new data for occupancy 1.
		$raw_occupancy_promotion_data = [
			'prices' => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 90,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 85,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 80,
						],
					],
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 120,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 110,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 105,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 100,
						],
					],
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 130,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 120,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 115,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 110,
						],
					],
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 140,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 130,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 125,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 120,
						],
					],
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 140,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 135,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 130,
						],
					],
				],
			],
		];

		// Update occupancy promotions.
		$is_updated = update_occupancy_promotions( $raw_occupancy_promotion_data['prices'], $occupancy_id );
		$this->assertTrue( $is_updated );

		// All cache should have been busted due to the update.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_3, CACHE_GROUP ) );

		// Test if data was updated correctly.
		$actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, $promo_id1 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Get occupancy promotions by occupancy id.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertIsArray( $occ_promotions );
		$this->assertNotEmpty( $occ_promotions );
		$this->assertCount( 3, $occ_promotions );

		// Get first occupancy promotion.
		$occ_promotion1 = $occ_promotions[0];
		$this->assertIsArray( $occ_promotion1 );
		$this->assertNotEmpty( $occ_promotion1 );

		// Verify price update for each promotion.
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => $promo_id1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$this->assertEquals( $expected, $occ_promotion1 );

		// Test if data was updated correctly for second promotion.
		$occ_promotion2 = $occ_promotions[1];
		$this->assertIsArray( $occ_promotion2 );
		$this->assertNotEmpty( $occ_promotion2 );

		// Verify price update for each promotion.
		$expected = [
			'id'                   => 2,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => $promo_id2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$this->assertEquals( $expected, $occ_promotion2 );

		// Test if data was updated correctly for third promotion - newly created.
		$occ_promotion3 = $occ_promotions[2];
		$this->assertIsArray( $occ_promotion3 );
		$this->assertNotEmpty( $occ_promotion3 );

		// Verify price update for each promotion.
		$expected = [
			'id'                   => 3,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => 3,
			'price_per_person_usd' => 80,
			'price_per_person_cad' => 100,
			'price_per_person_aud' => 110,
			'price_per_person_gbp' => 120,
			'price_per_person_eur' => 130,
		];
		$this->assertEquals( $expected, $occ_promotion3 );

		// Let's remove 10PROMO from USD price while keeping on other.
		$raw_occupancy_promotion_data = [
			'prices' => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
					'promos'         => [
						'20PROMO' => [
							'promoPricePerPerson' => 85,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 80,
						],
					],
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 120,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 110,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 105,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 100,
						],
					],
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 130,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 120,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 115,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 110,
						],
					],
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 140,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 130,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 125,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 120,
						],
					],
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 140,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 135,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 130,
						],
					],
				],
			],
		];

		// Update occupancy promotions.
		$is_updated = update_occupancy_promotions( $raw_occupancy_promotion_data['prices'], $occupancy_id );
		$this->assertTrue( $is_updated );

		// All cache should have been busted due to the update.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_3, CACHE_GROUP ) );

		// Get occupancy promotions by occupancy id.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertIsArray( $occ_promotions );
		$this->assertNotEmpty( $occ_promotions );
		$this->assertCount( 3, $occ_promotions );

		// Get first occupancy promotion.
		$occ_promotion1 = $occ_promotions[0];
		$this->assertIsArray( $occ_promotion1 );
		$this->assertNotEmpty( $occ_promotion1 );

		// Verify price update for each promotion.
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => $promo_id1,
			'price_per_person_usd' => 0,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$this->assertEquals( $expected, $occ_promotion1 );

		// Get second occupancy promotion.
		$occ_promotion2 = $occ_promotions[1];
		$this->assertIsArray( $occ_promotion2 );
		$this->assertNotEmpty( $occ_promotion2 );

		// Verify price update for each promotion.
		$expected = [
			'id'                   => 2,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => $promo_id2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$this->assertEquals( $expected, $occ_promotion2 );

		// Get third occupancy promotion.
		$occ_promotion3 = $occ_promotions[2];
		$this->assertIsArray( $occ_promotion3 );
		$this->assertNotEmpty( $occ_promotion3 );

		// Verify price update for each promotion.
		$expected = [
			'id'                   => 3,
			'occupancy_id'         => $occupancy_id,
			'promotion_id'         => 3,
			'price_per_person_usd' => 80,
			'price_per_person_cad' => 100,
			'price_per_person_aud' => 110,
			'price_per_person_gbp' => 120,
			'price_per_person_eur' => 130,
		];
		$this->assertEquals( $expected, $occ_promotion3 );

		// Let's remove 10PROMO from all prices.
		$raw_occupancy_promotion_data = [
			'prices' => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
					'promos'         => [
						'20PROMO' => [
							'promoPricePerPerson' => 85,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 80,
						],
					],
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 120,
					'promos'         => [
						'20PROMO' => [
							'promoPricePerPerson' => 105,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 100,
						],
					],
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 130,
					'promos'         => [
						'20PROMO' => [
							'promoPricePerPerson' => 115,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 110,
						],
					],
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 140,
					'promos'         => [
						'20PROMO' => [
							'promoPricePerPerson' => 125,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 120,
						],
					],
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
					'promos'         => [
						'20PROMO' => [
							'promoPricePerPerson' => 135,
						],
						'30PROMO' => [
							'promoPricePerPerson' => 130,
						],
					],
				],
			],
		];

		// Update occupancy promotions.
		$is_updated = update_occupancy_promotions( $raw_occupancy_promotion_data['prices'], $occupancy_id );
		$this->assertTrue( $is_updated );

		// All cache should have been busted due to the update.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_3, CACHE_GROUP ) );

		// Get occupancy promotions by occupancy id.
		$occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
		$this->assertIsArray( $occ_promotions );
		$this->assertNotEmpty( $occ_promotions );
		$this->assertCount( 2, $occ_promotions );
	}

	/**
	 * Test get occupancy promotions by occupancy id.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy
	 *
	 * @return void
	 */
	public function test_get_occupancy_promotions_by_occupancy(): void {
		// Test with no arguments.
		$actual   = get_occupancy_promotions_by_occupancy();
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with default occupancy id.
		$actual   = get_occupancy_promotions_by_occupancy( 0 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Test with non-existing occupancy id.
		$actual   = get_occupancy_promotions_by_occupancy( 999 );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Setup variables.
		global $wpdb;
		$table_name  = get_table_name();
		$cache_key_1 = CACHE_KEY_PREFIX . '_occupancy_id_1';
		$cache_key_2 = CACHE_KEY_PREFIX . '_occupancy_id_2';

		// Insert first occupancy promotion.
		$occupancy_promotion_data1 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data1 );
		$occupancy_promotion_id1 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id1 );

		// Insert second occupancy promotion to same occupancy.
		$occupancy_promotion_data2 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data2 );
		$occupancy_promotion_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id2 );

		// Insert an occupancy promotion to different occupancy.
		$occupancy_promotion_data3 = [
			'occupancy_id'         => 2,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data3 );
		$occupancy_promotion_id3 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id3 );

		// Cache should be empty.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );

		// Test with valid occupancy id.
		$actual = get_occupancy_promotions_by_occupancy( 1 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 2, $actual );

		// Cache should have been set.
		$actual_from_cache = wp_cache_get( $cache_key_1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first occupancy promotion.
		$occ_promotion1 = $actual[0];
		$this->assertIsArray( $occ_promotion1 );
		$this->assertNotEmpty( $occ_promotion1 );

		// Verify first occupancy promotion.
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$this->assertEquals( $expected, $occ_promotion1 );

		// Get second occupancy promotion.
		$occ_promotion2 = $actual[1];
		$this->assertIsArray( $occ_promotion2 );
		$this->assertNotEmpty( $occ_promotion2 );

		// Verify second occupancy promotion.
		$expected = [
			'id'                   => 2,
			'occupancy_id'         => 1,
			'promotion_id'         => 2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$this->assertEquals( $expected, $occ_promotion2 );

		// Cache should be absent for second occupancy.
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );

		// Test for second occupancy.
		$actual = get_occupancy_promotions_by_occupancy( 2 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been set.
		$actual_from_cache = wp_cache_get( $cache_key_2, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first occupancy promotion.
		$occ_promotion1 = $actual[0];
		$this->assertIsArray( $occ_promotion1 );
		$this->assertNotEmpty( $occ_promotion1 );

		// Verify first occupancy promotion.
		$expected = [
			'id'                   => 3,
			'occupancy_id'         => 2,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$this->assertEquals( $expected, $occ_promotion1 );
	}

	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\get_lowest_price
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Setup variables.
		global $wpdb;
		$table_name  = get_table_name();
		$promo_code1 = '10PROMO';
		$promo_code2 = '20PROMO';
		$promo_code3 = '30PROMO';

		// Add all promotions.
		$promotions = [
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-28T00:00:00',
				'description'   => 'Save 10%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.1',
				'promotionCode' => $promo_code1,
				'isPIF'         => false,
			],
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-28T00:00:00',
				'description'   => 'Save 20%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.2',
				'promotionCode' => $promo_code2,
				'isPIF'         => false,
			],
			[
				'endDate'       => '2050-12-31T00:00:00',
				'startDate'     => '2023-09-28T00:00:00',
				'description'   => 'Save 30%',
				'discountType'  => 'percentage_off',
				'discountValue' => '0.3',
				'promotionCode' => $promo_code3,
				'isPIF'         => false,
			],
		];
		$is_saved   = update_promotions( $promotions );
		$this->assertTrue( $is_saved );

		// Get first promotion.
		$promos = get_promotions_by_code( $promo_code1 );
		$this->assertIsArray( $promos );
		$this->assertNotEmpty( $promos );
		$this->assertCount( 1, $promos );
		$promo1 = $promos[0];
		$this->assertIsArray( $promo1 );
		$this->assertNotEmpty( $promo1 );
		$this->assertArrayHasKey( 'id', $promo1 );
		$promo_id1 = $promo1['id'];
		$this->assertIsInt( $promo_id1 );

		// Get second promotion.
		$promos = get_promotions_by_code( $promo_code2 );
		$this->assertIsArray( $promos );
		$this->assertNotEmpty( $promos );
		$this->assertCount( 1, $promos );
		$promo2 = $promos[0];
		$this->assertIsArray( $promo2 );
		$this->assertNotEmpty( $promo2 );
		$this->assertArrayHasKey( 'id', $promo2 );
		$promo_id2 = $promo2['id'];
		$this->assertIsInt( $promo_id2 );

		// Get third promotion.
		$promos = get_promotions_by_code( $promo_code3 );
		$this->assertIsArray( $promos );
		$this->assertNotEmpty( $promos );
		$this->assertCount( 1, $promos );
		$promo3 = $promos[0];
		$this->assertIsArray( $promo3 );
		$this->assertNotEmpty( $promo3 );
		$this->assertArrayHasKey( 'id', $promo3 );
		$promo_id3 = $promo3['id'];
		$this->assertIsInt( $promo_id3 );

		// Insert first occupancy promotion.
		$occupancy_promotion_data1 = [
			'occupancy_id'         => 1,
			'promotion_id'         => $promo_id1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data1 );
		$occupancy_promotion_id1 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id1 );

		// Insert second occupancy promotion.
		$occupancy_promotion_data2 = [
			'occupancy_id'         => 1,
			'promotion_id'         => $promo_id2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data2 );
		$occupancy_promotion_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id2 );

		// Insert third occupancy promotion.
		$occupancy_promotion_data3 = [
			'occupancy_id'         => 2,
			'promotion_id'         => $promo_id1,
			'price_per_person_usd' => 92,
			'price_per_person_cad' => 88,
			'price_per_person_aud' => 121,
			'price_per_person_gbp' => 189,
			'price_per_person_eur' => 110,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data3 );
		$occupancy_promotion_id3 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id3 );

		// Test with no arguments.
		$actual = get_lowest_price();
		$this->assertEquals( 0, $actual );

		// Test with default occupancy id.
		$actual = get_lowest_price( 0 );
		$this->assertEquals( 0, $actual );

		// Test with non-existing occupancy id.
		$actual = get_lowest_price( 999 );
		$this->assertEquals( 0, $actual );

		// Test with valid occupancy id but invalid currency.
		$actual = get_lowest_price( 1, 'XYZ' );
		$this->assertEquals( 0, $actual );

		// Test with valid occupancy id and default currency - USD.
		$actual = get_lowest_price( 1 );
		$this->assertEquals( 85, $actual );

		// Test with valid occupancy id and currency - USD.
		$actual = get_lowest_price( 1, 'USD' );
		$this->assertEquals( 85, $actual );

		// Test with lower case currency.
		$actual = get_lowest_price( 1, 'usd' );
		$this->assertEquals( 85, $actual );

		// Test with valid occupancy id and currency - CAD.
		$actual = get_lowest_price( 1, 'CAD' );
		$this->assertEquals( 105, $actual );

		// Test with valid occupancy id and currency - AUD.
		$actual = get_lowest_price( 1, 'AUD' );
		$this->assertEquals( 115, $actual );

		// Test with valid occupancy id and currency - GBP.
		$actual = get_lowest_price( 1, 'GBP' );
		$this->assertEquals( 125, $actual );

		// Test with valid occupancy id and currency - EUR.
		$actual = get_lowest_price( 1, 'EUR' );
		$this->assertEquals( 135, $actual );

		// Test with occupancy id, currency and promotion code - 10PROMO.
		$actual = get_lowest_price( 1, 'USD', $promo_code1 );
		$this->assertEquals( 90, $actual );

		// Test with occupancy id, currency and promotion code - 20PROMO.
		$actual = get_lowest_price( 1, 'USD', $promo_code2 );
		$this->assertEquals( 85, $actual );

		// Test with occupancy id, currency and promotion code - 30PROMO. Non-associated.
		$actual = get_lowest_price( 1, 'USD', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test with occupancy id, currency and promotion code - 10PROMO CAD.
		$actual = get_lowest_price( 1, 'CAD', $promo_code1 );
		$this->assertEquals( 110, $actual );

		// Test with occupancy id, currency and promotion code - 20PROMO CAD.
		$actual = get_lowest_price( 1, 'CAD', $promo_code2 );
		$this->assertEquals( 105, $actual );

		// Test with occupancy id, currency and promotion code - 30PROMO CAD. Non-associated.
		$actual = get_lowest_price( 1, 'CAD', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test with occupancy id, currency and promotion code - 10PROMO AUD.
		$actual = get_lowest_price( 1, 'AUD', $promo_code1 );
		$this->assertEquals( 120, $actual );

		// Test with occupancy id, currency and promotion code - 20PROMO AUD.
		$actual = get_lowest_price( 1, 'AUD', $promo_code2 );
		$this->assertEquals( 115, $actual );

		// Test with occupancy id, currency and promotion code - 30PROMO AUD. Non-associated.
		$actual = get_lowest_price( 1, 'AUD', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test with occupancy id, currency and promotion code - 10PROMO GBP.
		$actual = get_lowest_price( 1, 'GBP', $promo_code1 );
		$this->assertEquals( 130, $actual );

		// Test with occupancy id, currency and promotion code - 20PROMO GBP.
		$actual = get_lowest_price( 1, 'GBP', $promo_code2 );
		$this->assertEquals( 125, $actual );

		// Test with occupancy id, currency and promotion code - 30PROMO GBP. Non-associated.
		$actual = get_lowest_price( 1, 'GBP', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test with occupancy id, currency and promotion code - 10PROMO EUR.
		$actual = get_lowest_price( 1, 'EUR', $promo_code1 );
		$this->assertEquals( 140, $actual );

		// Test with occupancy id, currency and promotion code - 20PROMO EUR.
		$actual = get_lowest_price( 1, 'EUR', $promo_code2 );
		$this->assertEquals( 135, $actual );

		// Test with occupancy id, currency and promotion code - 30PROMO EUR. Non-associated.
		$actual = get_lowest_price( 1, 'EUR', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test for second occupancy.
		$actual = get_lowest_price( 2 );
		$this->assertEquals( 92, $actual );

		// Insert one more occupancy promotion for second occupancy.
		$occupancy_promotion_data4 = [
			'occupancy_id'         => 2,
			'promotion_id'         => 2,
			'price_per_person_usd' => 80,
			'price_per_person_cad' => 100,
			'price_per_person_aud' => 110,
			'price_per_person_gbp' => 120,
			'price_per_person_eur' => 130,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data4 );
		$occupancy_promotion_id4 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id4 );

		// Flush cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_occupancy_id_2', CACHE_GROUP );

		// Test for price.
		$actual = get_lowest_price( 2 );
		$this->assertEquals( 80, $actual );

		// Test for currency - CAD.
		$actual = get_lowest_price( 2, 'CAD' );
		$this->assertEquals( 88, $actual );

		// Test for currency - AUD.
		$actual = get_lowest_price( 2, 'AUD' );
		$this->assertEquals( 110, $actual );

		// Test for currency - GBP.
		$actual = get_lowest_price( 2, 'GBP' );
		$this->assertEquals( 120, $actual );

		// Test for currency - EUR.
		$actual = get_lowest_price( 2, 'EUR' );
		$this->assertEquals( 110, $actual );

		// Test lowest price by promotion code - 20PROMO.
		$actual = get_lowest_price( 2, 'USD', $promo_code2 );
		$this->assertEquals( 80, $actual );

		// Test lowest price by promotion code - 10PROMO.
		$actual = get_lowest_price( 2, 'USD', $promo_code1 );
		$this->assertEquals( 92, $actual );

		// Test lowest price by promotion code - 30PROMO. Non-associated.
		$actual = get_lowest_price( 2, 'USD', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test lowest price by promotion code - 10PROMO CAD.
		$actual = get_lowest_price( 2, 'CAD', $promo_code1 );
		$this->assertEquals( 88, $actual );

		// Test lowest price by promotion code - 20PROMO CAD.
		$actual = get_lowest_price( 2, 'CAD', $promo_code2 );
		$this->assertEquals( 100, $actual );

		// Test lowest price by promotion code - 30PROMO CAD. Non-associated.
		$actual = get_lowest_price( 2, 'CAD', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test lowest price by promotion code - 10PROMO AUD.
		$actual = get_lowest_price( 2, 'AUD', $promo_code1 );
		$this->assertEquals( 121, $actual );

		// Test lowest price by promotion code - 20PROMO AUD.
		$actual = get_lowest_price( 2, 'AUD', $promo_code2 );
		$this->assertEquals( 110, $actual );

		// Test lowest price by promotion code - 30PROMO AUD. Non-associated.
		$actual = get_lowest_price( 2, 'AUD', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test lowest price by promotion code - 10PROMO GBP.
		$actual = get_lowest_price( 2, 'GBP', $promo_code1 );
		$this->assertEquals( 189, $actual );

		// Test lowest price by promotion code - 20PROMO GBP.
		$actual = get_lowest_price( 2, 'GBP', $promo_code2 );
		$this->assertEquals( 120, $actual );

		// Test lowest price by promotion code - 30PROMO GBP. Non-associated.
		$actual = get_lowest_price( 2, 'GBP', $promo_code3 );
		$this->assertEquals( 0, $actual );

		// Test lowest price by promotion code - 10PROMO EUR.
		$actual = get_lowest_price( 2, 'EUR', $promo_code1 );
		$this->assertEquals( 110, $actual );

		// Test lowest price by promotion code - 20PROMO EUR.
		$actual = get_lowest_price( 2, 'EUR', $promo_code2 );
		$this->assertEquals( 130, $actual );

		// Test lowest price by promotion code - 30PROMO EUR. Non-associated.
		$actual = get_lowest_price( 2, 'EUR', $promo_code3 );
		$this->assertEquals( 0, $actual );
	}

	/**
	 * Test delete occupancy promotion by occupancy id.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_occupancy_id
	 *
	 * @return void
	 */
	public function test_delete_occupancy_promotions_by_occupancy_id(): void {
		// Setup variables.
		global $wpdb;
		$table_name        = get_table_name();
		$cache_key_1       = CACHE_KEY_PREFIX . '_occupancy_id_1';
		$cache_key_2       = CACHE_KEY_PREFIX . '_occupancy_id_2';
		$cache_key_promo_1 = CACHE_KEY_PREFIX . '_occupancy_id_1_promotion_id_1';
		$cache_key_promo_2 = CACHE_KEY_PREFIX . '_occupancy_id_1_promotion_id_2';

		// Insert first occupancy promotion.
		$occupancy_promotion_data1 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data1 );
		$occupancy_promotion_id1 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id1 );

		// Insert second occupancy promotion.
		$occupancy_promotion_data2 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data2 );
		$occupancy_promotion_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id2 );

		// Insert third occupancy promotion.
		$occupancy_promotion_data3 = [
			'occupancy_id'         => 2,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data3 );
		$occupancy_promotion_id3 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id3 );

		// Cache should be empty.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );

		// Test with no arguments.
		$actual = delete_occupancy_promotions_by_occupancy_id();
		$this->assertFalse( $actual );

		// Test with default occupancy id.
		$actual = delete_occupancy_promotions_by_occupancy_id( 0 );
		$this->assertFalse( $actual );

		// Test with non-existing occupancy id.
		$actual = delete_occupancy_promotions_by_occupancy_id( 999 );
		$this->assertTrue( $actual );

		// Get occupancies promos by occupancy id before deletion.
		$occupancy1_promos = get_occupancy_promotions_by_occupancy( 1 );
		$this->assertIsArray( $occupancy1_promos );
		$this->assertNotEmpty( $occupancy1_promos );
		$this->assertCount( 2, $occupancy1_promos );

		// Cache should have been set.
		$actual_from_cache = wp_cache_get( $cache_key_1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $occupancy1_promos, $actual_from_cache );

		// Get occupancies promos by occupancy id and promotion id.
		$occupancy1_promo1 = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1, 1 );
		$this->assertIsArray( $occupancy1_promo1 );
		$this->assertNotEmpty( $occupancy1_promo1 );
		$this->assertCount( 1, $occupancy1_promo1 );

		// Cache should have been set.
		$actual_from_cache = wp_cache_get( $cache_key_promo_1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $occupancy1_promo1, $actual_from_cache );

		// Get occupancies promos by occupancy id and promotion id.
		$occupancy1_promo2 = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1, 2 );
		$this->assertIsArray( $occupancy1_promo2 );
		$this->assertNotEmpty( $occupancy1_promo2 );
		$this->assertCount( 1, $occupancy1_promo2 );

		// Cache should have been set.
		$actual_from_cache = wp_cache_get( $cache_key_promo_2, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $occupancy1_promo2, $actual_from_cache );

		// Test with valid occupancy id.
		$actual = delete_occupancy_promotions_by_occupancy_id( 1 );
		$this->assertTrue( $actual );

		// Cache should have been deleted - occupancy level and promos level.
		$this->assertFalse( wp_cache_get( $cache_key_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_promo_1, CACHE_GROUP ) );
		$this->assertFalse( wp_cache_get( $cache_key_promo_2, CACHE_GROUP ) );

		// Get occupancies promos by occupancy id after deletion.
		$occupancy1_promos = get_occupancy_promotions_by_occupancy( 1 );
		$this->assertIsArray( $occupancy1_promos );
		$this->assertEmpty( $occupancy1_promos );

		// Get occupancies promos by occupancy id and promotion id after deletion.
		$occupancy1_promo1 = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1, 1 );
		$this->assertIsArray( $occupancy1_promo1 );
		$this->assertEmpty( $occupancy1_promo1 );

		// Get occupancies promos by occupancy id and promotion id after deletion.
		$occupancy1_promo2 = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1, 2 );
		$this->assertIsArray( $occupancy1_promo2 );
		$this->assertEmpty( $occupancy1_promo2 );

		// Test for second occupancy.
		$actual = delete_occupancy_promotions_by_occupancy_id( 2 );
		$this->assertTrue( $actual );

		// Cache should have been deleted - occupancy level.
		$this->assertFalse( wp_cache_get( $cache_key_2, CACHE_GROUP ) );

		// Get occupancies promos by occupancy id after deletion.
		$occupancy2_promos = get_occupancy_promotions_by_occupancy( 2 );
		$this->assertIsArray( $occupancy2_promos );
		$this->assertEmpty( $occupancy2_promos );
	}

	/**
	 * Test delete occupancy promotions by id.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_id
	 *
	 * @return void
	 */
	public function test_delete_occupancy_promotions_by_id(): void {
		// Setup variables.
		global $wpdb;
		$table_name = get_table_name();

		// Insert first occupancy promotion.
		$occupancy_promotion_data1 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data1 );
		$occupancy_promotion_id1 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id1 );

		// Insert second occupancy promotion.
		$occupancy_promotion_data2 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data2 );
		$occupancy_promotion_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id2 );

		// Get occupancy promotion by id.
		$occupancy_promotions = get_occupancy_promo_by_id( $occupancy_promotion_id1 );
		$this->assertIsArray( $occupancy_promotions );
		$this->assertNotEmpty( $occupancy_promotions );
		$this->assertCount( 1, $occupancy_promotions );

		// Test with no arguments.
		$actual = delete_occupancy_promotions_by_id();
		$this->assertFalse( $actual );

		// Test with default id.
		$actual = delete_occupancy_promotions_by_id( 0 );
		$this->assertFalse( $actual );

		// Test with non-existing id.
		$actual = delete_occupancy_promotions_by_id( 999 );
		$this->assertFalse( $actual );

		// Test with valid id.
		$actual = delete_occupancy_promotions_by_id( $occupancy_promotion_id1 );
		$this->assertTrue( $actual );

		// Cache should have been deleted.
		$this->assertFalse( wp_cache_get( CACHE_KEY_PREFIX . '_id_' . $occupancy_promotion_id1, CACHE_GROUP ) );

		// Get occupancy promotion by id after deletion.
		$occupancy_promotions = get_occupancy_promo_by_id( $occupancy_promotion_id1 );
		$this->assertIsArray( $occupancy_promotions );
		$this->assertEmpty( $occupancy_promotions );

		// Get occupancy promotion by id.
		$occupancy_promotions = get_occupancy_promo_by_id( $occupancy_promotion_id2 );
		$this->assertIsArray( $occupancy_promotions );
		$this->assertNotEmpty( $occupancy_promotions );
		$this->assertCount( 1, $occupancy_promotions );

		// Test for second id.
		$actual = delete_occupancy_promotions_by_id( $occupancy_promotion_id2 );
		$this->assertTrue( $actual );

		// Cache should have been deleted.
		$this->assertFalse( wp_cache_get( CACHE_KEY_PREFIX . '_id_' . $occupancy_promotion_id2, CACHE_GROUP ) );

		// Get occupancy promotion by id after deletion.
		$occupancy_promotions = get_occupancy_promo_by_id( $occupancy_promotion_id2 );
		$this->assertIsArray( $occupancy_promotions );
		$this->assertEmpty( $occupancy_promotions );
	}

	/**
	 * Get occupancy promo by id.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\get_occupancy_promo_by_id
	 *
	 * @return void
	 */
	public function test_get_occupancy_promo_by_id(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancy_promo_by_id();
		$this->assertEquals( $expected, $actual );

		// Test with default id.
		$expected = [];
		$actual   = get_occupancy_promo_by_id( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing id.
		$expected = [];
		$actual   = get_occupancy_promo_by_id( 999 );
		$this->assertEquals( $expected, $actual );

		// Setup variables.
		global $wpdb;
		$table_name = get_table_name();

		// Insert first occupancy promotion.
		$occupancy_promotion_data1 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data1 );
		$occupancy_promotion_id1 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id1 );

		// Insert second occupancy promotion.
		$occupancy_promotion_data2 = [
			'occupancy_id'         => 1,
			'promotion_id'         => 2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$wpdb->insert( $table_name, $occupancy_promotion_data2 );
		$occupancy_promotion_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_promotion_id2 );

		// Get occupancy promotion by id.
		$occupancy_promotions = get_occupancy_promo_by_id( $occupancy_promotion_id1 );
		$this->assertIsArray( $occupancy_promotions );
		$this->assertNotEmpty( $occupancy_promotions );
		$this->assertCount( 1, $occupancy_promotions );

		// Verify first occupancy promotion.
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$this->assertEquals( $expected, $occupancy_promotions[0] );

		// Get occupancy promotion by id.
		$occupancy_promotions = get_occupancy_promo_by_id( $occupancy_promotion_id2 );
		$this->assertIsArray( $occupancy_promotions );
		$this->assertNotEmpty( $occupancy_promotions );
		$this->assertCount( 1, $occupancy_promotions );

		// Verify second occupancy promotion.
		$expected = [
			'id'                   => 2,
			'occupancy_id'         => 1,
			'promotion_id'         => 2,
			'price_per_person_usd' => 85,
			'price_per_person_cad' => 105,
			'price_per_person_aud' => 115,
			'price_per_person_gbp' => 125,
			'price_per_person_eur' => 135,
		];
		$this->assertEquals( $expected, $occupancy_promotions[0] );
	}

	/**
	 * Format row data from db.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\format_row_data_from_db
	 *
	 * @return void
	 */
	public function test_format_row_data_from_db(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = format_row_data_from_db();
		$this->assertEquals( $expected, $actual );

		// Test with empty array.
		$expected = [];
		$actual   = format_row_data_from_db( [] );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing id - required.
		$data     = [
			'occupancy_id' => '1',
			'promotion_id' => '1',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing occupancy id - required.
		$data     = [
			'id'           => '1',
			'promotion_id' => '1',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing promotion id - required.
		$data     = [
			'id'           => '1',
			'occupancy_id' => '1',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with all required fields.
		$data     = [
			'id'           => '1',
			'occupancy_id' => '1',
			'promotion_id' => '1',
		];
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 0,
			'price_per_person_cad' => 0,
			'price_per_person_aud' => 0,
			'price_per_person_gbp' => 0,
			'price_per_person_eur' => 0,
		];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with valid data.
		$data     = [
			'id'                   => '1',
			'occupancy_id'         => '1',
			'promotion_id'         => '1',
			'price_per_person_usd' => '90',
			'price_per_person_cad' => '110',
			'price_per_person_aud' => '120',
			'price_per_person_gbp' => '130',
			'price_per_person_eur' => '140',
		];
		$expected = [
			'id'                   => 1,
			'occupancy_id'         => 1,
			'promotion_id'         => 1,
			'price_per_person_usd' => 90,
			'price_per_person_cad' => 110,
			'price_per_person_aud' => 120,
			'price_per_person_gbp' => 130,
			'price_per_person_eur' => 140,
		];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Format rows data from db.
	 *
	 * @covers \Quark\Softrip\OccupancyPromotions\format_rows_data_from_db
	 *
	 * @return void
	 */
	public function test_format_rows_data_from_db(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = format_rows_data_from_db();
		$this->assertEquals( $expected, $actual );

		// Test with empty array.
		$expected = [];
		$actual   = format_rows_data_from_db( [] );
		$this->assertEquals( $expected, $actual );

		// Test with single row.
		$data     = [
			[
				'id'                   => '1',
				'occupancy_id'         => '1',
				'promotion_id'         => '1',
				'price_per_person_usd' => '90',
				'price_per_person_cad' => '110',
				'price_per_person_aud' => '120',
				'price_per_person_gbp' => '130',
				'price_per_person_eur' => '140',
			],
		];
		$expected = [
			[
				'id'                   => 1,
				'occupancy_id'         => 1,
				'promotion_id'         => 1,
				'price_per_person_usd' => 90,
				'price_per_person_cad' => 110,
				'price_per_person_aud' => 120,
				'price_per_person_gbp' => 130,
				'price_per_person_eur' => 140,
			],
		];
		$actual   = format_rows_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with multiple rows.
		$data     = [
			[
				'id'                   => '1',
				'occupancy_id'         => '1',
				'promotion_id'         => '1',
				'price_per_person_usd' => '90',
				'price_per_person_cad' => '110',
				'price_per_person_aud' => '120',
				'price_per_person_gbp' => '130',
				'price_per_person_eur' => '140',
			],
			[
				'id'                   => '2',
				'occupancy_id'         => '1',
				'promotion_id'         => '2',
				'price_per_person_usd' => '85',
				'price_per_person_cad' => '105',
				'price_per_person_aud' => '115',
				'price_per_person_gbp' => '125',
				'price_per_person_eur' => '135',
			],
		];
		$expected = [
			[
				'id'                   => 1,
				'occupancy_id'         => 1,
				'promotion_id'         => 1,
				'price_per_person_usd' => 90,
				'price_per_person_cad' => 110,
				'price_per_person_aud' => 120,
				'price_per_person_gbp' => 130,
				'price_per_person_eur' => 140,
			],
			[
				'id'                   => 2,
				'occupancy_id'         => 1,
				'promotion_id'         => 2,
				'price_per_person_usd' => 85,
				'price_per_person_cad' => 105,
				'price_per_person_aud' => 115,
				'price_per_person_gbp' => 125,
				'price_per_person_eur' => 135,
			],
		];
		$actual   = format_rows_data_from_db( $data );
		$this->assertEquals( $expected, $actual );
	}
}
