<?php
/**
 * Test suite for Occupancy Promotions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Occupancy_Promotions;

use Quark\Tests\Softrip\Softrip_TestCase;

use const Quark\Softrip\OccupancyPromotions\CACHE_GROUP;
use const Quark\Softrip\OccupancyPromotions\CACHE_KEY_PREFIX;
use const Quark\Softrip\TABLE_PREFIX_NAME;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\Occupancies\format_data as format_raw_occupancy_data;
use function Quark\Softrip\Occupancies\get_table_name as get_occupancies_table_name;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy_id_and_promotion_id;
use function Quark\Softrip\OccupancyPromotions\get_table_name;
use function Quark\Softrip\OccupancyPromotions\get_table_sql;
use function Quark\Softrip\OccupancyPromotions\update_occupancy_promotions;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\update_promotions;

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
        $actual = update_occupancy_promotions( [
            [
                'USD' => [
                    'currencyCode' => 'USD',
                    'pricePerPerson' => 100,
                    'promos' => [
                        '10PROMO' => 90,
                    ]
                ]
            ]
        ], 999 );
        $this->assertFalse( $actual );

        // Create an occupancy.
        $raw_occupancy_data = [
            'id' => 'OCC1',
            'name' => 'Single',
            'mask' => 'A',
            'availabilityStatus' => 'O',
            'availabilityDescription' => 'Available',
            'spacesAvailable' => 11,
            'prices' => [
                'USD' => [
                    'currencyCode' => 'USD',
                    'pricePerPerson' => 100,
                ]
            ]
        ];
        $departure_post_id = 3483;
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
                'currencyCode' => 'USD',
                'pricePerPerson' => 100,
                'promos' => [
                    '10PROMO' => 90,
                ]
            ]
        ];
        $actual = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
        $this->assertTrue( $actual );

        // Get occupancy promotions by occupancy id - should be empty.
        $occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
        $this->assertEmpty( $occ_promotions );

        // Test with valid occupancy id and promotions raw data.
        $raw_promotions_data = [
            'USD' => [
                'currencyCode' => 'USD',
                'pricePerPerson' => 100,
                'promos' => [
                    '10PROMO' => [
                        'pricePerPerson' => 90,
                    ],
                    '20PROMO' => [
                        'pricePerPerson' => 80,
                    ],
                ]
            ]
        ];
        $actual = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
        $this->assertTrue( $actual );

        // Get occupancy promotions by occupancy id - still empty. Because promotions are not created yet.
        $occ_promotions = get_occupancy_promotions_by_occupancy( $occupancy_id, true );
        $this->assertEmpty( $occ_promotions );

        // Create promotions.
        $promotions = [
            [
                "endDate" => "2050-12-31T00:00:00",
                "startDate" => "2023-09-19T00:00:00",
                "description" => "Save 10%",
                "discountType" => "percentage_off",
                "discountValue" => "0.1",
                "promotionCode" => "10PROMO",
                "isPIF" => true
            ],
            [
                "endDate" => "2050-12-31T00:00:00",
                "startDate" => "2023-09-28T00:00:00",
                "description" => "Save 20%",
                "discountType" => "percentage_off",
                "discountValue" => "0.2",
                "promotionCode" => "20PROMO",
                "isPIF" => false
            ]
        ];
        $is_saved = update_promotions( $promotions );
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
                'currencyCode' => 'USD',
                'pricePerPerson' => 100,
                'promos' => [
                    '10PROMO' => [
                        'promoPricePerPerson' => 90,
                    ],
                    '20PROMO' => [
                        'promoPricePerPerson' => 80,
                    ],
                ]
            ]
        ];
        $actual = update_occupancy_promotions( $raw_promotions_data, $occupancy_id );
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
        $promos = get_promotions_by_code('10PROMO');
        $this->assertNotEmpty( $promos );
        $this->assertIsArray( $promos );
        $this->assertCount( 1, $promos );
        $promo = $promos[0];
        $this->assertIsArray( $promo );
        $this->assertNotEmpty( $promo );
        $this->assertArrayHasKey( 'id', $promo );
        $promo_id1 = $promo['id'];

        // Get promotion id by code.
        $promos = get_promotions_by_code('20PROMO');
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
                'currencyCode' => 'USD',
                'pricePerPerson' => 100,
                'promos' => [
                    '10PROMO' => [
                        'promoPricePerPerson' => 90, // old.
                    ],
                    '20PROMO' => [
                        'promoPricePerPerson' => 85,
                    ],
                ]
            ],
            'CAD' => [
                'currencyCode' => 'CAD',
                'pricePerPerson' => 120,
                'promos' => [
                    '10PROMO' => [
                        'promoPricePerPerson' => 110,
                    ],
                    '20PROMO' => [
                        'promoPricePerPerson' => 105,
                    ],
                ]
            ],
            'AUD' => [
                'currencyCode' => 'AUD',
                'pricePerPerson' => 130,
                'promos' => [
                    '10PROMO' => [
                        'promoPricePerPerson' => 120,
                    ],
                    '20PROMO' => [
                        'promoPricePerPerson' => 115,
                    ],
                ]
            ],
            'GBP' => [
                'currencyCode' => 'GBP',
                'pricePerPerson' => 140,
                'promos' => [
                    '10PROMO' => [
                        'promoPricePerPerson' => 130,
                    ],
                    '20PROMO' => [
                        'promoPricePerPerson' => 125,
                    ],
                ]
            ],
            'EUR' => [
                'currencyCode' => 'EUR',
                'pricePerPerson' => 150,
                'promos' => [
                    '10PROMO' => [
                        'promoPricePerPerson' => 140,
                    ],
                    '20PROMO' => [
                        'promoPricePerPerson' => 135,
                    ],
                ]
            ]
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
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id();
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Test with default occupancy id.
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 0 );
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Test with default promotion id.
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1 );
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Test with non-existing occupancy id.
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 999, 1 );
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Test with non-existing promotion id.
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( 1, 999 );
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Create promotions.
        $promotions = [
            [
                "endDate" => "2050-12-31T00:00:00",
                "startDate" => "2023-09-19T00:00:00",
                "description" => "Save 10%",
                "discountType" => "percentage_off",
                "discountValue" => "0.1",
                "promotionCode" => "10PROMO",
                "isPIF" => true
            ],
            [
                "endDate" => "2050-12-31T00:00:00",
                "startDate" => "2023-09-28T00:00:00",
                "description" => "Save 20%",
                "discountType" => "percentage_off",
                "discountValue" => "0.2",
                "promotionCode" => "20PROMO",
                "isPIF" => false
            ]
        ];
        $is_saved = update_promotions( $promotions );
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
        $raw_occupancy_data = [
            'id' => 'OCC1',
            'name' => 'Single',
            'mask' => 'A',
            'availabilityStatus' => 'O',
            'availabilityDescription' => 'Available',
            'spacesAvailable' => 11,
            'prices' => [
                'USD' => [
                    'currencyCode' => 'USD',
                    'pricePerPerson' => 100,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 90,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 80,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 70,
                        ],
                    ]
                ],
                'CAD' => [
                    'currencyCode' => 'CAD',
                    'pricePerPerson' => 120,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 100,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 90,
                        ],
                    ]
                ],
                'AUD' => [
                    'currencyCode' => 'AUD',
                    'pricePerPerson' => 130,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 100,
                        ],
                    ]
                ],
                'GBP' => [
                    'currencyCode' => 'GBP',
                    'pricePerPerson' => 140,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                    ]
                ],
                'EUR' => [
                    'currencyCode' => 'EUR',
                    'pricePerPerson' => 150,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 140,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                    ]
                ]
            ]
        ];
        $departure_post_id = 3483;
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
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, 999 );
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Test with valid occupancy id and promotion id.
        $actual = get_occupancy_promotions_by_occupancy_id_and_promotion_id( $occupancy_id, $promo_id1 );
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
            'id' => 1,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => $promo_id1,
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
            'id' => 2,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => $promo_id2,
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
                "endDate" => "2050-12-31T00:00:00",
                "startDate" => "2023-09-28T00:00:00",
                "description" => "Save 30%",
                "discountType" => "percentage_off",
                "discountValue" => "0.3",
                "promotionCode" => "30PROMO",
                "isPIF" => false
            ]
        ];
        $is_saved = update_promotions( $promotions );
        $this->assertTrue( $is_saved );

        // Update occupancy promotions with new data for occupancy 1.
        $raw_occupancy_promotion_data = [
            'prices' => [
                'USD' => [
                    'currencyCode' => 'USD',
                    'pricePerPerson' => 100,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 90,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 85,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 80,
                        ],
                    ]
                ],
                'CAD' => [
                    'currencyCode' => 'CAD',
                    'pricePerPerson' => 120,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 105,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 100,
                        ],
                    ]
                ],
                'AUD' => [
                    'currencyCode' => 'AUD',
                    'pricePerPerson' => 130,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 115,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                    ]
                ],
                'GBP' => [
                    'currencyCode' => 'GBP',
                    'pricePerPerson' => 140,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 125,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                    ]
                ],
                'EUR' => [
                    'currencyCode' => 'EUR',
                    'pricePerPerson' => 150,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 140,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 135,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                    ]
                ]
            ]
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
            'id' => 1,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => $promo_id1,
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
            'id' => 2,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => $promo_id2,
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
            'id' => 3,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => 3,
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
                    'currencyCode' => 'USD',
                    'pricePerPerson' => 100,
                    'promos' => [
                        '20PROMO' => [
                            'promoPricePerPerson' => 85,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 80,
                        ],
                    ]
                ],
                'CAD' => [
                    'currencyCode' => 'CAD',
                    'pricePerPerson' => 120,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 105,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 100,
                        ],
                    ]
                ],
                'AUD' => [
                    'currencyCode' => 'AUD',
                    'pricePerPerson' => 130,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 115,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                    ]
                ],
                'GBP' => [
                    'currencyCode' => 'GBP',
                    'pricePerPerson' => 140,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 125,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                    ]
                ],
                'EUR' => [
                    'currencyCode' => 'EUR',
                    'pricePerPerson' => 150,
                    'promos' => [
                        '10PROMO' => [
                            'promoPricePerPerson' => 140,
                        ],
                        '20PROMO' => [
                            'promoPricePerPerson' => 135,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                    ]
                ]
            ]
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
            'id' => 1,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => $promo_id1,
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
            'id' => 2,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => $promo_id2,
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
            'id' => 3,
            'occupancy_id' => $occupancy_id,
            'promotion_id' => 3,
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
                    'currencyCode' => 'USD',
                    'pricePerPerson' => 100,
                    'promos' => [
                        '20PROMO' => [
                            'promoPricePerPerson' => 85,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 80,
                        ],
                    ]
                ],
                'CAD' => [
                    'currencyCode' => 'CAD',
                    'pricePerPerson' => 120,
                    'promos' => [
                        '20PROMO' => [
                            'promoPricePerPerson' => 105,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 100,
                        ],
                    ]
                ],
                'AUD' => [
                    'currencyCode' => 'AUD',
                    'pricePerPerson' => 130,
                    'promos' => [
                        '20PROMO' => [
                            'promoPricePerPerson' => 115,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 110,
                        ],
                    ]
                ],
                'GBP' => [
                    'currencyCode' => 'GBP',
                    'pricePerPerson' => 140,
                    'promos' => [
                        '20PROMO' => [
                            'promoPricePerPerson' => 125,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 120,
                        ],
                    ]
                ],
                'EUR' => [
                    'currencyCode' => 'EUR',
                    'pricePerPerson' => 150,
                    'promos' => [
                        '20PROMO' => [
                            'promoPricePerPerson' => 135,
                        ],
                        '30PROMO' => [
                            'promoPricePerPerson' => 130,
                        ],
                    ]
                ]
            ]
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
}
