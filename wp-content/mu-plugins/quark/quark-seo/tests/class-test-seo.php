<?php
/**
 * SEO test suite.
 *
 * @package quark-seo
 */

namespace Quark\SEO\Tests;

use WP_UnitTestCase;

use function Quark\SEO\custom_robots_txt;

/**
 * Class Test_SEO.
 */
class Test_SEO extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\SEO\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test hook.
		$this->assertSame( 999999, has_filter( 'robots_txt', 'Quark\SEO\custom_robots_txt' ) );
	}

	/**
	 * Test breadcrumbs.
	 *
	 * @covers \Quark\SEO\custom_robots_txt()
	 *
	 * @return void
	 */
	public function test_custom_robots_txt(): void {
		// Update option.
		update_option( 'options_seo_robots_txt', 'User-agent: *' );

		// Test hook.
		$this->assertEquals( 'User-agent: *', custom_robots_txt() );
		$this->assertEquals( 'User-agent: *', apply_filters( 'robots_txt', '' ) );
	}
}
