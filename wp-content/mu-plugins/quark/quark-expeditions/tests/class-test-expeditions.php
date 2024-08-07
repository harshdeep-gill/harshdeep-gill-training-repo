<?php
/**
 * Expeditions test suite.
 *
 * @package quark-expeditions
 */

namespace Quark\Expeditions\Tests;

use WP_Post;
use WP_Term;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Expeditions\get;
use function Quark\Expeditions\get_region_terms;
use function Quark\Expeditions\get_itineraries;
use function Quark\Expeditions\get_minimum_duration;
use function Quark\Expeditions\get_starting_from_locations;
use function Quark\Expeditions\get_details_data;
use function Quark\Expeditions\get_expedition_ship_ids;
use function Quark\Softrip\do_sync;

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
class Test_Expeditions extends Softrip_TestCase {
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

		// Create Itinerary posts.
		$itinerary_query_args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'meta_query'             => [
				[
					'key'     => 'softrip_package_id',
					'value'   => [
						'ABC-123',
						'PQR-345',
						'JKL-012',
					],
					'compare' => 'IN',
				],
			],
		];

		// Get Itinerary posts.
		$itinerary_posts = get_posts( $itinerary_query_args );

		// Assert fetched posts are not empty.
		$this->assertEquals( 3, count( $itinerary_posts ) );

		// Create another Itinerary post with draft status.
		$itinerary_post_4 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 3',
				'post_content' => 'Post content 3',
				'post_status'  => 'draft',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '5',
					'start_location'   => $departure_location_term_1->term_id,
				],
			],
		);

		// Assert fetched posts are not empty.
		$this->assertIsInt( $itinerary_posts[0] );
		$this->assertIsInt( $itinerary_posts[1] );
		$this->assertIsInt( $itinerary_posts[2] );

		// Assert $itinerary_post_4 is instance of WP_Post.
		$this->assertTrue( $itinerary_post_4 instanceof WP_Post );

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
						$itinerary_posts[0],
						$itinerary_posts[1],
						$itinerary_posts[2],
						$itinerary_post_4->ID,
					],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Add duration_in_days meta to Itinerary posts.
		update_post_meta( $itinerary_posts[0], 'duration_in_days', 12 );
		update_post_meta( $itinerary_posts[1], 'duration_in_days', 11 );
		update_post_meta( $itinerary_posts[2], 'duration_in_days', 16 );

		// Add start_location meta to Itinerary posts.
		update_post_meta( $itinerary_posts[0], 'start_location', $departure_location_term_1->term_id );
		update_post_meta( $itinerary_posts[1], 'start_location', $departure_location_term_2->term_id );
		update_post_meta( $itinerary_posts[2], 'start_location', $departure_location_term_1->term_id );

		// Set related_expedition meta to Itinerary posts.
		update_post_meta( $itinerary_posts[0], 'related_expedition', $post_1->ID );
		update_post_meta( $itinerary_posts[1], 'related_expedition', $post_1->ID );
		update_post_meta( $itinerary_posts[2], 'related_expedition', $post_1->ID );
		update_post_meta( $itinerary_post_4->ID, 'related_expedition', $post_1->ID );

		// Clear cache for itinerary posts.
		wp_cache_delete( ITINERARY_POST_TYPE . "_$itinerary_posts[0]", ITINERARY_POST_TYPE );
		wp_cache_delete( ITINERARY_POST_TYPE . "_$itinerary_posts[1]", ITINERARY_POST_TYPE );
		wp_cache_delete( ITINERARY_POST_TYPE . "_$itinerary_posts[2]", ITINERARY_POST_TYPE );

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
		$this->assertEquals( 3, count( $itineraries ) );

		// Assert itineraries is correct.
		$itinerary_post_ids = [];

		// Assert itineraries is correct.
		foreach ( $itineraries as $itinerary ) {
			// Assert itinerary is instance of WP_Post.
			$this->assertTrue( $itinerary['post'] instanceof WP_Post );
			$itinerary_post_ids[] = $itinerary['post']->ID;
		}

		// Assert draft itineraries not included.
		$this->assertNotContains( $itinerary_post_4->ID, $itinerary_post_ids );

		// Get get_minimum_duration.
		$minimum_duration = get_minimum_duration( $post_1->ID );

		// Assert minimum duration is correct.
		$this->assertEquals( 11, $minimum_duration );

		// Get start_location.
		$start_location = get_starting_from_locations( $post_1->ID );

		// Assert start_location is correct.
		$this->assertEquals(
			[
				$departure_location_term_1->term_id => [
					'title' => $departure_location_term_1->name,
				],
				$departure_location_term_2->term_id => [
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
				$departure_location_term_1->term_id => [
					'title' => $departure_location_term_1->name,
				],
				$departure_location_term_2->term_id => [
					'title' => $departure_location_term_2->name,
				],
			],
			'total_departures' => 0,
			'date_range'       => '',
		];

		// Assert expedition_details_card_data is correct.
		$this->assertEquals( $expected_data, $expedition_details_card_data );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with exising posts.
		do_sync();

		// Get get_details_data.
		$expedition_details_card_data = get_details_data( $post_1->ID );

		// Update expected data with softrip sync data.
		$expected_data['from_price']       = '$34,600 USD';
		$expected_data['total_departures'] = 3;
		$expected_data['date_range']       = 'between January 2025 to March 2026';

		// Get ship posts with meta 'ship_id' is 'OEX' and 'ULT'.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'     => 'ship_id',
						'value'   => [
							'OEX',
							'ULT',
						],
						'compare' => 'IN',
					],
				],
			]
		);

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_data['ships'][] = [
				'title' => $ship_post->post_title,
			];
		}

		// Assert expedition_details_card_data is correct.
		$this->assertEquals( $expected_data, $expedition_details_card_data );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
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
}
