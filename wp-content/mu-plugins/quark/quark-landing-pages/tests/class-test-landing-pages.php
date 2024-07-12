<?php
/**
 * Landing Pages test suite.
 *
 * @package quark-landing-pages
 */

namespace Quark\LandingPages\Tests;

use WP_Post;
use WP_UnitTestCase;

use function Quark\LandingPages\get;
use function Quark\LandingPages\layout_single;

use const Quark\LandingPages\POST_TYPE;

/**
 * Class Test_Landing_Pages.
 */
class Test_Landing_Pages extends WP_UnitTestCase {

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
				'post_type'    => POST_TYPE,
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
		$the_post = get( $post_1->ID );

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
