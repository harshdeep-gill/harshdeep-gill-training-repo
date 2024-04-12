<?php
/**
 * Press Release test suite.
 *
 * @package quark-press-releases
 */

namespace Quark\PressReleases\Tests;

use WP_Post;
use WP_UnitTestCase;

/**
 * Class Test_Press_Releases.
 */
class Test_Press_Releases extends WP_UnitTestCase {

	/**
	 * Test single layout.
	 *
	 * @covers \Quark\PressReleases\layout_single()
	 *
	 * @return void
	 */
	public function test_layout_single(): void {
		// No post.
		$this->assertEquals(
			[],
			\Quark\PressReleases\layout_single()
		);

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\PressReleases\POST_TYPE,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Replace current post.
		global $post;
		$post = $post_1; // phpcs:ignore

		// Test with post.
		$layout = \Quark\PressReleases\layout_single();

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'layout' => 'single',
				'data'   => [
					'post'         => $post_1,
					'permalink'    => 'http://test.quarkexpeditions.com/press-releases/test-post',
					'post_content' => 'Post content',
				],
			],
			$layout
		);

		// Simulate front-end.
		do_action( 'quark_get_front_end_data' );

		// Assert expected post content is equal to actual post content.
		$this->assertEquals(
			"<p>Post content</p>\n",
			$layout['data']['post_content'] // @phpstan-ignore-line
		);
	}

	/**
	 * Test single layout.
	 *
	 * @covers Quark\PressReleases\get()
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
				'post_type'    => \Quark\PressReleases\POST_TYPE,
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
		$the_post = \Quark\PressReleases\get( $post_1->ID );

		// Assert post's expected permalink is correct is equal to actual permalink.
		$this->assertEquals(
			'http://test.quarkexpeditions.com/press-releases/test-post',
			$the_post['permalink']
		);

		// Test getting incorrect post.
		$this->assertEquals(
			[
				'post'      => null,
				'permalink' => '',
			],
			\Quark\PressReleases\get( $post_2->ID )
		);
	}
}
