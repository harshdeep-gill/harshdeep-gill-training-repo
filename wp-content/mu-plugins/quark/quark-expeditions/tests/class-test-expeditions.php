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

use function Quark\Expeditions\bust_post_cache;
use function Quark\Expeditions\get;
use function Quark\Expeditions\get_region_terms;
use function Quark\Expeditions\get_itineraries;
use function Quark\Expeditions\get_minimum_duration;
use function Quark\Expeditions\get_starting_from_locations;
use function Quark\Expeditions\get_details_data;
use function Quark\Expeditions\get_expedition_category_terms;
use function Quark\Expeditions\get_expedition_ship_ids;
use function Quark\Expeditions\get_formatted_date_range;
use function Quark\Expeditions\get_minimum_duration_itinerary;
use function Quark\Expeditions\get_total_departures;
use function Quark\Expeditions\get_ships;
use function Quark\Expeditions\get_breadcrumbs_ancestors;
use function Quark\Expeditions\get_seo_structured_data;
use function Quark\Expeditions\get_starting_from_price;
use function Quark\Expeditions\translate_meta_keys;
use function Quark\Expeditions\translate_term_meta_keys;
use function Quark\Localization\set_current_currency;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\do_sync;

use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE;
use const Quark\Expeditions\EXPEDITION_CATEGORY_TAXONOMY;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\CACHE_GROUP;
use const Quark\Expeditions\CACHE_KEY;
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

		// Bust post cache.
		bust_post_cache( $post_1->ID );

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
				'data'            => [],
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

		// Bust post cache.
		bust_post_cache( $post_1->ID );

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
	 * Test get_expedition_category_terms.
	 *
	 * @covers \Quark\Expeditions\get_expedition_category_terms()
	 *
	 * @return void
	 */
	public function test_get_expedition_category_terms(): void {
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
				'taxonomy' => EXPEDITION_CATEGORY_TAXONOMY,
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $term_1 instanceof WP_Term );

		// Create another term.
		$term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => EXPEDITION_CATEGORY_TAXONOMY,
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
			EXPEDITION_CATEGORY_TAXONOMY
		);

		// Bust post cache.
		bust_post_cache( $post_1->ID );

		// Test getting expedition categories.
		$expedition_categories = get_expedition_category_terms( $post_1->ID );

		// Assert expedition categories are correct.
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
			$expedition_categories
		);
	}

	/**
	 * Test getting Itineraries.
	 *
	 * @covers \Quark\Expeditions\get_itineraries()
	 * @covers \Quark\Expeditions\get_minimum_duration()
	 * @covers \Quark\Expeditions\get_starting_from_locations()
	 * @covers \Quark\Expeditions\get_starting_from_price()
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
					'key'     => 'softrip_package_code',
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
				'post_title'   => 'Test Post: Sub title',
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

		// bust post cache.
		bust_post_cache( $post_1->ID );

		// Get get_details_data().
		$expedition_details_card_data = get_details_data( $post_1->ID );

		// Assert expedition_details_card_data is correct.
		$expected_data = [
			'title'            => 'Test Post',
			'sub_title'        => 'Sub title',
			'region'           => $term_1->name,
			'duration'         => 11,
			'from_price'       => [
				'original'   => '',
				'discounted' => '',
			],
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

		// Flush cache.
		wp_cache_flush();

		// Get get_details_data.
		$expedition_details_card_data = get_details_data( $post_1->ID );

		// Update expected data with softrip sync data.
		$expected_data['from_price']       = [
			'original'   => '$34,895 USD',
			'discounted' => '$26,171 USD',
		];
		$expected_data['total_departures'] = 3;
		$expected_data['date_range']       = 'between January 2025 to March 2026';

		// Get ship posts with meta 'ship_code' is 'OEX' and 'ULT'.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'order'          => 'ASC',
				'orderby'        => 'title',
				'meta_query'     => [
					[
						'key'     => 'ship_code',
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
		$this->assertEqualsCanonicalizing( $expected_data, $expedition_details_card_data );

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
					'ship_code' => 'ABC123',
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
					'related_ship' => $ship_post->ID,
					'ship_code'    => 'ABC123',
					'softrip_id'   => 'ABC-123',
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
	 * Test Getting formatted date range.
	 *
	 * @covers \Quark\Expeditions\get_formatted_date_range()
	 * @covers \Quark\Expeditions\get_starting_from_date()
	 * @covers \Quark\Expeditions\get_ending_to_date()
	 *
	 * @return void
	 */
	public function test_get_formatted_date_range(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync departure posts.
		do_sync();

		// Get Itinerary posts.
		$itinerary_query_args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'meta_query'             => [
				[
					'key'   => 'softrip_package_code',
					'value' => 'ABC-123',
				],
			],
		];

		// Get Itinerary posts.
		$itinerary_posts = get_posts( $itinerary_query_args );

		// Assert fetched posts are not empty.
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Create an Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Expedition Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_itineraries' => $itinerary_posts,
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Get formatted date range.
		$date_range = get_formatted_date_range( $expedition_post->ID );

		// Assert date range is correct.
		$this->assertEquals( 'between February 2026 to March 2026', $date_range );

		// Get Departure posts.
		$departure_post_ids = get_departures_by_itinerary( absint( $itinerary_posts[0] ) );

		// Assert fetched post count is 1.
		$this->assertEquals( 1, count( $departure_post_ids ) );

		// Modify departure dates.
		update_post_meta( $departure_post_ids[0], 'start_date', '2026-02-01' );
		update_post_meta( $departure_post_ids[0], 'end_date', '2026-02-11' );

		// Get formatted date range.
		$date_range = get_formatted_date_range( $expedition_post->ID );

		// Assert date range is correct.
		$this->assertEquals( 'in February 2026', $date_range );

		// Update expedition meta.
		$itinerary_query_args['meta_query'] = [
			[
				'key'     => 'softrip_package_code',
				'value'   => [
					'ABC-123',
					'PQR-345',
					'JKL-012',
				],
				'compare' => 'IN',
			],
		];

		// Get Itinerary posts.
		$itinerary_posts = get_posts( $itinerary_query_args );

		// Assert fetched posts are not empty.
		$this->assertEquals( 3, count( $itinerary_posts ) );

		// Update expedition meta.
		update_post_meta( $expedition_post->ID, 'related_itineraries', $itinerary_posts );

		// bust post cache.
		bust_post_cache( $expedition_post->ID );

		// Get formatted date range.
		$date_range = get_formatted_date_range( $expedition_post->ID );

		// Assert date range is correct.
		$this->assertEquals( 'between January 2025 to February 2026', $date_range );
	}

	/**
	 * Test get_total_departures.
	 *
	 * @covers \Quark\Expeditions\get_total_departures()
	 *
	 * @return void
	 */
	public function test_get_total_departures(): void {
		// Create 3 Itineraries.
		$itinerary_post_ids = $this->factory()->post->create_many(
			3,
			[
				'post_type'   => ITINERARY_POST_TYPE,
				'post_status' => 'publish',
			]
		);

		// Create Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Expedition Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_itineraries' => $itinerary_post_ids,
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Set expected total departures.
		$expected_total = 0;

		// Create 18 Departure posts.
		// Assign 6 Departure posts to each Itinerary.
		foreach ( $itinerary_post_ids as $itinerary_post_id ) {
			$this->factory()->post->create_many(
				6,
				[
					'post_type'   => DEPARTURE_POST_TYPE,
					'post_status' => 'publish',
					'post_parent' => $itinerary_post_id,
					'meta_input'  => [
						'itinerary'          => $itinerary_post_id,
						'related_expedition' => $expedition_post->ID,
					],
				]
			);

			// Update expected total departures.
			$expected_total += 6;

			// Get total departures.
			$total_departures = get_total_departures( $expedition_post->ID );

			// Assert total departures is correct.
			$this->assertEquals( $expected_total, $total_departures );
		}

		// Test : Get total departures for an Expedition post with extra departures.
		// Create an itinerary post.
		$itinerary_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => ITINERARY_POST_TYPE,
				'post_title'  => 'Test Itinerary Post',
				'post_status' => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post instanceof WP_Post );

		// Create a Departure post with no related Expedition.
		$departure_post_no_expedition = $this->factory()->post->create_and_get(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_title'  => 'Test Departure Post',
				'post_status' => 'publish',
				'post_parent' => $itinerary_post->ID,
				'meta_input'  => [
					'itinerary' => $itinerary_post->ID,
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $departure_post_no_expedition instanceof WP_Post );

		// Get total departures.
		$this->assertEquals( $expected_total, get_total_departures( $expedition_post->ID ) );

		// Test: Get total departures for an Expedition post with no related Itineraries.
		$expedition_post_no_itineraries = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Expedition Post',
				'post_status' => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post_no_itineraries instanceof WP_Post );

		// Get total departures.
		$this->assertEmpty( get_total_departures( $expedition_post_no_itineraries->ID ) );
	}

	/**
	 * Test get_ships.
	 *
	 * @covers \Quark\Expeditions\get_ships()
	 *
	 * @return void
	 */
	public function test_get_ships(): void {
		// Add filter.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync departure posts.
		do_sync();

		// Create Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Expedition Post',
				'post_status' => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Get ships.
		$ships = get_ships();

		// Assert ships is correct.
		$this->assertEmpty( $ships );

		// Get Itineraries posts.
		$itinerary_query_args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'meta_query'             => [
				[
					'key'     => 'softrip_package_code',
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

		// Update expedition meta.
		update_post_meta( $expedition_post->ID, 'related_itineraries', $itinerary_posts );

		// Bust post cache.
		bust_post_cache( $expedition_post->ID );

		// Get ships.
		$ships = get_ships( $expedition_post->ID );

		// Assert ships is correct.
		$this->assertEquals( 2, count( $ships ) );
	}

	/**
	 * Test get_seo_structured_data.
	 *
	 * @covers \Quark\Expeditions\get_seo_structured_data()
	 *
	 * @return void
	 */
	public function test_get_seo_structured_data(): void {
		// Create Itinerary days post.
		$itinerary_1_day_1_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Test Itinerary 1 Day 1 Post',
				'post_status'  => 'publish',
				'post_content' => 'Itinerary 1 Day 1 content',
				'meta_input'   => [
					'day_number_from' => 1,
					'day_number_to'   => 1,
					'day_title'       => 'Exploring the Antarctic Peninsula',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_1_day_1_post instanceof WP_Post );

		// Create Itinerary days post.
		$itinerary_1_day_2_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Test Itinerary 1 Day 2 Post',
				'post_status'  => 'publish',
				'post_content' => 'Itinerary 1 Day 2 content',
				'meta_input'   => [
					'day_number_from' => 2,
					'day_number_to'   => 2,
					'day_title'       => 'Exploring the Antarctic Peninsula 2',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_1_day_2_post instanceof WP_Post );

		// Create Itinerary days post.
		$itinerary_2_day_1_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Test Itinerary 2 Day 1 Post',
				'post_status'  => 'publish',
				'post_content' => 'Itinerary 2 Day 1 content',
				'meta_input'   => [
					'day_number_from' => 1,
					'day_number_to'   => 1,
					'day_title'       => 'Exploring the Arctic Peninsula',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_2_day_1_post instanceof WP_Post );

		// Create Itinerary days post.
		$itinerary_2_day_2_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Test Itinerary 2 Day 2 to 4 Post',
				'post_content' => 'Itinerary 2 Day 2 to 4 content',
				'post_status'  => 'publish',
				'meta_input'   => [
					'day_number_from' => 2,
					'day_number_to'   => 4,
					'day_title'       => 'Exploring the Arctic Peninsula 2 to 4',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_2_day_2_post instanceof WP_Post );

		// Create Itinerary post.
		$itinerary_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => ITINERARY_POST_TYPE,
				'post_title'  => 'ARC-SHL-2D2024: Arctic Express: Spitsbergen',
				'post_status' => 'publish',
				'meta_input'  => [
					'softrip_package_code' => 'ABC-123',
					'duration_in_days'     => 2,
					'start_location'       => 'Longyearbyen',
					'itinerary_days'       => [ $itinerary_1_day_1_post->ID, $itinerary_1_day_2_post->ID ],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post instanceof WP_Post );

		// Create Itinerary post.
		$itinerary_post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'   => ITINERARY_POST_TYPE,
				'post_title'  => 'ARC-SHL-4D2025: Arctic Express: Spitsbergen 4',
				'post_status' => 'publish',
				'meta_input'  => [
					'softrip_package_code' => 'PQR-345',
					'duration_in_days'     => 4,
					'start_location'       => 'Longyearbyen',
					'itinerary_days'       => [ $itinerary_2_day_1_post->ID, $itinerary_2_day_2_post->ID ],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post_2 instanceof WP_Post );

		// Create Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_type'      => POST_TYPE,
				'post_title'     => 'Test Expedition Post',
				'post_status'    => 'publish',
				'post_content'   => 'Expedition content',
				'post_excerpt'   => 'Expedition excerpt',
				'featured_image' => 1,
				'meta_input'     => [
					'related_itineraries' => [ $itinerary_post->ID, $itinerary_post_2->ID ],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Assert SEO structured data is correct.
		$this->assertEquals(
			[
				[
					'@context'    => 'https://schema.org',
					'@type'       => 'Product',
					'name'        => 'Test Expedition Post',
					'description' => 'Expedition excerpt',
					'brand'       => 'Quark Expeditions',
					'url'         => get_permalink( $expedition_post->ID ),
					'image'       => get_the_post_thumbnail_url( $expedition_post->ID ),
				],
				[
					'@context'    => 'https://schema.org',
					'@type'       => 'TouristTrip',
					'name'        => 'Test Expedition Post',
					'description' => 'Expedition excerpt',
					'subTrip'     => [
						[
							'@type'     => 'Trip',
							'name'      => 'Arctic Express: Spitsbergen',
							'itinerary' => [
								[
									'@type'       => 'TouristAttraction',
									'name'        => 'Day 1: Exploring the Antarctic Peninsula',
									'description' => 'Itinerary 1 Day 1 content',
								],
								[
									'@type'       => 'TouristAttraction',
									'name'        => 'Day 2: Exploring the Antarctic Peninsula 2',
									'description' => 'Itinerary 1 Day 2 content',
								],
							],
						],
						[
							'@type'     => 'Trip',
							'name'      => 'Arctic Express: Spitsbergen 4',
							'itinerary' => [
								[
									'@type'       => 'TouristAttraction',
									'name'        => 'Day 1: Exploring the Arctic Peninsula',
									'description' => 'Itinerary 2 Day 1 content',
								],
								[
									'@type'       => 'TouristAttraction',
									'name'        => 'Day 2 to 4: Exploring the Arctic Peninsula 2 to 4',
									'description' => 'Itinerary 2 Day 2 to 4 content',
								],
							],
						],
					],
				],
			],
			get_seo_structured_data( $expedition_post->ID )
		);

		// Cleanup.
		wp_delete_post( $expedition_post->ID, true );
		wp_delete_post( $itinerary_post->ID, true );
		wp_delete_post( $itinerary_post_2->ID, true );
		wp_delete_post( $itinerary_1_day_1_post->ID, true );
		wp_delete_post( $itinerary_1_day_2_post->ID, true );
		wp_delete_post( $itinerary_2_day_1_post->ID, true );
		wp_delete_post( $itinerary_2_day_2_post->ID, true );
	}

	/**
	 * Test get_minimum_duration_itinerary.
	 *
	 * @covers \Quark\Expeditions\get_minimum_duration_itinerary()
	 *
	 * @return void
	 */
	public function test_get_minimun_duration_itinerary(): void {
		// Create Itinerary posts.
		$itinerary_post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 1',
				'post_content' => 'Post content 1',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '10',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post_1 instanceof WP_Post );

		// Create Itinerary posts.
		$itinerary_post_2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 2',
				'post_content' => 'Post content 2',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '5',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post_2 instanceof WP_Post );

		// Create Itinerary posts.
		$itinerary_post_3 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post 3',
				'post_content' => 'Post content 3',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'duration_in_days' => '15',
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $itinerary_post_3 instanceof WP_Post );

		// Create Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Expedition Post',
				'post_content' => 'Expedition content',
				'post_status'  => 'publish',
				'post_type'    => POST_TYPE,
				'meta_input'   => [
					'related_itineraries' => [
						$itinerary_post_1->ID,
						$itinerary_post_2->ID,
						$itinerary_post_3->ID,
					],
				],
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Assert the function returns the correct minimum duration itinerary post.
		$this->assertEquals(
			$itinerary_post_2,
			get_minimum_duration_itinerary( $expedition_post->ID )
		);

		// Cleanup.
		wp_delete_post( $itinerary_post_1->ID, true );
		wp_delete_post( $itinerary_post_2->ID, true );
		wp_delete_post( $itinerary_post_3->ID, true );
		wp_delete_post( $expedition_post->ID, true );
	}

	/**
	 * Test get breadcrumbs ancestors.
	 *
	 * @covers \Quark\Expeditions\get_breadcrumbs_ancestors()
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
		update_option( 'options_expeditions_page', $page->ID );

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
	}

	/**
	 * Test get starting price.
	 *
	 * @covers \Quark\Expeditions\get_starting_from_price()
	 *
	 * @return void
	 */
	public function test_get_starting_from_price(): void {
		// Default lowest price.
		$default_lowest_price = [
			'discounted' => 0,
			'original'   => 0,
		];

		// Cache prefix.
		$cache_prefix = CACHE_KEY . '_starting_from_price_';

		// Test without any post id.
		$this->assertEquals( $default_lowest_price, get_starting_from_price() );

		// Invalid post id.
		$this->assertEquals( $default_lowest_price, get_starting_from_price( 0 ) );

		// Non-expedition post.
		$this->assertEquals( $default_lowest_price, get_starting_from_price( 1938 ) );
		$cache_key = $cache_prefix . '1938';
		$this->assertFalse( wp_cache_get( $cache_key, CACHE_GROUP ) );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );

		// Get one expedition.
		$expedition_posts = get_posts(
			[
				'post_type'      => POST_TYPE,
				'posts_per_page' => 5,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			]
		);
		$this->assertNotEmpty( $expedition_posts );

		// Convert to integer.
		$expedition_posts = array_map( 'absint', $expedition_posts );

		// First expedition.
		$expedition_post1 = $expedition_posts[0];

		// Get starting price.
		$starting_price = get_starting_from_price( $expedition_post1 );
		$this->assertEmpty( $starting_price['discounted'] );
		$this->assertEmpty( $starting_price['original'] );

		// Second expedition.
		$expedition_post2 = $expedition_posts[1];

		// Get starting price USD.
		$starting_price = get_starting_from_price( $expedition_post2 );
		$this->assertEquals( '34600', $starting_price['original'] );
		$this->assertEquals( '29410', $starting_price['discounted'] );

		// Get starting price EUR.
		set_current_currency( 'EUR' );
		$starting_price = get_starting_from_price( $expedition_post2 );
		$this->assertEquals( '32200', $starting_price['original'] );
		$this->assertEquals( '27370', $starting_price['discounted'] );

		// Get starting price GBP.
		set_current_currency( 'GBP' );
		$starting_price = get_starting_from_price( $expedition_post2 );
		$this->assertEquals( '27600', $starting_price['original'] );
		$this->assertEquals( '23460', $starting_price['discounted'] );

		// Get starting price AUD.
		set_current_currency( 'AUD' );
		$starting_price = get_starting_from_price( $expedition_post2 );
		$this->assertEquals( '54200', $starting_price['original'] );
		$this->assertEquals( '46070', $starting_price['discounted'] );

		// Get starting price CAD.
		set_current_currency( 'CAD' );
		$starting_price = get_starting_from_price( $expedition_post2 );
		$this->assertEquals( '47000', $starting_price['original'] );
		$this->assertEquals( '39950', $starting_price['discounted'] );

		// Reset currency.
		set_current_currency( 'USD' );
	}

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\Expeditions\translate_meta_keys()
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
				'meta_key'                  => 'string',
				'icon'                      => 'attachment',
				'overview'                  => 'text',
				'included_activities'       => 'Quark\Expeditions\translate_meta_key',
				'related_adventure_options' => 'Quark\Expeditions\translate_meta_key',
				'related_pre_post_trips'    => 'Quark\Expeditions\translate_meta_key',
				'related_itineraries'       => 'Quark\Expeditions\translate_meta_key',
			],
			translate_meta_keys( $input )
		);
	}

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\Expeditions\translate_term_meta_keys()
	 *
	 * @return void
	 */
	public function test_translate_term_meta_keys(): void {
		// Input data.
		$input = [
			'meta_key' => 'string',
			'icon'     => 'attachment',
		];

		// Assert data.
		$this->assertEquals(
			[
				'meta_key'          => 'string',
				'icon'              => 'attachment',
				'destination_image' => 'attachment',
			],
			translate_term_meta_keys( $input )
		);
	}
}
