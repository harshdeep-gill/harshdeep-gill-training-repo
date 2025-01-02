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

use function Quark\Expeditions\bust_post_cache as bust_expedition_post_cache;
use function Quark\Softrip\do_sync;

use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_SETS_POST_TYPE;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGES_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\Ships\POST_TYPE as SHIPS_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAYS_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\DEFAULT_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\USD_CURRENCY;

/**
 * Class Test_Itineraries.
 */
class Test_Itineraries extends Softrip_TestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Itineraries\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_action( 'init', 'Quark\Itineraries\register_itinerary_post_type' ) );
		$this->assertEquals( 10, has_action( 'init', 'Quark\Itineraries\register_departure_location_taxonomy' ) );
		$this->assertEquals( 10, has_action( 'init', 'Quark\Itineraries\register_tax_type_taxonomy' ) );
		$this->assertEquals( 10, has_filter( 'qe_departure_location_taxonomy_post_types', 'Quark\Itineraries\opt_in' ) );
		$this->assertEquals( 10, has_filter( 'qe_tax_types_taxonomy_post_types', 'Quark\Itineraries\opt_in' ) );
		$this->assertEquals( 10, has_filter( 'qe_season_taxonomy_post_types', 'Quark\Itineraries\opt_in' ) );
		$this->assertEquals( 11, has_action( 'save_post', 'Quark\Itineraries\bust_post_cache' ) );
	}

	/**
	 * Test get().
	 *
	 * @covers \Quark\Itineraries\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Set DEPARTURE LOCATION taxonomy term.
		$departure_location_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
				'name'     => 'Test Term',
			]
		);

		// Assert term is created.
		$this->assertTrue( $departure_location_1 instanceof WP_Term );

		// Assign term to post.
		wp_set_object_terms( $post_1->ID, $departure_location_1->term_id, DEPARTURE_LOCATION_TAXONOMY );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get post.
		$the_post = get( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				'post'            => $post_1,
				'post_meta'       => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
				'post_taxonomies' => [
					DEPARTURE_LOCATION_TAXONOMY => [
						[
							'term_id'     => strval( $departure_location_1->term_id ),
							'name'        => $departure_location_1->name,
							'slug'        => $departure_location_1->slug,
							'taxonomy'    => $departure_location_1->taxonomy,
							'description' => $departure_location_1->description,
							'parent'      => $departure_location_1->parent,
							'term_group'  => $departure_location_1->term_group,
						],
					],
				],
			],
			$the_post
		);

		// clean up.
		wp_delete_post( $post_1->ID, true );
	}

	/**
	 * Test for get_starting_from_location().
	 *
	 * @covers \Quark\Itineraries\get_starting_from_location()
	 *
	 * @return void
	 */
	public function test_get_starting_from_location(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get starting from location.
		$starting_from_location = get_starting_from_location( $post_1->ID );

		// Assert starting from location is empty.
		$this->assertEmpty( $starting_from_location );

		// Create DEPARTURE LOCATION taxonomy term.
		$departure_location_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $departure_location_1 instanceof WP_Term );

		// Set term ID to post meta.
		update_post_meta( $post_1->ID, 'start_location', $departure_location_1->term_id );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get starting from location.
		$starting_from_location = get_starting_from_location( $post_1->ID );

		// Assert starting from location is equal to term.
		$this->assertEquals( $departure_location_1->name, $starting_from_location );
	}

	/**
	 * Test for get_mandatory_transfer_price.
	 *
	 * @covers \Quark\Itineraries\get_mandatory_transfer_price()
	 * @covers \Quark\Itineraries\get_supplemental_price()
	 *
	 * @return void
	 */
	public function test_get_mandatory_transfer_price(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get mandatory transfer price.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID );

		// Assert mandatory transfer price is empty.
		$this->assertEmpty( $mandatory_transfer_price );

		// Get supplemental price.
		$supplemental_price = get_supplemental_price( $post_1->ID );

		// Assert supplemental price is empty.
		$this->assertEmpty( $supplemental_price );

		// Set mandatory transfer price to post meta.
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_usd', 100 );
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_cad', 200 );
		update_post_meta( $post_1->ID, 'supplement_price_usd', 300 );
		update_post_meta( $post_1->ID, 'supplement_price_eur', 400 );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get mandatory transfer price.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID );

		// Assert mandatory transfer price is equal to 100.
		$this->assertEquals( 100, $mandatory_transfer_price );

		// Get mandatory transfer price for CAD.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID, 'cad' );

		// Assert mandatory transfer price is equal to 200.
		$this->assertEquals( 200, $mandatory_transfer_price );

		// Get mandatory transfer price for EUR.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID, 'eur' );

		// Assert mandatory transfer price is empty.
		$this->assertEmpty( $mandatory_transfer_price );

		// Get supplemental price.
		$supplemental_price = get_supplemental_price( $post_1->ID );

		// Assert supplemental price is equal to 300.
		$this->assertEquals( 300, $supplemental_price );

		// Get supplemental price for EUR.
		$supplemental_price = get_supplemental_price( $post_1->ID, 'eur' );

		// Assert supplemental price is equal to 400.
		$this->assertEquals( 400, $supplemental_price );

		// Assert supplemental price for CAD is empty.
		$supplemental_price = get_supplemental_price( $post_1->ID, 'cad' );

		// Assert supplemental price is empty.
		$this->assertEmpty( $supplemental_price );
	}

	/**
	 * Test for get_included_transfer_package_details().
	 *
	 * @covers \Quark\Itineraries\get_included_transfer_package_details()
	 *
	 * @return void
	 */
	public function test_get_included_transfer_package_details(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get included transfer package details.
		$included_transfer_package_details = get_included_transfer_package_details( $post_1->ID );

		// Assert included transfer package details are not set.
		$this->assertEmpty( $included_transfer_package_details['sets'] );
		$this->assertEmpty( $included_transfer_package_details['price'] );

		// Create a post.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'   => INCLUSION_SETS_POST_TYPE,
				'post_title'  => 'Test Post 2',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1'     => 'value_1',
					'set'        => '3',
					'set_0_item' => 'Test Item 1',
					'set_1_item' => 'Test Item 2',
					'set_2_item' => 'Test Item 3',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Set included transfer package details.
		update_post_meta( $post_1->ID, 'mandatory_transfer_package_inclusion', $post_2->ID );
		update_post_meta( $post_1->ID, 'included_transfer_package_details', 'Test Details' );
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_usd', 100 );
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_cad', 200 );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get included transfer package details.
		$included_transfer_package_details = get_included_transfer_package_details( $post_1->ID );

		// Assert included transfer package details are set.
		$this->assertEquals( 'Includes', $included_transfer_package_details['title'] );
		$this->assertEquals( '100', $included_transfer_package_details['price'] );
		$this->assertEquals( '$100 USD', $included_transfer_package_details['formatted_price'] );
		$this->assertCount( 3, $included_transfer_package_details['sets'] );

		// Get included transfer package details for CAD.
		$included_transfer_package_details = get_included_transfer_package_details( $post_1->ID, 'cad' );

		// Assert included transfer package details are set.
		$this->assertEquals( 'Includes', $included_transfer_package_details['title'] );
		$this->assertEquals( '200', $included_transfer_package_details['price'] );
		$this->assertEquals( '$200 CAD', $included_transfer_package_details['formatted_price'] );
	}

	/**
	 * Test for get_policy_banner_details().
	 *
	 * @covers \Quark\Itineraries\get_policy_banner_details()
	 *
	 * @return void
	 */
	public function test_get_policy_banner_details(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get policy banner details.
		$policy_banner_details = get_policy_banner_details( $post_1->ID );

		// Assert policy banner details are not set.
		$this->assertEmpty( $policy_banner_details['title'] );

		// Create a post of POLICY_PAGES.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'  => POLICY_PAGES_POST_TYPE,
				'meta_input' => [
					'alternate_title'          => 'value 1',
					'marketing_option_summary' => 'Lorem Ipsum Dolor Sit',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Set policy banner details.
		update_post_meta( $post_1->ID, 'tnc_cancellation_policy', $post_2->ID );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get policy banner details.
		$policy_banner_details = get_policy_banner_details( $post_1->ID );

		// Assert policy banner details are set.
		$this->assertEquals( 'value 1', $policy_banner_details['title'] );
		$this->assertEquals( 'Lorem Ipsum Dolor Sit', $policy_banner_details['description'] );
		$this->assertEquals( 0, $policy_banner_details['icon_id'] );
		$this->assertEquals( get_permalink( $post_2->ID ), $policy_banner_details['permalink'] );
	}

	/**
	 * Test get_season function.
	 *
	 * @covers ::get_season
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

		// Bust cache.
		bust_post_cache( $post->ID );

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
	 * @covers ::get_details_tabs_data
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

		// Test without expedition id.
		$details = get_details_tabs_data();
		$this->assertEmpty( $details );

		// Create expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_title'  => 'Test Expedition',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_itineraries' => $itinerary_posts,
				],
			]
		);
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Test with expedition id.
		$details = get_details_tabs_data( $expedition_post->ID );

		// Prepare expected details.
		$expected_details = [
			'active_tab'       => '2022',
			'itinerary_groups' => [
				2022 => [
					'tab_id'      => '2022',
					'tab_title'   => '2022 Season',
					'active_tab'  => 'tab-1',
					'itineraries' => [
						[
							'tab_id'              => 'tab-1',
							'tab_title'           => '10 Days',
							'tab_subtitle'        => 'From India',
							'tab_content_header'  => 'From India, 10 days, on ' . $expected_ship_oex[0]['name'],
							'duration'            => '10 days',
							'departing_from'      => $departure_location_india->name,
							'itinerary_days'      => [
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
							'map'                 => 0,
							'price'               => '$26,171 USD per person',
							'brochure'            => '',
							'ships'               => $expected_ship_oex,
							'request_a_quote_url' => '',
						],
						[
							'tab_id'              => 'tab-3',
							'tab_title'           => '12 Days',
							'tab_subtitle'        => 'From India',
							'tab_content_header'  => 'From India, 12 days, on ' . $expected_ship_ult[0]['name'],
							'duration'            => '12 days',
							'departing_from'      => 'India',
							'itinerary_days'      => [
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
							'map'                 => 0,
							'price'               => '$29,410 USD per person',
							'brochure'            => '',
							'ships'               => $expected_ship_ult,
							'request_a_quote_url' => '',
						],
					],
				],
				2023 => [
					'tab_id'      => '2023',
					'tab_title'   => '2023 Season',
					'active_tab'  => 'tab-2',
					'itineraries' => [
						[
							'tab_id'              => 'tab-2',
							'tab_title'           => '11 Days',
							'tab_subtitle'        => 'From Japan',
							'tab_content_header'  => 'From Japan, 11 days',
							'duration'            => '11 days',
							'departing_from'      => 'Japan',
							'itinerary_days'      => [
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
							'map'                 => 0,
							'price'               => '',
							'brochure'            => '',
							'ships'               => [],
							'request_a_quote_url' => '',
						],
					],
				],
			],
		];

		// Assert details.
		$this->assertEquals( $expected_details, $details );

		// Create a destination term.
		$destination = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'name'     => 'Test Destination',
			]
		);

		// Check if term was created.
		$this->assertTrue( $destination instanceof WP_Term );

		// Assign destination term to posts.
		wp_set_post_terms( $expedition_post->ID, [ $destination->term_id ], DESTINATION_TAXONOMY );

		// Update term meta.
		update_term_meta( $destination->term_id, 'show_next_year', true );

		// Bust expedition post cache.
		bust_expedition_post_cache( $expedition_post->ID );

		// Test with expedition id.
		$details = get_details_tabs_data( $expedition_post->ID );

		// Prepare expected details.
		$expected_details = [
			'active_tab'       => '2022',
			'itinerary_groups' => [
				2022 => [
					'tab_id'      => '2022',
					'tab_title'   => '2022.23 Season',
					'active_tab'  => 'tab-1',
					'itineraries' => [
						[
							'tab_id'              => 'tab-1',
							'tab_title'           => '10 Days',
							'tab_subtitle'        => 'From India',
							'tab_content_header'  => 'From India, 10 days, on ' . $expected_ship_oex[0]['name'],
							'duration'            => '10 days',
							'departing_from'      => $departure_location_india->name,
							'itinerary_days'      => [
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
							'map'                 => 0,
							'price'               => '$26,171 USD per person',
							'brochure'            => '',
							'ships'               => $expected_ship_oex,
							'request_a_quote_url' => '',
						],
						[
							'tab_id'              => 'tab-3',
							'tab_title'           => '12 Days',
							'tab_subtitle'        => 'From India',
							'tab_content_header'  => 'From India, 12 days, on ' . $expected_ship_ult[0]['name'],
							'duration'            => '12 days',
							'departing_from'      => 'India',
							'itinerary_days'      => [
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
							'map'                 => 0,
							'price'               => '$29,410 USD per person',
							'brochure'            => '',
							'ships'               => $expected_ship_ult,
							'request_a_quote_url' => '',
						],
					],
				],
				2023 => [
					'tab_id'      => '2023',
					'tab_title'   => '2023.24 Season',
					'active_tab'  => 'tab-2',
					'itineraries' => [
						[
							'tab_id'              => 'tab-2',
							'tab_title'           => '11 Days',
							'tab_subtitle'        => 'From Japan',
							'tab_content_header'  => 'From Japan, 11 days',
							'duration'            => '11 days',
							'departing_from'      => 'Japan',
							'itinerary_days'      => [
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
							'map'                 => 0,
							'price'               => '',
							'brochure'            => '',
							'ships'               => [],
							'request_a_quote_url' => '',
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
	 * @covers ::format_itinerary_day_title
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

	/**
	 * Test get tax types.
	 *
	 * @covers ::get_tax_type_details
	 *
	 * @return void
	 */
	public function test_get_tax_type_details(): void {
		// Empty post id.
		$this->assertEmpty( get_tax_type_details() );

		// Invalid post id.
		$this->assertEmpty( get_tax_type_details( 9348 ) );

		// Create a post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Check if post was created.
		$this->assertTrue( $post instanceof WP_Post );

		// Empty tax type.
		$this->assertEmpty( get_tax_type_details( $post->ID ) );

		// Add tax type term.
		$tax_type = $this->factory()->term->create_and_get(
			[
				'taxonomy' => TAX_TYPE_TAXONOMY,
				'name'     => 'Test Tax Type',
			]
		);

		// Check if term was created.
		$this->assertTrue( $tax_type instanceof WP_Term );

		// Assign tax type to post.
		wp_set_object_terms( $post->ID, $tax_type->term_id, TAX_TYPE_TAXONOMY );

		// Bust post cache.
		bust_post_cache( $post->ID );

		// Get tax type details.
		$tax_type_details = get_tax_type_details( $post->ID );
		$this->assertNotEmpty( $tax_type_details );

		// Expected.
		$expected_details = [
			[
				'id'          => $tax_type->term_id,
				'name'        => $tax_type->name,
				'description' => $tax_type->description,
				'rate'        => 0,
			],
		];
		$this->assertEquals( $expected_details, $tax_type_details );

		// Add 'rate' term meta.
		update_term_meta( $tax_type->term_id, 'rate', 10 );

		// Get tax type details.
		$tax_type_details = get_tax_type_details( $post->ID );
		$this->assertNotEmpty( $tax_type_details );

		// Expected.
		$expected_details = [
			[
				'id'          => $tax_type->term_id,
				'name'        => $tax_type->name,
				'description' => $tax_type->description,
				'rate'        => 10,
			],
		];
		$this->assertEquals( $expected_details, $tax_type_details );
	}

	/**
	 * Test update_related_expedition_on_itineraries_save function.
	 *
	 * @covers ::update_related_expedition_on_itineraries_save
	 *
	 * @return void
	 */
	public function test_update_related_expedition_on_itineraries_save(): void {
		// Mock $_POST data.
		global $_POST; // phpcs:ignore

		// Create expedition post.
		$expedition_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_title'  => 'Test Expedition 1',
				'post_status' => 'publish',
			]
		);

		// Create two expedition post.
		$expedition_2 = $this->factory()->post->create_and_get(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_title'  => 'Test Expedition 2',
				'post_status' => 'publish',
			]
		);

		// Verify posts were created.
		$this->assertTrue( $expedition_1 instanceof WP_Post );
		$this->assertTrue( $expedition_2 instanceof WP_Post );

		// Case 1 - it skips different post_type.
		// Create a post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_type'   => 'post',
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_expedition' => $expedition_1->ID,
				],
			]
		);

		// Check if post was created.
		$this->assertTrue( $post instanceof WP_Post );

		// Mock $_POST data.
		$_POST['acf']['field_65f2dab2046df'] = $expedition_1->ID;

		// Update the post using update_post().
		$args = [
			'ID'           => $post->ID,
			'post_content' => 'Lorem ipsum',
		];
		wp_update_post( $args );

		// Verify no meta was added.
		$this->assertEmpty( get_post_meta( $expedition_1->ID, 'related_itineraries', true ) );

		// Case 2 - it skips same meta data.
		// Create an itinerary post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_expedition' => $expedition_1->ID,
				],
			]
		);

		// Check if post was created.
		$this->assertTrue( $post instanceof WP_Post );

		// Update the post using update_post().
		$args = [
			'ID'           => $post->ID,
			'post_content' => 'Lorem ipsum',
			'meta_input'   => [
				'related_expedition' => $expedition_1->ID,
			],
		];

		// Update the post using update_post().
		wp_update_post( $args );

		// Verify no meta was added.
		$this->assertEmpty( get_post_meta( $expedition_1->ID, 'related_itineraries', true ) );

		// Case 3 - it updates meta data.
		// Mock $_POST data.
		$_POST['acf']['field_65f2dab2046df'] = $expedition_2->ID;

		// Update the post using update_post().
		wp_update_post( $args );

		// Verify no meta was added.
		$this->assertEquals( [ $post->ID ], get_post_meta( $expedition_2->ID, 'related_itineraries', true ) );

		// Case 4 - it removes meta data.
		// Set Expedition meta.
		update_post_meta( $post->ID, 'related_expedition', $expedition_1->ID );
		update_post_meta( $expedition_1->ID, 'related_itineraries', [ 123, $post->ID, 456 ] );
		update_post_meta( $expedition_2->ID, 'related_itineraries', [ 123 ] );

		// Mock $_POST data.
		$_POST['acf']['field_65f2dab2046df'] = $expedition_2->ID;

		// Update the post using update_post().
		wp_update_post( $args );

		// Now it should remove the post ID from $expedition_1 - related_itineraries and added to $expedition_2.
		$this->assertEqualsCanonicalizing( [ 123, 456 ], get_post_meta( $expedition_1->ID, 'related_itineraries', true ) );
		$this->assertEqualsCanonicalizing( [ 123, $post->ID ], get_post_meta( $expedition_2->ID, 'related_itineraries', true ) );

		// Case 5 - it removes old meta data.
		// Mock $_POST data.
		$_POST['acf']['field_65f2dab2046df'] = '';

		// Set Expedition meta.
		update_post_meta( $post->ID, 'related_expedition', $expedition_1->ID );
		update_post_meta( $expedition_1->ID, 'related_itineraries', [ 123, $post->ID, 456 ] );
		update_post_meta( $expedition_2->ID, 'related_itineraries', [ 123 ] );

		// Update the post using update_post().
		wp_update_post( $args );

		// Now it should remove the post ID from $expedition_1 - related_itineraries.
		$this->assertEqualsCanonicalizing( [ 123, 456 ], get_post_meta( $expedition_1->ID, 'related_itineraries', true ) );
		$this->assertEqualsCanonicalizing( [ 123 ], get_post_meta( $expedition_2->ID, 'related_itineraries', true ) );
	}

	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Itineraries\get_lowest_price
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Cache prefix.
		$cache_prefix = CACHE_KEY . '_lowest_price_';

		// Invalid post ID.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price() );

		// Invalid currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( 1, 'INVALID' ) );

		// Create an itinerary post.
		$itinerary_id = $this->factory()->post->create( [ 'post_type' => POST_TYPE ] );
		$this->assertIsInt( $itinerary_id );

		// Itinerary with no departure.
		$expected  = [
			'original'   => 0,
			'discounted' => 0,
		];
		$cache_key = $cache_prefix . $itinerary_id . '_' . DEFAULT_CURRENCY;
		$this->assertFalse( wp_cache_get( $cache_key, CACHE_GROUP ) );
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key, CACHE_GROUP ) );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Flush cache before sync.
		wp_cache_flush();

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache after sync.
		wp_cache_flush();

		// Get itinerary post with package code ABC-123.
		$itinerary_posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'ABC-123',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// USD cache key.
		$cache_key_usd = $cache_prefix . $itinerary_id . '_' . USD_CURRENCY;

		// Get lowest price for itinerary with package code ABC-123 with USD currency.
		$this->assertFalse( wp_cache_get( $cache_key_usd, CACHE_GROUP ) );
		$expected = [
			'original'   => 34895,
			'discounted' => 26171,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_usd, CACHE_GROUP ) );

		// AUD cache key.
		$cache_key_aud = $cache_prefix . $itinerary_id . '_' . AUD_CURRENCY;

		// Get lowest price for itinerary with package code ABC-123 with AUD currency.
		$this->assertFalse( wp_cache_get( $cache_key_aud, CACHE_GROUP ) );
		$expected = [
			'original'   => 54795,
			'discounted' => 41096,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, AUD_CURRENCY ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_aud, CACHE_GROUP ) );

		// EUR cache key.
		$cache_key_eur = $cache_prefix . $itinerary_id . '_' . EUR_CURRENCY;

		// Get lowest price for itinerary with package code ABC-123 with EUR currency.
		$this->assertFalse( wp_cache_get( $cache_key_eur, CACHE_GROUP ) );
		$expected = [
			'original'   => 32495,
			'discounted' => 24371,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, EUR_CURRENCY ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_eur, CACHE_GROUP ) );

		// GBP cache key.
		$cache_key_gbp = $cache_prefix . $itinerary_id . '_' . GBP_CURRENCY;

		// Get lowest price for itinerary with package code ABC-123 with GBP currency.
		$this->assertFalse( wp_cache_get( $cache_key_gbp, CACHE_GROUP ) );
		$expected = [
			'original'   => 27995,
			'discounted' => 20996,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'GBP' ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_gbp, CACHE_GROUP ) );

		// Invalid currency cache key.
		$cache_key_invalid = $cache_prefix . $itinerary_id . '_INVALID';

		// Get lowest price for itinerary with package code ABC-123 with invalid currency.
		$this->assertFalse( wp_cache_get( $cache_key_invalid, CACHE_GROUP ) );
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'INVALID' ) );
		$this->assertFalse( wp_cache_get( $cache_key_invalid, CACHE_GROUP ) );

		// Get itinerary with package code DEF-456.
		$itinerary_posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'DEF-456',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// USD cache key.
		$cache_key_usd = $cache_prefix . $itinerary_id . '_' . USD_CURRENCY;

		// Get lowest price for itinerary with package code DEF-456 with USD currency.
		$this->assertFalse( wp_cache_get( $cache_key_usd, CACHE_GROUP ) );
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_usd, CACHE_GROUP ) );

		// AUD cache key.
		$cache_key_aud = $cache_prefix . $itinerary_id . '_' . AUD_CURRENCY;

		// Get lowest price for itinerary with package code DEF-456 with AUD currency.
		$this->assertFalse( wp_cache_get( $cache_key_aud, CACHE_GROUP ) );
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'AUD' ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_aud, CACHE_GROUP ) );

		// EUR cache key.
		$cache_key_eur = $cache_prefix . $itinerary_id . '_' . EUR_CURRENCY;

		// Get lowest price for itinerary with package code DEF-456 with EUR currency.
		$this->assertFalse( wp_cache_get( $cache_key_eur, CACHE_GROUP ) );
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'EUR' ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_eur, CACHE_GROUP ) );

		// GBP cache key.
		$cache_key_gbp = $cache_prefix . $itinerary_id . '_' . GBP_CURRENCY;

		// Get lowest price for itinerary with package code DEF-456 with GBP currency.
		$this->assertFalse( wp_cache_get( $cache_key_gbp, CACHE_GROUP ) );
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'GBP' ) );
		$this->assertNotEmpty( wp_cache_get( $cache_key_gbp, CACHE_GROUP ) );

		// Invalid currency cache key.
		$cache_key_invalid = $cache_prefix . $itinerary_id . '_INVALID';

		// Get lowest price for itinerary with package code DEF-456 with invalid currency.
		$this->assertFalse( wp_cache_get( $cache_key_invalid, CACHE_GROUP ) );
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'INVALID' ) );
		$this->assertFalse( wp_cache_get( $cache_key_invalid, CACHE_GROUP ) );

		// Get itinerary with package code HIJ-456.
		$itinerary_posts = get_posts(
			[
				'post_type'              => POST_TYPE,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'suppress_filters'       => false,
				'ignore_sticky_posts'    => true,
				'fields'                 => 'ids',
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'HIJ-456',
						'compare' => '=',
					],
				],
			]
		);
		$this->assertNotEmpty( $itinerary_posts );
		$this->assertEquals( 1, count( $itinerary_posts ) );

		// Itinerary id.
		$itinerary_id = $itinerary_posts[0];
		$this->assertIsInt( $itinerary_id );

		// Get lowest price for itinerary with package code HIJ-456 with USD currency.
		$expected = [
			'original'   => 10995,
			'discounted' => 9896,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id ) );

		// Get lowest price for itinerary with package code HIJ-456 with AUD currency.
		$expected = [
			'original'   => 17200,
			'discounted' => 15480,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'AUD' ) );

		// Get lowest price for itinerary with package code HIJ-456 with EUR currency.
		$expected = [
			'original'   => 10200,
			'discounted' => 9180,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'EUR' ) );

		// Get lowest price for itinerary with package code HIJ-456 with GBP currency.
		$expected = [
			'original'   => 10300,
			'discounted' => 8240,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'GBP' ) );

		// Get lowest price for itinerary with package code HIJ-456 with invalid currency.
		$expected = [
			'original'   => 0,
			'discounted' => 0,
		];
		$this->assertEquals( $expected, get_lowest_price( $itinerary_id, 'INVALID' ) );
	}

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\Itineraries\translate_meta_keys()
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
				'meta_key'                             => 'string',
				'icon'                                 => 'attachment',
				'boilerplate'                          => 'string',
				'related_expedition'                   => 'post',
				'start_location'                       => 'taxonomy',
				'end_location'                         => 'taxonomy',
				'embarkation_port'                     => 'post',
				'disembarkation_port'                  => 'post',
				'brochure'                             => 'post',
				'map'                                  => 'attachment',
				'inclusions'                           => 'post',
				'exclusions'                           => 'post',
				'itinerary_days'                       => 'Quark\Itineraries\translate_meta_key',
				'offer_inclusion_text'                 => 'string',
				'mandatory_transfer_package_inclusion' => 'post',
				'mandatory_transfer_package_exclusion' => 'post',
				'tnc_cancellation_policy'              => 'post',
				'tnc_terms_and_conditions'             => 'post',
			],
			translate_meta_keys( $input )
		);
	}
}
