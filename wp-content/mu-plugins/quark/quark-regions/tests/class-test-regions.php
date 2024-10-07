<?php
/**
 * Regions test suite.
 *
 * @package quark-regions
 */

namespace Quark\Regions\Tests;

use WP_Post;
use WP_UnitTestCase;

use function Quark\Regions\get;
use function Quark\Regions\get_custom_permalink;

use const Quark\Regions\POST_TYPE;

/**
 * Class Test_Regions.
 */
class Test_Regions extends WP_UnitTestCase {

	/**
	 * Test single layout.
	 *
	 * @covers Quark\Regions\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'    => 'Test Post',
				'post_content'  => 'Post content',
				'post_status'   => 'publish',
				'post_type'     => POST_TYPE,
				'post_date'     => '2020-12-01 00:00:00',
				'post_date_gmt' => '2020-12-01 00:00:00',
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
		$this->assertEquals( get_permalink( $post_1->ID ), $the_post['permalink'] );
	}

	/**
	 * Test get_custom_permalink.
	 *
	 * @covers Quark\Regions\get_custom_permalink()
	 *
	 * @return void
	 */
	public function test_get_custom_permalink(): void {
		// Create post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
			]
		);

		// Test getting custom permalink.
		$custom_permalink = home_url( '/regions/test-post' );

		// Assert valid post.
		$this->assertTrue( $post instanceof WP_Post );

		// Get custom permalink.
		$the_custom_permalink = get_custom_permalink( $custom_permalink, $post );

		// Assert custom permalink is correct.
		$this->assertEquals( get_post_permalink( $post->ID ), $the_custom_permalink );
	}
}
