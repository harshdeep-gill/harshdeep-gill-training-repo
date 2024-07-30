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
	 * Itinerary post.
	 *
	 * @var WP_Post|null
	 */
	protected static ?WP_Post $itinerary_post = null;

	/**
	 * Departure posts.
	 *
	 * @var mixed[]
	 */
	protected static array $departure_ids = [];

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		// Run parent and include setup.
		parent::set_up_before_class();
		include_once 'setup.php';

		// Create a test itinerary post.
		$post = self::factory()->post->create_and_get(
			[
				'post_title'   => 'Test Itinerary',
				'post_content' => 'Itinerary content',
				'post_status'  => 'publish',
				'post_type'    => ITINERARY_POST_TYPE,
				'meta_input'   => [
					'test_meta'          => 1,
					'softrip_package_id' => 'ABC-123',
				],
			]
		);

		// Set itinerary post.
		self::$itinerary_post = $post instanceof WP_Post ? $post : null;

		// Create some test departures.
		self::$departure_ids = self::factory()->post->create_many(
			15,
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_status' => 'publish',
				'post_parent' => $post instanceof WP_Post ? $post->ID : null,
			]
		);

		// Loop through the departures and set meta.
		foreach ( self::$departure_ids as $departure_id ) {
			update_post_meta( absint( $departure_id ), 'departure_unique_id', rand_str( 10 ) );
			wp_cache_delete( DEPARTURE_POST_TYPE . '_' . absint( $departure_id ), DEPARTURE_POST_TYPE );
		}
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

		// Delete the test itinerary post.
		if ( self::$itinerary_post instanceof WP_Post ) {
			wp_delete_post( self::$itinerary_post->ID, true );
		}

		// Delete the test departures.
		foreach ( self::$departure_ids as $departure_id ) {
			wp_delete_post( absint( $departure_id ), true );
		}

		// Reset the itinerary post.
		self::$itinerary_post = null;

		// Reset the departure ids.
		self::$departure_ids = [];
	}

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// Mock the response for the POST request.
		add_filter( 'pre_http_request', 'Quark\Softrip\mock_http_request', 10, 3 );
	}

	/**
	 * Tear down after tests.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// Remove the filter.
		remove_filter( 'pre_http_request', 'Quark\Softrip\mock_http_request' );
	}

	/**
	 * Test get_data.
	 *
	 * @covers \Quark\Softrip\Itinerary::__construct()
	 * @covers \Quark\Softrip\Itinerary::load()
	 * @covers \Quark\Softrip\Softrip_Object::get_data()
	 *
	 * @return void
	 */
	public function test_get_data(): void {
		// Get post.
		$post = self::$itinerary_post;

		// Test if is a post.
		if ( ! $post instanceof WP_Post ) {
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
		// Get post.
		$post = self::$itinerary_post;

		// Test if is a post.
		if ( ! $post instanceof WP_Post ) {
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
		$this->assertEquals(
			[
				'test_meta'          => 1,
				'softrip_package_id' => 'ABC-123',
			],
			$all_data
		);
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
		// Get post.
		$post = self::$itinerary_post;

		// Test if is a post.
		if ( ! $post instanceof WP_Post ) {
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
		// Get post.
		$post = self::$itinerary_post;

		// Test if is a post.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Create a new itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Test id.
		$this->assertEquals( $itinerary->get_id(), $post->ID );
	}

	/**
	 * Test load_departures.
	 *
	 * @covers \Quark\Softrip\Itinerary::load_departures()
	 *
	 * @return void
	 */
	public function test_load_departures(): void {
		// Get post.
		$post = self::$itinerary_post;

		// Test if is a post.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Create a new itinerary.
		$itinerary = new Itinerary( $post->ID );

		// Get departures.
		$departures = $itinerary->get_departures();

		// assert departures.
		$this->assertIsArray( $departures );
		$this->assertCount( 15, $departures );

		// Pick random departure from array.
		$random_departure_id = self::$departure_ids[ array_rand( self::$departure_ids ) ];

		// Get departure unique id.
		$departure_unique_id = get_post_meta( absint( $random_departure_id ), 'departure_unique_id', true );
		$random_departure    = $itinerary->get_departure( strval( $departure_unique_id ) );

		// Test if departure is correct.
		$this->assertEquals( $random_departure->get_id(), $random_departure_id );
	}

	/**
	 * Test update_departures.
	 *
	 * @covers \Quark\Softrip\Itinerary::update_departures()
	 *
	 * @return void
	 */
	public function test_update_departures(): void {
		// Get post.
		$post = self::$itinerary_post;

		// Test if is a post.
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Create a new itinerary.
		$itinerary  = new Itinerary( $post->ID );
		$departures = $itinerary->get_departures();

		// Update departures.
		$itinerary->update_departures();

		// Test if is a WP_Error.
		$new_itinerary  = new Itinerary( $post->ID );
		$new_departures = $new_itinerary->get_departures();

		// Test if is an array.
		$this->assertIsArray( $departures );
		$this->assertIsArray( $new_departures );

		// Test if is not equal.
		$this->assertNotEquals( $departures, $new_departures );

		// Assert array key not exist.
		$this->assertArrayNotHasKey( 'ABC-123:2026-02-28', $departures );
		$this->assertArrayHasKey( 'ABC-123:2026-02-28', $new_departures );
	}
}
