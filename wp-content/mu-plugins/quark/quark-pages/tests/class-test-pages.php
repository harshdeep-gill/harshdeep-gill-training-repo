<?php
/**
 * Pages test suite.
 *
 * @package quark-pages
 */

namespace Quark\Pages\Tests;

use WP_Post;
use WP_UnitTestCase;

/**
 * Class Test_Pages.
 */
class Test_Pages extends WP_UnitTestCase {

	/**
	 * Test single layout.
	 *
	 * @covers \Quark\Pages\get()
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
				'post_type'    => \Quark\Pages\POST_TYPE,
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
		$the_post = \Quark\Pages\get( $post_1->ID );

		// Assert post's expected permalink is correct is equal to actual permalink.
		$this->assertEquals(
			'http://test.quarkexpeditions.com/test-post',
			$the_post['permalink']
		);

		// Assert post's expected post thumbnail is correct is equal to actual post thumbnail.
		$this->assertEquals(
			0,
			$the_post['post_thumbnail']
		);

		// Test getting incorrect post.
		$this->assertEquals(
			[
				'post'           => null,
				'permalink'      => '',
				'post_thumbnail' => 0,
			],
			\Quark\Pages\get( $post_2->ID )
		);
	}
}
