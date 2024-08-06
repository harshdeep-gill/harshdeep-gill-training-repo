<?php
/**
 * Test suite for Softrip DB.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Softrip_DB;
use WP_UnitTestCase;

/**
 * Class Test_Softrip_DB
 */
class Test_Softrip_DB extends WP_UnitTestCase {

	/**
	 * Class instance to use for the test.
	 *
	 * @var Softrip_DB
	 */
	protected $instance;

	/**
	 * Set up the class which will be tested.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// Call the parent set up.
		parent::set_up();

		// Create an instance of the class.
		$this->instance = new Softrip_DB();
	}

	/**
	 * Test case for get_db_tables_sql.
	 *
	 * @covers \Quark\Softrip\Softrip_DB::get_db_tables_sql()
	 *
	 * @return void
	 */
	public function test_get_db_tables_sql(): void {
		// Get the tables SQL.
		$tables = $this->instance->get_db_tables_sql();

		// Test case 1: Check if the returned value is an array.
		$this->assertIsArray( $tables );

		// Test case 2: Check if the returned array has the expected keys.
		$this->assertArrayHasKey( 'adventure_options', $tables );
		$this->assertArrayHasKey( 'cabin_categories', $tables );
		$this->assertArrayHasKey( 'occupancies', $tables );
		$this->assertArrayHasKey( 'occupancy_prices', $tables );
		$this->assertArrayHasKey( 'promos', $tables );

		// Test case 3: Check if the returned array has the expected datatype of value.
		$this->assertIsString( $tables['adventure_options'] );
		$this->assertIsString( $tables['cabin_categories'] );
		$this->assertIsString( $tables['occupancies'] );
		$this->assertIsString( $tables['occupancy_prices'] );
		$this->assertIsString( $tables['promos'] );

		// Get charset collate.
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// Set the engine and collate.
		$engine_collate = 'ENGINE=InnoDB';

		// If the charset_collate is not empty, add it to the engine_collate.
		if ( ! empty( $charset_collate ) ) {
			$engine_collate .= " $charset_collate";
		}

		// Test case 4: Check if the returned array has the expected SQL for creating adventure_options table.
		$table_name   = $this->instance->prefix_table_name( 'adventure_options' );
		$expected_sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			title VARCHAR(255) NOT NULL,
			Departure BIGINT NOT NULL,
			departure_id VARCHAR(20) NOT NULL,
			package_id VARCHAR(20) NOT NULL,
			option_id VARCHAR(10) NOT NULL,
			spaces_available BIGINT NOT NULL,
			adventure_option_term BIGINT NOT NULL,
			price_per_person_usd BIGINT NOT NULL,
			price_per_person_cad BIGINT NOT NULL,
			price_per_person_aud BIGINT NOT NULL,
			price_per_person_gbp BIGINT NOT NULL,
			price_per_person_eur BIGINT NOT NULL
		) $engine_collate";
		$this->assertSame( $expected_sql, $tables['adventure_options'] );

		// Test case 5: Check if the returned array has the expected SQL for creating cabin_categories table.
		$table_name   = $this->instance->prefix_table_name( 'cabin_categories' );
		$expected_sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			title VARCHAR(255) NOT NULL,
			departure BIGINT NOT NULL,
			cabin_category BIGINT NOT NULL,
			package_id VARCHAR(45) NOT NULL,
			departure_id VARCHAR(45) NOT NULL,
			ship_id VARCHAR(10) NOT NULL,
			cabin_category_id VARCHAR(45) NOT NULL,
			availability_status VARCHAR(4) NOT NULL,
			spaces_available INT NOT NULL,
			UNIQUE KEY cabin_category_title_unique (title)
		) $engine_collate";
		$this->assertSame( $expected_sql, $tables['cabin_categories'] );

		// Test case 6: Check if the returned array has the expected SQL for creating occupancies table.
		$table_name   = $this->instance->prefix_table_name( 'occupancies' );
		$expected_sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			cabin_category BIGINT NOT NULL,
			title VARCHAR(255) NOT NULL,
			occupancy_mask VARCHAR(12) NOT NULL
		) $engine_collate";
		$this->assertSame( $expected_sql, $tables['occupancies'] );

		// Test case 7: Check if the returned array has the expected SQL for creating occupancy_prices table.
		$table_name   = $this->instance->prefix_table_name( 'occupancy_prices' );
		$expected_sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			occupancy_id BIGINT NOT NULL,
			currency_code VARCHAR(3) NOT NULL,
			price_per_person DECIMAL(8, 2) NOT NULL,
			total_price_per_person DECIMAL(8, 2) NOT NULL,
			promotion_code VARCHAR(255) NOT NULL,
			promo_price_per_person DECIMAL(8, 2) NOT NULL
		) $engine_collate";
		$this->assertSame( $expected_sql, $tables['occupancy_prices'] );

		// Test case 8: Check if the returned array has the expected SQL for creating promos table.
		$table_name   = $this->instance->prefix_table_name( 'promos' );
		$expected_sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			promotion_code VARCHAR(255) NOT NULL,
			start_date DATETIME NOT NULL,
			end_date DATETIME NOT NULL,
			description VARCHAR(255) NOT NULL,
			discount_type VARCHAR(255) NOT NULL,
			discount_value VARCHAR(255) NOT NULL,
			pif TINYINT(1) NOT NULL
		) $engine_collate";
		$this->assertSame( $expected_sql, $tables['promos'] );
	}

	/**
	 * Test case for getting table prefix name.
	 *
	 * @covers \Quark\Softrip\Softrip_DB::prefix_table_name()
	 *
	 * @return void
	 */
	public function test_prefix_table_name(): void {
		// Get table prefix name.
		$table_name = $this->instance->prefix_table_name( 'test_table' );

		// Assert.
		$this->assertIsString( $table_name );
		$this->assertEquals( 'qrk_test_table', $table_name );
	}
}
