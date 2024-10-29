<?php
/**
 * Tests for the Page cache functions.
 *
 * @package quark-page-cache
 */

namespace Quark\Cache\Tests;

use WP_UnitTestCase;
use WP_Post;

use function Quark\Cache\Edge\flush_and_warm_edge_cache;

/**
 * Class Test_Page_Cache
 */
class Test_Page_Cache extends WP_UnitTestCase {

	/**
	 * Time took.
	 *
	 * @var mixed
	 */
	private $time_taken;

	/**
	 * Test flush_and_warm_edge_cache function.
	 *
	 * @covers \Quark\PageCache\flush_and_warm_edge_cache
	 *
	 * @return void
	 */
	public function test_flush_and_warm_edge_cache(): void {
		// Attach the hook.
		add_action( 'quark_page_cache_flushed', [ $this, 'quark_page_cache_flushed' ] );

		// Time took should be empty.
		$this->assertEmpty( $this->time_taken );

		// Test that the action is not fired before the function is called.
		$this->assertEmpty( did_action( 'quark_page_cache_flushed' ) );
		$this->assertEmpty( $this->time_taken );

		// Call the function.
		flush_and_warm_edge_cache();

		// Test that the action is fired after the function is called.
		$this->assertNotEmpty( did_action( 'quark_page_cache_flushed' ) );
		$this->assertNotEmpty( $this->time_taken );
		$this->assertIsScalar( $this->time_taken );
		$this->assertIsFloat( $this->time_taken );

		// Reset the hook.
		remove_action( 'quark_page_cache_flushed', [ $this, 'quark_page_cache_flushed' ] );

		// Reset variable.
		$this->time_taken = null;
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
		$this->assertArrayHasKey( 'time_taken', $data );

		// Set the time took.
		$this->time_taken = $data['time_taken'];
	}

	/**
	 * Test set_meta_for_pricing_block_posts function.
	 *
	 * @covers \Quark\PageCache\set_meta_for_pricing_block_posts
	 *
	 * @return void
	 */
	public function test_set_meta_for_pricing_block_posts(): void {
		// Create and get post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => 'post',
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Assert that the meta is not set.
		$this->assertEmpty( get_post_meta( $post->ID, '_has_a_block_with_pricing_information', true ) );

		// Update the post.
		wp_update_post(
			[
				'ID'           => $post->ID,
				'post_content' => '<!-- wp:quark/book-departures-expeditions /-->',
			]
		);

		// Assert that the meta is set.
		$this->assertTrue( (bool) get_post_meta( $post->ID, '_has_a_block_with_pricing_information', true ) );
	}
}
