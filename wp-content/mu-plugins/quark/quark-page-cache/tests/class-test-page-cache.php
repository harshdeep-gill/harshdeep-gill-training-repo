<?php
/**
 * Tests for the Page cache functions.
 *
 * @package quark-page-cache
 */

namespace Quark\PageCache\Tests;

use WP_UnitTestCase;

use function Quark\PageCache\flush_and_warm_up_page_cache;

/**
 * Class Test_Page_Cache
 */
class Test_Page_Cache extends WP_UnitTestCase {

	/**
	 * Time took.
	 *
	 * @var mixed
	 */
	private $time_took;

	/**
	 * Test flush_and_warm_up_page_cache function.
	 *
	 * @covers \Quark\PageCache\flush_and_warm_up_page_cache
	 *
	 * @return void
	 */
	public function test_flush_and_warm_up_page_cache(): void {
		// Attach the hook.
		add_action( 'quark_page_cache_flushed', [ $this, 'quark_page_cache_flushed' ] );

		// Time took should be empty.
		$this->assertEmpty( $this->time_took );

		// Test that the action is not fired before the function is called.
		$this->assertEmpty( did_action( 'quark_page_cache_flushed' ) );
		$this->assertEmpty( $this->time_took );

		// Call the function.
		flush_and_warm_up_page_cache();

		// Test that the action is fired after the function is called.
		$this->assertNotEmpty( did_action( 'quark_page_cache_flushed' ) );
		$this->assertNotEmpty( $this->time_took );
		$this->assertIsScalar( $this->time_took );
		$this->assertIsFloat( $this->time_took );

		// Reset the hook.
		remove_action( 'quark_page_cache_flushed', [ $this, 'quark_page_cache_flushed' ] );

		// Reset variable.
		$this->time_took = null;
	}

	/**
	 * Hook on `quark_page_cache_flushed`.
	 *
	 * @param mixed[] $data Data.
	 *
	 * @return void
	 */
	public function quark_page_cache_flushed( array $data = [] ): void {
		// Validate.
		$this->assertNotEmpty( $data );
		$this->assertArrayHasKey( 'time_took', $data );

		// Set the time took.
		$this->time_took = $data['time_took'];
	}
}
