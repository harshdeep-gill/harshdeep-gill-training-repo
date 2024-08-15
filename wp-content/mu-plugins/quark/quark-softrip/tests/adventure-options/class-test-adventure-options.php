<?php
/**
 * Test suite for Adventure Options.
 *
 * @package quark-softrip
 */

namespace Quark\Tests\AdventureOptions;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\AdventureOptions\get_table_name;
use function Quark\Softrip\AdventureOptions\get_table_sql;
use function Quark\Softrip\get_engine_collate;

use const Quark\Softrip\TABLE_PREFIX_NAME;

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
}