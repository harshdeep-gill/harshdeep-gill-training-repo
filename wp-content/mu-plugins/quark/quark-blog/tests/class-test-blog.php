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
use WPSEO_Meta;

use function Quark\Blog\get_structured_data;
use function Quark\Blog\primary_term_taxonomies;
use function Quark\Blog\get;
use function Quark\Blog\Authors\get as author_get;
use function Quark\Blog\get_blog_post_author_info;
use function Quark\Blog\get_cards_data;
use function Quark\Blog\breadcrumbs_ancestors;
use function Quark\Blog\get_breadcrumbs_ancestors;

use const Quark\Blog\POST_TYPE;
use const Quark\Blog\Authors\POST_TYPE as AUTHOR_POST_TYPE;
use const Quark\Pages\POST_TYPE as PAGE_POST_TYPE;

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
		$this->assertEquals( 10, has_filter( 'travelopia_primary_term_taxonomies', 'Quark\Blog\primary_term_taxonomies' ) );
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

	/**
	 * Test breadcrumbs_ancestors.
	 *
	 * @covers \Quark\Blog\breadcrumbs_ancestors()
	 *
	 * @return void
	 */
	public function test_breadcrumbs_ancestors(): void {
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

		// Set Category as primary term.
		\update_post_meta( $post_1->ID, '_yoast_wpseo_primary_category', $category_term->term_id );

		// Set terms.
		wp_set_object_terms( $post_1->ID, $category_term->term_id, 'category' );

		// Set post single page.
		WP_UnitTestCase::go_to( strval( get_permalink( $post_1->ID ) ) );

		// Mock is_singular function.
		$this->assertTrue( is_singular( POST_TYPE ) );

		// Test getting post breadcrumbs.
		$breadcrumbs = breadcrumbs_ancestors( [] );

		// Assert expected breadcrumbs is equal to actual breadcrumbs.
		$this->assertEquals(
			[
				[
					'title' => $category_term->name,
					'url'   => get_term_link( $category_term ),
				],
			],
			$breadcrumbs
		);

		// Create Blog page.
		$page_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Blog',
				'post_content' => 'Page content',
				'post_status'  => 'publish',
				'post_type'    => PAGE_POST_TYPE,
			]
		);

		// Assert created page is instance of WP_Post.
		$this->assertTrue( $page_1 instanceof WP_Post );

		// Set page as page_for_posts.
		\update_option( 'page_for_posts', $page_1->ID );

		// Test getting post breadcrumbs.
		$breadcrumbs = breadcrumbs_ancestors( [] );

		// Assert expected breadcrumbs is equal to actual breadcrumbs.
		$this->assertEquals(
			[
				[
					'title' => $page_1->post_title,
					'url'   => get_permalink( $page_1->ID ),
				],
				[
					'title' => $category_term->name,
					'url'   => get_term_link( $category_term ),
				],
			],
			$breadcrumbs
		);

		// Clean up.
		update_option( 'page_for_posts', 0 );

		// remove posts and terms.
		wp_delete_post( $post_1->ID, true );
		wp_delete_post( $page_1->ID, true );
		wp_delete_term( $category_term->term_id, 'category' );
	}

	/**
	 * Test get.
	 *
	 * @covers \Quark\Blog\get_cards_data()
	 *
	 * @return void
	 */
	public function test_get_cards_data(): void {
		// Create post 1.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 1',
				'post_content' => 'Post content 1',
				'post_excerpt' => 'Post excerpt 1',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'_thumbnail_id' => 35,
				],
			]
		);

		// Calculate reading time.
		if ( $post_1 instanceof WP_Post ) {
			\Quark\Blog\calculate_post_reading_time( $post_1->ID, $post_1 );
		}

		// Create post 2.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 2',
				'post_content' => 'Post content 2',
				'post_excerpt' => 'Post excerpt 2',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'_thumbnail_id' => 32,
				],
			]
		);

		// Calculate reading time.
		if ( $post_2 instanceof WP_Post ) {
			\Quark\Blog\calculate_post_reading_time( $post_2->ID, $post_2 );
		}

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Create author.
		$author_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => AUTHOR_POST_TYPE,
				'post_title'  => 'Test Author',
				'post_status' => 'publish',
			]
		);

		// Asset that author is created.
		$this->assertTrue( $author_1 instanceof WP_Post );

		// Update post meta with author.
		update_post_meta( $post_1->ID, 'blog_authors', [ $author_1->ID ] );
		update_post_meta( $post_2->ID, 'blog_authors', [ $author_1->ID ] );

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
		wp_set_object_terms( $post_2->ID, $category_term->term_id, 'category' );
		wp_set_object_terms( $post_2->ID, $post_tag_term->term_id, 'post_tag' );

		// Test 1: get post card data by passing single post id.
		wp_cache_flush();
		$post_data = get_cards_data( [ $post_1->ID ] );

		// Assert expected cards data with actual.
		$this->assertEquals(
			[
				[
					'post'           => $post_1,
					'title'          => 'Test Post 1',
					'permalink'      => 'http://test.quarkexpeditions.com/test-post-1',
					'excerpt'        => 'Post excerpt 1',
					'featured_image' => 35,
					'authors'        => [
						author_get( $author_1->ID ),
					],
					'read_time'      => 1,
					'taxonomies'     => [
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
			$post_data
		);

		// Test 2: get post cards data by passing multiple post ids.
		wp_cache_flush();
		$post_data = get_cards_data(
			[
				$post_1->ID,
				$post_2->ID,
			]
		);

		// Assert expected cards data with actual.
		$this->assertEquals(
			[
				[
					'post'           => $post_1,
					'title'          => 'Test Post 1',
					'permalink'      => 'http://test.quarkexpeditions.com/test-post-1',
					'excerpt'        => 'Post excerpt 1',
					'featured_image' => 35,
					'authors'        => [
						author_get( $author_1->ID ),
					],
					'read_time'      => 1,
					'taxonomies'     => [
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
				[
					'post'           => $post_2,
					'title'          => 'Test Post 2',
					'permalink'      => 'http://test.quarkexpeditions.com/test-post-2',
					'excerpt'        => 'Post excerpt 2',
					'featured_image' => 32,
					'authors'        => [
						author_get( $author_1->ID ),
					],
					'read_time'      => 1,
					'taxonomies'     => [
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
			$post_data
		);

		// Test 3: pass empty array.
		wp_cache_flush();
		$post_data = get_cards_data( [] );

		// Assert that returned data is empty.
		$this->assertEmpty( $post_data );
	}

	/**
	 * Test get_blog_post_author_info.
	 *
	 * @covers \Quark\Blog\get_blog_post_author_info()
	 *
	 * @return void
	 */
	public function test_get_blog_post_author_info(): void {
		// Create author.
		$author = $this->factory()->post->create_and_get(
			[
				'post_type'   => AUTHOR_POST_TYPE,
				'post_title'  => 'Test Author',
				'post_status' => 'publish',
				'meta_input'  => [
					'_thumbnail_id' => 35,
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $author instanceof WP_Post );

		// Create another author.
		$author_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => AUTHOR_POST_TYPE,
				'post_title'  => 'Test Author 1',
				'post_status' => 'publish',
				'meta_input'  => [
					'_thumbnail_id' => 36,
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $author_1 instanceof WP_Post );

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 1',
				'post_content' => 'Post content 1',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'read_time_minutes' => 1,
					'_thumbnail_id'     => 35,
					'blog_authors'      => [ $author->ID, $author_1->ID ],
				],
			]
		);

		// Asset that author is created.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Set post single page.
		WP_UnitTestCase::go_to( strval( get_permalink( $post_1->ID ) ) );

		// Mock is_singular function.
		$this->assertTrue( is_singular( POST_TYPE ) );

		// Test getting author info.
		$author_info = get_blog_post_author_info();

		// Assert expected author info with actual.
		$this->assertEquals(
			[
				'duration' => 1,
				'authors'  => [
					0 => [
						'image_id' => 35,
						'title'    => 'Test Author',
					],
					1 => [
						'image_id' => 36,
						'title'    => 'Test Author 1',
					],
				],
			],
			$author_info
		);
	}

	/**
	 * Test get structured data.
	 *
	 * @covers \Quark\Blog\get_structured_data()
	 *
	 * @return void
	 */
	public function test_get_structured_data(): void {
		// create author.
		$author_one = $this->factory()->post->create_and_get(
			[
				'post_type'   => AUTHOR_POST_TYPE,
				'post_title'  => 'Test Author 1',
				'post_status' => 'publish',
			]
		);
		$author_two = $this->factory()->post->create_and_get(
			[
				'post_type'   => AUTHOR_POST_TYPE,
				'post_title'  => 'Test Author 2',
				'post_status' => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $author_one instanceof WP_Post );
		$this->assertTrue( $author_two instanceof WP_Post );

		// Prepare post arguments.
		$post_arguments = [
			'post_title'   => 'Test Post',
			'post_content' => 'Post content',
			'post_status'  => 'publish',
			'post_type'    => POST_TYPE,
			'meta_input'   => [
				'blog_authors' => [ $author_one->ID, $author_two->ID ],
			],
		];

		// create article.
		$post = $this->factory()->post->create_and_get( $post_arguments );

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post instanceof WP_Post );

		// Prepare structured data.
		$structured_data = [
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => $post->post_title,
			'datePublished' => $post->post_date,
			'dateModified'  => $post->post_modified,
			'image'         => [],
			'author'        => [
				[
					'@type' => 'Person',
					'name'  => $author_one->post_title,
				],
				[
					'@type' => 'Person',
					'name'  => $author_two->post_title,
				],
			],
		];

		// Assert modified structured data.
		$this->assertEquals( $structured_data, get_structured_data( $post->ID ) );

		// Delete Post.
		wp_delete_post( $post->ID, true );

		// Test when blog authors meta is not set.
		$post_arguments['meta_input'] = [];

		// create article.
		$post = $this->factory()->post->create_and_get( $post_arguments );

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post instanceof WP_Post );

		// Prepare structured data.
		unset( $structured_data['author'] );

		// Assert modified structured data.
		$this->assertEquals( $structured_data, get_structured_data( $post->ID ) );

		// Delete Post.
		wp_delete_post( $post->ID, true );
		wp_delete_post( $author_one->ID, true );
		wp_delete_post( $author_two->ID, true );
	}

	/**
	 * Test get breadcrumbs ancestors.
	 *
	 * @covers \Quark\Blog\get_breadcrumbs_ancestors()
	 *
	 * @return void
	 */
	public function test_get_breadcrumbs_ancestors(): void {
		// Test without any post id.
		$this->assertEmpty( get_breadcrumbs_ancestors() );

		// Create a blog post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post instanceof WP_Post );

		// Test without any active post.
		$this->assertEmpty( get_breadcrumbs_ancestors( $post->ID ) );

		// Create a page.
		$page = $this->factory()->post->create_and_get(
			[
				'post_title' => 'Test Page',
				'post_type'  => 'page',
			]
		);

		// Assert created page is instance of WP_Post.
		$this->assertTrue( $page instanceof WP_Post );

		// Set as archive page.
		update_option( 'page_for_posts', $page->ID );

		// Test with archive page.
		$this->assertEquals(
			[
				[
					'title' => $page->post_title,
					'url'   => get_permalink( $page->ID ),
				],
			],
			get_breadcrumbs_ancestors( $post->ID )
		);

		// Create a category term.
		$category_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'category',
			]
		);

		// Assert term is created.
		$this->assertTrue( $category_term instanceof WP_Term );

		// Set terms.
		wp_set_object_terms( $post->ID, $category_term->term_id, 'category' );

		// Get breadcrumbs ancestors.
		$breadcrumbs = get_breadcrumbs_ancestors( $post->ID );

		// Assert expected breadcrumbs is equal to actual breadcrumbs - without any primary term.
		$this->assertEquals(
			[
				[
					'title' => $page->post_title,
					'url'   => get_permalink( $page->ID ),
				],
			],
			$breadcrumbs
		);

		// Add a primary term.
		update_post_meta( $post->ID, WPSEO_Meta::$meta_prefix . 'primary_category', $category_term->term_id );

		// Get breadcrumbs ancestors.
		$breadcrumbs = get_breadcrumbs_ancestors( $post->ID );

		// Assert expected breadcrumbs is equal to actual breadcrumbs - with primary term.
		$this->assertEquals(
			[
				[
					'title' => $page->post_title,
					'url'   => get_permalink( $page->ID ),
				],
				[
					'title' => $category_term->name,
					'url'   => get_term_link( $category_term ),
				],
			],
			$breadcrumbs
		);
	}
}
