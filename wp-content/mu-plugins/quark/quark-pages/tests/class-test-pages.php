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
	 * @covers \Quark\Pages\layout_single()
	 *
	 * @return void
	 */
	public function test_layout_single(): void {
		// No post.
		$this->assertEquals(
			[],
			\Quark\Pages\layout_single()
		);

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\Pages\POST_TYPE,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Replace current post.
		global $post;
		$post_backup = $post;
		$post        = $post_1; // phpcs:ignore

		// Test with post.
		$layout = \Quark\Pages\layout_single();

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

		// Test page template.
		update_post_meta( $post_1->ID, '_wp_page_template', 'templates/something.php' );
		$this->assertEquals(
			[
				'layout' => 'something',
				'data'   => [
					'post'           => $post_1,
					'permalink'      => 'http://test.quarkexpeditions.com/test-post',
					'post_thumbnail' => 0,
					'post_content'   => 'Post content',
				],
			],
			\Quark\Pages\layout_single()
		);

		// Test home page.
		global $wp_query;
		$wp_query_backup = $wp_query;
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $post_1->ID );
		$wp_query->is_page        = true;
		$wp_query->queried_object = $post_1;

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'layout' => 'home',
				'data'   => [
					'post'           => $post_1,
					'permalink'      => 'http://test.quarkexpeditions.com/',
					'post_thumbnail' => 0,
					'post_content'   => 'Post content',
				],
			],
			\Quark\Pages\layout_single()
		);

		// Clean up.
		$post     = $post_backup;  // phpcs:ignore
		$wp_query = $wp_query_backup;  // phpcs:ignore
		update_option( 'show_on_front', 'posts' );
		update_option( 'page_on_front', '0' );
	}

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
