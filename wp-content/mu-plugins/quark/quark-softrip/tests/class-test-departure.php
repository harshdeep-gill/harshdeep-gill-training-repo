<?php
/**
 * Test suite for the Departure class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Cabin;
use Quark\Softrip\Departure;
use Quark\Softrip\Itinerary;
use WP_Post;
use WP_Query;
use WP_UnitTestCase;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Departure
 */
class Test_Departure extends WP_UnitTestCase {

	/**
	 * Test get id.
	 *
	 * @covers \Quark\Softrip\Departure::get_id
	 *
	 * @return void
	 */
	public function test_get_id(): void {
		// Create a test departure post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Test Departure Content',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta'            => 1,
					'softrip_package_id'   => 'UNQ-123',
					'departure_unique_id'  => 'UNQ-123:2026-01-01',
					'softrip_code' => 'UPL20260101',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create a departure object without any ID.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$this->assertEquals( 0, $departure->get_id() );

		// Create a departure object with the test post ID.
		$departure = new Departure( $post->ID );
		$id        = $departure->get_id();
		$this->assertEquals( $post->ID, $id );

		// Cleanup.
		wp_delete_post( $post->ID, true );
		unset( $departure );
	}

	/**
	 * Test is valid.
	 *
	 * @covers \Quark\Softrip\Departure::is_valid
	 *
	 * @return void
	 */
	public function test_is_valid(): void {
		// Create a test departure post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Test Departure Content',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta'            => 1,
					'softrip_package_id'   => 'UNQ-123',
					'departure_unique_id'  => 'UNQ-123:2026-01-01',
					'softrip_code' => 'UPL20260101',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create a departure object without any ID.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$this->assertFalse( $departure->is_valid() );

		// Create a departure object with the test post ID.
		$departure = new Departure( $post->ID );
		$this->assertTrue( $departure->is_valid() );

		// Cleanup.
		wp_delete_post( $post->ID, true );
		unset( $departure );
	}

	/**
	 * Test get data.
	 *
	 * @covers \Quark\Softrip\Departure::get_data()
	 *
	 * @return void
	 */
	public function test_get_data(): void {
		// Create a test departure post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Test Departure Content',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta'            => 1,
					'softrip_package_id'   => 'UNQ-123',
					'departure_unique_id'  => 'UNQ-123:2026-01-01',
					'softrip_code' => 'UPL20260101',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create spoken language terms.
		$spoken_language_term_ids = $this->factory()->term->create_many(
			5,
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
			]
		);
		$this->assertTrue( is_array( $spoken_language_term_ids ) );

		// Assign spoken language terms to the post.
		wp_set_object_terms( $post->ID, $spoken_language_term_ids, SPOKEN_LANGUAGE_TAXONOMY );

		// Create a departure object without any ID.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$this->assertEmpty( $departure->get_data() );

		// Create a departure object with the test post ID.
		$departure = new Departure( $post->ID );
		$data      = $departure->get_data();
		$this->assertNotEmpty( $data );
		$this->assertArrayHasKey( 'post', $data );
		$this->assertArrayHasKey( 'post_meta', $data );
		$this->assertArrayHasKey( 'post_taxonomies', $data );

		// Validate each field.
		$this->assertTrue( $data['post'] instanceof WP_Post );
		$this->assertIsArray( $data['post_meta'] );
		$this->assertIsArray( $data['post_taxonomies'] );

		// Validate meta.
		$this->assertNotEmpty( $data['post_meta'] );
		$meta_data = $data['post_meta'];
		$this->assertArrayHasKey( 'test_meta', $meta_data );
		$this->assertEquals( 1, $meta_data['test_meta'] );
		$this->assertArrayHasKey( 'softrip_package_id', $meta_data );
		$this->assertEquals( 'UNQ-123', $meta_data['softrip_package_id'] );
		$this->assertArrayHasKey( 'departure_unique_id', $meta_data );
		$this->assertEquals( 'UNQ-123:2026-01-01', $meta_data['departure_unique_id'] );
		$this->assertArrayHasKey( 'softrip_code', $meta_data );
		$this->assertEquals( 'UPL20260101', $meta_data['softrip_code'] );

		// Validate taxonomies.
		$this->assertNotEmpty( $data['post_taxonomies'] );
		$taxonomy_data = $data['post_taxonomies'];
		$this->assertArrayHasKey( SPOKEN_LANGUAGE_TAXONOMY, $taxonomy_data );
		$this->assertIsArray( $taxonomy_data[ SPOKEN_LANGUAGE_TAXONOMY ] );

		// Validate spoken language terms.
		foreach ( $taxonomy_data[ SPOKEN_LANGUAGE_TAXONOMY ] as $term ) {
			$this->assertIsArray( $term );
			$this->assertContains( absint( $term['term_id'] ), $spoken_language_term_ids );
		}

		// Cleanup.
		wp_delete_post( $post->ID, true );
		unset( $departure );
	}

	/**
	 * Test get post meta.
	 *
	 * @covers \Quark\Softrip\Departure::get_post_meta()
	 *
	 * @return void
	 */
	public function test_get_post_meta(): void {
		// Create a test departure post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Test Departure Content',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta'            => 1,
					'softrip_package_id'   => 'UNQ-123',
					'departure_unique_id'  => 'UNQ-123:2026-01-01',
					'softrip_code' => 'UPL20260101',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create a departure object without any ID.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$this->assertEmpty( $departure->get_post_meta() );

		// Create a departure object with the test post ID.
		$departure = new Departure( $post->ID );
		$meta      = $departure->get_post_meta();
		$this->assertIsArray( $meta );
		$this->assertNotEmpty( $meta );
		$this->assertArrayHasKey( 'test_meta', $meta );
		$this->assertEquals( 1, $meta['test_meta'] );
		$this->assertArrayHasKey( 'softrip_package_id', $meta );
		$this->assertEquals( 'UNQ-123', $meta['softrip_package_id'] );
		$this->assertArrayHasKey( 'departure_unique_id', $meta );
		$this->assertEquals( 'UNQ-123:2026-01-01', $meta['departure_unique_id'] );
		$this->assertArrayHasKey( 'softrip_code', $meta );
		$this->assertEquals( 'UPL20260101', $meta['softrip_code'] );
		$this->assertArrayNotHasKey( 'non_existent_meta', $meta );

		// Get meta by key.
		$this->assertEquals( 1, $departure->get_post_meta( 'test_meta' ) );
		$this->assertEquals( 'UNQ-123', $departure->get_post_meta( 'softrip_package_id' ) );
		$this->assertEquals( 'UNQ-123:2026-01-01', $departure->get_post_meta( 'departure_unique_id' ) );
		$this->assertEquals( 'UPL20260101', $departure->get_post_meta( 'softrip_code' ) );
		$this->assertEmpty( $departure->get_post_meta( 'non_existent_meta' ) );

		// Cleanup.
		wp_delete_post( $post->ID, true );
		unset( $departure );
	}

	/**
	 * Test get status.
	 *
	 * @covers \Quark\Softrip\Departure::get_status()
	 *
	 * @return void
	 */
	public function test_get_status(): void {
		// Create a test departure post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Test Departure Content',
				'post_type'    => DEPARTURE_POST_TYPE,
				'post_status'  => 'publish',
				'meta_input'   => [
					'test_meta'            => 1,
					'softrip_package_id'   => 'UNQ-123',
					'departure_unique_id'  => 'UNQ-123:2026-01-01',
					'softrip_code' => 'UPL20260101',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create a departure object without any ID.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$this->assertSame( 'draft', $departure->get_status() );

		// Create a departure object with the test post ID.
		$departure = new Departure( $post->ID );
		$this->assertEquals( 'publish', $departure->get_status() );

		// Cleanup.
		wp_delete_post( $post->ID, true );
		unset( $departure );
	}

	/**
	 * Test load.
	 *
	 * @covers \Quark\Softrip\Departure::load()
	 *
	 * @return void
	 */
	public function test_load(): void {
		// Create a departure object.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$this->assertEmpty( $departure->get_id() );

		// Create a test departure post.
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

		// Cleanup.
		wp_delete_post( $post->ID, true );
		unset( $departure );
	}

	/**
	 * Test set data.
	 *
	 * @covers \Quark\Softrip\Departure::format_data()
	 * @covers \Quark\Softrip\Departure::set()
	 * @covers \Quark\Softrip\Departure::save()
	 *
	 * @return void
	 */
	public function test_set(): void {
		// Test 1: Empty raw data.
		$raw_departure_data = [];
		$departure          = new Departure();
		$departure->set( $raw_departure_data );
		$data = $departure->get_data();
		$this->assertEmpty( $data );

		// Get all departure posts .
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'draft,publish',
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertEmpty( $departure_posts->posts );

		// Create itinerary post.
		$itinerary_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Test Itinerary Content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $itinerary_post instanceof WP_Post );

		// Create itinerary instance.
		$itinerary_obj = new Itinerary( $itinerary_post->ID );

		// Create a departure object without any ID.
		$departure = new Departure();
		$this->assertTrue( $departure instanceof Departure );
		$departure->set_itinerary( $itinerary_obj );
		$this->assertEmpty( $departure->get_data() );

		// Test 2: New valid raw data, but without save and without itinerary.
		$raw_departure_data = [
			'id'         => 'UNQ-123:2026-01-01',
			'code'       => 'LPL20260101',
			'startDate'  => '2026-01-01',
			'endDate'    => '2026-01-25',
			'duration'   => 7,
			'shipCode'   => 'YUP',
			'marketCode' => 'QTY',
		];
		$departure          = new Departure();
		$departure->set( $raw_departure_data );
		$data = $departure->get_data();
		$this->assertEmpty( $data );

		// Test 3: New valid raw data with itinerary, but without save.
		$departure = new Departure();
		$departure->set_itinerary( $itinerary_obj );
		$departure->set( $raw_departure_data );
		$data = $departure->get_data();
		$this->assertEmpty( $data );

		// Test 4: New valid raw data with itinerary and save.
		$departure = new Departure();
		$departure->set_itinerary( $itinerary_obj );
		$departure->set( $raw_departure_data, true );
		$data = $departure->get_data();
		$this->assertNotEmpty( $data );

		// New departure post should have been created.
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'draft,publish',
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
				'meta_query'             => [
					[
						'key'   => 'departure_unique_id',
						'value' => 'UNQ-123:2026-01-01',
					],
				],
			]
		);
		$this->assertNotEmpty( $departure_posts->posts );
		$this->assertNotEmpty( $departure_posts->posts );
		$this->assertCount( 1, $departure_posts->posts );

		// Title must be the same as the id.
		$this->assertEquals( $raw_departure_data['id'], get_the_title( $departure_posts->posts[0] ) );

		// Test case 4: Add more valid raw data with itinerary and save.
		$raw_departure_data = [
			'id'         => 'UNQ-123:2026-01-01',
			'code'       => 'LPL20260101',
			'startDate'  => '2026-01-01',
			'endDate'    => '2026-01-25',
			'duration'   => 20, // Updated duration.
			'shipCode'   => 'OLI', // Updated ship code.
			'marketCode' => 'QTY',
		];
		$departure          = new Departure();
		$departure->set_itinerary( $itinerary_obj );
		$departure->set( $raw_departure_data, true );

		// A new departure should have been created.
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'draft,publish',
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotEmpty( $departure_posts->posts );
		$this->assertCount( 2, $departure_posts->posts );

		/**
		 * Testing via itinerary object.
		 */
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

		// Test 5: New valid raw data with itinerary and save.
		$itinerary_post_id = $this->factory()->post->create(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Test Itinerary Content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'JKL-012',
				],
			]
		);
		$this->assertTrue( is_int( $itinerary_post_id ) );

		// Create itinerary instance.
		$itinerary = new Itinerary( $itinerary_post_id );
		$departure = $itinerary->get_departure( strval( $raw_departure['id'] ) );
		$this->assertNotNull( $departure );
		$this->assertInstanceOf( Departure::class, $departure );

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
		$this->assertEquals( $raw_departure['code'], $departure->get_post_meta( 'softrip_code' ) );
		$this->assertEquals( $raw_departure['id'], $departure->get_post_meta( 'departure_unique_id' ) );
		$this->assertEquals( $raw_departure['packageCode'], $departure->get_post_meta( 'softrip_package_id' ) );
		$this->assertEquals( $raw_departure['startDate'], $departure->get_post_meta( 'departure_start_date' ) );
		$this->assertEquals( $raw_departure['endDate'], $departure->get_post_meta( 'departure_end_date' ) );
		$this->assertEquals( $raw_departure['duration'], $departure->get_post_meta( 'duration' ) );
		$this->assertEquals( $itinerary_post_id, $departure->get_post_meta( 'itinerary' ) );
		$this->assertEquals( $raw_departure['shipCode'], $departure->get_post_meta( 'ship_id' ) );
		$this->assertEquals( $raw_departure['marketCode'], $departure->get_post_meta( 'region' ) );

		// Validate with past dated departure.
		$raw_departure['id']        = 'JKL-012:2023-01-09';
		$raw_departure['startDate'] = gmdate( 'Y-m-d', strtotime( 'last year' ) );
		$raw_departure['endDate']   = gmdate( 'Y-m-d', strtotime( 'last year + 16 days' ) );

		// Get the departure.
		$departure = $itinerary->get_departure( $raw_departure['id'] );
		$this->assertNotNull( $departure );
		$this->assertInstanceOf( Departure::class, $departure );

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

		// Cleanup.
		wp_delete_post( $itinerary_post->ID, true );
		wp_delete_post( $itinerary_post_id, true );

		// Delete all departure posts.
		foreach ( $departure_posts->posts as $post_id ) {
			if ( ! is_int( $post_id ) ) {
				continue;
			}

			// Delete the post.
			wp_delete_post( $post_id, true );
		}
		unset( $departure );

		// Unset the itinerary object.
		unset( $itinerary );
	}

	/**
	 * Test get cabins.
	 *
	 * @covers \Quark\Softrip\Departure::get_cabins()
	 *
	 * @return void
	 */
	public function test_get_cabins(): void {
		// Create a itinerary post.
		$itinerary_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Test Itinerary Content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $itinerary_post instanceof WP_Post );

		// Create itinerary instance.
		$itinerary = new Itinerary( $itinerary_post->ID );

		// Raw departure data without any cabin data.
		$raw_departure_data = [
			'id'         => 'UNQ-123:2026-01-01',
			'code'       => 'LPL20260101',
			'startDate'  => '2026-01-01',
			'endDate'    => '2026-01-25',
			'duration'   => 7,
			'shipCode'   => 'YUP',
			'marketCode' => 'QTY',
		];

		// Create a departure object.
		$departure = new Departure();
		$departure->set_itinerary( $itinerary );
		$departure->set( $raw_departure_data, true );

		// Get the cabins.
		$cabins = $departure->get_cabins();
		$this->assertIsArray( $cabins );
		$this->assertEmpty( $cabins );

		// Add cabin raw data.
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

		// Update the departure.
		$departure->set( $raw_departure, true );

		// Again get the cabins.
		$cabins = $departure->get_cabins();

		// Assert the cabins.
		$this->assertIsArray( $cabins );
		$this->assertNotEmpty( $cabins );
		$this->assertCount( 2, $cabins );
		$this->assertArrayHasKey( 'CABIN1', $cabins );
		$this->assertArrayHasKey( 'CABIN2', $cabins );

		// Assert that cabins are instances of Cabin class.
		$this->assertInstanceOf( Cabin::class, $cabins['CABIN1'] );
		$this->assertInstanceOf( Cabin::class, $cabins['CABIN2'] );

		// Cleanup.
		wp_delete_post( $itinerary_post->ID, true );

		// Get all departure posts.
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'draft,publish',
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);

		// Delete all departure posts.
		foreach ( $departure_posts->posts as $post_id ) {
			if ( ! is_int( $post_id ) ) {
				continue;
			}

			// Delete the post.
			wp_delete_post( $post_id, true );
		}

		// Unset the departure object.
		unset( $departure );

		// Unset the cabin objects.
		unset( $cabins );

		// Unset the itinerary object.
		unset( $itinerary );
	}

	/**
	 * Test get a cabin by code.
	 *
	 * @covers \Quark\Softrip\Departure::get_cabin()
	 *
	 * @return void
	 */
	public function test_get_cabin(): void {
		// Create a itinerary post.
		$itinerary_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Test Itinerary Content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $itinerary_post instanceof WP_Post );

		// Create itinerary instance.
		$itinerary = new Itinerary( $itinerary_post->ID );

		// Create a departure instance.
		$departure = new Departure();
		$departure->set_itinerary( $itinerary );

		// Get a cabin by no code.
		$cabin = $departure->get_cabin();
		$this->assertNull( $cabin );

		// Get a cabin by non-existent code - creates a new cabin instance.
		$cabin = $departure->get_cabin( 'CABIN1' );
		$this->assertInstanceOf( Cabin::class, $cabin );
		$this->assertEmpty( $cabin->get_data() );
		$this->assertEmpty( $cabin->get_id() );
		$this->assertEmpty( $cabin->get_entry_data() );
		$this->assertEmpty( $cabin->get_entry_data( 'title' ) );

		// Raw departure data without any cabin data.
		$raw_departure_data = [
			'id'         => 'UNQ-123:2026-01-01',
			'code'       => 'LPL20260101',
			'startDate'  => '2026-01-01',
			'endDate'    => '2026-01-25',
			'duration'   => 7,
			'shipCode'   => 'YUP',
			'marketCode' => 'QTY',
			'cabins'     => [
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
			],
		];
		$departure->set( $raw_departure_data, true );

		// Get first cabin by code.
		$cabin1 = $departure->get_cabin( 'CABIN1' );
		$this->assertInstanceOf( Cabin::class, $cabin1 );
		$this->assertNotEmpty( $cabin1->get_entry_data() );
		$this->assertEquals( 'CABIN1', $cabin1->get_entry_data( 'title' ) );

		// Get second cabin by code.
		$cabin2 = $departure->get_cabin( 'CABIN2' );
		$this->assertInstanceOf( Cabin::class, $cabin2 );
		$this->assertNotEmpty( $cabin2->get_entry_data() );
		$this->assertEquals( 'CABIN2', $cabin2->get_entry_data( 'title' ) );

		// Cleanup.
		wp_delete_post( $itinerary_post->ID, true );

		// Get all departure posts.
		$departure_posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'draft,publish',
				'update_post_meta_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);

		// Delete all departure posts.
		foreach ( $departure_posts->posts as $post_id ) {
			if ( ! is_int( $post_id ) ) {
				continue;
			}

			// Delete the post.
			wp_delete_post( $post_id, true );
		}

		// Unset the departure object.
		unset( $departure );

		// Unset the cabin objects.
		unset( $cabin1 );
		unset( $cabin2 );

		// Unset the itinerary object.
		unset( $itinerary );
	}

	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Softrip\Departure::get_lowest_price()
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Create a test itinerary post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create an instance of Itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Departure instance.
		$departure = new Departure();

		// Without any currency.
		$actual = $departure->get_lowest_price();
		$this->assertEquals( 0, $actual );

		// Set itinerary.
		$departure->set_itinerary( $itinerary );
		$actual = $departure->get_lowest_price();
		$this->assertEquals( 0, $actual );

		// Departure data.
		$departure_data = [
			'id'          => 'UNQ-123:2026-02-28',
			'code'        => 'OEX20260228',
			'packageCode' => 'UNQ-123',
			'startDate'   => '2026-02-28',
			'endDate'     => '2026-03-11',
			'duration'    => 11,
			'shipCode'    => 'LOQ',
			'marketCode'  => 'ANT',
			'cabins'      => [
				[
					'id'          => 'UNQ-123:2026-02-28:LOQ-SGL',
					'code'        => 'LOQ-SGL',
					'name'        => 'Studio Single',
					'departureId' => 'UNQ-123:2026-02-28',
					'occupancies' => [
						[
							'id'                      => 'UNQ-123:2026-02-28:LOQ-SGL:A',
							'name'                    => 'UNQ-123:2026-02-28:LOQ-SGL:A',
							'mask'                    => 'A',
							'availabilityStatus'      => 'C',
							'availabilityDescription' => 'Unavailable',
							'spacesAvailable'         => 0,
							'seq'                     => '100',
							'prices'                  => [
								'USD' => [
									'pricePerPerson' => 34895,
									'currencyCode'   => 'USD',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 26171,
										],
									],
								],
								'AUD' => [
									'pricePerPerson' => 54795,
									'currencyCode'   => 'AUD',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 41096,
										],
									],
								],
								'CAD' => [
									'pricePerPerson' => 47495,
									'currencyCode'   => 'CAD',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 35621,
										],
									],
								],
								'EUR' => [
									'pricePerPerson' => 32495,
									'currencyCode'   => 'EUR',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 24371,
										],
									],
								],
								'GBP' => [
									'pricePerPerson' => 27995,
									'currencyCode'   => 'GBP',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 20996,
										],
									],
								],
							],
						],
					],
				],
			],
		];

		// Expected lowest price - inferred from above departure data.
		$expected_lowest_price = [
			'USD' => 34895,
			'AUD' => 54795,
			'CAD' => 47495,
			'EUR' => 32495,
			'GBP' => 27995,
		];

		// Set departure.
		$departure->set( $departure_data, true );

		// Get lowest price without currency - default USD.
		$lowest_price = $departure->get_lowest_price();
		$this->assertIsFloat( $lowest_price );
		$this->assertEquals( $expected_lowest_price['USD'], $lowest_price );

		// Verify with each currency.
		foreach ( $expected_lowest_price as $currency => $price ) {
			$lowest_price = $itinerary->get_lowest_price( $currency );
			$this->assertIsFloat( $lowest_price );
			$this->assertEquals( $price, $lowest_price );
		}

		// Cleanup.
		wp_delete_post( $post->ID, true );

		// Delete all departure posts.
		foreach ( $itinerary->get_departures() as $departure ) {
			wp_delete_post( $departure->get_id(), true );
		}
		unset( $itinerary );
		unset( $departure );
	}

	/**
	 * Test get ship.
	 *
	 * @covers \Quark\Softrip\Departure::get_ship()
	 *
	 * @return void
	 */
	public function test_get_ship(): void {
		// Create a test itinerary post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create some ships.
		$ship1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Ship 1',
				'post_content' => 'Ship 1 content',
				'post_type'    => SHIP_POST_TYPE,
				'meta_input'   => [
					'ship_id' => 'LOQ',
				],
			]
		);
		$this->assertTrue( $ship1 instanceof WP_Post );

		// Create some ships.
		$ship2 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Ship 2',
				'post_content' => 'Ship 2 content',
				'post_type'    => SHIP_POST_TYPE,
				'meta_input'   => [
					'ship_id' => 'LOQ2',
				],
			]
		);
		$this->assertTrue( $ship2 instanceof WP_Post );

		// Create an instance of Itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Departure instance.
		$departure = new Departure();

		// Without setting itinerary.
		$actual = $departure->get_ship();
		$this->assertEmpty( $actual );

		// Set itinerary.
		$departure->set_itinerary( $itinerary );
		$actual = $departure->get_ship();
		$this->assertEmpty( $actual );

		// Raw departure data.
		$raw_departure_data = [
			'id'          => 'UNQ-123:2026-02-28',
			'code'        => 'OEX20260228',
			'packageCode' => 'UNQ-123',
			'startDate'   => '2026-02-28',
			'endDate'     => '2026-03-11',
			'duration'    => 11,
			'shipCode'    => 'LOQ',
			'marketCode'  => 'ANT',
			'cabins'      => [
				[
					'id'          => 'UNQ-123:2026-02-28:LOQ-SGL',
					'code'        => 'LOQ-SGL',
					'name'        => 'Studio Single',
					'departureId' => 'UNQ-123:2026-02-28',
					'occupancies' => [
						[
							'id'                      => 'UNQ-123:2026-02-28:LOQ-SGL:A',
							'name'                    => 'UNQ-123:2026-02-28:LOQ-SGL:A',
							'mask'                    => 'A',
							'availabilityStatus'      => 'C',
							'availabilityDescription' => 'Unavailable',
							'spacesAvailable'         => 0,
							'seq'                     => '100',
							'prices'                  => [
								'USD' => [
									'pricePerPerson' => 34895,
									'currencyCode'   => 'USD',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 26171,
										],
									],
								],
								'AUD' => [
									'pricePerPerson' => 54795,
									'currencyCode'   => 'AUD',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 41096,
										],
									],
								],
								'CAD' => [
									'pricePerPerson' => 47495,
									'currencyCode'   => 'CAD',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 35621,
										],
									],
								],
								'EUR' => [
									'pricePerPerson' => 32495,
									'currencyCode'   => 'EUR',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 24371,
										],
									],
								],
								'GBP' => [
									'pricePerPerson' => 27995,
									'currencyCode'   => 'GBP',
									'promos'         => [
										'25PROMO' => [
											'promoPricePerPerson' => 20996,
										],
									],
								],
							],
						],
					],
				],
			],
		];

		// Set departure.
		$departure->set( $raw_departure_data, true );

		// Get ship.
		$actual = $departure->get_ship();
		$this->assertIsArray( $actual );
		$this->assertNotEmpty( $actual );
		$this->assertArrayHasKey( 'post', $actual );
		$this->assertArrayHasKey( 'post_meta', $actual );
		$this->assertInstanceOf( WP_Post::class, $actual['post'] );
		$this->assertEquals( $ship1->ID, $actual['post']->ID );

		// Cleanup.
		wp_delete_post( $post->ID, true );
		wp_delete_post( $ship1->ID, true );
		wp_delete_post( $ship2->ID, true );

		// Delete all departure posts.
		foreach ( $itinerary->get_departures() as $departure ) {
			wp_delete_post( $departure->get_id(), true );
		}
		unset( $itinerary );
		unset( $departure );
	}

	/**
	 * Test get starting date.
	 *
	 * @covers \Quark\Softrip\Departure::get_starting_date()
	 *
	 * @return void
	 */
	public function test_get_starting_date(): void {
		// Create a test itinerary post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create an instance of Itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Departure instance.
		$departure = new Departure();

		// Without setting itinerary.
		$actual = $departure->get_starting_date();
		$this->assertEmpty( $actual );

		// Set itinerary.
		$departure->set_itinerary( $itinerary );
		$actual = $departure->get_starting_date();
		$this->assertEmpty( $actual );

		// Raw departure data.
		$raw_departure_data = [
			'id'          => 'UNQ-123:2026-02-28',
			'code'        => 'OEX20260228',
			'packageCode' => 'UNQ-123',
			'startDate'   => '2026-02-28',
			'endDate'     => '2026-03-11',
			'duration'    => 11,
			'shipCode'    => 'LOQ',
			'marketCode'  => 'ANT',
		];

		// Set departure.
		$departure->set( $raw_departure_data, true );

		// Get starting date.
		$actual = $departure->get_starting_date();
		$this->assertEquals( $raw_departure_data['startDate'], $actual );
	}

	/**
	 * Test get ending date.
	 *
	 * @covers \Quark\Softrip\Departure::get_ending_date()
	 *
	 * @return void
	 */
	public function test_get_ending_date(): void {
		// Create a test itinerary post.
		$post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'UNQ-123',
				],
			]
		);
		$this->assertTrue( $post instanceof WP_Post );

		// Create an instance of Itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Departure instance.
		$departure = new Departure();

		// Without setting itinerary.
		$actual = $departure->get_ending_date();
		$this->assertEmpty( $actual );

		// Set itinerary.
		$departure->set_itinerary( $itinerary );
		$actual = $departure->get_ending_date();
		$this->assertEmpty( $actual );

		// Raw departure data.
		$raw_departure_data = [
			'id'          => 'UNQ-123:2026-02-28',
			'code'        => 'OEX20260228',
			'packageCode' => 'UNQ-123',
			'startDate'   => '2026-02-28',
			'endDate'     => '2026-03-11',
			'duration'    => 11,
			'shipCode'    => 'LOQ',
			'marketCode'  => 'ANT',
		];

		// Set departure.
		$departure->set( $raw_departure_data, true );

		// Get ending date.
		$actual = $departure->get_ending_date();
		$this->assertEquals( $raw_departure_data['endDate'], $actual );
	}
}
