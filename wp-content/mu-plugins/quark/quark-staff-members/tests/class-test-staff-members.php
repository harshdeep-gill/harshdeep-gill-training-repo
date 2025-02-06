<?php
/**
 * Quark test staff members.
 *
 * @package quark-staff-members
 */

namespace Quark\StaffMembers\Tests;

use WP_UnitTestCase;
use WP_Post;
use WP_Error;
use WP_Term;

use function Quark\StaffMembers\get_cards_data;
use function Quark\StaffMembers\get;
use function Quark\StaffMembers\get_breadcrumbs_ancestors;
use function Quark\StaffMembers\get_department;
use function Quark\StaffMembers\get_departments;
use function Quark\StaffMembers\get_roles;
use function Quark\StaffMembers\get_structured_data;
use function Quark\StaffMembers\translate_meta_keys;

use const Quark\StaffMembers\DEPARTMENT_TAXONOMY;
use const Quark\StaffMembers\DEPARTURE_STAFF_ROLE_TAXONOMY;
use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBER_POST_TYPE;

/**
 * Class Test_Staff_Members.
 */
class Test_Staff_Members extends WP_UnitTestCase {

	/**
	 * Get a post to test with.
	 *
	 * @return WP_Post|WP_Error
	 */
	public function get_post(): WP_Post|WP_Error {
		// Create and return a post.
		return $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => STAFF_MEMBER_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
	}

	/**
	 * Test get function.
	 *
	 * @covers \Quark\StaffMembers\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create a post.
		$post = $this->get_post();

		// Test if this is a post.
		$this->assertTrue( $post instanceof WP_Post );

		// Test if the post has the correct post type.
		$this->assertEquals( STAFF_MEMBER_POST_TYPE, $post->post_type );

		// Assign Taxonomies.
		$department = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'qrk_department',
				'name'     => 'Test Department',
			]
		);
		$season     = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'qrk_season',
				'name'     => 'Test Season',
			]
		);

		// Assert the taxonomies are created.
		$this->assertTrue( $department instanceof WP_Term );
		$this->assertTrue( $season instanceof WP_Term );

		// Assign taxonomies to the post.
		wp_set_post_terms( $post->ID, [ $department->term_id ], 'qrk_department' );
		wp_set_post_terms( $post->ID, [ $season->term_id ], 'qrk_season' );

		// Add role post meta.
		update_post_meta( $post->ID, 'job_title', 'Test Role' );

		// Assert the post data.
		$this->assertEquals(
			[
				'post'            => $post,
				'permalink'       => get_permalink( $post ),
				'post_thumbnail'  => 0,
				'post_taxonomies' => [
					'qrk_department' => [
						[
							'term_id'     => strval( $department->term_id ),
							'name'        => $department->name,
							'slug'        => $department->slug,
							'term_group'  => $department->term_group,
							'taxonomy'    => 'qrk_department',
							'description' => $department->description,
							'parent'      => $department->parent,
						],
					],
					'qrk_season'     => [
						[
							'term_id'     => strval( $season->term_id ),
							'name'        => $season->name,
							'slug'        => $season->slug,
							'term_group'  => $season->term_group,
							'taxonomy'    => 'qrk_season',
							'description' => $season->description,
							'parent'      => $season->parent,
						],
					],
				],
				'post_meta'       => [
					'job_title' => 'Test Role',
					'test_meta' => '1',
				],
			],
			get( $post->ID )
		);

		// Clean up.
		wp_delete_post( $post->ID, true );
		wp_delete_term( $department->term_id, 'qrk_department' );
		wp_delete_term( $season->term_id, 'qrk_season' );
	}

	/**
	 * Test get_cards_data function.
	 *
	 * @covers \Quark\StaffMembers::get_cards_data()
	 *
	 * @return void
	 */
	public function test_get_cards_data(): void {
		// Create a few staff members posts.
		$staff_member_1 = $this->get_post();
		$staff_member_2 = $this->get_post();
		$staff_member_3 = $this->get_post();

		// Test if these are a post.
		$this->assertTrue( $staff_member_1 instanceof WP_Post );
		$this->assertTrue( $staff_member_2 instanceof WP_Post );
		$this->assertTrue( $staff_member_3 instanceof WP_Post );

		// Create taxonomies.
		$season_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'qrk_season',
				'name'     => 'Test Season 1',
			]
		);
		$season_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'qrk_season',
				'name'     => 'Test Season 2',
			]
		);

		// Assert the taxonomies are created.
		$this->assertTrue( $season_1 instanceof WP_Term );
		$this->assertTrue( $season_2 instanceof WP_Term );

		// Assign taxonomies to posts.
		wp_set_post_terms( $staff_member_1->ID, [ $season_1->term_id ], 'qrk_season' );
		wp_set_post_terms( $staff_member_2->ID, [ $season_2->term_id ], 'qrk_season' );
		wp_set_post_terms( $staff_member_3->ID, [ $season_1->term_id ], 'qrk_season' );

		// Add role post meta.
		update_post_meta( $staff_member_1->ID, 'job_title', 'Test Role 1' );
		update_post_meta( $staff_member_2->ID, 'job_title', 'Test Role 2' );
		update_post_meta( $staff_member_3->ID, 'job_title', 'Test Role 2' );

		// Assert the cards data.
		$this->assertEquals(
			[
				[
					'title'          => $staff_member_1->post_title,
					'permalink'      => get_permalink( $staff_member_1->ID ),
					'featured_image' => 0,
					'role'           => 'Test Role 1',
					'season'         => $season_1->name,
				],
				[
					'title'          => $staff_member_2->post_title,
					'permalink'      => get_permalink( $staff_member_2->ID ),
					'featured_image' => 0,
					'role'           => 'Test Role 2',
					'season'         => $season_2->name,
				],
				[
					'title'          => $staff_member_3->post_title,
					'permalink'      => get_permalink( $staff_member_3->ID ),
					'featured_image' => 0,
					'role'           => 'Test Role 2',
					'season'         => $season_1->name,
				],
			],
			get_cards_data( [ $staff_member_1->ID, $staff_member_2->ID, $staff_member_3->ID ] )
		);

		// Clean up.
		wp_delete_post( $staff_member_1->ID, true );
		wp_delete_post( $staff_member_2->ID, true );
		wp_delete_post( $staff_member_3->ID, true );
		wp_delete_term( $season_1->term_id, 'qrk_season' );
		wp_delete_term( $season_2->term_id, 'qrk_season' );
	}

	/**
	 * Test get department function.
	 *
	 * @covers \Quark\StaffMembers\get_department()
	 *
	 * @return void
	 */
	public function test_get_department(): void {
		// Create a post.
		$post = $this->get_post();

		// Test if this is a post.
		$this->assertTrue( $post instanceof WP_Post );

		// Assign Taxonomies.
		$department = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTMENT_TAXONOMY,
				'name'     => 'Test Department',
			]
		);

		// Assert the taxonomies are created.
		$this->assertTrue( $department instanceof WP_Term );

		// Assign taxonomies to the post.
		wp_set_post_terms( $post->ID, [ $department->term_id ], DEPARTMENT_TAXONOMY );

		// Assert the department.
		$this->assertEquals(
			[
				'term_id'     => strval( $department->term_id ),
				'name'        => $department->name,
				'slug'        => $department->slug,
				'term_group'  => $department->term_group,
				'taxonomy'    => DEPARTMENT_TAXONOMY,
				'description' => $department->description,
				'parent'      => $department->parent,
			],
			get_department( $post->ID )
		);

		// Clean up.
		wp_delete_post( $post->ID, true );
		wp_delete_term( $department->term_id, DEPARTMENT_TAXONOMY );
	}

	/**
	 * Test get departments function.
	 *
	 * @covers \Quark\StaffMembers\get_departments()
	 *
	 * @return void
	 */
	public function test_get_departments(): void {
		// Create a post.
		$post = $this->get_post();

		// Test if this is a post.
		$this->assertTrue( $post instanceof WP_Post );

		// Assign Taxonomies.
		$department_one = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTMENT_TAXONOMY,
				'name'     => 'Test Department',
			]
		);
		$department_two = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTMENT_TAXONOMY,
				'name'     => 'Test Department 2',
			]
		);

		// Assert the taxonomies are created.
		$this->assertTrue( $department_one instanceof WP_Term );
		$this->assertTrue( $department_two instanceof WP_Term );

		// Assign taxonomies to the post.
		wp_set_post_terms( $post->ID, [ $department_one->term_id, $department_two->term_id ], DEPARTMENT_TAXONOMY );

		// Assert the department.
		$this->assertEquals(
			[
				[
					'term_id'     => strval( $department_one->term_id ),
					'name'        => $department_one->name,
					'slug'        => $department_one->slug,
					'term_group'  => $department_one->term_group,
					'taxonomy'    => DEPARTMENT_TAXONOMY,
					'description' => $department_one->description,
					'parent'      => $department_one->parent,
				],
				[
					'term_id'     => strval( $department_two->term_id ),
					'name'        => $department_two->name,
					'slug'        => $department_two->slug,
					'term_group'  => $department_two->term_group,
					'taxonomy'    => DEPARTMENT_TAXONOMY,
					'description' => $department_two->description,
					'parent'      => $department_two->parent,
				],
			],
			get_departments( $post->ID )
		);

		// Clean up.
		wp_delete_post( $post->ID, true );
		wp_delete_term( $department_one->term_id, DEPARTMENT_TAXONOMY );
		wp_delete_term( $department_two->term_id, DEPARTMENT_TAXONOMY );
	}

	/**
	 * Test get roles function.
	 *
	 * @covers \Quark\StaffMembers\get_roles()
	 *
	 * @return void
	 */
	public function test_get_roles(): void {
		// Create a post.
		$post = $this->get_post();

		// Test if this is a post.
		$this->assertTrue( $post instanceof WP_Post );

		// Assign Taxonomies.
		$role_one = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_STAFF_ROLE_TAXONOMY,
				'name'     => 'Test Role',
			]
		);
		$role_two = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_STAFF_ROLE_TAXONOMY,
				'name'     => 'Test Role 2',
			]
		);

		// Assert the taxonomies are created.
		$this->assertTrue( $role_one instanceof WP_Term );
		$this->assertTrue( $role_two instanceof WP_Term );

		// Assign taxonomies to the post.
		wp_set_post_terms( $post->ID, [ $role_one->term_id, $role_two->term_id ], DEPARTURE_STAFF_ROLE_TAXONOMY );

		// Assert the department.
		$this->assertEquals(
			[
				[
					'term_id'     => strval( $role_one->term_id ),
					'name'        => $role_one->name,
					'slug'        => $role_one->slug,
					'term_group'  => $role_one->term_group,
					'taxonomy'    => DEPARTURE_STAFF_ROLE_TAXONOMY,
					'description' => $role_one->description,
					'parent'      => $role_one->parent,
				],
				[
					'term_id'     => strval( $role_two->term_id ),
					'name'        => $role_two->name,
					'slug'        => $role_two->slug,
					'term_group'  => $role_two->term_group,
					'taxonomy'    => DEPARTURE_STAFF_ROLE_TAXONOMY,
					'description' => $role_two->description,
					'parent'      => $role_two->parent,
				],
			],
			get_roles( $post->ID )
		);

		// Clean up.
		wp_delete_post( $post->ID, true );
		wp_delete_term( $role_one->term_id, DEPARTURE_STAFF_ROLE_TAXONOMY );
		wp_delete_term( $role_two->term_id, DEPARTURE_STAFF_ROLE_TAXONOMY );
	}

	/**
	 * Test get staff members breadcrumbs.
	 *
	 * @covers \Quark\StaffMembers\get_breadcrumbs_ancestors()
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
		update_option( 'options_staff_members_page', $page->ID );

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
	 * Test get structured data function.
	 *
	 * @covers \Quark\StaffMembers\get_structured_data()
	 *
	 * @return void
	 */
	public function test_get_structured_data(): void {
		// Create a post.
		$post = $this->get_post();

		// Test if this is a post.
		$this->assertTrue( $post instanceof WP_Post );

		// Assign Taxonomies.
		$department = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTMENT_TAXONOMY,
				'name'     => 'Test Department',
			]
		);
		$role_one   = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_STAFF_ROLE_TAXONOMY,
				'name'     => 'Test Role',
			]
		);
		$role_two   = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_STAFF_ROLE_TAXONOMY,
				'name'     => 'Test Role 2',
			]
		);

		// Assert the taxonomies are created.
		$this->assertTrue( $department instanceof WP_Term );
		$this->assertTrue( $role_one instanceof WP_Term );
		$this->assertTrue( $role_two instanceof WP_Term );

		// Assign taxonomies to the post.
		wp_set_post_terms( $post->ID, [ $department->term_id ], DEPARTMENT_TAXONOMY );
		wp_set_post_terms( $post->ID, [ $role_two->term_id, $role_one->term_id ], DEPARTURE_STAFF_ROLE_TAXONOMY );

		// Add role post meta.
		update_post_meta( $post->ID, 'first_name', 'Dummy' );
		update_post_meta( $post->ID, 'last_name', 'Member' );

		// Get role.
		$role = get_roles( $post->ID );
		$role = $role[0]['name'];

		// Prepare the expected structured data.
		$expected = [
			'@context'    => 'https://schema.org',
			'@type'       => 'Person',
			'name'        => 'Dummy Member',
			'jobTitle'    => $role,
			'affiliation' => [
				'@type' => 'Organization',
				'name'  => 'Employee',
			],
			'url'         => 'http://test.quarkexpeditions.com/staff/test-post',
		];

		// Assert the structured data.
		$this->assertEquals(
			$expected,
			get_structured_data( $post->ID )
		);
	}

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\StaffMembers\translate_meta_keys()
	 *
	 * @return void
	 */
	public function test_translate_meta_keys(): void {
		// Input data.
		$input = [
			'meta_key' => 'string',
			'icon'     => 'attachment',
		];

		// Assert data.
		$this->assertEquals(
			[
				'meta_key'             => 'string',
				'icon'                 => 'attachment',
				'job_title'            => 'string',
				'first_name'           => 'string',
				'last_name'            => 'string',
				'hometown'             => 'string',
				'countries_travelled'  => 'post',
				'favorite_destination' => 'post',
			],
			translate_meta_keys( $input )
		);
	}
}
