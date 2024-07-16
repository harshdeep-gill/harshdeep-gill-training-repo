<?php
/**
 * Softrip test itinerary.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;
use WP_Post;
use WP_Error;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Itinerary.
 */
class Test_Itinerary extends WP_UnitTestCase {

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
	 * Test get_data.
	 *
	 * @covers \Quark\Softrip\Softrip_Object::get_data()
	 *
	 * @return void
	 */
	public function test_get_data(): void {
		// Get a post.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a new itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Get data.
		$data = $itinerary->get_data();

		// Test post.
		$this->assertEquals( $post, $data['post'] );

		// Test meta.
		$this->assertEquals( 1, $data['post_meta']['test_meta'] );
	}

	/**
	 * Test get_post_meta.
	 *
	 * @covers \Quark\Softrip\Softrip_Object::get_post_meta()
	 *
	 * @return void
	 */
	public function test_get_post_meta(): void {
		// Get a post.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a new itinerary.
		$valid_itinerary   = new Itinerary( $post->ID );
		$invalid_itinerary = new Itinerary();

		// Get data.
		$all_data = $valid_itinerary->get_post_meta();
		$single   = $valid_itinerary->get_post_meta( 'test_meta' );
		$invalid  = $invalid_itinerary->get_post_meta( 'nothing' );

		// Test data.
		$this->assertEquals( [ 'test_meta' => 1 ], $all_data );
		$this->assertEquals( 1, $single );
		$this->assertEquals( '', $invalid );
	}

	/**
	 * Test get_departures.
	 *
	 * @covers \Quark\Softrip\Itinerary::get_departures()
	 *
	 * @return void
	 */
	public function test_get_departures(): void {
		// Get post and itinerary.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a new itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Setup departure.
		$departure  = $itinerary->get_departure( 'test_departure' );
		$departures = $itinerary->get_departures();

		// Test if in array.
		$this->assertTrue( $departures['test_departure'] instanceof Departure );
		$this->assertEquals( $departure, $departures['test_departure'] );
	}

	/**
	 * Test get_id.
	 *
	 * @covers \Quark\Softrip\Softrip_Object::get_id()
	 *
	 * @return void
	 */
	public function test_get_id(): void {
		// Get post and itinerary.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a new itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Test id.
		$this->assertEquals( $itinerary->get_id(), $post->ID );
	}

	/**
	 * Test update_departures.
	 *
	 * @covers \Quark\Softrip\Itinerary::update_departures()
	 *
	 * @return void
	 */
	public function test_update_departures(): void {
		// Get post and itinerary.
		$post = $this->get_post();

		// Test if is a post.
		if ( $post instanceof WP_Error ) {
			return;
		}

		// Create a new itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Setup test data.
		$test_data = [
			'departures' => [
				[
					'id' => 'test_departure_one',
				],
				[
					'id' => 'test_departure_two',
				],
			],
		];

		// update departures.
		$itinerary->update_departures( $test_data );

		// get departures.
		$departures = $itinerary->get_departures();
		$this->assertTrue( $departures['test_departure_one'] instanceof Departure );
		$this->assertTrue( $departures['test_departure_two'] instanceof Departure );

		// test a single departure.
		$departure      = $itinerary->get_departure( 'test_departure_two' );
		$departure_id   = $departure->get_id();
		$departure_post = get_post( $departure_id );

		// Test if departure is correct.
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Test post is correct.
		if ( $departure_post instanceof WP_Post ) {
			$this->assertEquals( DEPARTURE_POST_TYPE, $departure_post->post_type );
		}
	}
}
