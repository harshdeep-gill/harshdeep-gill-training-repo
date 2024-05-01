<?php
/**
 * Brochurese test suite.
 *
 * @package quark-brochures
 */

namespace Quark\Brochures\Tests;

use WP_UnitTestCase;

use const Quark\Brochures\POST_TYPE;

/**
 * Class Test_brochures.
 */
class Test_Brochures extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Brochures\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_filter( 'init', 'Quark\Brochures\register_brochures_post_type' ) );
	}

	/**
	 * Make sure post type is registered.
	 *
	 * @covers \Quark\Brochures\register_brochures_post_type()
	 *
	 * @return void
	 */
	public function test_register_brochures_post_type(): void {
		// Test if post type is actually registered.
		$this->assertTrue( post_type_exists( POST_TYPE ) );
	}
}
