<?php
/**
 * Blog test suite.
 *
 * @package quark-blog
 */

namespace Quark\Blog\Tests;

use WP_Post;
use WP_UnitTestCase;

/**
 * Class Test_Blog.
 */
class Test_Blog extends WP_UnitTestCase {

	/**
	 * Test single layout.
	 *
	 * @covers \Quark\Blog\layout_single()
	 *
	 * @return void
	 */
	public function test_layout_single(): void {
		// No post.
		$this->assertEquals(
			[],
			\Quark\Blog\layout_single()
		);

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\Blog\POST_TYPE,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Replace current post.
		global $post;
		$post_backup = $post;
		$post        = $post_1; // phpcs:ignore

		// Test with post.
		$layout = \Quark\Blog\layout_single();

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'layout' => 'single',
				'data'   => [
					'post'           => $post_1,
					'permalink'      => 'http://test.quarkexpeditions.com/test-post',
					'post_thumbnail' => 0,
					'post_content'   => 'Post content',
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

		// Assert expected post thumbnail is equal to actual post thumbnail.
		$this->assertEquals(
			0,
			$layout['data']['post_thumbnail'] // @phpstan-ignore-line
		);

		// Clean up.
		update_option( 'show_on_front', 'posts' );
		update_option( 'page_on_front', '0' );
	}

	/**
	 * Test single layout.
	 *
	 * @covers \Quark\Blog\get()
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
				'post_type'    => \Quark\Blog\POST_TYPE,
			]
		);

		// Create another post.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'    => 'page',
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Test getting post.
		$the_post = \Quark\Blog\get( $post_1->ID );

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
			\Quark\Blog\get( $post_2->ID )
		);
	}
}
