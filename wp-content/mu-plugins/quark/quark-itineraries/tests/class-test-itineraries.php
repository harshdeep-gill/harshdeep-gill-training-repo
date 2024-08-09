<?php
/**
 * Itineraries test suite.
 *
 * @package quark-itineraries
 */

namespace Quark\Itineraries;

use WP_Post;
use WP_Term;
use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\do_sync;

use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\Ships\POST_TYPE as SHIPS_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAYS_POST_TYPE;

/**
 * Class Test_Core.
 */
class Test_Itineraries extends Softrip_TestCase {

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
		// Create Itinerary posts.
		$itinerary_posts = [];

		// Get Itinerary posts.
		$posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);

		// Assert fetched posts are not empty.
		$this->assertEquals( 1, count( $posts ) );

		// Merge fetched posts.
		$itinerary_posts = array_merge( $itinerary_posts, $posts );

		// Get Itinerary posts.
		$posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'PQR-345',
						'compare' => '=',
					],
				],
			]
		);

		// Assert fetched posts are not empty.
		$this->assertEquals( 1, count( $posts ) );

		// Merge fetched posts.
		$itinerary_posts = array_merge( $itinerary_posts, $posts );

		// Get Itinerary posts.
		$posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'JKL-012',
						'compare' => '=',
					],
				],
			]
		);

		// Assert fetched posts are not empty.
		$this->assertEquals( 1, count( $posts ) );

		// Merge fetched posts.
		$itinerary_posts = array_merge( $itinerary_posts, $posts );

		// Assert fetched posts are not empty.
		$this->assertEquals( 3, count( $itinerary_posts ) );

		// Extract post IDs.
		$itinerary_posts = array_column( $itinerary_posts, 'ID' );

		// Assert is array.
		$this->assertIsArray( $itinerary_posts );

		// Assert fetched posts are not empty.
		$this->assertIsInt( $itinerary_posts[0] );
		$this->assertIsInt( $itinerary_posts[1] );
		$this->assertIsInt( $itinerary_posts[2] );

		// Create Itinerary Day posts.
		$itinerary_day_one   = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day One',
				'post_content' => 'Day One content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title'       => 'A',
					'day_number_from' => 1,
					'day_number_to'   => 1,
				],
			]
		);
		$itinerary_day_two   = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day Two',
				'post_content' => 'Day Two content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title'       => 'B',
					'day_number_from' => 2,
					'day_number_to'   => 3,
				],
			]
		);
		$itinerary_day_three = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day Three',
				'post_content' => 'Day Three content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title'       => 'C',
					'day_number_from' => 4,
					'day_number_to'   => 6,
				],
			]
		);

		// Check if post was created.
		$this->assertTrue( $itinerary_day_one instanceof WP_Post );
		$this->assertTrue( $itinerary_day_two instanceof WP_Post );
		$this->assertTrue( $itinerary_day_three instanceof WP_Post );

		// Create term DEPARTURE_LOCATION_TAXONOMY.
		$departure_location_india = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
				'name'     => 'India',
			]
		);
		$departure_location_japan = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
				'name'     => 'Japan',
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $departure_location_india instanceof WP_Term );
		$this->assertTrue( $departure_location_japan instanceof WP_Term );

		// Create Season term.
		$season_2022 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => '2022',
				'slug'     => '2022',
			]
		);
		$season_2023 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => '2023',
				'slug'     => '2023',
			]
		);

		// Assert created terms are instance of WP_Term.
		$this->assertTrue( $season_2022 instanceof WP_Term );
		$this->assertTrue( $season_2023 instanceof WP_Term );

		// Assign Departures terms to posts.
		add_post_meta( $itinerary_posts[0], 'start_location', $departure_location_india->term_id );
		add_post_meta( $itinerary_posts[1], 'start_location', $departure_location_japan->term_id );
		add_post_meta( $itinerary_posts[2], 'start_location', $departure_location_india->term_id );

		// Assign Season terms to posts.
		wp_set_post_terms( $itinerary_posts[0], [ $season_2022->term_id ], SEASON_TAXONOMY );
		wp_set_post_terms( $itinerary_posts[1], [ $season_2023->term_id ], SEASON_TAXONOMY );
		wp_set_post_terms( $itinerary_posts[2], [ $season_2022->term_id ], SEASON_TAXONOMY );

		// Assign duration in days to posts.
		add_post_meta( $itinerary_posts[0], 'duration_in_days', 10 );
		add_post_meta( $itinerary_posts[1], 'duration_in_days', 11 );
		add_post_meta( $itinerary_posts[2], 'duration_in_days', 12 );

		// Assign itinerary days to posts.
		add_post_meta( $itinerary_posts[0], 'itinerary_days', [ $itinerary_day_one->ID, $itinerary_day_two->ID, $itinerary_day_three->ID ] );
		add_post_meta( $itinerary_posts[1], 'itinerary_days', [ $itinerary_day_one->ID, $itinerary_day_two->ID, $itinerary_day_three->ID ] );
		add_post_meta( $itinerary_posts[2], 'itinerary_days', [ $itinerary_day_one->ID, $itinerary_day_two->ID, $itinerary_day_three->ID ] );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with exising posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get ship posts with meta 'ship_code' is 'ULT'.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIPS_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'     => 'ship_code',
						'value'   => 'ULT',
						'compare' => '=',
					],
				],
			]
		);

		// Prepare expected ship data.
		$expected_ship_ult = [];

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_ship_ult[] = [
				'name' => $ship_post->post_title,
				'link' => get_permalink( $ship_post->ID ),
			];
		}

		// Get ship posts with meta 'ship_code' is 'OEX'.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIPS_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'     => 'ship_code',
						'value'   => 'OEX',
						'compare' => '=',
					],
				],
			]
		);

		// Prepare expected ship data.
		$expected_ship_oex = [];

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_ship_oex[] = [
				'name' => $ship_post->post_title,
				'link' => get_permalink( $ship_post->ID ),
			];
		}

		// Get details tabs data.
		$details = get_details_tabs_data( $itinerary_posts );

		// Prepare expected details.
		$expected_details = [
			'active_tab'       => 2023,
			'itinerary_groups' => [
				[
					'tab_id'      => 2023,
					'tab_title'   => '2023.24 Season',
					'active_tab'  => 'tab-2',
					'itineraries' => [
						[
							'tab_id'             => 'tab-2',
							'tab_title'          => '11 Days',
							'tab_subtitle'       => 'From Japan',
							'tab_content_header' => 'From Japan, 11 days',
							'duration'           => '11 days',
							'departing_from'     => 'Japan',
							'itinerary_days'     => [
								[
									'title'   => 'Day 1: A',
									'content' => 'Day One content',
								],
								[
									'title'   => 'Day 2 & 3: B',
									'content' => 'Day Two content',
								],
								[
									'title'   => 'Day 4 to 6: C',
									'content' => 'Day Three content',
								],
							],
							'map'                => 0,
							'price'              => '',
							'brochure'           => '',
							'ships'              => [],
						],
					],
				],
				[
					'tab_id'      => 2022,
					'tab_title'   => '2022.23 Season',
					'active_tab'  => 'tab-1',
					'itineraries' => [
						[
							'tab_id'             => 'tab-1',
							'tab_title'          => '10 Days',
							'tab_subtitle'       => 'From India',
							'tab_content_header' => 'From India, 10 days, on ' . $expected_ship_oex[0]['name'],
							'duration'           => '10 days',
							'departing_from'     => $departure_location_india->name,
							'itinerary_days'     => [
								[
									'title'   => 'Day 1: A',
									'content' => 'Day One content',
								],
								[
									'title'   => 'Day 2 & 3: B',
									'content' => 'Day Two content',
								],
								[
									'title'   => 'Day 4 to 6: C',
									'content' => 'Day Three content',
								],
							],
							'map'                => 0,
							'price'              => '$34,895 USD per person',
							'brochure'           => '',
							'ships'              => $expected_ship_oex,
						],
						[
							'tab_id'             => 'tab-3',
							'tab_title'          => '12 Days',
							'tab_subtitle'       => 'From India',
							'tab_content_header' => 'From India, 12 days, on ' . $expected_ship_ult[0]['name'],
							'duration'           => '12 days',
							'departing_from'     => 'India',
							'itinerary_days'     => [
								[
									'title'   => 'Day 1: A',
									'content' => 'Day One content',
								],
								[
									'title'   => 'Day 2 & 3: B',
									'content' => 'Day Two content',
								],
								[
									'title'   => 'Day 4 to 6: C',
									'content' => 'Day Three content',
								],
							],
							'map'                => 0,
							'price'              => '$34,600 USD per person',
							'brochure'           => '',
							'ships'              => $expected_ship_ult,
						],
					],
				],
			],
		];

		// Assert details.
		$this->assertEquals( $expected_details, $details );
	}

	/**
	 * Test format_itinerary_day_title function.
	 *
	 * @covers format_itinerary_day_title
	 *
	 * @return void
	 */
	public function test_format_itinerary_day_title(): void {
		// Create Itinerary Day posts.
		$itinerary_day_one   = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day One',
				'post_content' => 'Day One content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title'       => 'A',
					'day_number_from' => 1,
					'day_number_to'   => 1,
				],
			]
		);
		$itinerary_day_two   = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day Two',
				'post_content' => 'Day Two content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title'       => 'B',
					'day_number_from' => 2,
					'day_number_to'   => 3,
				],
			]
		);
		$itinerary_day_three = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day Three',
				'post_content' => 'Day Three content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title'       => 'C',
					'day_number_from' => 4,
					'day_number_to'   => 6,
				],
			]
		);
		$itinerary_day_four  = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Day Four',
				'post_content' => 'Day Four content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_DAYS_POST_TYPE,
				'meta_input'   => [
					'day_title' => 'D',
				],
			]
		);

		// Check if post was created.
		$this->assertTrue( $itinerary_day_one instanceof WP_Post );
		$this->assertTrue( $itinerary_day_two instanceof WP_Post );
		$this->assertTrue( $itinerary_day_three instanceof WP_Post );
		$this->assertTrue( $itinerary_day_four instanceof WP_Post );

		// Test formatting itinerary day title.
		$this->assertEquals( 'Day 1: A', format_itinerary_day_title( $itinerary_day_one->ID ) );
		$this->assertEquals( 'Day 2 & 3: B', format_itinerary_day_title( $itinerary_day_two->ID ) );
		$this->assertEquals( 'Day 4 to 6: C', format_itinerary_day_title( $itinerary_day_three->ID ) );
		$this->assertEquals( 'D', format_itinerary_day_title( $itinerary_day_four->ID ) );
	}
}
