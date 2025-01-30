<?php
/**
 * Test suite for Adventure Options.
 *
 * @package quark-softrip
 */

namespace Quark\Tests\AdventureOptions;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\AdventureOptions\delete_adventure_option_by_id;
use function Quark\Softrip\AdventureOptions\format_adventure_option_data;
use function Quark\Softrip\AdventureOptions\format_row_data_from_db;
use function Quark\Softrip\AdventureOptions\format_rows_data_from_db;
use function Quark\Softrip\AdventureOptions\get_adventure_option_by_departure_post_id;
use function Quark\Softrip\AdventureOptions\get_adventure_option_by_softrip_option_id;
use function Quark\Softrip\AdventureOptions\get_adventure_option_taxonomy_term_by_service_id;
use function Quark\Softrip\AdventureOptions\get_adventure_options_by_id;
use function Quark\Softrip\AdventureOptions\get_departures_by_adventure_option_term_id;
use function Quark\Softrip\AdventureOptions\get_table_name;
use function Quark\Softrip\AdventureOptions\get_table_sql;
use function Quark\Softrip\AdventureOptions\update_adventure_options;
use function Quark\Softrip\get_engine_collate;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Softrip\TABLE_PREFIX_NAME;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\Softrip\AdventureOptions\CACHE_GROUP;
use const Quark\Softrip\AdventureOptions\CACHE_KEY_PREFIX;

/**
 * Class Test_Adventure_Options
 */
class Test_Adventure_Options extends Softrip_TestCase {
	/**
	 * Test get table name.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_table_name
	 *
	 * @return void
	 */
	public function test_get_table_name(): void {
		// Test table name.
		$expected = TABLE_PREFIX_NAME . 'adventure_options';
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
			softrip_option_id VARCHAR(255) NOT NULL UNIQUE,
			departure_post_id BIGINT NOT NULL,
			softrip_package_code VARCHAR(20) NOT NULL,
			service_ids VARCHAR(255) NOT NULL,
			spaces_available BIGINT NOT NULL,
			adventure_option_term_id BIGINT NOT NULL,
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
	 * Test update adventure option data.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\update_adventure_option
	 *
	 * @return void
	 */
	public function test_update_adventure_option(): void {
		// Setup variables.
		$table_name = get_table_name();
		global $wpdb;

		// Test with no arguments.
		$expected = false;
		$actual   = update_adventure_options();
		$this->assertSame( $expected, $actual );

		// Test with invalid data.
		$adventure_option_data = [];
		$expected              = false;
		$actual                = update_adventure_options( $adventure_option_data );
		$this->assertSame( $expected, $actual );

		// Test with invalid data.
		$adventure_option_data = [
			'softrip_option_id'        => '123',
			'departure_post_id'        => 123,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 123,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected              = false;
		$actual                = update_adventure_options( $adventure_option_data );
		$this->assertSame( $expected, $actual );

		// Create a departure.
		$softrip_package_code1 = 'UNQ-123';
		$departure_post_id1    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code1,
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Create another departure.
		$softrip_package_code2 = 'UNQ-456';
		$departure_post_id2    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Test with valid data.
		$raw_adventure_options = [];
		$expected              = false;
		$actual                = update_adventure_options( $raw_adventure_options, $departure_post_id1 );
		$this->assertSame( $expected, $actual );

		// Get adventure options meta on departure - it should have been set with empty array.
		$actual = get_post_meta( $departure_post_id1, 'adventure_options', true );
		$this->assertIsArray( $actual );
		$this->assertEmpty( $actual );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';
		$service_id3 = 'service_id_3';

		// Attach service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// Raw adventure option for departure 1.
		$raw_adventure_option1 = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];

		// One more raw adventure option for departure 1.
		$raw_adventure_option2 = [
			'id'              => '456',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 200,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 22,
			'serviceIds'      => [ $service_id2 ],
		];

		// Raw adventure option for departure 2.
		$raw_adventure_option3 = [
			'id'              => '789',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 300,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 33,
			'serviceIds'      => [ $service_id3, $service_id2 ],
		];

		// Club raw adventure options for departure 1 and 2.
		$raw_adventure_options1 = [ $raw_adventure_option1, $raw_adventure_option2 ];

		// Club raw adventure options for departure 2.
		$raw_adventure_options2 = [ $raw_adventure_option3 ];

		// There should be no adventure option for departure 1.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertEmpty( $actual );

		// Update adventure option for departure 1.
		$expected = true;
		$actual   = update_adventure_options( $raw_adventure_options1, $departure_post_id1 );
		$this->assertSame( $expected, $actual );

		// Get adventure option for departure 1.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 2, $actual );

		// Get first adventure option data.
		$actual1 = $actual[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertArrayHasKey( 'softrip_option_id', $actual1 );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertArrayHasKey( 'softrip_package_code', $actual1 );
		$this->assertArrayHasKey( 'service_ids', $actual1 );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertEquals( $raw_adventure_option1['id'], $actual1['softrip_option_id'] );
		$this->assertEquals( $departure_post_id1, $actual1['departure_post_id'] );
		$this->assertEquals( $softrip_package_code1, $actual1['softrip_package_code'] );
		$this->assertEquals( implode( ',', $raw_adventure_option1['serviceIds'] ), $actual1['service_ids'] );
		$this->assertEquals( $raw_adventure_option1['spacesAvailable'], $actual1['spaces_available'] );
		$this->assertEquals( $adventure_option_term_id1, $actual1['adventure_option_term_id'] );
		$this->assertEquals( $raw_adventure_option1['price']['USD']['pricePerPerson'], $actual1['price_per_person_usd'] );
		$this->assertEquals( $raw_adventure_option1['price']['CAD']['pricePerPerson'], $actual1['price_per_person_cad'] );
		$this->assertEquals( $raw_adventure_option1['price']['AUD']['pricePerPerson'], $actual1['price_per_person_aud'] );
		$this->assertEquals( $raw_adventure_option1['price']['GBP']['pricePerPerson'], $actual1['price_per_person_gbp'] );
		$this->assertEquals( $raw_adventure_option1['price']['EUR']['pricePerPerson'], $actual1['price_per_person_eur'] );

		// Get second adventure option data.
		$actual2 = $actual[1];
		$this->assertIsArray( $actual2 );
		$this->assertNotEmpty( $actual2 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual2 );
		$this->assertArrayHasKey( 'softrip_option_id', $actual2 );
		$this->assertArrayHasKey( 'departure_post_id', $actual2 );
		$this->assertArrayHasKey( 'softrip_package_code', $actual2 );
		$this->assertArrayHasKey( 'service_ids', $actual2 );
		$this->assertArrayHasKey( 'spaces_available', $actual2 );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual2 );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual2 );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual2 );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual2 );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual2 );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual2 );
		$this->assertEquals( $raw_adventure_option2['id'], $actual2['softrip_option_id'] );
		$this->assertEquals( $departure_post_id1, $actual2['departure_post_id'] );
		$this->assertEquals( $softrip_package_code1, $actual2['softrip_package_code'] );
		$this->assertEquals( implode( ',', $raw_adventure_option2['serviceIds'] ), $actual2['service_ids'] );
		$this->assertEquals( $raw_adventure_option2['spacesAvailable'], $actual2['spaces_available'] );
		$this->assertEquals( $adventure_option_term_id2, $actual2['adventure_option_term_id'] );
		$this->assertEquals( $raw_adventure_option2['price']['USD']['pricePerPerson'], $actual2['price_per_person_usd'] );
		$this->assertEquals( $raw_adventure_option2['price']['CAD']['pricePerPerson'], $actual2['price_per_person_cad'] );
		$this->assertEquals( $raw_adventure_option2['price']['AUD']['pricePerPerson'], $actual2['price_per_person_aud'] );
		$this->assertEquals( $raw_adventure_option2['price']['GBP']['pricePerPerson'], $actual2['price_per_person_gbp'] );
		$this->assertEquals( $raw_adventure_option2['price']['EUR']['pricePerPerson'], $actual2['price_per_person_eur'] );

		// There should be no adventure option for departure 2.
		$actual3 = get_adventure_option_by_departure_post_id( $departure_post_id2 );
		$this->assertEmpty( $actual3 );

		// Update adventure option for departure 2.
		$expected = true;
		$actual3  = update_adventure_options( $raw_adventure_options2, $departure_post_id2 );
		$this->assertSame( $expected, $actual3 );

		// Get adventure option for departure 2.
		$actual3 = get_adventure_option_by_departure_post_id( $departure_post_id2 );
		$this->assertNotEmpty( $actual3 );
		$this->assertIsArray( $actual3 );
		$this->assertCount( 1, $actual3 );

		// Get first adventure option data.
		$actual3_1 = $actual3[0];
		$this->assertIsArray( $actual3_1 );
		$this->assertNotEmpty( $actual3_1 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual3_1 );
		$this->assertArrayHasKey( 'softrip_option_id', $actual3_1 );
		$this->assertArrayHasKey( 'departure_post_id', $actual3_1 );
		$this->assertArrayHasKey( 'softrip_package_code', $actual3_1 );
		$this->assertArrayHasKey( 'service_ids', $actual3_1 );
		$this->assertArrayHasKey( 'spaces_available', $actual3_1 );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual3_1 );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual3_1 );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual3_1 );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual3_1 );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual3_1 );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual3_1 );
		$this->assertEquals( $raw_adventure_option3['id'], $actual3_1['softrip_option_id'] );
		$this->assertEquals( $departure_post_id2, $actual3_1['departure_post_id'] );
		$this->assertEquals( $softrip_package_code2, $actual3_1['softrip_package_code'] );
		$this->assertEquals( implode( ',', $raw_adventure_option3['serviceIds'] ), $actual3_1['service_ids'] );
		$this->assertEquals( $raw_adventure_option3['spacesAvailable'], $actual3_1['spaces_available'] );
		$this->assertEquals( $adventure_option_term_id2, $actual3_1['adventure_option_term_id'] );
		$this->assertEquals( $raw_adventure_option3['price']['USD']['pricePerPerson'], $actual3_1['price_per_person_usd'] );
		$this->assertEquals( $raw_adventure_option3['price']['CAD']['pricePerPerson'], $actual3_1['price_per_person_cad'] );
		$this->assertEquals( $raw_adventure_option3['price']['AUD']['pricePerPerson'], $actual3_1['price_per_person_aud'] );
		$this->assertEquals( $raw_adventure_option3['price']['GBP']['pricePerPerson'], $actual3_1['price_per_person_gbp'] );
		$this->assertEquals( $raw_adventure_option3['price']['EUR']['pricePerPerson'], $actual3_1['price_per_person_eur'] );

		// Update adventure option for departure 1 - price update.
		$raw_adventure_option1['price']['USD']['pricePerPerson'] = 200;
		$raw_adventure_option1['price']['CAD']['pricePerPerson'] = 400;
		$raw_adventure_option1['price']['AUD']['pricePerPerson'] = 300;
		$raw_adventure_option1['price']['GBP']['pricePerPerson'] = 200;
		$raw_adventure_option1['price']['EUR']['pricePerPerson'] = 100;

		/**
		 * Only provide first adventure option for update.
		 * This should only update the first and since, the second is not provided, it should be deleted.
		 */
		$raw_adventure_options1 = [ $raw_adventure_option1 ];
		$expected               = true;
		$actual                 = update_adventure_options( $raw_adventure_options1, $departure_post_id1 );
		$this->assertSame( $expected, $actual );

		// Get adventure option for departure 1.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Get first adventure option data.
		$actual1 = $actual[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Validate price update.
		$this->assertEquals( $raw_adventure_option1['price']['USD']['pricePerPerson'], $actual1['price_per_person_usd'] );
		$this->assertEquals( $raw_adventure_option1['price']['CAD']['pricePerPerson'], $actual1['price_per_person_cad'] );
		$this->assertEquals( $raw_adventure_option1['price']['AUD']['pricePerPerson'], $actual1['price_per_person_aud'] );
		$this->assertEquals( $raw_adventure_option1['price']['GBP']['pricePerPerson'], $actual1['price_per_person_gbp'] );
		$this->assertEquals( $raw_adventure_option1['price']['EUR']['pricePerPerson'], $actual1['price_per_person_eur'] );

		// Check adventure option term id.
		$this->assertEquals( $adventure_option_term_id1, $actual1['adventure_option_term_id'] );

		// Check adventure_options meta on departure post - only one should be attached.
		$actual = get_post_meta( $departure_post_id1, 'adventure_options', true );
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertCount( 1, $actual );
		$this->assertContains( $adventure_option_term_id1, $actual );
		$this->assertNotContains( $adventure_option_term_id2, $actual );

		// Add new service id to term meta.
		$service_id4 = 'service_id_4';
		update_term_meta( $adventure_option_term_id1, 'softrip_1_id', $service_id4 );

		// Raw adventure option for departure 1.
		$raw_adventure_option1 = [
			'id'              => '1234',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 120,
				],
				'CAD' => [
					'pricePerPerson' => 354,
				],
				'AUD' => [
					'pricePerPerson' => 213,
				],
				'GBP' => [
					'pricePerPerson' => 204,
				],
				'EUR' => [
					'pricePerPerson' => 113,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id4 ],
		];

		// Update adventure option for departure 1.
		$raw_adventure_options1 = [ $raw_adventure_option1 ];

		// Update adventure option for departure 1.
		$expected = true;
		$actual   = update_adventure_options( $raw_adventure_options1, $departure_post_id1 );
		$this->assertSame( $expected, $actual );

		// Get adventure option for departure 1.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertNotEmpty( $actual );

		// Get first adventure option data.
		$actual1 = $actual[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Validate price update.
		$this->assertEquals( $raw_adventure_option1['price']['USD']['pricePerPerson'], $actual1['price_per_person_usd'] );
		$this->assertEquals( $raw_adventure_option1['price']['CAD']['pricePerPerson'], $actual1['price_per_person_cad'] );
		$this->assertEquals( $raw_adventure_option1['price']['AUD']['pricePerPerson'], $actual1['price_per_person_aud'] );
		$this->assertEquals( $raw_adventure_option1['price']['GBP']['pricePerPerson'], $actual1['price_per_person_gbp'] );
		$this->assertEquals( $raw_adventure_option1['price']['EUR']['pricePerPerson'], $actual1['price_per_person_eur'] );
	}

	/**
	 * Test format adventure option data.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\format_adventure_option_data
	 *
	 * @return void
	 */
	public function test_format_adventure_option_data(): void {
		// No arguments.
		$expected = [];
		$actual   = format_adventure_option_data();
		$this->assertSame( $expected, $actual );

		// Test with default values.
		$expected = [];
		$actual   = format_adventure_option_data( [], 0 );
		$this->assertSame( $expected, $actual );

		// Test for required values.
		$raw_adventure_option = [
			'id'              => '',
			'price'           => [],
			'spacesAvailable' => 1,
		];
		$expected             = [];
		$actual               = format_adventure_option_data( $raw_adventure_option, 0 );
		$this->assertSame( $expected, $actual );

		// Test with valid data with non-existing departure post.
		$departure_post_id    = 9271;
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				[],
			],
			'spacesAvailable' => 1,
		];
		$expected             = [];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// Test with negative spaces available.
		$departure_post_id    = 9271;
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				[],
			],
			'spacesAvailable' => -1,
		];
		$expected             = [];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a departure.
		$softrip_package_code = 'PQO-123';
		$departure_post_id    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code,
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Test without service id.
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 0,
				],
				'AUD' => [],
			],
			'spacesAvailable' => 1,
		];
		$expected             = [];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';

		// Without any service id assigned to term.
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 0,
				],
				'AUD' => [],
			],
			'spacesAvailable' => 1,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];
		$expected             = [];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// With service id attached to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// First service id should be attached to term meta.
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 0,
				],
				'AUD' => [],
			],
			'spacesAvailable' => 1,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];
		$expected             = [
			'softrip_option_id'        => '123',
			'departure_post_id'        => $departure_post_id,
			'softrip_package_code'     => $softrip_package_code,
			'service_ids'              => implode( ',', [ $service_id1, $service_id2 ] ),
			'spaces_available'         => 1,
			'adventure_option_term_id' => $adventure_option_term_id1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 0,
			'price_per_person_aud'     => 0,
			'price_per_person_gbp'     => 0,
			'price_per_person_eur'     => 0,
		];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// Remove first service id from term meta.
		delete_term_meta( $adventure_option_term_id1, 'softrip_0_id' );

		// Second service id should be attached to term meta.
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 0,
				],
				'AUD' => [],
			],
			'spacesAvailable' => 1,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];
		$expected             = [
			'softrip_option_id'        => '123',
			'departure_post_id'        => $departure_post_id,
			'softrip_package_code'     => $softrip_package_code,
			'service_ids'              => implode( ',', [ $service_id1, $service_id2 ] ),
			'spaces_available'         => 1,
			'adventure_option_term_id' => $adventure_option_term_id2,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 0,
			'price_per_person_aud'     => 0,
			'price_per_person_gbp'     => 0,
			'price_per_person_eur'     => 0,
		];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );

		// Test with complete valid data.
		$raw_adventure_option = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];
		$expected             = [
			'softrip_option_id'        => '123',
			'departure_post_id'        => $departure_post_id,
			'softrip_package_code'     => $softrip_package_code,
			'service_ids'              => implode( ',', [ $service_id1, $service_id2 ] ),
			'spaces_available'         => 11,
			'adventure_option_term_id' => $adventure_option_term_id2,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$actual               = format_adventure_option_data( $raw_adventure_option, $departure_post_id );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get adventure option taxonomy term by service id.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_adventure_option_term_by_service_id
	 *
	 * @return void
	 */
	public function test_get_adventure_option_taxonomy_term_by_service_id(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_adventure_option_taxonomy_term_by_service_id();
		$this->assertSame( $expected, $actual );

		// Test with invalid service id.
		$service_id = 'invalid_service_id';
		$expected   = [];
		$actual     = get_adventure_option_taxonomy_term_by_service_id( $service_id );
		$this->assertSame( $expected, $actual );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';

		// Without any service id attached to term meta.
		$expected = [];
		$actual   = get_adventure_option_taxonomy_term_by_service_id( $service_id1 );
		$this->assertSame( $expected, $actual );

		// Attach service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// Test with valid service id.
		$expected = [ $adventure_option_term_id1 ];
		$actual   = get_adventure_option_taxonomy_term_by_service_id( $service_id1 );
		$this->assertSame( $expected, $actual );

		// Test with another valid service id.
		$expected = [ $adventure_option_term_id2 ];
		$actual   = get_adventure_option_taxonomy_term_by_service_id( $service_id2 );
		$this->assertSame( $expected, $actual );

		// Add one more service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_1_id', $service_id2 );

		// Test with valid service id.
		$expected = [ $adventure_option_term_id1 ];
		$actual   = get_adventure_option_taxonomy_term_by_service_id( $service_id1 );
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test get adventure option by departure post id.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_adventure_option_by_departure_post_id
	 *
	 * @return void
	 */
	public function test_get_adventure_option_by_departure_post_id(): void {
		// Setup variables.
		$table_name = get_table_name();
		global $wpdb;

		// Test with no arguments.
		$expected = [];
		$actual   = get_adventure_option_by_departure_post_id();
		$this->assertSame( $expected, $actual );

		// Test with invalid departure post id.
		$departure_post_id = 0;
		$expected          = [];
		$actual            = get_adventure_option_by_departure_post_id( $departure_post_id );
		$this->assertSame( $expected, $actual );

		// Create a departure.
		$softrip_package_code1 = 'UNQ-123';
		$departure_post_id1    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code1,
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Create another departure.
		$softrip_package_code2 = 'UNQ-456';
		$departure_post_id2    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Test with valid departure post id.
		$expected = [];
		$actual   = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertSame( $expected, $actual );

		// Clear the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . "_departure_post_id_$departure_post_id1", CACHE_GROUP );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';

		// Attach service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// Raw adventure option for departure 1.
		$raw_adventure_option1 = [
			'id'              => '123',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data1 = format_adventure_option_data( $raw_adventure_option1, $departure_post_id1 );
		$this->assertNotEmpty( $adventure_option_data1 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data1 );

		// Get inserted id.
		$adventure_option_id1 = $wpdb->insert_id;
		$this->assertIsInt( $adventure_option_id1 );

		// Cache should be empty.
		$cache_key1        = CACHE_KEY_PREFIX . "_departure_post_id_$departure_post_id1";
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Get adventure option by departure post id.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been prepared.
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first adventure option data.
		$actual1 = $actual[0];
		$this->assertIsArray( $actual1 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertEquals( $adventure_option_id1, $actual1['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual1 );
		$this->assertEquals( $raw_adventure_option1['id'], $actual1['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertEquals( $departure_post_id1, $actual1['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual1 );
		$this->assertEquals( $softrip_package_code1, $actual1['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual1 );
		$this->assertEquals( implode( ',', $raw_adventure_option1['serviceIds'] ), $actual1['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertEquals( $raw_adventure_option1['spacesAvailable'], $actual1['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual1 );
		$this->assertEquals( $adventure_option_term_id1, $actual1['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['USD']['pricePerPerson'], $actual1['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['CAD']['pricePerPerson'], $actual1['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['AUD']['pricePerPerson'], $actual1['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['GBP']['pricePerPerson'], $actual1['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['EUR']['pricePerPerson'], $actual1['price_per_person_eur'] );

		// Raw adventure option for departure 2.
		$raw_adventure_option2 = [
			'id'              => '456',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 200,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 22,
			'serviceIds'      => [ $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data2 = format_adventure_option_data( $raw_adventure_option2, $departure_post_id2 );
		$this->assertNotEmpty( $adventure_option_data2 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data2 );

		// Get inserted id.
		$adventure_option_id2 = $wpdb->insert_id;

		// Get adventure option by departure post id.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id2 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Get first adventure option data.
		$actual2 = $actual[0];
		$this->assertIsArray( $actual2 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual2 );
		$this->assertEquals( $adventure_option_id2, $actual2['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual2 );
		$this->assertEquals( $raw_adventure_option2['id'], $actual2['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual2 );
		$this->assertEquals( $departure_post_id2, $actual2['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual2 );
		$this->assertEquals( $softrip_package_code2, $actual2['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual2 );
		$this->assertEquals( implode( ',', $raw_adventure_option2['serviceIds'] ), $actual2['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual2 );
		$this->assertEquals( $raw_adventure_option2['spacesAvailable'], $actual2['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual2 );
		$this->assertEquals( $adventure_option_term_id2, $actual2['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['USD']['pricePerPerson'], $actual2['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['CAD']['pricePerPerson'], $actual2['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['AUD']['pricePerPerson'], $actual2['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['GBP']['pricePerPerson'], $actual2['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['EUR']['pricePerPerson'], $actual2['price_per_person_eur'] );

		// Insert one more adventure option for departure post 1.
		$raw_adventure_option3 = [
			'id'              => '789',
			'price'           => [
				'USD' => [
					'pricePerPerson' => 300,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 33,
			'serviceIds'      => [ $service_id1 ],
		];

		// Format adventure option data.
		$adventure_option_data3 = format_adventure_option_data( $raw_adventure_option3, $departure_post_id1 );
		$this->assertNotEmpty( $adventure_option_data3 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data3 );

		// Bust cache.
		wp_cache_delete( CACHE_KEY_PREFIX . "_departure_post_id_$departure_post_id1", CACHE_GROUP );

		// Get inserted id.
		$adventure_option_id3 = $wpdb->insert_id;

		// Get adventure option by departure post id.
		$actual = get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 2, $actual );

		// Get first adventure option data.
		$actual3 = $actual[0];
		$this->assertIsArray( $actual3 );
		$this->assertEquals( $actual1, $actual3 );

		// Get second adventure option data.
		$actual4 = $actual[1];
		$this->assertIsArray( $actual4 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual3 );
		$this->assertEquals( $adventure_option_id3, $actual4['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual4 );
		$this->assertEquals( $raw_adventure_option3['id'], $actual4['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual4 );
		$this->assertEquals( $departure_post_id1, $actual4['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual4 );
		$this->assertEquals( $softrip_package_code1, $actual4['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual4 );
		$this->assertEquals( implode( ',', $raw_adventure_option3['serviceIds'] ), $actual4['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual4 );
		$this->assertEquals( $raw_adventure_option3['spacesAvailable'], $actual4['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual4 );
		$this->assertEquals( $adventure_option_term_id1, $actual4['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual4 );
		$this->assertEquals( $raw_adventure_option3['price']['USD']['pricePerPerson'], $actual4['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual4 );
		$this->assertEquals( $raw_adventure_option3['price']['CAD']['pricePerPerson'], $actual4['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual4 );
		$this->assertEquals( $raw_adventure_option3['price']['AUD']['pricePerPerson'], $actual4['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual4 );
		$this->assertEquals( $raw_adventure_option3['price']['GBP']['pricePerPerson'], $actual4['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual4 );
		$this->assertEquals( $raw_adventure_option3['price']['EUR']['pricePerPerson'], $actual4['price_per_person_eur'] );
	}

	/**
	 * Test get adventure option by softrip option id.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_adventure_option_by_softrip_option_id
	 *
	 * @return void
	 */
	public function test_get_adventure_option_by_softrip_option_id(): void {
		// Setup variables.
		$table_name = get_table_name();
		global $wpdb;
		$softrip_option_id1 = 'OPT-123';
		$softrip_option_id2 = 'OPT-456';

		// Test with no arguments.
		$expected = [];
		$actual   = get_adventure_option_by_softrip_option_id();
		$this->assertSame( $expected, $actual );

		// Test with default softrip option id.
		$softrip_option_id = '';
		$expected          = [];
		$actual            = get_adventure_option_by_softrip_option_id( $softrip_option_id );
		$this->assertSame( $expected, $actual );

		// Create a departure.
		$softrip_package_code1 = 'UNQ-123';
		$departure_post_id1    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code1,
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Create another departure.
		$softrip_package_code2 = 'UNQ-456';
		$departure_post_id2    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Test with valid softrip option id.
		$expected = [];
		$actual   = get_adventure_option_by_softrip_option_id( '123' );
		$this->assertSame( $expected, $actual );

		// Clear the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_softrip_option_id_123', CACHE_GROUP );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';

		// Attach service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// Raw adventure option for departure 1.
		$raw_adventure_option1 = [
			'id'              => $softrip_option_id1,
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data1 = format_adventure_option_data( $raw_adventure_option1, $departure_post_id1 );
		$this->assertNotEmpty( $adventure_option_data1 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data1 );

		// Get inserted id.
		$adventure_option_id1 = $wpdb->insert_id;

		// Cache should be empty.
		$cache_key1        = CACHE_KEY_PREFIX . "_softrip_option_id_$softrip_option_id1";
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );

		// Get adventure option by departure post id.
		$actual = get_adventure_option_by_softrip_option_id( $softrip_option_id1 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been prepared.
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first adventure option data.
		$actual1 = $actual[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertEquals( $adventure_option_id1, $actual1['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual1 );
		$this->assertEquals( $raw_adventure_option1['id'], $actual1['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertEquals( $departure_post_id1, $actual1['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual1 );
		$this->assertEquals( $softrip_package_code1, $actual1['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual1 );
		$this->assertEquals( implode( ',', $raw_adventure_option1['serviceIds'] ), $actual1['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertEquals( $raw_adventure_option1['spacesAvailable'], $actual1['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual1 );
		$this->assertEquals( $adventure_option_term_id1, $actual1['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['USD']['pricePerPerson'], $actual1['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['CAD']['pricePerPerson'], $actual1['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['AUD']['pricePerPerson'], $actual1['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['GBP']['pricePerPerson'], $actual1['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['EUR']['pricePerPerson'], $actual1['price_per_person_eur'] );

		// Raw adventure option for departure 2.
		$raw_adventure_option2 = [
			'id'              => $softrip_option_id2,
			'price'           => [
				'USD' => [
					'pricePerPerson' => 200,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 22,
			'serviceIds'      => [ $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data2 = format_adventure_option_data( $raw_adventure_option2, $departure_post_id2 );
		$this->assertNotEmpty( $adventure_option_data2 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data2 );

		// Get inserted id.
		$adventure_option_id2 = $wpdb->insert_id;

		// Cache should be empty.
		$cache_key2        = CACHE_KEY_PREFIX . "_softrip_option_id_$softrip_option_id2";
		$actual_from_cache = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Get adventure option by departure post id.
		$actual = get_adventure_option_by_softrip_option_id( $softrip_option_id2 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been prepared.
		$actual_from_cache = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first adventure option data.
		$actual2 = $actual[0];
		$this->assertIsArray( $actual2 );
		$this->assertNotEmpty( $actual2 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual2 );
		$this->assertEquals( $adventure_option_id2, $actual2['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual2 );
		$this->assertEquals( $raw_adventure_option2['id'], $actual2['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual2 );
		$this->assertEquals( $departure_post_id2, $actual2['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual2 );
		$this->assertEquals( $softrip_package_code2, $actual2['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual2 );
		$this->assertEquals( implode( ',', $raw_adventure_option2['serviceIds'] ), $actual2['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual2 );
		$this->assertEquals( $raw_adventure_option2['spacesAvailable'], $actual2['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual2 );
		$this->assertEquals( $adventure_option_term_id2, $actual2['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['USD']['pricePerPerson'], $actual2['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['CAD']['pricePerPerson'], $actual2['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['AUD']['pricePerPerson'], $actual2['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['GBP']['pricePerPerson'], $actual2['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['EUR']['pricePerPerson'], $actual2['price_per_person_eur'] );
	}

	/**
	 * Test format row data from db.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\format_row_data_from_db
	 *
	 * @return void
	 */
	public function test_format_row_data_from_db(): void {
		// Empty arguments.
		$expected = [];
		$actual   = format_row_data_from_db();
		$this->assertEquals( $expected, $actual );

		// Test with non-existing id.
		$row_data = [
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with 0 id.
		$row_data = [
			'id'                       => 0,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non existing softrip option id.
		$row_data = [
			'id'                       => 1,
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with empty string softrip option id.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing departure post id.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with 0 departure post id.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 0,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing adventure option term id.
		$row_data = [
			'id'                   => 1,
			'softrip_option_id'    => '123',
			'departure_post_id'    => 1,
			'softrip_package_code' => 'UNQ-123',
			'service_ids'          => 'service_id_1,service_id_2',
			'spaces_available'     => 11,
			'price_per_person_usd' => 100,
			'price_per_person_cad' => 344,
			'price_per_person_aud' => 293,
			'price_per_person_gbp' => 234,
			'price_per_person_eur' => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with 0 adventure option term id.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 0,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing service ids.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => '',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing spaces available.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 0,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing price per person usd.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 0,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing price per person cad.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 0,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing price per person aud.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 0,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 123,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing price per person gbp.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_eur'     => 123,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 0,
			'price_per_person_eur'     => 123,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non-existing price per person eur.
		$row_data = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
		];
		$expected = [
			'id'                       => 1,
			'softrip_option_id'        => '123',
			'departure_post_id'        => 1,
			'softrip_package_code'     => 'UNQ-123',
			'service_ids'              => 'service_id_1,service_id_2',
			'spaces_available'         => 11,
			'adventure_option_term_id' => 1,
			'price_per_person_usd'     => 100,
			'price_per_person_cad'     => 344,
			'price_per_person_aud'     => 293,
			'price_per_person_gbp'     => 234,
			'price_per_person_eur'     => 0,
		];
		$actual   = format_row_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test format rows data from db.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\format_rows_data_from_db
	 *
	 * @return void
	 */
	public function test_format_rows_data_from_db(): void {
		// Empty arguments.
		$expected = [];
		$actual   = format_rows_data_from_db();
		$this->assertEquals( $expected, $actual );

		// Test with non-existing id.
		$row_data = [
			[
				'softrip_option_id'        => '123',
				'departure_post_id'        => '1',
				'softrip_package_code'     => 'UNQ-123',
				'service_ids'              => 'service_id_1,service_id_2',
				'spaces_available'         => '11',
				'adventure_option_term_id' => '1',
				'price_per_person_usd'     => '100',
				'price_per_person_cad'     => '344',
				'price_per_person_aud'     => '293',
				'price_per_person_gbp'     => '234',
				'price_per_person_eur'     => '123',
			],
		];
		$expected = [];
		$actual   = format_rows_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with 0 id.
		$row_data = [
			[
				'id'                       => '0',
				'softrip_option_id'        => '123',
				'departure_post_id'        => '1',
				'softrip_package_code'     => 'UNQ-123',
				'service_ids'              => 'service_id_1,service_id_2',
				'spaces_available'         => '11',
				'adventure_option_term_id' => '1',
				'price_per_person_usd'     => '100',
				'price_per_person_cad'     => '344',
				'price_per_person_aud'     => '293',
				'price_per_person_gbp'     => '234',
				'price_per_person_eur'     => '123',
			],
		];
		$expected = [];
		$actual   = format_rows_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with non existing softrip option id.
		$row_data = [
			[
				'id'                       => '1',
				'departure_post_id'        => '1',
				'softrip_package_code'     => 'UNQ-123',
				'service_ids'              => 'service_id_1,service_id_2',
				'spaces_available'         => '11',
				'adventure_option_term_id' => '1',
				'price_per_person_usd'     => '100',
				'price_per_person_cad'     => '344',
				'price_per_person_aud'     => '293',
				'price_per_person_gbp'     => '234',
				'price_per_person_eur'     => '123',
			],
		];
		$expected = [];
		$actual   = format_rows_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );

		// Test with valid rows.
		$row_data = [
			[
				'id'                       => '1',
				'softrip_option_id'        => '123',
				'departure_post_id'        => '1',
				'softrip_package_code'     => 'UNQ-123',
				'service_ids'              => 'service_id_1,service_id_2',
				'spaces_available'         => '11',
				'adventure_option_term_id' => '1',
				'price_per_person_usd'     => '100',
				'price_per_person_cad'     => '344',
				'price_per_person_aud'     => '293',
				'price_per_person_gbp'     => '234',
				'price_per_person_eur'     => '123',
			],
			[
				'id'                       => '2',
				'softrip_option_id'        => '456',
				'departure_post_id'        => '2',
				'softrip_package_code'     => 'UNQ-456',
				'service_ids'              => 'service_id_2',
				'spaces_available'         => '22',
				'adventure_option_term_id' => '2',
				'price_per_person_usd'     => '200',
				'price_per_person_cad'     => '344',
				'price_per_person_aud'     => '293',
				'price_per_person_gbp'     => '234',
				'price_per_person_eur'     => '123',
			],
		];
		$expected = [
			[
				'id'                       => 1,
				'softrip_option_id'        => '123',
				'departure_post_id'        => 1,
				'softrip_package_code'     => 'UNQ-123',
				'service_ids'              => 'service_id_1,service_id_2',
				'spaces_available'         => 11,
				'adventure_option_term_id' => 1,
				'price_per_person_usd'     => 100,
				'price_per_person_cad'     => 344,
				'price_per_person_aud'     => 293,
				'price_per_person_gbp'     => 234,
				'price_per_person_eur'     => 123,
			],
			[
				'id'                       => 2,
				'softrip_option_id'        => '456',
				'departure_post_id'        => 2,
				'softrip_package_code'     => 'UNQ-456',
				'service_ids'              => 'service_id_2',
				'spaces_available'         => 22,
				'adventure_option_term_id' => 2,
				'price_per_person_usd'     => 200,
				'price_per_person_cad'     => 344,
				'price_per_person_aud'     => 293,
				'price_per_person_gbp'     => 234,
				'price_per_person_eur'     => 123,
			],
		];
		$actual   = format_rows_data_from_db( $row_data );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get adventure option by id.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\get_adventure_options_by_id
	 *
	 * @return void
	 */
	public function test_get_adventure_options_by_id(): void {
		// Setup variables.
		$table_name = get_table_name();
		global $wpdb;
		$softrip_option_id1 = 'OPT-123';
		$softrip_option_id2 = 'OPT-456';

		// Test with no arguments.
		$expected = [];
		$actual   = get_adventure_options_by_id();
		$this->assertSame( $expected, $actual );

		// Test with default softrip option id.
		$softrip_option_id = 0;
		$expected          = [];
		$actual            = get_adventure_options_by_id( $softrip_option_id );
		$this->assertSame( $expected, $actual );

		// Create a departure.
		$softrip_package_code1 = 'UNQ-123';
		$departure_post_id1    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code1,
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Create another departure.
		$softrip_package_code2 = 'UNQ-456';
		$departure_post_id2    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Test with valid softrip option id.
		$expected = [];
		$actual   = get_adventure_options_by_id( 123 );
		$this->assertSame( $expected, $actual );

		// Clear the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_id_123', CACHE_GROUP );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';

		// Attach service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// Raw adventure option for departure 1.
		$raw_adventure_option1 = [
			'id'              => $softrip_option_id1,
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data1 = format_adventure_option_data( $raw_adventure_option1, $departure_post_id1 );
		$this->assertNotEmpty( $adventure_option_data1 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data1 );

		// Get inserted id.
		$adventure_option_id1 = $wpdb->insert_id;

		// Cache should be empty.
		$cache_key1        = CACHE_KEY_PREFIX . "_id_$adventure_option_id1";
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );

		// Get adventure option by departure post id.
		$actual = get_adventure_options_by_id( $adventure_option_id1 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been prepared.
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first adventure option data.
		$actual1 = $actual[0];
		$this->assertIsArray( $actual1 );
		$this->assertNotEmpty( $actual1 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual1 );
		$this->assertEquals( $adventure_option_id1, $actual1['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual1 );
		$this->assertEquals( $raw_adventure_option1['id'], $actual1['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual1 );
		$this->assertEquals( $departure_post_id1, $actual1['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual1 );
		$this->assertEquals( $softrip_package_code1, $actual1['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual1 );
		$this->assertEquals( implode( ',', $raw_adventure_option1['serviceIds'] ), $actual1['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual1 );
		$this->assertEquals( $raw_adventure_option1['spacesAvailable'], $actual1['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual1 );
		$this->assertEquals( $adventure_option_term_id1, $actual1['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['USD']['pricePerPerson'], $actual1['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['CAD']['pricePerPerson'], $actual1['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['AUD']['pricePerPerson'], $actual1['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['GBP']['pricePerPerson'], $actual1['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual1 );
		$this->assertEquals( $raw_adventure_option1['price']['EUR']['pricePerPerson'], $actual1['price_per_person_eur'] );

		// Raw adventure option for departure 2.
		$raw_adventure_option2 = [
			'id'              => $softrip_option_id2,
			'price'           => [
				'USD' => [
					'pricePerPerson' => 200,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 22,
			'serviceIds'      => [ $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data2 = format_adventure_option_data( $raw_adventure_option2, $departure_post_id2 );
		$this->assertNotEmpty( $adventure_option_data2 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data2 );

		// Get inserted id.
		$adventure_option_id2 = $wpdb->insert_id;

		// Cache should be empty.
		$cache_key2        = CACHE_KEY_PREFIX . "_id_$adventure_option_id2";
		$actual_from_cache = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Get adventure option by departure post id.
		$actual = get_adventure_options_by_id( $adventure_option_id2 );
		$this->assertNotEmpty( $actual );
		$this->assertIsArray( $actual );
		$this->assertCount( 1, $actual );

		// Cache should have been prepared.
		$actual_from_cache = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );
		$this->assertIsArray( $actual_from_cache );
		$this->assertEquals( $actual, $actual_from_cache );

		// Get first adventure option data.
		$actual2 = $actual[0];
		$this->assertIsArray( $actual2 );
		$this->assertNotEmpty( $actual2 );

		// Verify each field.
		$this->assertArrayHasKey( 'id', $actual2 );
		$this->assertEquals( $adventure_option_id2, $actual2['id'] );
		$this->assertArrayHasKey( 'softrip_option_id', $actual2 );
		$this->assertEquals( $raw_adventure_option2['id'], $actual2['softrip_option_id'] );
		$this->assertArrayHasKey( 'departure_post_id', $actual2 );
		$this->assertEquals( $departure_post_id2, $actual2['departure_post_id'] );
		$this->assertArrayHasKey( 'softrip_package_code', $actual2 );
		$this->assertEquals( $softrip_package_code2, $actual2['softrip_package_code'] );
		$this->assertArrayHasKey( 'service_ids', $actual2 );
		$this->assertEquals( implode( ',', $raw_adventure_option2['serviceIds'] ), $actual2['service_ids'] );
		$this->assertArrayHasKey( 'spaces_available', $actual2 );
		$this->assertEquals( $raw_adventure_option2['spacesAvailable'], $actual2['spaces_available'] );
		$this->assertArrayHasKey( 'adventure_option_term_id', $actual2 );
		$this->assertEquals( $adventure_option_term_id2, $actual2['adventure_option_term_id'] );
		$this->assertArrayHasKey( 'price_per_person_usd', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['USD']['pricePerPerson'], $actual2['price_per_person_usd'] );
		$this->assertArrayHasKey( 'price_per_person_cad', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['CAD']['pricePerPerson'], $actual2['price_per_person_cad'] );
		$this->assertArrayHasKey( 'price_per_person_aud', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['AUD']['pricePerPerson'], $actual2['price_per_person_aud'] );
		$this->assertArrayHasKey( 'price_per_person_gbp', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['GBP']['pricePerPerson'], $actual2['price_per_person_gbp'] );
		$this->assertArrayHasKey( 'price_per_person_eur', $actual2 );
		$this->assertEquals( $raw_adventure_option2['price']['EUR']['pricePerPerson'], $actual2['price_per_person_eur'] );
	}

	/**
	 * Test delete adventure option by id.
	 *
	 * @covers \Quark\Softrip\AdventureOptions\delete_adventure_option_by_id
	 *
	 * @return void
	 */
	public function test_delete_adventure_option_by_id(): void {
		// Setup variables.
		$table_name = get_table_name();
		global $wpdb;
		$softrip_option_id1 = 'OPT-123';
		$softrip_option_id2 = 'OPT-456';

		// Test with no arguments.
		$expected = false;
		$actual   = delete_adventure_option_by_id();
		$this->assertSame( $expected, $actual );

		// Test with default softrip option id.
		$softrip_option_id = 0;
		$expected          = false;
		$actual            = delete_adventure_option_by_id( $softrip_option_id );
		$this->assertSame( $expected, $actual );

		// Create a departure.
		$softrip_package_code1 = 'UNQ-123';
		$departure_post_id1    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code1,
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Create another departure.
		$softrip_package_code2 = 'UNQ-456';
		$departure_post_id2    = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $softrip_package_code2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Test with valid softrip option id.
		$expected = false;
		$actual   = delete_adventure_option_by_id( 123 );
		$this->assertSame( $expected, $actual );

		// Clear the cache.
		wp_cache_delete( CACHE_KEY_PREFIX . '_id_123', CACHE_GROUP );

		// Create some adventure option taxonomy term.
		$adventure_option_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$adventure_option_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id1 );
		$this->assertIsInt( $adventure_option_term_id2 );

		// Initialize service 1, 2.
		$service_id1 = 'service_id_1';
		$service_id2 = 'service_id_2';

		// Attach service id to term meta.
		update_term_meta( $adventure_option_term_id1, 'softrip_0_id', $service_id1 );
		update_term_meta( $adventure_option_term_id2, 'softrip_0_id', $service_id2 );

		// Raw adventure option for departure 1.
		$raw_adventure_option1 = [
			'id'              => $softrip_option_id1,
			'price'           => [
				'USD' => [
					'pricePerPerson' => 100,
				],
				'CAD' => [
					'pricePerPerson' => 344,
				],
				'AUD' => [
					'pricePerPerson' => 293,
				],
				'GBP' => [
					'pricePerPerson' => 234,
				],
				'EUR' => [
					'pricePerPerson' => 123,
				],
			],
			'spacesAvailable' => 11,
			'serviceIds'      => [ $service_id1, $service_id2 ],
		];

		// Format adventure option data.
		$adventure_option_data1 = format_adventure_option_data( $raw_adventure_option1, $departure_post_id1 );
		$this->assertNotEmpty( $adventure_option_data1 );

		// Insert adventure option data.
		$wpdb->insert( $table_name, $adventure_option_data1 );

		// Flush cache.
		// wp_cache_flush();
		// Get inserted id.
		$adventure_option_id1 = $wpdb->insert_id;

		// Cache should have been prepared.
		$cache_key1 = CACHE_KEY_PREFIX . "_id_$adventure_option_id1";
		get_adventure_options_by_id( $adventure_option_id1 );
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );

		// Cache key 2.
		$cache_key2 = CACHE_KEY_PREFIX . "_softrip_option_id_$softrip_option_id1";
		get_adventure_option_by_softrip_option_id( $softrip_option_id1 );
		$actual_from_cache = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );

		// Cache key 3.
		$cache_key3 = CACHE_KEY_PREFIX . "_departure_post_id_$departure_post_id1";
		get_adventure_option_by_departure_post_id( $departure_post_id1 );
		$actual_from_cache = wp_cache_get( $cache_key3, CACHE_GROUP );
		$this->assertNotEmpty( $actual_from_cache );

		// Cache key 4.
		$cache_key4 = CACHE_KEY_PREFIX . '_departure_adventure_option_term_id_' . $adventure_option_term_id1;
		get_departures_by_adventure_option_term_id( $adventure_option_term_id1 );
		$actual_from_cache = wp_cache_get( $cache_key4, CACHE_GROUP );
		$this->assertNotFalse( $actual_from_cache );
		$this->assertEmpty( $actual_from_cache );

		// Delete adventure option by id.
		$expected = true;
		$actual   = delete_adventure_option_by_id( $adventure_option_id1 );
		$this->assertSame( $expected, $actual );

		// Cache should have been cleared.
		$actual_from_cache = wp_cache_get( $cache_key1, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Cache key 2.
		$actual_from_cache = wp_cache_get( $cache_key2, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Cache key 3.
		$actual_from_cache = wp_cache_get( $cache_key3, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );

		// Cache key 4.
		$actual_from_cache = wp_cache_get( $cache_key4, CACHE_GROUP );
		$this->assertFalse( $actual_from_cache );
	}
}
