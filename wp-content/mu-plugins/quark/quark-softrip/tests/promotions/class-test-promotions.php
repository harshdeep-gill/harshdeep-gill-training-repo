<?php
/**
 * Test suite for Promotions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Promotions;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\Promotions\format_data;
use function Quark\Softrip\Promotions\format_row_data_from_db;
use function Quark\Softrip\Promotions\format_rows_data_from_db;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\get_promotions_by_id;
use function Quark\Softrip\Promotions\get_table_name;
use function Quark\Softrip\Promotions\get_table_sql;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Softrip\Promotions\CACHE_GROUP;
use const Quark\Softrip\Promotions\CACHE_KEY_PREFIX;
use const Quark\Softrip\TABLE_PREFIX_NAME;

/**
 * Class Test_Promotions
 */
class Test_Promotions extends Softrip_TestCase {
	/**
	 * Test get table name.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_table_name
	 *
	 * @return void
	 */
	public function test_get_table_name(): void {
		// Test table name.
		$expected = TABLE_PREFIX_NAME . 'promotions';
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
			code VARCHAR(255) NOT NULL UNIQUE,
			start_date VARCHAR(20) NOT NULL,
			end_date VARCHAR(20) NOT NULL,
			description VARCHAR(255) NOT NULL,
			discount_type VARCHAR(255) NOT NULL,
			discount_value VARCHAR(255) NOT NULL,
			is_pif TINYINT(1) NOT NULL,
			currency VARCHAR(255) DEFAULT NULL
		) $engine_collate";
		$actual       = get_table_sql();

		// Replace \n\r\s with empty string.
		$expected_sql = preg_replace( '/\r|\n|\s+/', '', $expected_sql );
		$actual       = preg_replace( '/\r|\n|\s+/', '', $actual );
		$this->assertEquals( $expected_sql, $actual );
	}

	/**
	 * Test update promotions.
	 *
	 * @covers \Quark\Softrip\Promotions\update_promotions
	 *
	 * @return void
	 */
	public function test_update_promotions(): void {
		// Test with no arguments.
		$expected = false;
		$actual   = update_promotions();
		$this->assertSame( $expected, $actual );

		// Test with default arguments.
		$expected = false;
		$actual   = update_promotions( [] );
		$this->assertSame( $expected, $actual );

		// Test with invalid data.
		$expected           = false;
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => 'true',
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = update_promotions( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Create departure post.
		$departure_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_id );

		// Test with valid data.
		$expected           = true;
		$raw_promotion_data = [
			[
				'description'   => 'Test Promotion',
				'discountType'  => 'percentage',
				'discountValue' => '10',
				'isPIF'         => true,
				'startDate'     => '2021-01-01T00:00:00',
				'endDate'       => '2021-01-01T00:00:00',
				'promotionCode' => 'test-promotion',
			],
		];
		$actual             = update_promotions( $raw_promotion_data, $departure_id );
		$this->assertSame( $expected, $actual );

		// Get promotion by code.
		$promotions = get_promotions_by_code( 'test-promotion' );
		$this->assertNotEmpty( $promotions );
		$this->assertIsArray( $promotions );
		$this->assertCount( 1, $promotions );

		// Get first promotion.
		$promotion = $promotions[0];
		$this->assertIsArray( $promotion );
		$this->assertArrayHasKey( 'id', $promotion );
		$this->assertArrayHasKey( 'code', $promotion );
		$this->assertArrayHasKey( 'start_date', $promotion );
		$this->assertArrayHasKey( 'end_date', $promotion );
		$this->assertArrayHasKey( 'description', $promotion );
		$this->assertArrayHasKey( 'discount_type', $promotion );
		$this->assertArrayHasKey( 'discount_value', $promotion );
		$this->assertArrayHasKey( 'is_pif', $promotion );
		$this->assertSame( 'test-promotion', $promotion['code'] );
		$this->assertSame( 'Test Promotion', $promotion['description'] );
		$this->assertSame( 'percentage', $promotion['discount_type'] );
		$this->assertSame( '10', $promotion['discount_value'] );
		$this->assertEquals( 1, $promotion['is_pif'] );
		$this->assertSame( '2021-01-01T00:00:00', $promotion['start_date'] );
		$this->assertSame( '2021-01-01T00:00:00', $promotion['end_date'] );

		// Test with special characters - update this time.
		$expected           = true;
		$raw_promotion_data = [
			[
				'description'   => '<h1>Test Promotion 1</h1>',
				'discountType'  => 'relative',
				'discountValue' => '10',
				'isPIF'         => true,
				'startDate'     => '2021-01-01T00:00:00',
				'endDate'       => '2021-01-01T00:00:00',
				'promotionCode' => 'test-promotion',
			],
		];
		$actual             = update_promotions( $raw_promotion_data, $departure_id );
		$this->assertSame( $expected, $actual );

		// Get promotion by code.
		$promotions = get_promotions_by_code( 'test-promotion' );
		$this->assertNotEmpty( $promotions );
		$this->assertIsArray( $promotions );
		$this->assertCount( 1, $promotions );

		// Get first promotion.
		$promotion = $promotions[0];
		$this->assertIsArray( $promotion );
		$this->assertEquals( 'test-promotion', $promotion['code'] );
		$this->assertEquals( 'Test Promotion 1', $promotion['description'] );
		$this->assertEquals( 'relative', $promotion['discount_type'] );
		$this->assertEquals( '10', $promotion['discount_value'] );
		$this->assertEquals( 1, $promotion['is_pif'] );
		$this->assertEquals( '2021-01-01T00:00:00', $promotion['start_date'] );
		$this->assertEquals( '2021-01-01T00:00:00', $promotion['end_date'] );

		// Get promotion codes on departure 1.
		$codes = get_post_meta( $departure_id, 'promotion_codes', true );
		$this->assertNotEmpty( $codes );
		$this->assertIsArray( $codes );
		$this->assertCount( 1, $codes );
		$this->assertEquals( 'test-promotion', $codes[0] );

		// Test with multiple promotions.
		$expected           = true;
		$raw_promotion_data = [
			[
				'description'   => 'Test Promotion 2',
				'discountType'  => 'percentage',
				'discountValue' => '20',
				'isPIF'         => false,
				'startDate'     => '2021-01-01T00:00:00',
				'endDate'       => '2021-01-01T00:00:00',
				'promotionCode' => 'test-promotion-2',
			],
			[
				'description'   => 'Test Promotion 3',
				'discountType'  => 'percentage',
				'discountValue' => '30',
				'isPIF'         => true,
				'startDate'     => '2021-01-01T00:00:00',
				'endDate'       => '2021-01-01T00:00:00',
				'promotionCode' => 'test-promotion-3',
			],
		];
		$actual             = update_promotions( $raw_promotion_data, $departure_id );
		$this->assertSame( $expected, $actual );

		// Get promotion by code.
		$promotions = get_promotions_by_code( 'test-promotion-2' );
		$this->assertNotEmpty( $promotions );
		$this->assertIsArray( $promotions );
		$this->assertCount( 1, $promotions );

		// Get first promotion.
		$promotion = $promotions[0];
		$this->assertIsArray( $promotion );
		$this->assertEquals( 'test-promotion-2', $promotion['code'] );
		$this->assertEquals( 'Test Promotion 2', $promotion['description'] );
		$this->assertEquals( 'percentage', $promotion['discount_type'] );
		$this->assertEquals( '20', $promotion['discount_value'] );
		$this->assertEquals( 0, $promotion['is_pif'] );
		$this->assertEquals( '2021-01-01T00:00:00', $promotion['start_date'] );
		$this->assertEquals( '2021-01-01T00:00:00', $promotion['end_date'] );

		// Get promotion by code.
		$promotions = get_promotions_by_code( 'test-promotion-3' );
		$this->assertNotEmpty( $promotions );
		$this->assertIsArray( $promotions );
		$this->assertCount( 1, $promotions );

		// Get first promotion.
		$promotion = $promotions[0];
		$this->assertIsArray( $promotion );
		$this->assertEquals( 'test-promotion-3', $promotion['code'] );
		$this->assertEquals( 'Test Promotion 3', $promotion['description'] );
		$this->assertEquals( 'percentage', $promotion['discount_type'] );
		$this->assertEquals( '30', $promotion['discount_value'] );
		$this->assertEquals( 1, $promotion['is_pif'] );
		$this->assertEquals( '2021-01-01T00:00:00', $promotion['start_date'] );
		$this->assertEquals( '2021-01-01T00:00:00', $promotion['end_date'] );

		// Get promotion codes on departure 1.
		$codes = get_post_meta( $departure_id, 'promotion_codes', true );
		$this->assertNotEmpty( $codes );
		$this->assertIsArray( $codes );
		$this->assertCount( 2, $codes );
		$this->assertNotContains( 'test-promotion', $codes );
		$this->assertContains( 'test-promotion-2', $codes );
		$this->assertContains( 'test-promotion-3', $codes );
	}

	/**
	 * Test format data.
	 *
	 * @covers \Quark\Softrip\Promotions\format_data
	 *
	 * @return void
	 */
	public function test_format_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = format_data();
		$this->assertSame( $expected, $actual );

		// Test with default arguments.
		$expected = [];
		$actual   = format_data( [] );
		$this->assertSame( $expected, $actual );

		// Test with non-existent end date.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => 'true',
			'startDate'     => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with empty end date.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with non-existent start date.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with empty start date.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with non-existent description.
		$expected           = [];
		$raw_promotion_data = [
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with empty description.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => '',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with non-existent discount type.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with empty discount type.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => '',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with non-existent discount value.
		$expected           = [];
		$raw_promotion_data = [
			'description'  => 'Test Promotion',
			'discountType' => 'percentage',
			'isPif'        => true,
			'startDate'    => '2021-01-01T00:00:00',
			'endDate'      => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with empty discount value.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with non-existent promotion code.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with empty promotion code.
		$expected           = [];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPif'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
			'promotionCode' => '',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with currency as null.
		$expected           = [
			'end_date'       => '2021-01-01T00:00:00',
			'start_date'     => '2021-01-01T00:00:00',
			'description'    => 'Test Promotion',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'code'           => 'test-promotion',
			'currency'       => null,
			'is_pif'         => 1,
		];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPIF'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
			'promotionCode' => 'test-promotion',
			'currencyCode'  => null,
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with valid data.
		$expected           = [
			'end_date'       => '2021-01-01T00:00:00',
			'start_date'     => '2021-01-01T00:00:00',
			'description'    => 'Test Promotion',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'code'           => 'test-promotion',
			'currency'       => 'USD',
			'is_pif'         => 1,
		];
		$raw_promotion_data = [
			'description'   => 'Test Promotion',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPIF'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
			'currencyCode'  => 'USD',
			'promotionCode' => 'test-promotion',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with special characters, invalid end date.
		$expected           = [
			'description'    => 'Test Promotion',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'code'           => 'test-promotion',
			'is_pif'         => 0,
			'start_date'     => '2021-01-01T00:00:00',
			'end_date'       => '2',
			'currency'       => null,
		];
		$raw_promotion_data = [
			'description'   => '<h1>Test Promotion</h1>',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPIF'         => false,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => 2,
			'promotionCode' => 'test-promotion',
		];
		$actual             = format_data( $raw_promotion_data );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get promotions by code.
	 *
	 * @covers \Quark\Softrip\Promotions\get_promotions_by_code
	 *
	 * @return void
	 */
	public function test_get_promotions_by_code(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_promotions_by_code();
		$this->assertSame( $expected, $actual );

		// Test with default arguments.
		$expected = [];
		$actual   = get_promotions_by_code( '' );
		$this->assertSame( $expected, $actual );

		// Test with non-existent promotion code.
		$expected = [];
		$actual   = get_promotions_by_code( 'test-promotion' );
		$this->assertSame( $expected, $actual );

		// Setup variables.
		global $wpdb;
		$table_name  = get_table_name();
		$promo_code1 = 'test1-promotion';
		$promo_code2 = 'test2-promotion';
		$cache_key1  = CACHE_KEY_PREFIX . '_promotion_code_' . $promo_code1;
		$cache_key2  = CACHE_KEY_PREFIX . '_promotion_code_' . $promo_code2;

		// First raw promotion data.
		$raw_promotion_data1 = [
			'description'   => 'Test Promotion 1',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPIF'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
			'promotionCode' => 'test1-promotion',
		];

		// Second raw promotion data.
		$raw_promotion_data2 = [
			'description'   => 'Test Promotion 2',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPIF'         => true,
			'startDate'     => '2022-03-21',
			'endDate'       => '2022-04-03',
			'promotionCode' => 'test2-promotion',
		];

		// There should be no promotions in the database.
		$expected = [];
		$actual   = get_promotions_by_code( 'test1-promotion' );
		$this->assertSame( $expected, $actual );

		// Cache should have been warmed up.
		$expected          = [];
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertSame( $expected, $actual_from_cache );

		// Remove the cache for further testing.
		wp_cache_delete( $cache_key1, CACHE_GROUP );

		// Insert the first promotion.
		$formatted_data1 = format_data( $raw_promotion_data1 );
		$this->assertNotEmpty( $formatted_data1 );
		$wpdb->insert( $table_name, $formatted_data1 );

		// Get the inserted ID.
		$promo_id1 = $wpdb->insert_id;

		// There should be no cache yet.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache1 );

		// Get the promotions by code.
		$actual1 = get_promotions_by_code( 'test1-promotion' );
		$this->assertNotEmpty( $actual1 );
		$this->assertIsArray( $actual1 );
		$this->assertCount( 1, $actual1 );

		// Cache should have been warmed up.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache1 );
		$this->assertIsArray( $actual_from_cache1 );
		$this->assertEquals( $actual1, $actual_from_cache1 );

		// Pick first promotion.
		$actual_promo1 = $actual1[0];
		$this->assertIsArray( $actual_promo1 );
		$this->assertArrayHasKey( 'id', $actual_promo1 );
		$this->assertArrayHasKey( 'code', $actual_promo1 );
		$this->assertArrayHasKey( 'start_date', $actual_promo1 );
		$this->assertArrayHasKey( 'end_date', $actual_promo1 );
		$this->assertArrayHasKey( 'description', $actual_promo1 );
		$this->assertArrayHasKey( 'discount_type', $actual_promo1 );
		$this->assertArrayHasKey( 'discount_value', $actual_promo1 );
		$this->assertArrayHasKey( 'is_pif', $actual_promo1 );
		$this->assertSame( $promo_code1, $actual_promo1['code'] );
		$this->assertSame( $formatted_data1['description'], $actual_promo1['description'] );
		$this->assertSame( $formatted_data1['discount_type'], $actual_promo1['discount_type'] );
		$this->assertSame( $formatted_data1['discount_value'], $actual_promo1['discount_value'] );
		$this->assertEquals( $formatted_data1['is_pif'], $actual_promo1['is_pif'] );
		$this->assertSame( $formatted_data1['start_date'], $actual_promo1['start_date'] );
		$this->assertSame( $formatted_data1['end_date'], $actual_promo1['end_date'] );

		// Insert the second promotion.
		$formatted_data2 = format_data( $raw_promotion_data2 );
		$this->assertNotEmpty( $formatted_data2 );
		$wpdb->insert( $table_name, $formatted_data2 );

		// Get the inserted ID.
		$promo_id2 = $wpdb->insert_id;

		// There should be no cache yet.
		$actual_from_cache2 = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache2 );

		// Get the promotions by code.
		$actual2 = get_promotions_by_code( 'test2-promotion' );
		$this->assertNotEmpty( $actual2 );
		$this->assertIsArray( $actual2 );
		$this->assertCount( 1, $actual2 );

		// Cache should have been warmed up.
		$actual_from_cache2 = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache2 );
		$this->assertIsArray( $actual_from_cache2 );
		$this->assertEquals( $actual2, $actual_from_cache2 );

		// Pick second promotion.
		$actual_promo2 = $actual2[0];
		$this->assertIsArray( $actual_promo2 );
		$this->assertArrayHasKey( 'id', $actual_promo2 );
		$this->assertArrayHasKey( 'code', $actual_promo2 );
		$this->assertArrayHasKey( 'start_date', $actual_promo2 );
		$this->assertArrayHasKey( 'end_date', $actual_promo2 );
		$this->assertArrayHasKey( 'description', $actual_promo2 );
		$this->assertArrayHasKey( 'discount_type', $actual_promo2 );
		$this->assertArrayHasKey( 'discount_value', $actual_promo2 );
		$this->assertArrayHasKey( 'is_pif', $actual_promo2 );
		$this->assertSame( $promo_code2, $actual_promo2['code'] );
		$this->assertSame( $formatted_data2['description'], $actual_promo2['description'] );
		$this->assertSame( $formatted_data2['discount_type'], $actual_promo2['discount_type'] );
		$this->assertSame( $formatted_data2['discount_value'], $actual_promo2['discount_value'] );
		$this->assertEquals( $formatted_data2['is_pif'], $actual_promo2['is_pif'] );
		$this->assertSame( $formatted_data2['start_date'], $actual_promo2['start_date'] );
		$this->assertSame( $formatted_data2['end_date'], $actual_promo2['end_date'] );
	}

	/**
	 * Test get promotions by id.
	 *
	 * @covers \Quark\Softrip\Promotions\get_promotions_by_id
	 *
	 * @return void
	 */
	public function test_get_promotions_by_id(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_promotions_by_id();
		$this->assertSame( $expected, $actual );

		// Test with default arguments.
		$expected = [];
		$actual   = get_promotions_by_id( 0 );
		$this->assertSame( $expected, $actual );

		// Test with non-existent promotion ID.
		$expected = [];
		$actual   = get_promotions_by_id( 1 );
		$this->assertSame( $expected, $actual );

		// Remove the cache for further testing.
		wp_cache_delete( CACHE_KEY_PREFIX . '_promotion_id_1', CACHE_GROUP );

		// Setup variables.
		global $wpdb;
		$table_name  = get_table_name();
		$promo_code1 = 'test1-promotion';

		// First raw promotion data.
		$raw_promotion_data1 = [
			'description'   => 'Test Promotion 1',
			'discountType'  => 'percentage',
			'discountValue' => '10',
			'isPIF'         => true,
			'startDate'     => '2021-01-01T00:00:00',
			'endDate'       => '2021-01-01T00:00:00',
			'promotionCode' => $promo_code1,
		];

		// Insert the first promotion.
		$formatted_data1 = format_data( $raw_promotion_data1 );
		$this->assertNotEmpty( $formatted_data1 );
		$wpdb->insert( $table_name, $formatted_data1 );

		// Get the inserted ID.
		$promo_id1 = $wpdb->insert_id;

		// Cache key.
		$cache_key1 = CACHE_KEY_PREFIX . '_promotion_id_' . $promo_id1;

		// There should be no cache yet.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache1 );

		// Get the promotions by ID.
		$actual1 = get_promotions_by_id( $promo_id1 );
		$this->assertNotEmpty( $actual1 );
		$this->assertIsArray( $actual1 );
		$this->assertCount( 1, $actual1 );

		// Cache should have been warmed up.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache1 );
		$this->assertIsArray( $actual_from_cache1 );
		$this->assertEquals( $actual1, $actual_from_cache1 );

		// Pick first promotion.
		$actual_promo1 = $actual1[0];
		$this->assertIsArray( $actual_promo1 );
		$this->assertArrayHasKey( 'id', $actual_promo1 );
		$this->assertArrayHasKey( 'code', $actual_promo1 );
		$this->assertArrayHasKey( 'start_date', $actual_promo1 );
		$this->assertArrayHasKey( 'end_date', $actual_promo1 );
		$this->assertArrayHasKey( 'description', $actual_promo1 );
		$this->assertArrayHasKey( 'discount_type', $actual_promo1 );
		$this->assertArrayHasKey( 'discount_value', $actual_promo1 );
		$this->assertArrayHasKey( 'is_pif', $actual_promo1 );
		$this->assertSame( $promo_code1, $actual_promo1['code'] );
		$this->assertSame( $formatted_data1['description'], $actual_promo1['description'] );
		$this->assertSame( $formatted_data1['discount_type'], $actual_promo1['discount_type'] );
		$this->assertSame( $formatted_data1['discount_value'], $actual_promo1['discount_value'] );
		$this->assertEquals( $formatted_data1['is_pif'], $actual_promo1['is_pif'] );
		$this->assertSame( $formatted_data1['start_date'], $actual_promo1['start_date'] );
		$this->assertSame( $formatted_data1['end_date'], $actual_promo1['end_date'] );
	}

	/**
	 * Test format row data from db.
	 *
	 * @covers \Quark\Softrip\Promotions\format_row_data_from_db
	 *
	 * @return void
	 */
	public function test_format_row_data_from_db(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = format_row_data_from_db();
		$this->assertSame( $expected, $actual );

		// Test with default arguments.
		$expected = [];
		$actual   = format_row_data_from_db( [] );
		$this->assertSame( $expected, $actual );

		// Test with valid data.
		$expected           = [
			'id'             => 1,
			'code'           => 'test-promotion',
			'start_date'     => '2021-01-01T00:00:00',
			'end_date'       => '2021-01-01T00:00:00',
			'description'    => 'Test Promotion',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'currency'       => null,
			'is_pif'         => 1,
		];
		$raw_promotion_data = [
			'id'             => '1',
			'code'           => 'test-promotion',
			'start_date'     => '2021-01-01T00:00:00',
			'end_date'       => '2021-01-01T00:00:00',
			'description'    => 'Test Promotion',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'is_pif'         => '1',
			'currency'       => null,
		];
		$actual             = format_row_data_from_db( $raw_promotion_data );
		$this->assertSame( $expected, $actual );

		// Test with special characters.
		$expected           = [
			'id'             => 1,
			'code'           => 'test-promotion',
			'start_date'     => '2021-01-01T00:00:00',
			'end_date'       => '2021-01-01T00:00:00',
			'description'    => 'Test Promotion',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'currency'       => 'USD',
			'is_pif'         => 0,
		];
		$raw_promotion_data = [
			'id'             => '1',
			'code'           => 'test-promotion',
			'start_date'     => '2021-01-01T00:00:00',
			'end_date'       => '2021-01-01T00:00:00',
			'description'    => '<h1>Test Promotion</h1>',
			'discount_type'  => 'percentage',
			'discount_value' => '10',
			'is_pif'         => '0',
			'currency'       => 'USD',
		];
		$actual             = format_row_data_from_db( $raw_promotion_data );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test format rows data from db.
	 *
	 * @covers \Quark\Softrip\Promotions\format_rows_data_from_db
	 *
	 * @return void
	 */
	public function test_format_rows_data_from_db(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = format_rows_data_from_db();
		$this->assertSame( $expected, $actual );

		// Test with default arguments.
		$expected = [];
		$actual   = format_rows_data_from_db( [] );
		$this->assertSame( $expected, $actual );

		// Test with valid data.
		$expected           = [
			[
				'id'             => 1,
				'code'           => 'test-promotion',
				'start_date'     => '2021-01-01T00:00:00',
				'end_date'       => '2021-01-01T00:00:00',
				'description'    => 'Test Promotion',
				'discount_type'  => 'percentage',
				'discount_value' => '10',
				'currency'       => null,
				'is_pif'         => 1,
			],
			[
				'id'             => 2,
				'code'           => 'test2-promotion',
				'start_date'     => '2021-01-01T00:00:00',
				'end_date'       => '2021-01-01T00:00:00',
				'description'    => 'Test Promotion 2',
				'discount_type'  => 'percentage',
				'discount_value' => '10',
				'currency'       => GBP_CURRENCY,
				'is_pif'         => 0,
			],
		];
		$raw_promotion_data = [
			[
				'id'             => '1',
				'code'           => 'test-promotion',
				'start_date'     => '2021-01-01T00:00:00',
				'end_date'       => '2021-01-01T00:00:00',
				'description'    => 'Test Promotion',
				'discount_type'  => 'percentage',
				'discount_value' => '10',
				'is_pif'         => '1',
				'currency'       => null,
			],
			[
				'id'             => '2',
				'code'           => 'test2-promotion',
				'start_date'     => '2021-01-01T00:00:00',
				'end_date'       => '2021-01-01T00:00:00',
				'description'    => 'Test Promotion 2',
				'discount_type'  => 'percentage',
				'discount_value' => '10',
				'currency'       => GBP_CURRENCY,
				'is_pif'         => '0',
			],
		];
		$actual             = format_rows_data_from_db( $raw_promotion_data );
		$this->assertSame( $expected, $actual );
	}
}
