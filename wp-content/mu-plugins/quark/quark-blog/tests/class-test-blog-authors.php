<?php
/**
 * Blog Authors test suite.
 *
 * @package quark-blog-authors
 */

namespace Quark\Blog\Tests;

use WP_UnitTestCase;
use WP_Post;

use const Quark\Blog\Authors\POST_TYPE;

/**
 * Class Test_Blog_Authors.
 */
class Test_Blog_Authors extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Blog\Authors\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_filter( 'init', 'Quark\Blog\Authors\register_blog_author_post_type' ) );
	}

	/**
	 * Make sure post type is registered.
	 *
	 * @covers \Quark\Blog\Authors\register_blog_author_post_type()
	 *
	 * @return void
	 */
	public function test_register_blog_author_post_type(): void {
		// Test if post type is actually registered.
		$this->assertTrue( post_type_exists( POST_TYPE ) );
	}

	/**
	 * Test getting a base.
	 *
	 * @covers \Quark\Blog\Authors\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Make sure post is created.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Test getting post.
		$this->assertEquals(
			[
				'post'           => $post_1,
				'post_thumbnail' => 0,
				'post_meta'      => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			],
			\Quark\Blog\Authors\get( $post_1->ID )
		);
	}
}
