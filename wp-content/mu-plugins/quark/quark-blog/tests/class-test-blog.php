<?php
/**
 * Blog test suite.
 *
 * @package quark-blog
 */

namespace Quark\Blog\Tests;

use WP_Post;
use WP_Term;
use WP_UnitTestCase;

use function Quark\Blog\primary_term_taxonomies;
use function Quark\Blog\get;
use function Quark\Blog\layout_single;

use const Quark\Blog\POST_TYPE;

/**
 * Class Test_Blog.
 */
class Test_Blog extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Blog\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_action( 'template_redirect', 'Quark\Blog\layout' ) );
		$this->assertEquals( 10, has_filter( 'travelopia_primary_term_taxonomies', 'Quark\Blog\primary_term_taxonomies' ) );
	}

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
			layout_single()
		);

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Create category terms.
		$category_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'category',
			]
		);

		// Assert term is created.
		$this->assertTrue( $category_term instanceof WP_Term );

		// Create post_tag terms.
		$post_tag_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'post_tag',
			]
		);

		// Assert term is created.
		$this->assertTrue( $post_tag_term instanceof WP_Term );

		// Set terms.
		wp_set_object_terms( $post_1->ID, $category_term->term_id, 'category' );
		wp_set_object_terms( $post_1->ID, $post_tag_term->term_id, 'post_tag' );

		// Replace current post.
		global $post;
		$post = $post_1; // phpcs:ignore

		// Test with post.
		$layout = layout_single();

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'layout' => 'single',
				'data'   => [
					'post'            => $post_1,
					'permalink'       => 'http://test.quarkexpeditions.com/test-post',
					'post_thumbnail'  => 0,
					'post_content'    => 'Post content',
					'post_meta'       => [
						'meta_1'            => 'value_1',
						'meta_2'            => 'value_2',
						'read_time_minutes' => 1,
					],
					'post_taxonomies' => [
						'category' => [
							[
								'term_id'     => strval( $category_term->term_id ),
								'name'        => $category_term->name,
								'slug'        => $category_term->slug,
								'taxonomy'    => $category_term->taxonomy,
								'description' => $category_term->description,
								'parent'      => $category_term->parent,
								'term_group'  => $category_term->term_group,
							],
						],
						'post_tag' => [
							[
								'term_id'     => strval( $post_tag_term->term_id ),
								'name'        => $post_tag_term->name,
								'slug'        => $post_tag_term->slug,
								'taxonomy'    => $post_tag_term->taxonomy,
								'description' => $post_tag_term->description,
								'parent'      => $post_tag_term->parent,
								'term_group'  => $post_tag_term->term_group,
							],
						],
					],
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
	 * Test get.
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
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Create category terms.
		$category_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'category',
			]
		);

		// Assert term is created.
		$this->assertTrue( $category_term instanceof WP_Term );

		// Create post_tag terms.
		$post_tag_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'post_tag',
			]
		);

		// Assert term is created.
		$this->assertTrue( $post_tag_term instanceof WP_Term );

		// Set terms.
		wp_set_object_terms( $post_1->ID, $category_term->term_id, 'category' );
		wp_set_object_terms( $post_1->ID, $post_tag_term->term_id, 'post_tag' );

		// Test getting post.
		$the_post = get( $post_1->ID );

		// Assert expected layout is equal to actual layout.
		$this->assertEquals(
			[
				'post'            => $post_1,
				'permalink'       => 'http://test.quarkexpeditions.com/test-post',
				'post_thumbnail'  => 0,
				'post_meta'       => [
					'meta_1'            => 'value_1',
					'meta_2'            => 'value_2',
					'read_time_minutes' => 1,
				],
				'post_taxonomies' => [
					'category' => [
						[
							'term_id'     => strval( $category_term->term_id ),
							'name'        => $category_term->name,
							'slug'        => $category_term->slug,
							'taxonomy'    => $category_term->taxonomy,
							'description' => $category_term->description,
							'parent'      => $category_term->parent,
							'term_group'  => $category_term->term_group,
						],
					],
					'post_tag' => [
						[
							'term_id'     => strval( $post_tag_term->term_id ),
							'name'        => $post_tag_term->name,
							'slug'        => $post_tag_term->slug,
							'taxonomy'    => $post_tag_term->taxonomy,
							'description' => $post_tag_term->description,
							'parent'      => $post_tag_term->parent,
							'term_group'  => $post_tag_term->term_group,
						],
					],
				],
			],
			$the_post
		);

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
	}

	/**
	 * Test primary_term_taxonomies.
	 *
	 * @covers \Quark\Blog\primary_term_taxonomies()
	 *
	 * @return void
	 */
	public function test_primary_term_taxonomies(): void {
		// Create dummy taxonomies list.
		$taxonomies = [
			'tax_1',
			'tax_2',
			'tax_3',
		];

		// Test without Post type slug.
		$this->assertEquals(
			$taxonomies,
			primary_term_taxonomies( $taxonomies, 'custom_post_type' )
		);

		// Test with Post type slug.
		$this->assertContains(
			'category',
			primary_term_taxonomies( $taxonomies, POST_TYPE )
		);
	}
}
