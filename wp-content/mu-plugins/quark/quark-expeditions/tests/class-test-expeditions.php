<?php
/**
 * Expeditions test suite.
 *
 * @package quark-expeditions
 */

namespace Quark\Expeditions\Tests;

use WP_Post;
use WP_Term;
use WP_UnitTestCase;

use function Quark\Expeditions\get;
use function Quark\Expeditions\get_region_terms;
use function Quark\Expeditions\get_itineraries;
use function Quark\Expeditions\get_minimum_duration;
use function Quark\Expeditions\get_starting_from_locations;
use function Quark\Expeditions\get_details_data;
use function Quark\Expeditions\get_expedition_ship_ids;
use function Quark\Expeditions\organise_desination_terms;

use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE;
use const Quark\Expeditions\EXPEDITION_CATEGORY_TAXONOMY;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAY_POST_TYPE;

/**
 * Class Test_Expeditions.
 */
class Test_Expeditions extends WP_UnitTestCase {
	/**
	 * Test getting an expedition.
	 *
	 * @covers \Quark\Expeditions\get()
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

		// Create another post.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'meta_input'   => [
					'meta_1' => 'value_3',
					'meta_2' => 'value_4',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Create category terms.
		$category_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => EXPEDITION_CATEGORY_TAXONOMY,
			]
		);

		// Assert term is created.
		$this->assertTrue( $category_term instanceof WP_Term );

		// Set terms.
		wp_set_object_terms( $post_1->ID, $category_term->term_id, EXPEDITION_CATEGORY_TAXONOMY );

		// Test getting post.
		$the_post = get( $post_1->ID );

		// Assert post's expected permalink is correct is equal to actual permalink.
		$this->assertEquals(
			'http://test.quarkexpeditions.com/expeditions/test-post',
			$the_post['permalink']
		);

		// Assert post's post_taxonomies is correct.
		$this->assertEquals(
			[
				EXPEDITION_CATEGORY_TAXONOMY => [
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
			],
			$the_post['post_taxonomies']
		);

		// Assert post's meta is correct.
		$this->assertEquals(
			[
				'meta_1' => 'value_1',
				'meta_2' => 'value_2',
			],
			$the_post['post_meta']
		);

		// Test getting incorrect post.
		$this->assertEquals(
			[
				'post'            => null,
				'permalink'       => '',
				'post_meta'       => [],
				'post_taxonomies' => [],
			],
			get( $post_2->ID )
		);
	}

	/**
	 * Test get_region_terms.
	 *
	 * @covers \Quark\Expeditions\get_region_terms()
	 *
	 * @return void
	 */
	public function test_get_region_terms(): void {
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

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Create term.
		$term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $term_1 instanceof WP_Term );

		// Create another term.
		$term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $term_1->term_id,
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $term_2 instanceof WP_Term );

		// Set terms.
		wp_set_object_terms(
			$post_1->ID,
			[
				$term_1->term_id,
				$term_2->term_id,
			],
			DESTINATION_TAXONOMY
		);

		// Test getting regions.
		$regions = get_region_terms( $post_1->ID );

		// Assert regions is correct.
		$this->assertEquals(
			[
				[
					'term_id'     => strval( $term_1->term_id ),
					'name'        => $term_1->name,
					'slug'        => $term_1->slug,
					'taxonomy'    => $term_1->taxonomy,
					'description' => $term_1->description,
					'parent'      => $term_1->parent,
					'term_group'  => $term_1->term_group,
				],
			],
			$regions
		);
	}

	/**
	 * Test getting Itineraries.
	 *
	 * @covers \Quark\Expeditions\get_itineraries()
	 * @covers \Quark\Expeditions\get_minimum_duration()
	 * @covers \Quark\Expeditions\get_starting_from_locations()
	 * @covers \Quark\Expeditions\get_details_data()
	 *
	 * @return void
	 */
	public function test_get_details_data(): void {
		// Create term DEPARTURE_LOCATION_TAXONOMY.
		$departure_location_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
			]
		);

		// Create term DEPARTURE_LOCATION_TAXONOMY.
		$departure_location_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $departure_location_term_1 instanceof WP_Term );
		$this->assertTrue( $departure_location_term_2 instanceof WP_Term );

		// Create Itinerary post.
		$itinerary_post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '12',
					'start_location'   => $departure_location_term_1->term_id,
				],
			]
		);

		// Create Itinerary post.
		$itinerary_post_2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 2',
				'post_content' => 'Post content 2',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '11',
					'start_location'   => $departure_location_term_2->term_id,
				],
			]
		);

		// Create another Itinerary post with draft status.
		$itinerary_post_3 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 3',
				'post_content' => 'Post content 3',
				'post_status'  => 'draft',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '5',
					'start_location'   => $departure_location_term_1->term_id,
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post_1 instanceof WP_Post );
		$this->assertTrue( $itinerary_post_2 instanceof WP_Post );
		$this->assertTrue( $itinerary_post_3 instanceof WP_Post );

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'meta_1'              => 'value_1',
					'meta_2'              => 'value_2',
					'related_itineraries' => [
						$itinerary_post_1->ID,
						$itinerary_post_2->ID,
						$itinerary_post_3->ID,
					],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Create term.
		$term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $term_1 instanceof WP_Term );

		// Set terms.
		wp_set_object_terms( $post_1->ID, $term_1->term_id, DESTINATION_TAXONOMY );

		// Get itineraries.
		$itineraries = get_itineraries( $post_1->ID );

		// Assert itineraries is correct.
		$this->assertEquals( 2, count( $itineraries ) );

		// Assert itineraries is correct.
		$itinerary_post_ids = [];

		// Assert itineraries is correct.
		foreach ( $itineraries as $itinerary ) {
			// Assert itinerary is instance of WP_Post.
			$this->assertTrue( $itinerary['post'] instanceof WP_Post );
			$itinerary_post_ids[] = $itinerary['post']->ID;
		}

		// Assert draft itineraries not included.
		$this->assertNotContains( $itinerary_post_3->ID, $itinerary_post_ids );

		// Get get_minimum_duration.
		$minimum_duration = get_minimum_duration( $post_1->ID );

		// Assert minimum duration is correct.
		$this->assertEquals( 11, $minimum_duration );

		// Get start_location.
		$start_location = get_starting_from_locations( $post_1->ID );

		// Assert start_location is correct.
		$this->assertEquals(
			[
				[
					'title' => $departure_location_term_1->name,
				],
				[
					'title' => $departure_location_term_2->name,
				],
			],
			$start_location
		);

		// Get get_details_data().
		$expedition_details_card_data = get_details_data( $post_1->ID );

		// Assert expedition_details_card_data is correct.
		$expected_data = [
			'title'            => $post_1->post_title,
			'region'           => $term_1->name,
			'duration'         => 11,
			'from_price'       => '',
			'starting_from'    => [
				[
					'title' => $departure_location_term_1->name,
				],
				[
					'title' => $departure_location_term_2->name,
				],
			],
			'total_departures' => 0,
		];

		// Assert expedition_details_card_data is correct.
		$this->assertEquals( $expected_data, $expedition_details_card_data );
	}

	/**
	 * Test getting expedition ship IDs.
	 *
	 * @covers \Quark\Expeditions\get_expedition_ship_ids()
	 *
	 * @return void
	 */
	public function test_get_expedition_ship_ids(): void {
		// Create Ship Post.
		$ship_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Ship',
				'post_content' => 'Ship content',
				'post_status'  => 'publish',
				'post_type'    => SHIP_POST_TYPE,
				'meta_input'   => [
					'ship_id' => 'ABC123',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $ship_post instanceof WP_Post );

		// Create Itinerary post.
		$itinerary_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post instanceof WP_Post );

		// Create Itinerary day post.
		$itinerary_day_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary Day',
				'post_content' => 'Itinerary Day content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_parent'  => $itinerary_post->ID,
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_day_post instanceof WP_Post );

		// Create a departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Departure content',
				'post_status'  => 'publish',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'related_ship'        => $ship_post,
					'ship_id'             => 'ABC123',
					'departure_unique_id' => 'ABC-123',
				],
				'post_parent'  => $itinerary_post->ID,
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Create Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Expedition',
				'post_content' => 'Expedition content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'related_itineraries' => [
						$itinerary_post->ID,
					],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Assert the function returns the correct ship ID.
		$this->assertEquals(
			[ $ship_post->ID ],
			get_expedition_ship_ids( $expedition_post->ID )
		);
	}

	/**
	 * Test organise_desination_terms.
	 *
	 * @covers \Quark\Expeditions\organise_desination_terms()
	 *
	 * @return void
	 */
	public function test_organise_desination_terms(): void {
		// Create parent term 1.
		$parent_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
			]
		);
		$this->assertTrue( $parent_term_1 instanceof WP_Term );

		// Create parent term 2.
		$parent_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
			]
		);
		$this->assertTrue( $parent_term_2 instanceof WP_Term );

		// Create child term 1.
		$child_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $parent_term_1->term_id,
			]
		);
		$this->assertTrue( $child_term_1 instanceof WP_Term );

		// Create child term 2.
		$child_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $parent_term_1->term_id,
			]
		);
		$this->assertTrue( $child_term_2 instanceof WP_Term );

		// Create child term 3.
		$child_term_3 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $parent_term_2->term_id,
			]
		);
		$this->assertTrue( $child_term_3 instanceof WP_Term );

		// Assign the child terms to the parent terms.
		wp_set_object_terms(
			$parent_term_1->term_id,
			[
				$child_term_1->term_id,
				$child_term_2->term_id,
			],
			DESTINATION_TAXONOMY
		);
		wp_set_object_terms(
			$parent_term_2->term_id,
			[
				$child_term_3->term_id,
			],
			DESTINATION_TAXONOMY
		);

		// Assert the function returns the correct organised terms.
		$this->assertEquals(
			[
				$parent_term_1->term_id => [
					'parent_term' => $parent_term_1,
					'child_terms' => [
						$child_term_1,
						$child_term_2,
					],
				],
				$parent_term_2->term_id => [
					'parent_term' => $parent_term_2,
					'child_terms' => [
						$child_term_3,
					],
				],
			],
			organise_desination_terms(
				[
					$parent_term_1->term_id,
					$parent_term_2->term_id,
					$child_term_1->term_id,
					$child_term_2->term_id,
					$child_term_3->term_id,
				]
			)
		);
	}
}
