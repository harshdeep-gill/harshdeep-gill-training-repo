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

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Departure.
 */
class Test_Departure extends WP_UnitTestCase {

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
}
