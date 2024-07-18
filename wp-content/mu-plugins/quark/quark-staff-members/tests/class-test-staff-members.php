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

		// Add featured image.
		$attachment_id = $this->factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/test-image.png', 0 );

		// Assert the attachment is created.
		$this->assertIsInt( $attachment_id );

		// Set the attachment as the featured image for the post.
		set_post_thumbnail( $post->ID, $attachment_id );

		// Assert the post data.
		$this->assertEquals(
			[
				'post'            => $post,
				'permalink'       => get_permalink( $post ),
				'post_thumbnail'  => $attachment_id,
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
		wp_delete_attachment( $attachment_id, true );
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

		// Create a test media attachment.
		$attachment_id = $this->factory()->attachment->create_upload_object( DIR_TESTDATA . '/images/test-image.png', 0 );

		// Assert the attachment is created.
		$this->assertIsInt( $attachment_id );

		// Set the attachment as the featured image for the posts.
		set_post_thumbnail( $staff_member_1->ID, $attachment_id );
		set_post_thumbnail( $staff_member_2->ID, $attachment_id );
		set_post_thumbnail( $staff_member_3->ID, $attachment_id );

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
					'featured_image' => $attachment_id,
					'role'           => 'Test Role 1',
					'season'         => $season_1->name,
				],
				[
					'title'          => $staff_member_2->post_title,
					'permalink'      => get_permalink( $staff_member_2->ID ),
					'featured_image' => $attachment_id,
					'role'           => 'Test Role 2',
					'season'         => $season_2->name,
				],
				[
					'title'          => $staff_member_3->post_title,
					'permalink'      => get_permalink( $staff_member_3->ID ),
					'featured_image' => $attachment_id,
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
		wp_delete_attachment( $attachment_id, true );
		wp_delete_term( $season_1->term_id, 'qrk_season' );
		wp_delete_term( $season_2->term_id, 'qrk_season' );
	}
}
