<?php
/**
 * Softrip test Departure.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;
use WP_Post;
use WP_Error;
use WP_Query;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Departure.
 */
class Test_Departure extends WP_UnitTestCase {

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		// Run parent and include setup.
		parent::set_up_before_class();
		include_once 'setup.php';
	}

	/**
	 * Tear down after class.
	 *
	 * @return void
	 */
	public static function tear_down_after_class(): void {
		// Run parent.
		parent::tear_down_after_class();
		tear_down_softrip_db();

		// Delete all itineraries.
		$itineraries_query = new WP_Query(
			[
				'post_type'              => ITINERARY_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
			]
		);

		// Loop through the itineraries_query.
		foreach ( $itineraries_query->posts as $itinerary_post ) {
			wp_delete_post( is_int( $itinerary_post ) ? $itinerary_post : $itinerary_post->ID, true );
		}
	}

	/**
	 * Tear down after tests.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// Run parent.
		parent::tear_down();

		// clear the data.
		tear_down_softrip_db();

		// Delete all departures.
		$departure_query = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
			]
		);

		// Loop through the departures.
		foreach ( $departure_query->posts as $departure_post ) {
			wp_delete_post( is_int( $departure_post ) ? $departure_post : $departure_post->ID, true );
		}
	}

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
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
	}

	/**
	 * Test get_departure_status.
	 *
	 * @covers \Quark\Softrip\Departure::get_departure_status()
	 *
	 * @return void
	 */
	public function test_get_departure_status(): void {
		// Get post and departure.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a new Itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Setup test data.
		$test_data = [
			'departures' => [
				[
					'id'        => 'test_departure_one',
					'startDate' => '2024-01-01',
					'endDate'   => '2024-01-10',
				],
				[
					'id'        => 'test_departure_two',
					'startDate' => gmdate( 'Y-m-d', strtotime( 'next week' ) ),
					'endDate'   => gmdate( 'Y-m-d', strtotime( 'next month' ) ),
				],
			],
		];

		// Make a departures.
		$itinerary->update_departures( $test_data );

		// Test status.
		$departure_one  = $itinerary->get_departure( 'test_departure_one' );
		$post_id        = $departure_one->get_id();
		$departure_post = get_post( $post_id );

		// Check if is valid.
		if ( $departure_post instanceof WP_Post ) {
			// Tests.
			$this->assertEquals( 'draft', $departure_post->post_status );
		}

		// Test status.
		$departure_two  = $itinerary->get_departure( 'test_departure_two' );
		$post_id        = $departure_two->get_id();
		$departure_post = get_post( $post_id );

		// Check if is valid.
		if ( $departure_post instanceof WP_Post ) {
			$message = wp_json_encode(
				[
					'test_data'     => $test_data,
					'departure_two' => $departure_two->get_data(),
				],
			);

			// Tests.
			$this->assertEquals( 'publish', $departure_post->post_status, strval( $message ) );
		}
	}

	/**
	 * Test load().
	 *
	 * @covers \Quark\Softrip\Departure::load()
	 *
	 * @return void
	 */
	public function test_load(): void {
		// Create a Departure object.
		$departure = new Departure();

		// Get data to validate.
		$this->assertEmpty( $departure->get_id() );

		// Create a Departure post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure for load',
				'post_content' => 'Departure content',
				'post_status'  => 'publish',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);

		// Assert valid post.
		$this->assertInstanceOf( WP_Post::class, $post );

		// Load the post.
		$departure->load( $post->ID );

		// Get the ID.
		$this->assertEquals( $post->ID, $departure->get_id() );

		// Check if is valid.
		$this->assertTrue( $departure->is_valid() );

		// Remove the post.
		wp_delete_post( $post->ID, true );
	}

	/**
	 * Test Departure data validate - format and save functions.
	 *
	 * @covers \Quark\Softrip\Departure::save()
	 * @covers \Quark\Softrip\Departure::set()
	 * @covers \Quark\Softrip\Departure::format_departure_data()
	 * @covers \Quark\Softrip\Departure::get_departure_status()
	 *
	 * @return void
	 */
	public function test_departure_data_validate(): void {
		// Get Itinerary post.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Get a post with name 'JKL-012:2025-01-09' use WP_Query.
		$departure_query = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'name'                   => 'JKL-012:2025-01-09',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
			]
		);

		// Assert no posts found.
		$this->assertEmpty( $departure_query->posts );

		// Create a raw departure data.
		$raw_departure = [
			'id'          => 'JKL-012:2025-01-09',
			'code'        => 'ULT20250109',
			'packageCode' => 'JKL-012',
			'startDate'   => gmdate( 'Y-m-d', strtotime( 'next year' ) ),
			'endDate'     => gmdate( 'Y-m-d', strtotime( 'next year + 16 days' ) ),
			'duration'    => 16,
			'shipCode'    => 'ULT',
			'marketCode'  => 'ANT',
		];

		// Create a new Itinerary.
		$itinerary = new Itinerary( $post->ID );
		$departure = $itinerary->get_departure( strval( $raw_departure['id'] ) );

		// Set the departure.
		$departure->set( $raw_departure, true );

		// Get a post with name 'JKL-012:2025-01-09' use WP_Query.
		$departure_query = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'name'                   => 'JKL-012:2025-01-09',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'ignore_sticky_posts'    => true,
			]
		);

		// Assert posts found.
		$this->assertNotEmpty( $departure_query->posts );

		// Get the departure status.
		$this->assertEquals( 'publish', $departure->get_status() );

		// Assert meta.
		$this->assertEquals( $raw_departure['code'], $departure->get_post_meta( 'softrip_departure_id' ) );
		$this->assertEquals( $raw_departure['id'], $departure->get_post_meta( 'departure_unique_id' ) );
		$this->assertEquals( $raw_departure['packageCode'], $departure->get_post_meta( 'softrip_package_id' ) );
		$this->assertEquals( $raw_departure['startDate'], $departure->get_post_meta( 'departure_start_date' ) );
		$this->assertEquals( $raw_departure['endDate'], $departure->get_post_meta( 'departure_end_date' ) );
		$this->assertEquals( $raw_departure['duration'], $departure->get_post_meta( 'duration' ) );
		$this->assertEquals( $post->ID, $departure->get_post_meta( 'itinerary' ) );
		$this->assertEquals( $raw_departure['shipCode'], $departure->get_post_meta( 'ship_id' ) );
		$this->assertEquals( $raw_departure['marketCode'], $departure->get_post_meta( 'region' ) );

		// Validate with past dated departure.
		$raw_departure['id']        = 'JKL-012:2023-01-09';
		$raw_departure['startDate'] = gmdate( 'Y-m-d', strtotime( 'last year' ) );
		$raw_departure['endDate']   = gmdate( 'Y-m-d', strtotime( 'last year + 16 days' ) );

		// Get the departure.
		$departure = $itinerary->get_departure( $raw_departure['id'] );

		// Set the departure.
		$departure->set( $raw_departure, true );

		// Get the departure status.
		$this->assertEquals( 'draft', $departure->get_status() );

		// Get the Departure ID.
		$departure_id = $departure->get_id();

		// Update the same departure with new dates.
		$raw_departure['startDate'] = gmdate( 'Y-m-d', strtotime( 'next year' ) );
		$raw_departure['endDate']   = gmdate( 'Y-m-d', strtotime( 'next year + 16 days' ) );

		// Set the departure.
		$departure->set( $raw_departure, true );

		// Assert the current departure ID is the same as the previous one.
		$this->assertEquals( $departure_id, $departure->get_id() );

		// Get the departure status.
		$this->assertEquals( 'publish', $departure->get_status() );
	}

	/**
	 * Test cabins related functions.
	 *
	 * @covers \Quark\Softrip\Departure::ensure_cabins_loaded()
	 * @covers \Quark\Softrip\Departure::get_cabins()
	 * @covers \Quark\Softrip\Departure::load_cabins()
	 * @covers \Quark\Softrip\Departure::get_cabin()
	 *
	 * @return void
	 */
	public function test_cabins(): void {
		// Get Itinerary post.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a raw departure data.
		$raw_departure = [
			'id'          => 'JKL-012:2025-01-09',
			'code'        => 'ULT20250109',
			'packageCode' => 'JKL-012',
			'endDate'     => gmdate( 'Y-m-d', strtotime( 'next year + 16 days' ) ),
			'startDate'   => gmdate( 'Y-m-d', strtotime( 'next year' ) ),
			'duration'    => 16,
			'shipCode'    => 'ULT',
			'marketCode'  => 'ANT',
		];

		// Create a new Itinerary.
		$itinerary = new Itinerary( $post->ID );
		$departure = $itinerary->get_departure( strval( $raw_departure['id'] ) );

		// Set the departure.
		$departure->set( $raw_departure, true );

		// Get the cabins.
		$cabins = $departure->get_cabins();

		// Assert no cabins.
		$this->assertEmpty( $cabins );

		// Set Cabin data to raw_departure data.
		$raw_departure['cabins'] = [
			[
				'id'          => 'CABIN1',
				'code'        => 'CABIN1',
				'name'        => 'Cabin 1',
				'departureId' => 'JKL-012:2025-01-09',
				'occupancies' => [],
			],
			[
				'id'          => 'CABIN2',
				'code'        => 'CABIN2',
				'name'        => 'Cabin 2',
				'departureId' => 'JKL-012:2025-01-09',
				'occupancies' => [],
			],
		];

		// Set the departure.
		$departure->set( $raw_departure, true );

		// Get the cabins.
		$cabins = $departure->get_cabins();

		// Assert cabins.
		$this->assertNotEmpty( $cabins );
		$this->assertCount( 2, $cabins );

		// Get a cabin.
		$departure_cabin = $departure->get_cabin( 'CABIN1' );

		// Assert cabin.
		$this->assertInstanceOf( Cabin::class, $departure_cabin );
		$this->assertEquals( 'CABIN1', $departure_cabin->get_entry_data( 'title' ) );

		// Get a cabin.
		$departure_cabin = $departure->get_cabin( 'CABIN2' );

		// Assert cabin.
		$this->assertInstanceOf( Cabin::class, $departure_cabin );
		$this->assertEquals( 'CABIN2', $departure_cabin->get_entry_data( 'title' ) );

		// Get a cabin with invalid ID.
		$departure_cabin = $departure->get_cabin( 'CABIN3' );

		// Assert cabin.
		$this->assertEmpty( $departure_cabin->get_entry_data( 'title' ) );
	}
}
