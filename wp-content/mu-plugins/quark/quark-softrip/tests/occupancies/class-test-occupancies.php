<?php
/**
 * Test suite for Occupancies.
 *
 * @package quart-softrip
 */

namespace Quark\Softrip\Tests\Occupancies;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\do_sync;
use function Quark\Softrip\get_engine_collate;
use function Quark\Softrip\Occupancies\add_supplemental_and_mandatory_price;
use function Quark\Softrip\Occupancies\clear_occupancies_by_departure;
use function Quark\Softrip\Occupancies\delete_occupancy_by_id;
use function Quark\Softrip\Occupancies\format_data;
use function Quark\Softrip\Occupancies\format_row_data_from_db;
use function Quark\Softrip\Occupancies\format_rows_data_from_db;
use function Quark\Softrip\Occupancies\get_cabin_category_post_by_cabin_code;
use function Quark\Softrip\Occupancies\get_cabin_category_post_ids_by_departure;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\get_lowest_price;
use function Quark\Softrip\Occupancies\get_lowest_price_by_cabin_category_and_departure;
use function Quark\Softrip\Occupancies\get_lowest_price_by_cabin_category_and_departure_and_promotion_code;
use function Quark\Softrip\Occupancies\get_masks_mapping;
use function Quark\Softrip\Occupancies\get_occupancies_by_cabin_category_and_departure;
use function Quark\Softrip\Occupancies\get_occupancies_by_departure;
use function Quark\Softrip\Occupancies\get_occupancy_data_by_id;
use function Quark\Softrip\Occupancies\get_occupancy_data_by_softrip_id;
use function Quark\Softrip\Occupancies\get_table_name;
use function Quark\Softrip\Occupancies\get_table_sql;
use function Quark\Softrip\OccupancyPromotions\get_table_name as get_occupancy_promotions_table_name;
use function Quark\Softrip\OccupancyPromotions\update_occupancy_promotions;
use function Quark\Softrip\Promotions\get_table_name as get_promotions_table_name;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORIES_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Itineraries\CACHE_GROUP as ITINERARIES_CACHE_GROUP;
use const Quark\Itineraries\CACHE_KEY as ITINERARIES_CACHE_KEY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Softrip\Occupancies\CACHE_GROUP;
use const Quark\Softrip\Occupancies\CACHE_KEY_PREFIX;
use const Quark\Softrip\TABLE_PREFIX_NAME;

/**
 * Class Test_Occupancies
 */
class Test_Occupancies extends Softrip_TestCase {
	/**
	 * Test get table name.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_table_name
	 *
	 * @return void
	 */
	public function test_get_table_name(): void {
		// Test table name.
		$expected = TABLE_PREFIX_NAME . 'occupancies';
		$actual   = get_table_name();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get table sql.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_table_sql
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
			softrip_id VARCHAR(255) NOT NULL UNIQUE,
			softrip_name VARCHAR(255) NOT NULL,
			mask VARCHAR(12) NOT NULL,
			departure_post_id BIGINT NOT NULL,
			cabin_category_post_id BIGINT NOT NULL,
			spaces_available INT NOT NULL,
			availability_description VARCHAR(255) NOT NULL,
			availability_status VARCHAR(4) NOT NULL,
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
	 * Test format data.
	 *
	 * @covers \Quark\Softrip\Occupancies\format_data
	 *
	 * @return void
	 */
	public function test_format_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = format_data();
		$this->assertSame( $expected, $actual );

		// Test with empty array.
		$expected = [];
		$actual   = format_data( [] );
		$this->assertSame( $expected, $actual );

		// Test with non-empty array of raw data but no cabin category post ID or departure post id.
		$raw_occupancy_data = [
			'id'              => '123',
			'name'            => 'Test Occupancy',
			'mask'            => '123456789012',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'AV',
			'prices'          => [
				'USD' => '100',
				'CAD' => '150',
				'AUD' => '200',
				'GBP' => '250',
				'EUR' => '300',
			],
		];
		$expected           = [];
		$actual             = format_data( $raw_occupancy_data );
		$this->assertSame( $expected, $actual );

		// Test with non-empty array of raw data with cabin category post ID but empty departure post ID.
		$expected = [];
		$actual   = format_data( $raw_occupancy_data, 123 );
		$this->assertSame( $expected, $actual );

		// Test with all default values.
		$expected = [];
		$actual   = format_data( [], 0, 0 );
		$this->assertSame( $expected, $actual );

		/**
		 * Test with empty value for each required field.
		 * Should return an empty array.
		 */
		$raw_occupancy_data = [];

		// Add empty id.
		$raw_occupancy_data['id'] = '';
		$expected                 = [];
		$actual                   = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Add empty name.
		$raw_occupancy_data['name'] = '';
		$expected                   = [];
		$actual                     = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Add empty mask.
		$raw_occupancy_data['mask'] = '';
		$expected                   = [];
		$actual                     = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Add empty availability status.
		$raw_occupancy_data['saleStatusCode'] = '';
		$expected                             = [];
		$actual                               = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Add empty availability description.
		$raw_occupancy_data['availabilityDescription'] = '';
		$expected                                      = [];
		$actual                                        = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Add empty prices.
		$raw_occupancy_data['prices'] = [];
		$expected                     = [];
		$actual                       = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Test with invalid sale status other than O, ON, W, S.
		$raw_occupancy_data['saleStatusCode'] = 'INVALID';
		$expected                             = [];
		$actual                               = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Set all values expect spaces available. It should be 0.
		$raw_occupancy_data = [
			'id'             => '123',
			'name'           => 'Test Occupancy',
			'mask'           => '123456789012',
			'saleStatusCode' => 'O',
			'saleStatus'     => 'Open',
			'prices'         => [
				'USD' => '100',
				'CAD' => '150',
				'AUD' => '200',
				'GBP' => '250',
				'EUR' => '300',
			],
		];
		$expected           = [
			'softrip_id'               => '123',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => '123456789012',
			'departure_post_id'        => 456,
			'cabin_category_post_id'   => 123,
			'spaces_available'         => 0,
			'availability_description' => 'Open',
			'availability_status'      => 'O',
			'price_per_person_usd'     => 0,
			'price_per_person_cad'     => 0,
			'price_per_person_aud'     => 0,
			'price_per_person_gbp'     => 0,
			'price_per_person_eur'     => 0,
		];
		$actual             = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );
		$this->assertArrayHasKey( 'spaces_available', $actual );
		$this->assertEquals( 0, $actual['spaces_available'] );

		/**
		 * Test with valid raw data.
		 * Should return an array with the formatted data.
		 */
		$raw_occupancy_data = [
			'id'              => '123',
			'name'            => 'Test Occupancy',
			'mask'            => '123456789012',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD'     => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD'     => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD'     => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP'     => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'INVALID' => [ // This is invalid and should be ignored.
					'currencyCode'   => 'INVALID',
					'pricePerPerson' => 400,
				],
			],
		];
		$expected           = [
			'softrip_id'               => '123',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => '123456789012',
			'departure_post_id'        => 456,
			'cabin_category_post_id'   => 123,
			'spaces_available'         => 10,
			'availability_description' => 'Open',
			'availability_status'      => 'O',
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 150,
			'price_per_person_aud'     => 200,
			'price_per_person_gbp'     => 250,
			'price_per_person_eur'     => 0,
		];
		$actual             = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );

		// Add price for eur.
		$raw_occupancy_data['prices']['EUR'] = [
			'currencyCode'   => 'EUR',
			'pricePerPerson' => 300,
		];
		$expected                            = [
			'softrip_id'               => '123',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => '123456789012',
			'departure_post_id'        => 456,
			'cabin_category_post_id'   => 123,
			'spaces_available'         => 10,
			'availability_description' => 'Open',
			'availability_status'      => 'O',
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 150,
			'price_per_person_aud'     => 200,
			'price_per_person_gbp'     => 250,
			'price_per_person_eur'     => 300,
		];
		$actual                              = format_data( $raw_occupancy_data, 123, 456 );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get cabin category post by cabin code.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_cabin_category_post_by_cabin_code
	 *
	 * @return void
	 */
	public function test_get_cabin_category_post_by_cabin_code(): void {
		// Test with no arguments.
		$expected = 0;
		$actual   = get_cabin_category_post_by_cabin_code();
		$this->assertSame( $expected, $actual );

		// Test with empty cabin code.
		$expected = 0;
		$actual   = get_cabin_category_post_by_cabin_code( '' );
		$this->assertSame( $expected, $actual );

		// Test with invalid cabin code.
		$expected = 0;
		$actual   = get_cabin_category_post_by_cabin_code( 'INVALID' );
		$this->assertSame( $expected, $actual );

		// Create a cabin category post.
		$cabin_category_post_id = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORIES_POST_TYPE,
				'post_title' => 'Test Cabin Category',
				'post_name'  => 'test-cabin-category',
			]
		);
		$this->assertIsInt( $cabin_category_post_id );

		// Test with cabin code which is yet not assigned to any cabin category post.
		$expected = 0;
		$actual   = get_cabin_category_post_by_cabin_code( 'PQO' );
		$this->assertSame( $expected, $actual );

		// Assign cabin code to cabin category post.
		update_post_meta( $cabin_category_post_id, 'cabin_category_id', 'PQO' );

		// Test with valid cabin code.
		$expected = $cabin_category_post_id;
		$actual   = get_cabin_category_post_by_cabin_code( 'PQO' );
		$this->assertSame( $expected, $actual );

		// Test with mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get cabin category post where cabin id is 'OEX-SGL'.
		$cabin_category_post_id = get_posts(
			[
				'post_type'              => CABIN_CATEGORIES_POST_TYPE,
				'meta_key'               => 'cabin_category_id',
				'meta_value'             => 'OEX-SGL',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$cabin_category_post_id = $cabin_category_post_id[0];
		$this->assertIsInt( $cabin_category_post_id );

		// Test with valid cabin code.
		$expected = $cabin_category_post_id;
		$actual   = get_cabin_category_post_by_cabin_code( 'OEX-SGL' );
		$this->assertSame( $expected, $actual );

		// Test with invalid/non-existing cabin code.
		$expected = 0;
		$actual   = get_cabin_category_post_by_cabin_code( 'INVALID' );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get occupancy data by softrip id.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_occupancy_data_by_softrip_id
	 *
	 * @return void
	 */
	public function test_get_occupancy_data_by_softrip_id(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancy_data_by_softrip_id();
		$this->assertSame( $expected, $actual );

		// Test with empty softrip id.
		$expected = [];
		$actual   = get_occupancy_data_by_softrip_id( '' );
		$this->assertSame( $expected, $actual );

		// Test with invalid softrip id.
		$expected = [];
		$actual   = get_occupancy_data_by_softrip_id( 'INVALID' );
		$this->assertSame( $expected, $actual );

		/**
		 * Insert occupancy and then test getter.
		 */
		$raw_occupancy_data1 = [
			'id'              => 'PQO-123',
			'name'            => 'Test Occupancy',
			'mask'            => '123456789012',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$formatted_data1     = format_data( $raw_occupancy_data1, 123, 456 );
		$this->assertIsArray( $formatted_data1 );
		$this->assertNotEmpty( $formatted_data1 );

		// There should be no occupancy with this softrip id.
		$actual = get_occupancy_data_by_softrip_id( 'PQO-123' );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key = CACHE_KEY_PREFIX . '_softrip_id_PQO-123';

		// Delete the cache.
		wp_cache_delete( $cache_key, CACHE_GROUP );

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$table_name = get_table_name();
		$wpdb->insert( $table_name, $formatted_data1 );

		// Inserted occupancy ID.
		$occupancy_id1 = $wpdb->insert_id;

		// Cache should be empty.
		$actual = wp_cache_get( $cache_key, CACHE_GROUP );
		$this->assertFalse( $actual );

		// Test with valid softrip id.
		$actual = get_occupancy_data_by_softrip_id( 'PQO-123' );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Cache should not be empty.
		$actual_from_cache = wp_cache_get( $cache_key, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );

		// Get first occupancy.
		$actual = $actual[0];
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $actual );
		$this->assertEquals( $occupancy_id1, $actual['id'] );
		$this->assertArrayHasKey( 'softrip_id', $actual );
		$this->assertEquals( $formatted_data1['softrip_id'], $actual['softrip_id'] );
		$this->assertArrayHasKey( 'softrip_name', $actual );
		$this->assertEquals( $formatted_data1['softrip_name'], $actual['softrip_name'] );
		$this->assertArrayHasKey( 'mask', $actual );
		$this->assertEquals( $formatted_data1['mask'], $actual['mask'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual );
		$this->assertEquals( $formatted_data1['departure_post_id'], $actual['departure_post_id'] );
		$this->assertArrayHasKey( 'cabin_category_post_id', $actual );
		$this->assertEquals( $formatted_data1['cabin_category_post_id'], $actual['cabin_category_post_id'] );
		$this->assertArrayHasKey( 'spaces_available', $actual );
		$this->assertEquals( $formatted_data1['spaces_available'], $actual['spaces_available'] );
		$this->assertArrayHasKey( 'availability_description', $actual );
		$this->assertEquals( $formatted_data1['availability_description'], $actual['availability_description'] );
		$this->assertArrayHasKey( 'availability_status', $actual );
		$this->assertEquals( $formatted_data1['availability_status'], $actual['availability_status'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_usd'], $actual['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_cad'], $actual['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_aud'], $actual['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_gbp'], $actual['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_eur'], $actual['price_per_person_eur'] );

		// Insert one more row.
		$raw_occupancy_data2 = [
			'id'              => 'PQO-456',
			'name'            => 'Test Occupancy 2',
			'mask'            => '123456789012',
			'spacesAvailable' => 20,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 200,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 250,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 300,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 350,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 400,
				],
			],
		];
		$formatted_data2     = format_data( $raw_occupancy_data2, 123, 456 );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data2 );

		// Inserted occupancy ID.
		$occupancy_id2 = $wpdb->insert_id;

		// Test for presence of both occupancies.
		$actual = get_occupancy_data_by_softrip_id( 'PQO-123' );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Get first occupancy.
		$actual = $actual[0];
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $actual );
		$this->assertEquals( $occupancy_id1, $actual['id'] );
		$this->assertArrayHasKey( 'softrip_id', $actual );
		$this->assertEquals( $formatted_data1['softrip_id'], $actual['softrip_id'] );
		$this->assertArrayHasKey( 'softrip_name', $actual );
		$this->assertEquals( $formatted_data1['softrip_name'], $actual['softrip_name'] );
		$this->assertArrayHasKey( 'mask', $actual );
		$this->assertEquals( $formatted_data1['mask'], $actual['mask'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual );
		$this->assertEquals( $formatted_data1['departure_post_id'], $actual['departure_post_id'] );
		$this->assertArrayHasKey( 'cabin_category_post_id', $actual );
		$this->assertEquals( $formatted_data1['cabin_category_post_id'], $actual['cabin_category_post_id'] );
		$this->assertArrayHasKey( 'spaces_available', $actual );
		$this->assertEquals( $formatted_data1['spaces_available'], $actual['spaces_available'] );
		$this->assertArrayHasKey( 'availability_description', $actual );
		$this->assertEquals( $formatted_data1['availability_description'], $actual['availability_description'] );
		$this->assertArrayHasKey( 'availability_status', $actual );
		$this->assertEquals( $formatted_data1['availability_status'], $actual['availability_status'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_usd'], $actual['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_cad'], $actual['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_aud'], $actual['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_gbp'], $actual['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual );
		$this->assertEquals( $formatted_data1['price_per_person_eur'], $actual['price_per_person_eur'] );

		// Cache should be empty for second occupancy.
		$actual = wp_cache_get( CACHE_KEY_PREFIX . '_softrip_id_PQO-456', CACHE_GROUP );
		$this->assertFalse( $actual );

		// Test for presence of second occupancy.
		$actual = get_occupancy_data_by_softrip_id( 'PQO-456' );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Cache should not be empty anymore.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_softrip_id_PQO-456', CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );

		// Get first occupancy.
		$actual = $actual[0];
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $actual );
		$this->assertEquals( $occupancy_id2, $actual['id'] );
		$this->assertArrayHasKey( 'softrip_id', $actual );
		$this->assertEquals( $formatted_data2['softrip_id'], $actual['softrip_id'] );
		$this->assertArrayHasKey( 'softrip_name', $actual );
		$this->assertEquals( $formatted_data2['softrip_name'], $actual['softrip_name'] );
		$this->assertArrayHasKey( 'mask', $actual );
		$this->assertEquals( $formatted_data2['mask'], $actual['mask'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual );
		$this->assertEquals( $formatted_data2['departure_post_id'], $actual['departure_post_id'] );
		$this->assertArrayHasKey( 'cabin_category_post_id', $actual );
		$this->assertEquals( $formatted_data2['cabin_category_post_id'], $actual['cabin_category_post_id'] );
		$this->assertArrayHasKey( 'spaces_available', $actual );
		$this->assertEquals( $formatted_data2['spaces_available'], $actual['spaces_available'] );
		$this->assertArrayHasKey( 'availability_description', $actual );
		$this->assertEquals( $formatted_data2['availability_description'], $actual['availability_description'] );
		$this->assertArrayHasKey( 'availability_status', $actual );
		$this->assertEquals( $formatted_data2['availability_status'], $actual['availability_status'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual );
		$this->assertEquals( $formatted_data2['price_per_person_usd'], $actual['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual );
		$this->assertEquals( $formatted_data2['price_per_person_cad'], $actual['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual );
		$this->assertEquals( $formatted_data2['price_per_person_aud'], $actual['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual );
		$this->assertEquals( $formatted_data2['price_per_person_gbp'], $actual['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual );
		$this->assertEquals( $formatted_data2['price_per_person_eur'], $actual['price_per_person_eur'] );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Softrip id to test.
		$softrip_id = 'ABC-123:2026-02-28:OEX-SGL:A';

		// Cache should be empty for OEX-DBL.
		$actual = wp_cache_get( CACHE_KEY_PREFIX . '_softrip_id_' . $softrip_id, CACHE_GROUP );
		$this->assertFalse( $actual );

		// Get occupancy data where softrip id is ABC-123:2026-02-28:OEX-SGL:A.
		$occupancy_data = get_occupancy_data_by_softrip_id( $softrip_id );
		$this->assertIsArray( $occupancy_data );
		$this->assertNotEmpty( $occupancy_data );

		// Cache should not be empty anymore.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_softrip_id_' . $softrip_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );

		// Get first occupancy.
		$occupancy_data = $occupancy_data[0];
		$this->assertIsArray( $occupancy_data );
		$this->assertNotEmpty( $occupancy_data );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $occupancy_data );
		$this->assertArrayHasKey( 'softrip_id', $occupancy_data );
		$this->assertArrayHasKey( 'softrip_name', $occupancy_data );
		$this->assertArrayHasKey( 'mask', $occupancy_data );
		$this->assertArrayHasKey( 'departure_post_id', $occupancy_data );
		$this->assertArrayHasKey( 'cabin_category_post_id', $occupancy_data );
		$this->assertArrayHasKey( 'spaces_available', $occupancy_data );
		$this->assertArrayHasKey( 'availability_description', $occupancy_data );
		$this->assertArrayHasKey( 'availability_status', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_usd', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_cad', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_aud', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_gbp', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_eur', $occupancy_data );
		$this->assertSame( $softrip_id, $occupancy_data['softrip_id'] );
		$this->assertSame( $softrip_id, $occupancy_data['softrip_name'] );
		$this->assertSame( 'A', $occupancy_data['mask'] );
		$this->assertNotEmpty( $occupancy_data['departure_post_id'] );
		$this->assertNotEmpty( $occupancy_data['cabin_category_post_id'] );
		$this->assertEmpty( $occupancy_data['spaces_available'] );
	}

	/**
	 * Test get occupancies by departure.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_occupancies_by_departure
	 *
	 * @return void
	 */
	public function test_get_occupancies_by_departure(): void {
		// Cache key.
		$cache_key = CACHE_KEY_PREFIX . '_departure_post_id_0';

		// Cache should be empty.
		$actual_from_cache = wp_cache_get( $cache_key, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancies_by_departure();
		$this->assertSame( $expected, $actual );

		// Test with empty departure post ID.
		$expected = [];
		$actual   = get_occupancies_by_departure( 0 );
		$this->assertSame( $expected, $actual );

		// Test with invalid departure post ID.
		$departure_post_id = 999;

		// Cache should be absent.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Test with invalid departure post ID.
		$expected = [];
		$actual   = get_occupancies_by_departure( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Cache should be present now.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEmpty( $actual_from_cache );

		// Insert occupancy and then test getter.
		$raw_occupancy_data1     = [
			'id'              => 'PQO-123:2026-02-10:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id1      = 123;
		$cabin_category_post_id1 = 456;
		$formatted_data1         = format_data( $raw_occupancy_data1, $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $formatted_data1 );
		$this->assertNotEmpty( $formatted_data1 );

		// There should be no occupancy with this departure post ID.
		$actual = get_occupancies_by_departure( $departure_post_id1 );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key1 = CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id1;

		// Delete the cache.
		wp_cache_delete( $cache_key1, CACHE_GROUP );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data1 );

		// Inserted occupancy ID.
		$occupancy_id1 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache1 );

		// Now, there should be one occupancy with this departure post ID.
		$actual1 = get_occupancies_by_departure( $departure_post_id1 );
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );
		$this->assertCount( 1, $actual1 );

		// Cache should not be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache1 );
		$this->assertNotEmpty( $actual_from_cache1 );

		// Get first occupancy.
		$actual1 = $actual1[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertEquals( $occupancy_id1, $actual1['id'] );
		$this->assertArrayHasKey( 'softrip_id', $actual1 );
		$this->assertEquals( $formatted_data1['softrip_id'], $actual1['softrip_id'] );
		$this->assertArrayHasKey( 'softrip_name', $actual1 );
		$this->assertEquals( $formatted_data1['softrip_name'], $actual1['softrip_name'] );
		$this->assertArrayHasKey( 'mask', $actual1 );
		$this->assertEquals( $formatted_data1['mask'], $actual1['mask'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertEquals( $formatted_data1['departure_post_id'], $actual1['departure_post_id'] );
		$this->assertArrayHasKey( 'cabin_category_post_id', $actual1 );
		$this->assertEquals( $formatted_data1['cabin_category_post_id'], $actual1['cabin_category_post_id'] );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertEquals( $formatted_data1['spaces_available'], $actual1['spaces_available'] );
		$this->assertArrayHasKey( 'availability_description', $actual1 );
		$this->assertEquals( $formatted_data1['availability_description'], $actual1['availability_description'] );
		$this->assertArrayHasKey( 'availability_status', $actual1 );
		$this->assertEquals( $formatted_data1['availability_status'], $actual1['availability_status'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_usd'], $actual1['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_cad'], $actual1['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_aud'], $actual1['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_gbp'], $actual1['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_eur'], $actual1['price_per_person_eur'] );

		// Test with real mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get departure post ID.
		$departure_post_ids = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertIsArray( $departure_post_ids );
		$this->assertNotEmpty( $departure_post_ids );
		$departure_post_id = $departure_post_ids[0];
		$this->assertIsInt( $departure_post_id );

		// Cache should be empty for this departure post ID.
		$actual = wp_cache_get( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual );

		// Get occupancies by departure post ID.
		$occupancies = get_occupancies_by_departure( $departure_post_id );
		$this->assertIsArray( $occupancies );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Cache should not be empty anymore.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );

		// Get first occupancy.
		$occupancy_data = $occupancies[0];
		$this->assertIsArray( $occupancy_data );
		$this->assertNotEmpty( $occupancy_data );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $occupancy_data );
		$this->assertArrayHasKey( 'softrip_id', $occupancy_data );
		$this->assertArrayHasKey( 'softrip_name', $occupancy_data );
		$this->assertArrayHasKey( 'mask', $occupancy_data );
		$this->assertArrayHasKey( 'departure_post_id', $occupancy_data );
		$this->assertArrayHasKey( 'cabin_category_post_id', $occupancy_data );
		$this->assertArrayHasKey( 'spaces_available', $occupancy_data );
		$this->assertArrayHasKey( 'availability_description', $occupancy_data );
		$this->assertArrayHasKey( 'availability_status', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_usd', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_cad', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_aud', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_gbp', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_eur', $occupancy_data );
		$this->assertSame( 'ABC-123:2026-02-28:OEX-SGL:A', $occupancy_data['softrip_id'] );
		$this->assertSame( 'ABC-123:2026-02-28:OEX-SGL:A', $occupancy_data['softrip_name'] );
		$this->assertSame( 'A', $occupancy_data['mask'] );
		$this->assertNotEmpty( $occupancy_data['departure_post_id'] );
		$this->assertNotEmpty( $occupancy_data['cabin_category_post_id'] );
		$this->assertEmpty( $occupancy_data['spaces_available'] );
	}

	/**
	 * Test get occupancy data by id.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_occupancy_data_by_id
	 *
	 * @return void
	 */
	public function test_get_occupancy_data_by_id(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancy_data_by_id();
		$this->assertSame( $expected, $actual );

		// Test with empty ID.
		$expected = [];
		$actual   = get_occupancy_data_by_id( 0 );
		$this->assertSame( $expected, $actual );

		// Test with invalid ID.
		$expected = [];
		$actual   = get_occupancy_data_by_id( 999 );
		$this->assertSame( $expected, $actual );

		/**
		 * Insert occupancy and then test getter.
		 */
		$raw_occupancy_data1     = [
			'id'              => 'PQO-123',
			'name'            => 'Test Occupancy',
			'mask'            => '123456789012',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id1      = 123;
		$cabin_category_post_id1 = 456;
		$formatted_data1         = format_data( $raw_occupancy_data1, $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $formatted_data1 );
		$this->assertNotEmpty( $formatted_data1 );

		// New occupancy id.
		$occupancy_id1 = 1;

		// There should be no occupancy with this ID.
		$actual = get_occupancy_data_by_id( $occupancy_id1 );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key1 = CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id1;

		// Delete the cache.
		wp_cache_delete( $cache_key1, CACHE_GROUP );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data1 );

		// Cache should be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache1 );

		// Now, there should be one occupancy with this ID.
		$actual1 = get_occupancy_data_by_id( $occupancy_id1 );
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );
		$this->assertCount( 1, $actual1 );

		// Cache should not be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache1 );
		$this->assertNotEmpty( $actual_from_cache1 );

		// Get first occupancy.
		$actual1 = $actual1[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertEquals( $occupancy_id1, $actual1['id'] );
		$this->assertArrayHasKey( 'softrip_id', $actual1 );
		$this->assertEquals( $formatted_data1['softrip_id'], $actual1['softrip_id'] );
		$this->assertArrayHasKey( 'softrip_name', $actual1 );
		$this->assertEquals( $formatted_data1['softrip_name'], $actual1['softrip_name'] );
		$this->assertArrayHasKey( 'mask', $actual1 );
		$this->assertEquals( $formatted_data1['mask'], $actual1['mask'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertEquals( $formatted_data1['departure_post_id'], $actual1['departure_post_id'] );
		$this->assertArrayHasKey( 'cabin_category_post_id', $actual1 );
		$this->assertEquals( $formatted_data1['cabin_category_post_id'], $actual1['cabin_category_post_id'] );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertEquals( $formatted_data1['spaces_available'], $actual1['spaces_available'] );
		$this->assertArrayHasKey( 'availability_description', $actual1 );
		$this->assertEquals( $formatted_data1['availability_description'], $actual1['availability_description'] );
		$this->assertArrayHasKey( 'availability_status', $actual1 );
		$this->assertEquals( $formatted_data1['availability_status'], $actual1['availability_status'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_usd'], $actual1['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_cad'], $actual1['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_aud'], $actual1['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_gbp'], $actual1['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertEquals( $formatted_data1['price_per_person_eur'], $actual1['price_per_person_eur'] );

		// Test with real mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get occupancy ID.
		$occupancy_ids = $wpdb->get_col(
			$wpdb->prepare(
				'SELECT id FROM %i WHERE softrip_id = %s',
				[
					$table_name,
					'ABC-123:2026-02-28:OEX-SGL:A',
				]
			)
		);
		$this->assertIsArray( $occupancy_ids );
		$this->assertNotEmpty( $occupancy_ids );
		$this->assertCount( 1, $occupancy_ids );
		$occupancy_id = absint( $occupancy_ids[0] );

		// Cache should be empty for this occupancy ID.
		$actual = wp_cache_get( CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id, CACHE_GROUP );
		$this->assertFalse( $actual );

		// Get occupancy data by ID.
		$occupancy_data = get_occupancy_data_by_id( $occupancy_id );
		$this->assertIsArray( $occupancy_data );
		$this->assertNotEmpty( $occupancy_data );
		$this->assertCount( 1, $occupancy_data );

		// Cache should not be empty anymore.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );

		// Get first occupancy.
		$occupancy_data = $occupancy_data[0];
		$this->assertIsArray( $occupancy_data );
		$this->assertNotEmpty( $occupancy_data );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $occupancy_data );
		$this->assertArrayHasKey( 'softrip_id', $occupancy_data );
		$this->assertArrayHasKey( 'softrip_name', $occupancy_data );
		$this->assertArrayHasKey( 'mask', $occupancy_data );
		$this->assertArrayHasKey( 'departure_post_id', $occupancy_data );
		$this->assertArrayHasKey( 'cabin_category_post_id', $occupancy_data );
		$this->assertArrayHasKey( 'spaces_available', $occupancy_data );
		$this->assertArrayHasKey( 'availability_description', $occupancy_data );
		$this->assertArrayHasKey( 'availability_status', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_usd', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_cad', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_aud', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_gbp', $occupancy_data );
		$this->assertArrayHasKey( 'price_per_person_eur', $occupancy_data );
		$this->assertSame( 'ABC-123:2026-02-28:OEX-SGL:A', $occupancy_data['softrip_id'] );
		$this->assertSame( 'ABC-123:2026-02-28:OEX-SGL:A', $occupancy_data['softrip_name'] );
		$this->assertSame( 'A', $occupancy_data['mask'] );
		$this->assertNotEmpty( $occupancy_data['departure_post_id'] );
		$this->assertNotEmpty( $occupancy_data['cabin_category_post_id'] );
		$this->assertEmpty( $occupancy_data['spaces_available'] );
	}

	/**
	 * Test get cabin category post IDs by departure.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_cabin_category_post_ids_by_departure
	 *
	 * @return void
	 */
	public function test_get_cabin_category_post_ids_by_departure(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_cabin_category_post_ids_by_departure();
		$this->assertSame( $expected, $actual );

		// Test with empty departure post ID.
		$expected = [];
		$actual   = get_cabin_category_post_ids_by_departure( 0 );
		$this->assertSame( $expected, $actual );

		// Test with invalid departure post ID and invalid cabin category post ID.
		$departure_post_id = 9999;

		// Cache should be absent.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Test with invalid departure post ID.
		$expected = [];
		$actual   = get_cabin_category_post_ids_by_departure( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Cache should be present now.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEmpty( $actual_from_cache );

		// Insert occupancy and then test getter.
		$raw_occupancy_data1     = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id1      = 1231;
		$cabin_category_post_id1 = 4561;
		$formatted_data1         = format_data( $raw_occupancy_data1, $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $formatted_data1 );
		$this->assertNotEmpty( $formatted_data1 );

		// There should be no cabin category post ID with this departure post ID.
		$actual = get_cabin_category_post_ids_by_departure( $departure_post_id1 );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key1 = CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id1;

		// Delete the cache.
		wp_cache_delete( $cache_key1, CACHE_GROUP );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data1 );

		// Inserted occupancy ID.
		$occupancy_id1 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache1 );

		// Now, there should be one cabin category post ID with this departure post ID.
		$actual1 = get_cabin_category_post_ids_by_departure( $departure_post_id1 );
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );
		$this->assertCount( 1, $actual1 );

		// Cache should not be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache1 );
		$this->assertNotEmpty( $actual_from_cache1 );

		// Get first cabin category post ID.
		$actual1 = $actual1[0];
		$this->assertIsInt( $actual1 );
		$this->assertEquals( $cabin_category_post_id1, $actual1 );

		// Let's insert one more occupancy with same departure post ID but different cabin category post ID.
		$raw_occupancy_data2     = [
			'id'              => 'PQO-456:2026-02-19:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$cabin_category_post_id2 = 7891;
		$formatted_data2         = format_data( $raw_occupancy_data2, $cabin_category_post_id2, $departure_post_id1 );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// There should be already one cabin category post ID with this departure post ID.
		$actual = get_cabin_category_post_ids_by_departure( $departure_post_id1 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Construct cache key.
		$cache_key2 = CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id1;

		// Delete the cache.
		wp_cache_delete( $cache_key2, CACHE_GROUP );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data2 );

		// Inserted occupancy ID.
		$occupancy_id2 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache2 = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache2 );

		// Now, there should be two cabin category post IDs with this departure post ID.
		$actual2 = get_cabin_category_post_ids_by_departure( $departure_post_id1 );
		$this->assertIsArray( $actual2 );
		$this->assertNotEmpty( $actual2 );
		$this->assertCount( 2, $actual2 );
		$this->assertContains( $cabin_category_post_id1, $actual2 );
		$this->assertContains( $cabin_category_post_id2, $actual2 );

		// Cache should not be empty.
		$actual_from_cache2 = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache2 );
		$this->assertNotEmpty( $actual_from_cache2 );

		// Let's insert one more occupancy with same cabin category post ID but different departure post ID.
		$raw_occupancy_data3 = [
			'id'              => 'PQO-123:2026-02-15:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id2  = 4561;
		$formatted_data3     = format_data( $raw_occupancy_data3, $cabin_category_post_id1, $departure_post_id2 );
		$this->assertIsArray( $formatted_data3 );
		$this->assertNotEmpty( $formatted_data3 );

		// There should be no cabin category post ID with this departure post ID.
		$actual = get_cabin_category_post_ids_by_departure( $departure_post_id2 );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key3 = CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id2;

		// Delete the cache.
		wp_cache_delete( $cache_key3, CACHE_GROUP );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data3 );

		// Inserted occupancy ID.
		$occupancy_id3 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache3 = wp_cache_get( $cache_key3, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache3 );

		// Now, there should be one cabin category post ID with this departure post ID.
		$actual3 = get_cabin_category_post_ids_by_departure( $departure_post_id2 );
		$this->assertIsArray( $actual3 );
		$this->assertNotEmpty( $actual3 );
		$this->assertCount( 1, $actual3 );
		$this->assertContains( $cabin_category_post_id1, $actual3 );
		$this->assertNotContains( $cabin_category_post_id2, $actual3 );

		// Cache should not be empty.
		$actual_from_cache3 = wp_cache_get( $cache_key3, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache3 );
		$this->assertNotEmpty( $actual_from_cache3 );

		// Test with real mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get departure post ID.
		$departure_post_ids = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertIsArray( $departure_post_ids );
		$this->assertNotEmpty( $departure_post_ids );
		$departure_post_id = $departure_post_ids[0];
		$this->assertIsInt( $departure_post_id );

		// Cache should be empty for this departure post ID.
		$actual = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual );

		// Get cabin category post IDs by departure post ID.
		$cabin_category_post_ids = get_cabin_category_post_ids_by_departure( $departure_post_id );
		$this->assertIsArray( $cabin_category_post_ids );
		$this->assertNotEmpty( $cabin_category_post_ids );
		$this->assertCount( 1, $cabin_category_post_ids );

		// Cache should not be empty anymore.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
	}

	/**
	 * Test get occupancies by cabin category post id and departure post id.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_occupancies_by_cabin_category_and_departure
	 *
	 * @return void
	 */
	public function test_get_occupancies_by_cabin_category_and_departure(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancies_by_cabin_category_and_departure();
		$this->assertSame( $expected, $actual );

		// Test with empty cabin category post ID and empty departure post ID.
		$expected = [];
		$actual   = get_occupancies_by_cabin_category_and_departure( 0, 0 );
		$this->assertSame( $expected, $actual );

		// Test with invalid cabin category post ID and invalid departure post ID.
		$cabin_category_post_id = 9999;
		$departure_post_id      = 9999;

		// Construct cache key.
		$cache_key = CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id;

		// Cache should be absent.
		$actual_from_cache = wp_cache_get( $cache_key, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Test with invalid cabin category post ID and invalid departure post ID.
		$expected = [];
		$actual   = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Cache should be present now.
		$actual_from_cache = wp_cache_get( $cache_key, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEmpty( $actual_from_cache );

		// Insert occupancy and then test getter.
		$raw_occupancy_data1     = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id1      = 1231;
		$cabin_category_post_id1 = 4561;
		$formatted_data1         = format_data( $raw_occupancy_data1, $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $formatted_data1 );
		$this->assertNotEmpty( $formatted_data1 );

		// There should be no occupancy with this cabin category post ID and departure post ID.
		$actual = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key1 = CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id1 . '_departure_post_id_' . $departure_post_id1;

		// Delete the cache.
		wp_cache_delete( $cache_key1, CACHE_GROUP );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data1 );

		// Inserted occupancy ID.
		$occupancy_id1 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache1 );

		// Now, there should be one occupancy with this cabin category post ID and departure post ID.
		$actual1 = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );
		$this->assertCount( 1, $actual1 );

		// Cache should not be empty.
		$actual_from_cache1 = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache1 );
		$this->assertNotEmpty( $actual_from_cache1 );

		// Get first occupancy.
		$actual1 = $actual1[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertArrayHasKey( 'softrip_id', $actual1 );
		$this->assertArrayHasKey( 'softrip_name', $actual1 );
		$this->assertArrayHasKey( 'mask', $actual1 );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertArrayHasKey( 'cabin_category_post_id', $actual1 );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertArrayHasKey( 'availability_description', $actual1 );
		$this->assertArrayHasKey( 'availability_status', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertSame( 1, $actual1['id'] );
		$this->assertSame( 'PQO-123:2026-02-20:OEX-SGL:A', $actual1['softrip_id'] );
		$this->assertSame( 'Test Occupancy', $actual1['softrip_name'] );
		$this->assertSame( 'A', $actual1['mask'] );
		$this->assertEquals( $departure_post_id1, $actual1['departure_post_id'] );
		$this->assertEquals( $cabin_category_post_id1, $actual1['cabin_category_post_id'] );
		$this->assertEquals( 10, $actual1['spaces_available'] );
		$this->assertSame( 'Open', $actual1['availability_description'] );
		$this->assertSame( 'O', $actual1['availability_status'] );
		$this->assertEquals( 100, $actual1['price_per_person_usd'] );
		$this->assertEquals( 150, $actual1['price_per_person_cad'] );
		$this->assertEquals( 200, $actual1['price_per_person_aud'] );
		$this->assertEquals( 250, $actual1['price_per_person_gbp'] );
		$this->assertEquals( 300, $actual1['price_per_person_eur'] );

		// Let's insert one more occupancy with same cabin category post ID and departure post ID.
		$raw_occupancy_data2 = [
			'id'              => 'PQO-456:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$formatted_data2     = format_data( $raw_occupancy_data2, $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// There should be already one occupancy with this cabin category post ID and departure post ID.
		$actual = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );

		// Construct cache key.
		$cache_key2 = CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id1 . '_departure_post_id_' . $departure_post_id1;

		// Delete the cache.
		wp_cache_delete( $cache_key2, CACHE_GROUP );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data2 );

		// Inserted occupancy ID.
		$occupancy_id2 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache2 = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache2 );

		// Now, there should be two occupancies with this cabin category post ID and departure post ID.
		$actual2 = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_post_id1 );
		$this->assertIsArray( $actual2 );
		$this->assertNotEmpty( $actual2 );

		// Cache should not be empty.
		$actual_from_cache2 = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache2 );
		$this->assertNotEmpty( $actual_from_cache2 );

		// Let's insert one more occupancy with same cabin category post ID but different departure post ID.
		$raw_occupancy_data3 = [
			'id'              => 'PQO-123:2026-02-15:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id2  = 4561;
		$formatted_data3     = format_data( $raw_occupancy_data3, $cabin_category_post_id1, $departure_post_id2 );
		$this->assertIsArray( $formatted_data3 );
		$this->assertNotEmpty( $formatted_data3 );

		// There should be no occupancy with this cabin category post ID and departure post ID.
		$actual = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_post_id2 );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Construct cache key.
		$cache_key3 = CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id1 . '_departure_post_id_' . $departure_post_id2;

		// Delete the cache.
		wp_cache_delete( $cache_key3, CACHE_GROUP );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data3 );

		// Inserted occupancy ID.
		$occupancy_id3 = $wpdb->insert_id;

		// Cache should be empty.
		$actual_from_cache3 = wp_cache_get( $cache_key3, CACHE_GROUP );

		// Now, there should be one occupancy with this cabin category post ID and departure post ID.
		$actual3 = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_post_id2 );
		$this->assertIsArray( $actual3 );
		$this->assertNotEmpty( $actual3 );
		$this->assertCount( 1, $actual3 );

		// Cache should not be empty.
		$actual_from_cache3 = wp_cache_get( $cache_key3, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache3 );
		$this->assertNotEmpty( $actual_from_cache3 );

		// Test with real mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get cabin category post ID.
		$cabin_category_post_ids = get_posts(
			[
				'post_type'              => CABIN_CATEGORIES_POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'     => 'cabin_category_id',
						'value'   => 'OEX-SGL',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertIsArray( $cabin_category_post_ids );
		$this->assertNotEmpty( $cabin_category_post_ids );
		$cabin_category_post_id = $cabin_category_post_ids[0];
		$this->assertIsInt( $cabin_category_post_id );

		// Get departure post ID.
		$departure_post_ids = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertIsArray( $departure_post_ids );
		$this->assertNotEmpty( $departure_post_ids );
		$departure_post_id = $departure_post_ids[0];
		$this->assertIsInt( $departure_post_id );

		// Cache should be empty for this cabin category post ID and departure post ID.
		$actual = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual );

		// Get occupancies by cabin category post ID and departure post ID.
		$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $occupancies );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Cache should not be empty anymore.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertNotEmpty( $occupancy );

		// Assert each column.
		$this->assertArrayHasKey( 'id', $occupancy );
		$this->assertArrayHasKey( 'softrip_id', $occupancy );
		$this->assertArrayHasKey( 'softrip_name', $occupancy );
		$this->assertArrayHasKey( 'mask', $occupancy );
		$this->assertArrayHasKey( 'departure_post_id', $occupancy );
		$this->assertArrayHasKey( 'cabin_category_post_id', $occupancy );
		$this->assertArrayHasKey( 'spaces_available', $occupancy );
		$this->assertArrayHasKey( 'availability_description', $occupancy );
		$this->assertArrayHasKey( 'availability_status', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_usd', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_cad', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_aud', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_gbp', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_eur', $occupancy );
		$this->assertSame( 'ABC-123:2026-02-28:OEX-SGL:A', $occupancy['softrip_id'] );
		$this->assertSame( 'ABC-123:2026-02-28:OEX-SGL:A', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );
		$this->assertNotEmpty( $occupancy['departure_post_id'] );
		$this->assertNotEmpty( $occupancy['cabin_category_post_id'] );
		$this->assertEmpty( $occupancy['spaces_available'] );
	}

	/**
	 * Test get description and pax count by mask.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask
	 *
	 * @return void
	 */
	public function test_get_description_and_pax_count_by_mask(): void {
		// Default.
		$default_expected = [
			'description' => '',
			'pax_count'   => 0,
		];

		// Test with no arguments.
		$expected = $default_expected;
		$actual   = get_description_and_pax_count_by_mask();
		$this->assertSame( $expected, $actual );

		// Test with empty mask.
		$expected = $default_expected;
		$actual   = get_description_and_pax_count_by_mask( '' );
		$this->assertSame( $expected, $actual );

		// Test with invalid mask.
		$mask     = 'XYZ';
		$expected = $default_expected;
		$actual   = get_description_and_pax_count_by_mask( $mask );
		$this->assertSame( $expected, $actual );

		// Test with all valid masks.
		$expected = [
			'description' => 'Single Room',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'A' );
		$this->assertSame( $expected, $actual );

		// For AA.
		$expected = [
			'description' => 'Double Room',
			'pax_count'   => 2,
		];
		$actual   = get_description_and_pax_count_by_mask( 'AA' );
		$this->assertSame( $expected, $actual );

		// For SAA.
		$expected = [
			'description' => 'Double Room Shared',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'SAA' );
		$this->assertSame( $expected, $actual );

		// For SMAA.
		$expected = [
			'description' => 'Double Room Shared (Male)',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'SMAA' );
		$this->assertSame( $expected, $actual );

		// For SFAA.
		$expected = [
			'description' => 'Double Room Shared (Female)',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'SFAA' );
		$this->assertSame( $expected, $actual );

		// For AAA.
		$expected = [
			'description' => 'Triple Room',
			'pax_count'   => 3,
		];
		$actual   = get_description_and_pax_count_by_mask( 'AAA' );
		$this->assertSame( $expected, $actual );

		// For SAAA.
		$expected = [
			'description' => 'Triple Room Shared',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'SAAA' );
		$this->assertSame( $expected, $actual );

		// For SMAAA.
		$expected = [
			'description' => 'Triple Room Shared (Male)',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'SMAAA' );
		$this->assertSame( $expected, $actual );

		// For SFAAA.
		$expected = [
			'description' => 'Triple Room Shared (Female)',
			'pax_count'   => 1,
		];
		$actual   = get_description_and_pax_count_by_mask( 'SFAAA' );
		$this->assertSame( $expected, $actual );

		// For AAAA.
		$expected = [
			'description' => 'Quad Room',
			'pax_count'   => 4,
		];
		$actual   = get_description_and_pax_count_by_mask( 'AAAA' );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_lowest_price
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Default expected.
		$default_expected = [
			'original'   => 0,
			'discounted' => 0,
		];

		// Test with no arguments.
		$expected = $default_expected;
		$actual   = get_lowest_price();
		$this->assertSame( $expected, $actual );

		// Test with empty departure post id.
		$expected = $default_expected;
		$actual   = get_lowest_price( 0 );
		$this->assertSame( $expected, $actual );

		// Test with invalid currency.
		$departure_post_id = 1231;
		$expected          = $default_expected;
		$actual            = get_lowest_price( $departure_post_id, 'XYZ' );
		$this->assertSame( $expected, $actual );

		// Create an occupancy without promotion.
		$raw_occupancy_data     = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id      = 1231;
		$cabin_category_post_id = 4561;
		$formatted_data         = format_data( $raw_occupancy_data, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data );
		$this->assertNotEmpty( $formatted_data );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data );

		// Inserted occupancy ID.
		$occupancy_id = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id );

		// Test with valid currency without any promotion.
		$expected = [
			'original'   => 100,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Test with AUD currency without any promotion.
		$expected = [
			'original'   => 200,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Test with GBP currency without any promotion.
		$expected = [
			'original'   => 250,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Test with EUR currency without any promotion.
		$expected = [
			'original'   => 300,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'EUR' );
		$this->assertEquals( $expected, $actual );

		// Add another occupancy without promotion.
		$raw_occupancy_data2 = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:AA',
			'name'            => 'Test Occupancy 2',
			'mask'            => 'AA',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 50,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 75,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 100,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 125,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
				],
			],
		];
		$formatted_data2     = format_data( $raw_occupancy_data2, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data2 );

		// Bust the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );

		// Test with USD currency without any promotion.
		$expected = [
			'original'   => 50,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Test with CAD currency without any promotion.
		$expected = [
			'original'   => 75,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Test with AUD currency without any promotion.
		$expected = [
			'original'   => 100,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Test with GBP currency without any promotion.
		$expected = [
			'original'   => 125,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Test with EUR currency without any promotion.
		$expected = [
			'original'   => 150,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'EUR' );
		$this->assertEquals( $expected, $actual );

		// Invalid currency.
		$expected = $default_expected;
		$actual   = get_lowest_price( $departure_post_id, 'XYZ' );
		$this->assertSame( $expected, $actual );

		// Lowercase valid currency.
		$expected = [
			'original'   => 50,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'usd' );
		$this->assertEquals( $expected, $actual );

		// Create an itinerary post.
		$itinerary_post_id = wp_insert_post(
			[
				'post_title'  => 'Test Itinerary',
				'post_type'   => ITINERARY_POST_TYPE,
				'post_status' => 'publish',
				'meta_input'  => [
					'softrip_package_code' => 'PQO-123',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id );

		// Update post meta.
		update_post_meta( $departure_post_id, 'itinerary', $itinerary_post_id );

		// Add the supplemental price for USD.
		$supplemental_price_usd = 10;
		update_post_meta( $itinerary_post_id, 'supplemental_price_usd', $supplemental_price_usd );

		// Add the supplemental price for AUD.
		$supplemental_price_aud = 20;
		update_post_meta( $itinerary_post_id, 'supplemental_price_aud', $supplemental_price_aud );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// Test the lowest price.
		$expected = [
			'original'   => 60, // Including the supplemental price.
			'discounted' => 10, // The supplemental price only.
		];
		$actual   = get_lowest_price( $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Test the lowest price for AUD.
		$expected = [
			'original'   => 120, // Including the supplemental price.
			'discounted' => 20, // The supplemental price only.
		];
		$actual   = get_lowest_price( $departure_post_id, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Test the lowest price for GBP, remains same.
		$expected = [
			'original'   => 125,
			'discounted' => 0,
		];
		$actual   = get_lowest_price( $departure_post_id, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Add mandatory transfer price for USD.
		$mandatory_transfer_price_usd = 5;
		update_post_meta( $itinerary_post_id, 'mandatory_transfer_price_usd', $mandatory_transfer_price_usd );

		// Add mandatory transfer price for AUD.
		$mandatory_transfer_price_aud = 10;
		update_post_meta( $itinerary_post_id, 'mandatory_transfer_price_aud', $mandatory_transfer_price_aud );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// Test the lowest price.
		$expected = [
			'original'   => 65, // Including the supplemental price and mandatory transfer price.
			'discounted' => 15, // The supplemental price only.
		];
		$actual   = get_lowest_price( $departure_post_id, 'USD' );

		// Test the lowest price for AUD.
		$expected = [
			'original'   => 130, // Including the supplemental price and mandatory transfer price.
			'discounted' => 30, // The supplemental price only.
		];
		$actual   = get_lowest_price( $departure_post_id, 'AUD' );

		// Let's test with real mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get departure post ID.
		$departure_post_ids = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertIsArray( $departure_post_ids );

		// Get the departure post ID.
		$departure_post_id = $departure_post_ids[0];
		$this->assertIsInt( $departure_post_id );

		// Get the lowest price.
		$expected     = [
			'original'   => 34895,
			'discounted' => 26171,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'USD' );
		$this->assertIsArray( $lowest_price );
		$this->assertNotEmpty( $lowest_price );
		$this->assertArrayHasKey( 'original', $lowest_price );
		$this->assertArrayHasKey( 'discounted', $lowest_price );
		$this->assertEquals( $expected, $lowest_price );

		// Get the lowest price for CAD.
		$expected     = [
			'original'   => 47495,
			'discounted' => 35621,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'cad' );
		$this->assertIsArray( $lowest_price );
		$this->assertNotEmpty( $lowest_price );
		$this->assertArrayHasKey( 'original', $lowest_price );
		$this->assertArrayHasKey( 'discounted', $lowest_price );
		$this->assertEquals( $expected, $lowest_price );

		// Invalid currency.
		$expected = $default_expected;
		$actual   = get_lowest_price( $departure_post_id, 'XYZ' );
		$this->assertSame( $expected, $actual );

		// Get itinerary.
		$itinerary_post_id = absint( get_post_meta( $departure_post_id, 'itinerary', true ) );

		// Add supplemental price for USD for the departure.
		$supplemental_price_usd = 100;
		update_post_meta( $itinerary_post_id, 'supplemental_price_usd', $supplemental_price_usd );

		// Add mandatory transfer price for USD for the departure.
		$mandatory_transfer_price_usd = 50;
		update_post_meta( $itinerary_post_id, 'mandatory_transfer_price_usd', $mandatory_transfer_price_usd );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// Get the lowest price.
		$expected     = [
			'original'   => 34895 + $supplemental_price_usd + $mandatory_transfer_price_usd,
			'discounted' => 26171 + $supplemental_price_usd + $mandatory_transfer_price_usd,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $lowest_price );

		// Get the lowest price for CAD.
		$expected     = [
			'original'   => 47495,
			'discounted' => 35621,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'cad' );
		$this->assertEquals( $expected, $lowest_price );

		// Get departure with multiple occupancies and multiple cabins - HIJ-456:2025-09-04.
		$departure_post_ids = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'     => 'softrip_id',
						'value'   => 'HIJ-456:2025-09-04',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertIsArray( $departure_post_ids );

		// Get the departure post ID.
		$departure_post_id = $departure_post_ids[0];
		$this->assertIsInt( $departure_post_id );

		// Get the lowest price without any currency.
		$expected     = [
			'original'   => 12795,
			'discounted' => 10236,
		];
		$lowest_price = get_lowest_price( $departure_post_id );
		$this->assertIsArray( $lowest_price );
		$this->assertNotEmpty( $lowest_price );
		$this->assertEquals( $expected, $lowest_price );

		// Get the lowest price for USD.
		$expected     = [
			'original'   => 12795,
			'discounted' => 10236,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'USD' );
		$this->assertIsArray( $lowest_price );
		$this->assertNotEmpty( $lowest_price );
		$this->assertEquals( $expected, $lowest_price );

		// Get the lowest price for CAD.
		$expected     = [
			'original'   => 17500,
			'discounted' => 14000,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'CAD' );
		$this->assertIsArray( $lowest_price );
		$this->assertNotEmpty( $lowest_price );
		$this->assertEquals( $expected, $lowest_price );

		// Get the lowest price for AUD.
		$expected     = [
			'original'   => 20100,
			'discounted' => 16080,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'AUD' );
		$this->assertIsArray( $lowest_price );
		$this->assertNotEmpty( $lowest_price );
		$this->assertEquals( $expected, $lowest_price );

		// Invalid currency.
		$expected = $default_expected;
		$actual   = get_lowest_price( $departure_post_id, 'XYZ' );
		$this->assertSame( $expected, $actual );

		// Lowercase valid currency.
		$expected     = [
			'original'   => 11900,
			'discounted' => 9520,
		];
		$lowest_price = get_lowest_price( $departure_post_id, 'eur' );
		$this->assertEquals( $expected, $lowest_price );
	}

	/**
	 * Test add supplemental price and mandatory transfer price.
	 *
	 * @covers \Quark\Softrip\Occupancies\add_supplemental_and_mandatory_price
	 *
	 * @return void
	 */
	public function test_add_supplemental_and_mandatory_price(): void {
		// Default expected.
		$default_expected = [
			'original'   => 0,
			'discounted' => 0,
		];

		// Test with no arguments.
		$expected = $default_expected;
		$actual   = add_supplemental_and_mandatory_price();
		$this->assertEquals( $expected, $actual );

		// Test with default departure post id.
		$expected = $default_expected;
		$actual   = add_supplemental_and_mandatory_price( $default_expected );
		$this->assertEquals( $expected, $actual );

		// Test with invalid currency.
		$departure_post_id = 1231;
		$expected          = $default_expected;
		$actual            = add_supplemental_and_mandatory_price( $default_expected, $departure_post_id, 'XYZ' );
		$this->assertEquals( $expected, $actual );

		// Without supplement price and mandatory transfer price added to itinerary.
		$departure_post_id = 1231;
		$lowest_price      = [
			'original'   => 100,
			'discounted' => 0,
		];
		$expected          = $lowest_price;
		$actual            = add_supplemental_and_mandatory_price( $lowest_price, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Create an itinerary post.
		$itinerary_post_id = wp_insert_post(
			[
				'post_title'  => 'Test Itinerary',
				'post_type'   => ITINERARY_POST_TYPE,
				'post_status' => 'publish',
				'meta_input'  => [
					'softrip_package_code' => 'PQO-123',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id );

		// Update post meta.
		update_post_meta( $departure_post_id, 'itinerary', $itinerary_post_id );

		// Add the supplemental price for USD.
		$supplemental_price_usd = 10;
		update_post_meta( $itinerary_post_id, 'supplemental_price_usd', $supplemental_price_usd );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// With supplement price added to itinerary.
		$expected = [
			'original'   => 110,
			'discounted' => 10,
		];
		$actual   = add_supplemental_and_mandatory_price( $lowest_price, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Add mandatory transfer price for USD.
		$mandatory_transfer_price_usd = 5;

		// Add mandatory transfer price for USD.
		update_post_meta( $itinerary_post_id, 'mandatory_transfer_price_usd', $mandatory_transfer_price_usd );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// With mandatory transfer price added to itinerary.
		$expected = [
			'original'   => 115,
			'discounted' => 15,
		];
		$actual   = add_supplemental_and_mandatory_price( $lowest_price, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Delete supplemental price for USD.
		delete_post_meta( $itinerary_post_id, 'supplemental_price_usd' );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// With mandatory transfer price only.
		$expected = [
			'original'   => 105,
			'discounted' => 5,
		];
		$actual   = add_supplemental_and_mandatory_price( $lowest_price, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test delete occupancy by id.
	 *
	 * @covers \Quark\Softrip\Occupancies\delete_occupancy_by_id
	 *
	 * @return void
	 */
	public function test_delete_occupancy_by_id(): void {
		// Test with no arguments.
		$actual = delete_occupancy_by_id();
		$this->assertFalse( $actual );

		// Test with invalid occupancy ID.
		$occupancy_id = 0;
		$actual       = delete_occupancy_by_id( $occupancy_id );
		$this->assertFalse( $actual );

		// Test with non-existing occupancy ID.
		$occupancy_id = 1;
		$actual       = delete_occupancy_by_id( $occupancy_id );
		$this->assertFalse( $actual );

		// Create an occupancy without promotion.
		$raw_occupancy_data     = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id      = 1231;
		$cabin_category_post_id = 4561;
		$formatted_data         = format_data( $raw_occupancy_data, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data );
		$this->assertNotEmpty( $formatted_data );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data );

		// Inserted occupancy ID.
		$occupancy_id = $wpdb->insert_id;

		// Get occupancy by id.
		$occupancies = get_occupancy_data_by_id( $occupancy_id );
		$this->assertIsArray( $occupancies );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertNotEmpty( $occupancy );

		// Get occupancy by softrip ID.
		$occupancies_from_softrip_id = get_occupancy_data_by_softrip_id( $occupancies[0]['softrip_id'] );
		$this->assertIsArray( $occupancies_from_softrip_id );
		$this->assertNotEmpty( $occupancies_from_softrip_id );
		$this->assertCount( 1, $occupancies_from_softrip_id );

		// Cache key.
		$id_cache_key         = CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id;
		$softrip_id_cache_key = CACHE_KEY_PREFIX . '_softrip_id_' . $occupancy['softrip_id'];

		// Cache should not be empty.
		$actual_from_cache = wp_cache_get( $id_cache_key, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertEquals( $occupancies, $actual_from_cache );

		// Cache should not be empty.
		$actual_from_cache = wp_cache_get( $softrip_id_cache_key, CACHE_GROUP );
		$this->assertIsArray( $actual_from_cache );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertEquals( $occupancies, $actual_from_cache );

		// Test with valid occupancy ID.
		$actual = delete_occupancy_by_id( $occupancy_id );
		$this->assertTrue( $actual );

		// Cache should be empty.
		$actual_from_cache = wp_cache_get( $id_cache_key, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Cache should be empty.
		$actual_from_cache = wp_cache_get( $softrip_id_cache_key, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );
	}

	/**
	 * Test delete occupancies by departure id.
	 *
	 * @covers \Quark\Softrip\Occupancies\clear_occupancies_by_departure
	 *
	 * @return void
	 */
	public function test_clear_occupancies_by_departure(): void {
		// Test with no arguments.
		$actual = clear_occupancies_by_departure();
		$this->assertFalse( $actual );

		// Test with invalid departure ID.
		$departure_post_id = 0;
		$actual            = clear_occupancies_by_departure( $departure_post_id );
		$this->assertFalse( $actual );

		// Test with non-existing departure ID.
		$departure_post_id = 1;
		$actual            = clear_occupancies_by_departure( $departure_post_id );
		$this->assertFalse( $actual );

		// Create an occupancy without promotion.
		$raw_occupancy_data     = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id      = 1231;
		$cabin_category_post_id = 4561;
		$formatted_data         = format_data( $raw_occupancy_data, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data );
		$this->assertNotEmpty( $formatted_data );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data );

		// Inserted occupancy ID.
		$occupancy_id = $wpdb->insert_id;

		// Get occupancies by departure ID so that cache is warmed.
		$occupancies = get_occupancies_by_departure( $departure_post_id );
		$this->assertIsArray( $occupancies );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get occupancy by cabin category ID.
		$occupancies_by_cabin_category = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $occupancies_by_cabin_category );
		$this->assertNotEmpty( $occupancies_by_cabin_category );
		$this->assertCount( 1, $occupancies_by_cabin_category );

		// Get occupancy by softrip id.
		$occupancies_by_softrip_id = get_occupancy_data_by_softrip_id( $occupancies[0]['softrip_id'] );
		$this->assertIsArray( $occupancies_by_softrip_id );
		$this->assertNotEmpty( $occupancies_by_softrip_id );
		$this->assertCount( 1, $occupancies_by_softrip_id );

		// Get occupancy by ID.
		$occupancies_by_id = get_occupancy_data_by_id( $occupancy_id );
		$this->assertIsArray( $occupancies_by_id );
		$this->assertNotEmpty( $occupancies_by_id );
		$this->assertCount( 1, $occupancies_by_id );

		// Insert more occupancy.
		$raw_occupancy_data2 = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:AA',
			'name'            => 'Test Occupancy 2',
			'mask'            => 'AA',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 50,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 75,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 100,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 125,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
				],
			],
		];
		$formatted_data2     = format_data( $raw_occupancy_data2, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data2 );

		// Get inserted occupancy ID.
		$occupancy_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id2 );

		// Get occupancies.
		$occupancies = get_occupancies_by_departure( $departure_post_id, true );
		$this->assertIsArray( $occupancies );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 2, $occupancies );

		// Delete occupancies by departure ID.
		$actual = clear_occupancies_by_departure( $departure_post_id );
		$this->assertTrue( $actual );

		// Departure caches should be empty.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Cabin category caches should be empty.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_cabin_category_post_id_' . $cabin_category_post_id . '_departure_post_id_' . $departure_post_id, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Occupancy caches should be empty.
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );
		$actual_from_cache = wp_cache_get( CACHE_KEY_PREFIX . '_occupancy_id_' . $occupancy_id2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );
	}

	/**
	 * Get lowest price by cabin category and departure.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_lowest_price_by_cabin_category_and_departure
	 *
	 * @return void
	 */
	public function test_get_lowest_price_by_cabin_category_and_departure(): void {
		// Setup default expected.
		$expected_default = [
			'original'   => 0,
			'discounted' => 0,
		];

		// Test with no arguments.
		$actual = get_lowest_price_by_cabin_category_and_departure();
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid cabin category ID.
		$cabin_category_post_id = 0;
		$departure_post_id      = 1231;
		$actual                 = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid departure ID.
		$cabin_category_post_id = 4561;
		$departure_post_id      = 0;
		$actual                 = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertEquals( $expected_default, $actual );

		// Test with non-existing cabin category ID.
		$cabin_category_post_id = 1;
		$departure_post_id      = 1231;
		$actual                 = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertEquals( $expected_default, $actual );

		// Test with non-existing departure ID.
		$cabin_category_post_id = 4561;
		$departure_post_id      = 1;
		$actual                 = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertEquals( $expected_default, $actual );

		// Create an occupancy without promotion.
		$raw_occupancy_data     = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
				],
			],
		];
		$departure_post_id      = 1231;
		$cabin_category_post_id = 4561;
		$formatted_data         = format_data( $raw_occupancy_data, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data );
		$this->assertNotEmpty( $formatted_data );

		// Get table name.
		$table_name = get_table_name();

		// Get global wpdb object.
		global $wpdb;

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data );

		// Inserted occupancy ID.
		$occupancy_id = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id );

		// Get lowest price by cabin category and departure - USD.
		$expected = [
			'original'   => 100,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - CAD.
		$expected = [
			'original'   => 150,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - AUD.
		$expected = [
			'original'   => 200,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - GBP. Also, smaller case currency.
		$expected = [
			'original'   => 250,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'gbp' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - EUR.
		$expected = [
			'original'   => 300,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'EUR' );

		// Invalid currency.
		$expected = $expected_default;
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'XYZ' );
		$this->assertEquals( $expected, $actual );

		// Add one more occupancy to same cabin.
		$raw_occupancy_data2 = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:AA',
			'name'            => 'Test Occupancy 2',
			'mask'            => 'AA',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 50,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 75,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 100,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 125,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 150,
				],
			],
		];
		$formatted_data2     = format_data( $raw_occupancy_data2, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data2 );

		// Get inserted occupancy ID.
		$occupancy_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id2 );

		// Flush cache.
		wp_cache_flush();

		// Get lowest price by cabin category and departure.
		$expected = [
			'original'   => 50,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - CAD.
		$expected = [
			'original'   => 75,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - AUD.
		$expected = [
			'original'   => 100,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - GBP.
		$expected = [
			'original'   => 125,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - EUR.
		$expected = [
			'original'   => 150,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'eur' );
		$this->assertEquals( $expected, $actual );

		// Create an itinerary post.
		$itinerary_post_id = wp_insert_post(
			[
				'post_title'  => 'Test Itinerary',
				'post_type'   => ITINERARY_POST_TYPE,
				'post_status' => 'publish',
				'meta_input'  => [
					'softrip_package_code' => 'PQO-123',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id );

		// Update post meta.
		update_post_meta( $departure_post_id, 'itinerary', $itinerary_post_id );

		// Add supplemental price for USD for the departure.
		$supplemental_price_usd = 10;
		update_post_meta( $itinerary_post_id, 'supplemental_price_usd', $supplemental_price_usd );

		// Add mandatory transfer price for USD for the departure.
		$mandatory_transfer_price_usd = 5;
		update_post_meta( $itinerary_post_id, 'mandatory_transfer_price_usd', $mandatory_transfer_price_usd );

		// Flush the itinerary cache.
		wp_cache_delete( ITINERARIES_CACHE_KEY . "_$itinerary_post_id", ITINERARIES_CACHE_GROUP );

		// Get lowest price by cabin category and departure.
		$expected = [
			'original'   => 50 + $supplemental_price_usd + $mandatory_transfer_price_usd,
			'discounted' => $supplemental_price_usd + $mandatory_transfer_price_usd,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - CAD.
		$expected = [
			'original'   => 75,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Add one occupancy for different cabin category but same departure.
		$raw_occupancy_data3    = [
			'id'              => 'PQO-123:2026-02-20:OEX-SGL:AB',
			'name'            => 'Test Occupancy 3',
			'mask'            => 'AB',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 75,
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 100,
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 125,
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 150,
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 175,
				],
			],
		];
		$cabin_category_post_id = 4562;
		$formatted_data3        = format_data( $raw_occupancy_data3, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data3 );

		// Insert occupancy data.
		$wpdb->insert( $table_name, $formatted_data3 );

		// Get inserted occupancy ID.
		$occupancy_id3 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id3 );

		// Flush cache.
		wp_cache_flush();

		// Get lowest price by cabin category and departure - USD.
		$expected = [
			'original'   => 75 + $supplemental_price_usd + $mandatory_transfer_price_usd,
			'discounted' => $supplemental_price_usd + $mandatory_transfer_price_usd,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'USD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure - CAD.
		$expected = [
			'original'   => 100,
			'discounted' => 0,
		];
		$actual   = get_lowest_price_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id, 'CAD' );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test format row data from db.
	 *
	 * @covers \Quark\Softrip\Occupancies\format_row_data_from_db
	 *
	 * @return void
	 */
	public function test_format_row_data_from_db(): void {
		// Test with no arguments.
		$actual = format_row_data_from_db();
		$this->assertEmpty( $actual );

		// Test with empty data.
		$data   = [];
		$actual = format_row_data_from_db( $data );
		$this->assertEmpty( $actual );

		// Test with non-existing id.
		$data     = [
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with empty id.
		$data     = [
			'id'                       => '',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing cabin category post id.
		$data     = [
			'id'                       => '1',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with empty cabin category post id.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing departure post id.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with empty departure post id.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing softrip id.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with empty softrip id.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => '',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing softrip name.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with empty softrip name.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => '',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing mask.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with empty mask.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => '',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing spaces available.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
		];
		$expected = [
			'id'                       => 1,
			'cabin_category_post_id'   => 123,
			'departure_post_id'        => 456,
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => 0,
			'availability_description' => 'Available',
			'availability_status'      => 'O',
			'price_per_person_usd'     => 0,
			'price_per_person_cad'     => 0,
			'price_per_person_aud'     => 0,
			'price_per_person_gbp'     => 0,
			'price_per_person_eur'     => 0,
		];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with valid data.
		$data     = [
			'id'                       => '1',
			'cabin_category_post_id'   => '123',
			'departure_post_id'        => '456',
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => '10',
			'availability_description' => 'Available',
			'availability_status'      => 'O',
			'price_per_person_usd'     => '100',
			'price_per_person_cad'     => '150',
			'price_per_person_aud'     => '200',
			'price_per_person_gbp'     => '250',
			'price_per_person_eur'     => '300',
		];
		$expected = [
			'id'                       => 1,
			'cabin_category_post_id'   => 123,
			'departure_post_id'        => 456,
			'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
			'softrip_name'             => 'Test Occupancy',
			'mask'                     => 'A',
			'spaces_available'         => 10,
			'availability_description' => 'Available',
			'availability_status'      => 'O',
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 150,
			'price_per_person_aud'     => 200,
			'price_per_person_gbp'     => 250,
			'price_per_person_eur'     => 300,
		];
		$actual   = format_row_data_from_db( $data );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test format rows data from db.
	 *
	 * @covers \Quark\Softrip\Occupancies\format_rows_data_from_db
	 *
	 * @return void
	 */
	public function test_format_rows_data_from_db(): void {
		// Test with no arguments.
		$actual = format_rows_data_from_db();
		$this->assertEmpty( $actual );

		// Test with empty data.
		$data   = [];
		$actual = format_rows_data_from_db( $data );
		$this->assertEmpty( $actual );

		// Test with non-existing data.
		$data     = [
			[
				'cabin_category_post_id'   => '123',
				'departure_post_id'        => '456',
				'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
				'softrip_name'             => 'Test Occupancy',
				'mask'                     => 'A',
				'spaces_available'         => '10',
				'availability_description' => 'Available',
				'availability_status'      => 'O',
			],
		];
		$expected = [];
		$actual   = format_rows_data_from_db( $data );
		$this->assertEquals( $expected, $actual );

		// Test with valid data.
		$data     = [
			[
				'id'                       => '1',
				'cabin_category_post_id'   => '123',
				'departure_post_id'        => '456',
				'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
				'softrip_name'             => 'Test Occupancy',
				'mask'                     => 'A',
				'spaces_available'         => '10',
				'availability_description' => 'Available',
				'availability_status'      => 'O',
				'price_per_person_usd'     => '100',
				'price_per_person_cad'     => '150',
				'price_per_person_aud'     => '200',
				'price_per_person_gbp'     => '250',
				'price_per_person_eur'     => '300',
			],
			[
				'id'                       => '2',
				'cabin_category_post_id'   => '123',
				'departure_post_id'        => '456',
				'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:B',
				'softrip_name'             => 'Test Occupancy 2',
				'mask'                     => 'B',
				'spaces_available'         => '20',
				'availability_description' => 'Available',
				'availability_status'      => 'O',
				'price_per_person_usd'     => '200',
				'price_per_person_cad'     => '250',
				'price_per_person_aud'     => '300',
				'price_per_person_gbp'     => '350',
				'price_per_person_eur'     => '400',
			],
		];
		$expected = [
			[
				'id'                       => 1,
				'cabin_category_post_id'   => 123,
				'departure_post_id'        => 456,
				'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:A',
				'softrip_name'             => 'Test Occupancy',
				'mask'                     => 'A',
				'spaces_available'         => 10,
				'availability_description' => 'Available',
				'availability_status'      => 'O',
				'price_per_person_usd'     => 100,
				'price_per_person_cad'     => 150,
				'price_per_person_aud'     => 200,
				'price_per_person_gbp'     => 250,
				'price_per_person_eur'     => 300,
			],
			[
				'id'                       => 2,
				'cabin_category_post_id'   => 123,
				'departure_post_id'        => 456,
				'softrip_id'               => 'PQO-123:2026-02-20:OEX-SGL:B',
				'softrip_name'             => 'Test Occupancy 2',
				'mask'                     => 'B',
				'spaces_available'         => 20,
				'availability_description' => 'Available',
				'availability_status'      => 'O',
				'price_per_person_usd'     => 200,
				'price_per_person_cad'     => 250,
				'price_per_person_aud'     => 300,
				'price_per_person_gbp'     => 350,
				'price_per_person_eur'     => 400,
			],
		];
		$actual   = format_rows_data_from_db( $data );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get lowest price by cabin category and departure and promotion code.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_lowest_price_by_cabin_category_and_departure_and_promotion_code
	 *
	 * @return void
	 */
	public function test_get_lowest_price_by_cabin_category_and_departure_and_promotion_code(): void {
		// Setup default expected.
		$expected_default = 0;

		// Test with no arguments.
		$actual = get_lowest_price_by_cabin_category_and_departure_and_promotion_code();
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid cabin category ID.
		$cabin_category_post_id = 0;
		$departure_post_id      = 1231;
		$promotion_code         = 'TEST';
		$actual                 = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promotion_code );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid departure ID.
		$cabin_category_post_id = 4561;
		$departure_post_id      = 0;
		$promotion_code         = 'TEST';
		$actual                 = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promotion_code );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid promotion code.
		$cabin_category_post_id = 4561;
		$departure_post_id      = 1231;
		$promotion_code         = '';
		$actual                 = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promotion_code );
		$this->assertEquals( $expected_default, $actual );

		// Test with non-existing cabin category ID.
		$cabin_category_post_id = 1;
		$departure_post_id      = 1231;
		$promotion_code         = 'TEST';
		$actual                 = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promotion_code );
		$this->assertEquals( $expected_default, $actual );

		// Test with non-existing departure ID.
		$cabin_category_post_id = 4561;
		$departure_post_id      = 1;
		$promotion_code         = 'TEST';
		$actual                 = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promotion_code );
		$this->assertEquals( $expected_default, $actual );

		// Test with non-existing promotion code.
		$cabin_category_post_id = 4561;
		$departure_post_id      = 1231;
		$promotion_code         = 'TEST';
		$actual                 = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promotion_code );
		$this->assertEquals( $expected_default, $actual );

		// Setup variables.
		global $wpdb;
		$promotion_table_name            = get_promotions_table_name();
		$promo_code1                     = '10PROMO';
		$promo_code2                     = '20PROMO';
		$promo_code3                     = '10PIF';
		$occupancies_table_name          = get_table_name();
		$occupancy_promotions_table_name = get_occupancy_promotions_table_name();

		// Create a promotion - 10PROMO.
		$promotion1 = [
			'code'           => $promo_code1,
			'start_date'     => '2024-02-20',
			'end_date'       => '2026-02-20',
			'description'    => '10% off',
			'discount_type'  => 'percentage',
			'discount_value' => '0.1',
			'is_pif'         => '0',
		];
		$wpdb->insert( $promotion_table_name, $promotion1 );
		$promotion1_id = $wpdb->insert_id;
		$this->assertIsInt( $promotion1_id );

		// Create a promotion - 20PROMO.
		$promotion2 = [
			'code'           => $promo_code2,
			'start_date'     => '2024-03-18',
			'end_date'       => '2026-01-20',
			'description'    => '20% off',
			'discount_type'  => 'percentage',
			'discount_value' => '0.2',
			'is_pif'         => '0',
		];
		$wpdb->insert( $promotion_table_name, $promotion2 );
		$promotion2_id = $wpdb->insert_id;
		$this->assertIsInt( $promotion2_id );

		// Create a promotion - 10PIF.
		$promotion3 = [
			'code'           => $promo_code3,
			'start_date'     => '2024-02-20',
			'end_date'       => '2026-02-20',
			'description'    => 'Pay in full at time of booking and save 10%',
			'discount_type'  => 'percentage',
			'discount_value' => '0.1',
			'is_pif'         => '1',
		];
		$wpdb->insert( $promotion_table_name, $promotion3 );
		$promotion3_id = $wpdb->insert_id;
		$this->assertIsInt( $promotion3_id );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_id' => 'UNQ-123:2026-02-20',
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Create a cabin category post.
		$cabin_category_post_id = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORIES_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'OEX-UNQ',
				],
			]
		);
		$this->assertIsInt( $cabin_category_post_id );

		// Create an occupancy.
		$raw_occupancy_data1 = [
			'id'              => 'UNQ-123:2026-02-20:OEX-SGL:A',
			'name'            => 'Test Occupancy 1',
			'mask'            => 'A',
			'spacesAvailable' => 10,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
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
						'10PIF'   => [
							'promoPricePerPerson' => 89,
						],
					],
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 150,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 135,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 120,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 123,
						],
					],
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 200,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 180,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 187,
						],
					],
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 250,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 225,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 200,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 225,
						],
					],
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 300,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 270,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 240,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 210,
						],
					],
				],
			],
		];
		$formatted_data1     = format_data( $raw_occupancy_data1, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data1 );
		$this->assertNotEmpty( $formatted_data1 );

		// Insert occupancy data.
		$wpdb->insert( $occupancies_table_name, $formatted_data1 );

		// Get inserted occupancy ID.
		$occupancy_id1 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id1 );

		// Update the first occupancy promotion.
		$is_updated = update_occupancy_promotions( $raw_occupancy_data1['prices'], $occupancy_id1 );
		$this->assertTrue( $is_updated );

		// Create one more occupancy.
		$raw_occupancy_data2 = [
			'id'              => 'UNQ-123:2026-02-20:OEX-SGL:B',
			'name'            => 'Test Occupancy 2',
			'mask'            => 'B',
			'spacesAvailable' => 20,
			'saleStatus'      => 'Open',
			'saleStatusCode'  => 'O',
			'prices'          => [
				'USD' => [
					'currencyCode'   => 'USD',
					'pricePerPerson' => 200,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 180,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 160,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 180,
						],
					],
				],
				'CAD' => [
					'currencyCode'   => 'CAD',
					'pricePerPerson' => 250,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 225,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 200,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 225,
						],
					],
				],
				'AUD' => [
					'currencyCode'   => 'AUD',
					'pricePerPerson' => 300,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 270,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 240,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 270,
						],
					],
				],
				'GBP' => [
					'currencyCode'   => 'GBP',
					'pricePerPerson' => 350,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 315,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 280,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 315,
						],
					],
				],
				'EUR' => [
					'currencyCode'   => 'EUR',
					'pricePerPerson' => 400,
					'promos'         => [
						'10PROMO' => [
							'promoPricePerPerson' => 360,
						],
						'20PROMO' => [
							'promoPricePerPerson' => 320,
						],
						'10PIF'   => [
							'promoPricePerPerson' => 360,
						],
					],
				],
			],
		];
		$formatted_data2     = format_data( $raw_occupancy_data2, $cabin_category_post_id, $departure_post_id );
		$this->assertIsArray( $formatted_data2 );
		$this->assertNotEmpty( $formatted_data2 );

		// Insert occupancy data.
		$wpdb->insert( $occupancies_table_name, $formatted_data2 );

		// Get inserted occupancy ID.
		$occupancy_id2 = $wpdb->insert_id;
		$this->assertIsInt( $occupancy_id2 );

		// Update the second occupancy promotion.
		$is_updated = update_occupancy_promotions( $raw_occupancy_data2['prices'], $occupancy_id2 );
		$this->assertTrue( $is_updated );

		// Flush cache.
		wp_cache_flush();

		// Get lowest price by cabin category and departure and promotion code - 10PROMO for USD.
		$expected = 90;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code1 );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 20PROMO for USD.
		$expected = 80;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code2 );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PIF for USD.
		$expected = 89;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code3 );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PROMO for CAD.
		$expected = 135;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code1, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 20PROMO for CAD.
		$expected = 120;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code2, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PIF for CAD.
		$expected = 123;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code3, 'CAD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PROMO for AUD.
		$expected = 180;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code1, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 20PROMO for AUD.
		$expected = 240;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code2, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PIF for AUD.
		$expected = 187;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code3, 'AUD' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PROMO for GBP.
		$expected = 225;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code1, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 20PROMO for GBP.
		$expected = 200;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code2, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PIF for GBP.
		$expected = 225;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code3, 'GBP' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PROMO for EUR.
		$expected = 270;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code1, 'EUR' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 20PROMO for EUR.
		$expected = 240;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code2, 'EUR' );
		$this->assertEquals( $expected, $actual );

		// Get lowest price by cabin category and departure and promotion code - 10PIF for EUR.
		$expected = 210;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code3, 'EUR' );
		$this->assertEquals( $expected, $actual );

		// For invalid promotion code.
		$expected = 0;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, 'INVALID' );
		$this->assertEquals( $expected, $actual );

		// For invalid currency code.
		$expected = 0;
		$actual   = get_lowest_price_by_cabin_category_and_departure_and_promotion_code( $cabin_category_post_id, $departure_post_id, $promo_code1, 'INVALID' );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test getting mask mapping.
	 *
	 * @covers \Quark\Softrip\Occupancies\get_masks_mapping
	 *
	 * @return void
	 */
	public function test_get_masks_mapping(): void {
		// Test.
		$expected = [
			'A'     => [
				'description' => 'Single Room',
				'pax_count'   => 1,
			],
			'AA'    => [
				'description' => 'Double Room',
				'pax_count'   => 2,
			],
			'SAA'   => [
				'description' => 'Double Room Shared',
				'pax_count'   => 1,
			],
			'SMAA'  => [
				'description' => 'Double Room Shared (Male)',
				'pax_count'   => 1,
			],
			'SFAA'  => [
				'description' => 'Double Room Shared (Female)',
				'pax_count'   => 1,
			],
			'AAA'   => [
				'description' => 'Triple Room',
				'pax_count'   => 3,
			],
			'SAAA'  => [
				'description' => 'Triple Room Shared',
				'pax_count'   => 1,
			],
			'SMAAA' => [
				'description' => 'Triple Room Shared (Male)',
				'pax_count'   => 1,
			],
			'SFAAA' => [
				'description' => 'Triple Room Shared (Female)',
				'pax_count'   => 1,
			],
			'AAAA'  => [
				'description' => 'Quad Room',
				'pax_count'   => 4,
			],
		];
		$actual   = get_masks_mapping();
		$this->assertEquals( $expected, $actual );
	}
}
