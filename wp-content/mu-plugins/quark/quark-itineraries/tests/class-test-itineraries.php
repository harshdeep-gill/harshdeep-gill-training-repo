<?php
/**
 * Itineraries test suite.
 *
 * @package quark-itineraries
 */

namespace Quark\Itineraries;

use WP_Post;
use WP_Term;
use WP_UnitTestCase;

use const Quark\StaffMembers\SEASON_TAXONOMY;

/**
 * Class Test_Core.
 */
class Test_Itineraries extends WP_UnitTestCase {

	/**
	 * Test get_season function.
	 *
	 * @covers get_season
	 *
	 * @return void
	 */
	public function test_get_season(): void {
		// Create post.
		$post = $this->factory()->post->create_and_get(
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

		// Check if post was created.
		$this->assertTrue( $post instanceof WP_Post );

		// Create term.
		$season = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => 'Test Term',
			]
		);

		// Check if term was created.
		$this->assertTrue( $season instanceof WP_Term );

		// Assign term to post.
		wp_set_post_terms( $post->ID, [ $season->term_id ], SEASON_TAXONOMY );

		// Test getting trip group.
		$this->assertEquals(
			[
				'term_id'     => $season->term_id,
				'name'        => $season->name,
				'slug'        => $season->slug,
				'term_group'  => $season->term_group,
				'taxonomy'    => $season->taxonomy,
				'description' => $season->description,
				'parent'      => $season->parent,
			],
			get_season( $post->ID )
		);
	}

	/**
	 * Test get_details_tabs_data function.
	 *
	 * @covers get_details_tabs_data
	 *
	 * @return void
	 */
	public function test_get_details_tabs_data(): void {
		// Create post.
		$itinerary_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post A',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '10',
					'meta_2' => 'value_2',
				],
			]
		);

		// Check if post was created.
		$this->assertTrue( $itinerary_1 instanceof WP_Post );

		// Create post.
		$itinerary_2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post B',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '11',
					'meta_2' => 'value_2',
				],
			]
		);

		// Check if post was created.
		$this->assertTrue( $itinerary_2 instanceof WP_Post );

		// Create term.
		$season_2022 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => '2022',
				'slug'     => '2022',
			]
		);

		// Check if term was created.
		$this->assertTrue( $season_2022 instanceof WP_Term );

		// Create term.
		$season_2023 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => '2023',
				'slug'     => '2023',
			]
		);

		// Check if term was created.
		$this->assertTrue( $season_2023 instanceof WP_Term );

		// Create term.
		$departure_location = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
				'name'     => 'Test Departure Location',
				'slug'     => 'test-departure-location',
			]
		);

		// Check if term was created.
		$this->assertTrue( $departure_location instanceof WP_Term );

		// Create departure post.
		$departure = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\Departures\POST_TYPE,
				'post_parent'  => $itinerary_1->ID,
				'meta_input'   => [
					'' => '123',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assign term to post.
		wp_set_post_terms( $itinerary_1->ID, [ $season_2022->term_id ], SEASON_TAXONOMY );
		wp_set_post_terms( $itinerary_2->ID, [ $season_2023->term_id ], SEASON_TAXONOMY );
		wp_set_post_terms( $itinerary_1->ID, [ $departure_location->term_id ], DEPARTURE_LOCATION_TAXONOMY );
		wp_set_post_terms( $itinerary_2->ID, [ $departure_location->term_id ], DEPARTURE_LOCATION_TAXONOMY );


		get_details_tabs_data( [ $itinerary_1->ID, $itinerary_2->ID ] );

	}
}
