<?php
/**
 * Softrip test cabins.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;
use WP_Post;
use WP_Error;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Cabins.
 */
class Test_Cabins extends WP_UnitTestCase {
	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// Run parent.
		parent::set_up();

		// Mock the response for the POST request.
		add_filter( 'pre_http_request', 'Quark\Tests\mock_http_request', 10, 3 );
	}

	/**
	 * Tear down after tests.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// Run parent.
		parent::tear_down();

		// Remove the filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\mock_http_request' );
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
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
	}

	/**
	 * Make a cabin object.
	 *
	 * @return Cabin
	 */
	public function get_cabin(): Cabin {
		// Create Cabin.
		$cabin          = new Cabin();
		$departure_post = $this->get_post();

		// Add a departure post if found.
		if ( ! $departure_post instanceof WP_Error ) {
			$departure = new Departure();
			$departure->load( $departure_post->ID );
			$cabin->set_departure( $departure );
		}

		// Return new object.
		return $cabin;
	}

	/**
	 * Get test object.
	 *
	 * @return mixed[]
	 */
	public function get_test_data(): array {
		// Define a json string.
		$json = '{
            "id": "ARC-ISLN-15D2024:2024-08-25:OAD-TPL",
            "code": "OAD-TPL",
            "name": "Triple",
            "departureId": "ARC-ISLN-15D2024:2024-08-25"
        }';

		// Return the parsed array.
		return json_decode( $json, true );
	}

	/**
	 * Test load departure.
	 *
	 * @covers \Quark\Softrip\Cabin::set_departure()
	 *
	 * @return void
	 */
	public function test_set_departure(): void {
		// Get a cabin to test with.
		$cabin = $this->get_cabin();
		$data  = $this->get_test_data();
		$cabin->set( $data );

		// Get departure.
		$departure_id = absint( $cabin->get_entry_data( 'departure' ) );
		$this->assertNotEmpty( $departure_id );

		// Test departure type.
		$post_type = get_post_type( $departure_id );
		$this->assertEquals( DEPARTURE_POST_TYPE, $post_type );
	}

	/**
	 * Test cabin save
	 *
	 * @covers \Quark\Softrip\Cabin::save()
	 *
	 * @return void
	 */
	public function test_cabin_save(): void {
		// Get a cabin to test with.
		$cabin = $this->get_cabin();
		$data  = $this->get_test_data();
		$cabin->set( $data );

		// Assert no ID.
		$this->assertEquals( '', $cabin->get_entry_data( 'id' ) );

		// Save data to create an ID.
		$cabin->save();

		// Test ID exist.
		$this->assertNotEmpty( $cabin->get_entry_data( 'id' ) );
	}
}
