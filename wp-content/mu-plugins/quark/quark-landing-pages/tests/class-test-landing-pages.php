<?php
/**
 * Landing Pages test suite.
 *
 * @package quark-landing-pages
 */

namespace Quark\LandingPages\Tests;

use WP_Post;
use WP_UnitTestCase;

/**
 * Class Test_Landing_Pages.
 */
class Test_Landing_Pages extends WP_UnitTestCase {

	/**
	 * Test single layout.
	 *
	 * @covers \Quark\LandingPages\layout_single()
	 *
	 * @return void
	 */
	public function test_layout_single(): void {
		// No post.
		$this->assertEquals(
			[],
			\Quark\LandingPages\layout_single()
		);

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\LandingPages\POST_TYPE,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Replace current post.
		global $post;
		$post = $post_1; // phpcs:ignore

		// Test with post.
		$layout = \Quark\LandingPages\layout_single();

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'layout' => 'single-landing-page',
				'data'   => [
					'post'         => $post_1,
					'permalink'    => 'http://test.quarkexpeditions.com/ppc-landing-pages/test-post',
					'post_content' => "<p>Post content</p>\n",
				],
			],
			$layout
		);

		// Assert expected post content is equal to actual post content.
		$this->assertEquals(
			"<p>Post content</p>\n",
			$layout['data']['post_content'] // @phpstan-ignore-line
		);
	}

	/**
	 * Test getting an LP.
	 *
	 * @covers \Quark\LandingPages\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\LandingPages\POST_TYPE,
			]
		);

		// Create another post.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Test getting post.
		$the_post = \Quark\LandingPages\get( $post_1->ID );

		// Assert post's expected permalink is correct is equal to actual permalink.
		$this->assertEquals(
			'http://test.quarkexpeditions.com/ppc-landing-pages/test-post',
			$the_post['permalink']
		);

		// Test getting incorrect post.
		$this->assertEquals(
			[
				'post'      => null,
				'permalink' => '',
			],
			\Quark\LandingPages\get( $post_2->ID )
		);
	}
}
