<?php
/**
 * Press Releases test suite.
 *
 * @package quark-press-releases
 */

namespace Quark\PressReleases\Tests;

use WP_Post;
use WP_UnitTestCase;

use function Quark\PressReleases\get;
use function Quark\PressReleases\get_breadcrumbs_ancestors;
use function Quark\PressReleases\get_cards_data;

use const Quark\PressReleases\POST_TYPE;

/**
 * Class Test_Press_Releases.
 */
class Test_Press_Releases extends WP_UnitTestCase {

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
		$this->assertEquals(
			'http://test.quarkexpeditions.com/press-releases/2020/12/test-post',
			$the_post['permalink']
		);

		// Test getting incorrect post.
		$this->assertEquals(
			[
				'post'      => null,
				'permalink' => '',
			],
			get( $post_2->ID )
		);
	}

	/**
	 * Test add_date_to_permalink.
	 *
	 * @covers \Quark\PressReleases\add_date_to_permalink()
	 *
	 * @return void
	 */
	public function test_add_date_to_permalink(): void {
		// Create a post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'    => 'Test Post',
				'post_content'  => 'Post content',
				'post_status'   => 'publish',
				'post_type'     => POST_TYPE,
				'post_date'     => '2021-01-01 00:00:00',
				'post_date_gmt' => '2021-01-01 00:00:00',
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Get post permalink.
		$permalink = get_permalink( $post );

		// Assert the permalink.
		$this->assertEquals(
			'http://test.quarkexpeditions.com/press-releases/2021/01/test-post',
			$permalink
		);
	}

	/**
	 * Test get breadcrumbs.
	 *
	 * @covers \Quark\PressReleases\get_breadcrumbs_ancestors()
	 *
	 * @return void
	 */
	public function test_get_breadcrumbs_ancestors(): void {
		// Test with no ancestors.
		$this->assertEmpty( get_breadcrumbs_ancestors() );

		// Create a page.
		$page = $this->factory()->post->create_and_get(
			[
				'post_title' => 'Test Page',
				'post_type'  => 'page',
			]
		);
		$this->assertTrue( $page instanceof WP_Post );

		// Set as archive page.
		update_option( 'options_press_releases_page', $page->ID );

		// Assert the breadcrumbs.
		$this->assertEquals(
			[
				[
					'title' => 'Test Page',
					'url'   => get_permalink( $page ),
				],
			],
			get_breadcrumbs_ancestors()
		);
	}

	/**
	 * Test Get Cards Data.
	 *
	 * @covers Quark\PressReleases\get_cards_data()
	 *
	 * @return void
	 */
	public function test_get_cards_datas(): void {
		// Create a post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'    => 'Test Post',
				'post_content'  => 'Post content',
				'post_excerpt'  => 'Post excerpt',
				'post_status'   => 'publish',
				'post_type'     => POST_TYPE,
				'post_date'     => '2018-10-01 00:00:00',
				'post_date_gmt' => '2018-10-01 00:00:00',
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Get cards data.
		$cards_data = get_cards_data( [ $post->ID ] );

		// Assert the cards data.
		$this->assertEquals(
			[
				[
					'id'          => $post->ID,
					'title'       => $post->post_title,
					'description' => "<p>Post excerpt</p>\n",
					'permalink'   => 'http://test.quarkexpeditions.com/press-releases/2018/10/test-post',
				],
			],
			$cards_data
		);
	}
}
