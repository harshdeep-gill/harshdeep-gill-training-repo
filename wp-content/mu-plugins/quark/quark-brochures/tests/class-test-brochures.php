<?php
/**
 * Brochurese test suite.
 *
 * @package quark-brochures
 */

namespace Quark\Brochures\Tests;

use WP_Post;
use WP_UnitTestCase;

/**
 * Class Test_brochures.
 */
class Test_Brochures extends WP_UnitTestCase {

	/**
	 * Test single layout.
	 *
	 * @covers \Quark\Brochures\layout_single()
	 *
	 * @return void
	 */
	public function test_layout_single(): void {
		// No post.
		$this->assertEquals(
			[],
			\Quark\Brochures\layout_single()
		);

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'post_type'   => \Quark\Brochures\POST_TYPE,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Replace current post.
		global $post;
		$post = $post_1; // phpcs:ignore

		// Test with post.
		$layout = \Quark\Brochures\layout_single();

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'layout' => 'single',
				'data'   => [
					'post'      => $post_1,
					'permalink' => 'http://test.quarkexpeditions.com/?qrk_brochures=test-post',
				],
			],
			$layout
		);
	}

	/**
	 * Test single layout.
	 *
	 * @covers Quark\Brochures\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'post_type'   => \Quark\Brochures\POST_TYPE,
			]
		);

		// Create another post.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Test getting post.
		$the_post = \Quark\Brochures\get( $post_1->ID );

		// Assert post's expected permalink is correct is equal to actual permalink.
		$this->assertEquals(
			'http://test.quarkexpeditions.com/?qrk_brochures=test-post',
			$the_post['permalink']
		);

		// Test getting incorrect post.
		$this->assertEquals(
			[
				'post'      => null,
				'permalink' => '',
			],
			\Quark\Brochures\get( $post_2->ID )
		);
	}
}
