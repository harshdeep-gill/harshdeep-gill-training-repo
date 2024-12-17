<?php
/**
 * Test cases for the Multilingual.
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual\Tests;

use WP_UnitTestCase;

use function Quark\Multilingual\get_translation_adapter;

/**
 * Class Test_Multilingual
 */
class Test_Multilingual extends WP_UnitTestCase {
	/**
	 * Test case for bootstrap.
	 *
	 * @covers \Quark\Multilingual\bootstrap
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Assert filters.
		$this->assertEquals( 10, has_filter( 'travelopia_translation_adapter', 'Quark\\Multilingual\\get_translation_adapter' ) );
	}

	/**
	 * Test case to get translation adapter.
	 *
	 * @covers \Quark\Multilingual\get_translation_adapter
	 *
	 * @return void
	 */
	public function test_get_translation_adapter(): void {
		// Assert data.
		$this->assertEquals( 'deepl', get_translation_adapter() );
	}
}
