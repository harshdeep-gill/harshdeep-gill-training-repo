<?php
/**
 * Softrip test itinerary.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;

/**
 * Class Test_Itinerary.
 */
class Test_Itinerary extends WP_UnitTestCase {

	/**
	 * Test creating an itinerary object.
	 *
	 * @return void
	 */
	public function test_create() {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => \Quark\Itineraries\POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);

		// Check if is instance.
		if ( $post_1 instanceof \WP_Post ) {
			// Create a new itinerary.
			$itinerary = new Itinerary( $post_1->ID );

			// Assert meta exists.
			$this->assertEquals( 1, $itinerary->get_post_meta( 'test_meta' ) );
		}
	}
}
