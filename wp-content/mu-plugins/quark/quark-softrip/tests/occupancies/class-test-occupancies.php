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
use function Quark\Softrip\Occupancies\format_data;
use function Quark\Softrip\Occupancies\get_cabin_category_post_by_cabin_code;
use function Quark\Softrip\Occupancies\get_occupancies_by_departure;
use function Quark\Softrip\Occupancies\get_occupancy_data_by_softrip_id;
use function Quark\Softrip\Occupancies\get_table_name;
use function Quark\Softrip\Occupancies\get_table_sql;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORIES_POST_TYPE;
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
			'id'                      => '123',
			'name'                    => 'Test Occupancy',
			'mask'                    => '123456789012',
			'spacesAvailable'         => 10,
			'availabilityDescription' => 'Available',
			'availabilityStatus'      => 'AV',
			'prices'                  => [
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
		$raw_occupancy_data['availabilityStatus'] = '';
		$expected                                 = [];
		$actual                                   = format_data( $raw_occupancy_data, 123, 456 );
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

		// Set all values expect spaces available. It should be 0.
		$raw_occupancy_data = [
			'id'                      => '123',
			'name'                    => 'Test Occupancy',
			'mask'                    => '123456789012',
			'availabilityStatus'      => 'AV',
			'availabilityDescription' => 'Available',
			'prices'                  => [
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
			'availability_description' => 'Available',
			'availability_status'      => 'AV',
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
			'id'                      => '123',
			'name'                    => 'Test Occupancy',
			'mask'                    => '123456789012',
			'spacesAvailable'         => 10,
			'availabilityDescription' => 'Available',
			'availabilityStatus'      => 'AV',
			'prices'                  => [
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
			'availability_description' => 'Available',
			'availability_status'      => 'AV',
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
			'availability_description' => 'Available',
			'availability_status'      => 'AV',
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
			'id'                      => 'PQO-123',
			'name'                    => 'Test Occupancy',
			'mask'                    => '123456789012',
			'spacesAvailable'         => 10,
			'availabilityDescription' => 'Available',
			'availabilityStatus'      => 'AV',
			'prices'                  => [
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
			'id'                      => 'PQO-456',
			'name'                    => 'Test Occupancy 2',
			'mask'                    => '123456789012',
			'spacesAvailable'         => 20,
			'availabilityDescription' => 'Available',
			'availabilityStatus'      => 'AV',
			'prices'                  => [
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
		$expected = [];
		$actual   = get_occupancies_by_departure( $departure_post_id );
		$this->assertSame( $expected, $actual );
	}
}
