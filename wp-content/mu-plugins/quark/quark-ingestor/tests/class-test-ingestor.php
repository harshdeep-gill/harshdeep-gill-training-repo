<?php
/**
 * Test suite for ingestor.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Error;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\cron_is_scheduled;
use function Quark\Ingestor\cron_schedule_push;
use function Quark\Ingestor\do_push;
use function Quark\Ingestor\get_all_data;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ingestor\push_expedition_data;
use function Quark\Softrip\do_sync;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Ingestor\DATA_HASH_KEY;
use const Quark\Ingestor\SCHEDULE_HOOK;

const TEST_IMAGE_PATH = __DIR__ . '/data/test-image.jpg';

/**
 * Class Test_Ingestor
 */
class Test_Ingestor extends Softrip_TestCase {
	/**
	 * The URL used for the push request.
	 *
	 * @var string
	 */
	protected $push_url;

	/**
	 * The method used for the push request.
	 *
	 * @var string
	 */
	protected $push_method;

	/**
	 * Push error data.
	 *
	 * @var mixed[]
	 */
	protected $push_error_data;

	/**
	 * Push initiated data.
	 *
	 * @var mixed[]
	 */
	protected $push_initiated_data;

	/**
	 * Push success data.
	 *
	 * @var mixed[]
	 */
	protected $push_success_data;

	/**
	 * Push completed data.
	 *
	 * @var mixed[]
	 */
	protected $push_completed_data;

	/**
	 * Test for cron_is_scheduled.
	 *
	 * @covers \Quark\Ingestor\cron_is_scheduled()
	 *
	 * @return void
	 */
	public function test_cron_is_scheduled(): void {
		// Clear any existing scheduled event for testing.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );

		// Test case 1: Test if cron is not scheduled.
		$result = cron_is_scheduled();
		$this->assertFalse( $result );

		// Test case 2: Test if cron is scheduled.
		cron_schedule_push();
		$result = cron_is_scheduled();
		$this->assertTrue( $result );
	}

	/**
	 * Test for scheduling sync cron task.
	 *
	 * @covers \Quark\Ingestor\cron_schedule_push()
	 *
	 * @return void
	 */
	public function test_cron_schedule_push(): void {
		// Clear any existing scheduled event for testing.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );
		$timestamp = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertFalse( $timestamp );

		// Test case 1: Test scheduling push cron task.
		cron_schedule_push();
		$timestamp = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertNotFalse( $timestamp );

		// Verify the schedule interval.
		$schedule = wp_get_schedule( SCHEDULE_HOOK );
		$this->assertSame( 'hourly', $schedule );

		// Test case 2: Test if repeated call should not schedule again.
		cron_schedule_push();
		$timestamp2 = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertNotFalse( $timestamp2 );
		$this->assertSame( $timestamp, $timestamp2 );

		// Cleanup.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );
	}

	/**
	 * Test do push for no expedition posts.
	 *
	 * @covers \Quark\Ingestor\do_push
	 *
	 * @return void
	 */
	public function test_do_push_no_expedition_posts(): void {
		// Get all expedition posts.
		$expedition_posts = get_posts(
			[
				'post_type'      => EXPEDITION_POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			]
		);

		// Convert to int.
		$expedition_posts = array_map( 'absint', $expedition_posts );

		// Delete all expedition posts.
		foreach ( $expedition_posts as $expedition_post_id ) {
			wp_delete_post( $expedition_post_id, true );
		}

		// Setup action listeners.
		add_action( 'quark_ingestor_push_error', [ $this, 'ingestor_push_error' ] );
		add_action( 'quark_ingestor_push_initiated', [ $this, 'ingestor_push_initiated' ] );
		add_action( 'quark_ingestor_push_success', [ $this, 'ingestor_push_success' ] );
		add_action( 'quark_ingestor_push_completed', [ $this, 'ingestor_push_completed' ] );

		// Test: No expedition posts exist.
		do_push();
		$this->assertEquals( 1, did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_success' ) );
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_completed' ) );

		// Error data 1.
		$error_data1 = [
			'error'         => 'No expeditions found.',
			'initiated_via' => 'manually',
		];

		// Verify error data.
		$this->assertNotEmpty( $this->push_error_data );
		$this->assertEquals(
			[
				$error_data1,
			],
			$this->push_error_data
		);

		// Test: Invalid expedition id passed.
		$expedition_post_id = 999999;
		do_push( [ $expedition_post_id ] );
		$this->assertEquals( 2, did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_success' ) );
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_completed' ) );

		// Error data 2.
		$error_data2 = [
			'error'         => 'No expeditions found.',
			'initiated_via' => 'manually',
		];

		// Verify error data.
		$this->assertNotEmpty( $this->push_error_data );
		$this->assertEquals(
			[
				$error_data1,
				$error_data2,
			],
			$this->push_error_data
		);

		// Reset all data.
		$this->push_error_data     = [];
		$this->push_initiated_data = [];
		$this->push_success_data   = [];
		$this->push_completed_data = [];
	}

	/**
	 * Test do push.
	 *
	 * @covers \Quark\Ingestor\do_push
	 *
	 * @return void
	 */
	public function test_do_push(): void {
		// Setup action listeners.
		add_action( 'quark_ingestor_push_error', [ $this, 'ingestor_push_error' ] );
		add_action( 'quark_ingestor_push_initiated', [ $this, 'ingestor_push_initiated' ] );
		add_action( 'quark_ingestor_push_success', [ $this, 'ingestor_push_success' ] );
		add_action( 'quark_ingestor_push_completed', [ $this, 'ingestor_push_completed' ] );

		// Setup mock response.
		add_filter( 'pre_http_request', '\Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync all data.
		do_sync();

		// Flush cache.
		wp_cache_flush();

		// Remove Softrip mock response.
		remove_filter( 'pre_http_request', '\Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Setup mock response.
		add_filter( 'pre_http_request', '\Quark\Tests\Ingestor\mock_ingestor_http_request', 10, 3 );

		// Get all expedition ids.
		$expedition_post_ids = get_posts(
			[
				'post_type'      => EXPEDITION_POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'orderby'        => 'id',
				'order'          => 'ASC',
			]
		);

		// Convert to int.
		$expedition_post_ids = array_map( 'absint', $expedition_post_ids );

		// Do push.
		do_push();

		// Verify if actions were triggered.
		$this->assertEquals( 0, did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 1, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( count( $expedition_post_ids ), did_action( 'quark_ingestor_push_success' ) );
		$this->assertEquals( 1, did_action( 'quark_ingestor_push_completed' ) );

		// Verify push initiated data.
		$this->assertNotEmpty( $this->push_initiated_data );
		$this->assertEquals(
			[
				[
					'expedition_post_ids' => $expedition_post_ids,
					'initiated_via'       => 'manually',
					'changed_only'        => true,
					'total_count'         => count( $expedition_post_ids ),
				],
			],
			$this->push_initiated_data
		);

		// Prepare success data for each expedition.
		$expected_push_success_data = [];

		// Prepare expected push success data.
		foreach ( $expedition_post_ids as $expedition_post_id ) {
			// Data hash.
			$hash = get_post_meta( $expedition_post_id, DATA_HASH_KEY, true );

			// Add to expected push success data.
			$expected_push_success_data[] = [
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => 'manually',
				'changed_only'       => true,
				'hash'               => $hash,
			];
		}

		// Verify push success data.
		$this->assertNotEmpty( $this->push_success_data );

		// Assert.
		$this->assertEquals(
			$expected_push_success_data,
			$this->push_success_data
		);

		// Reset all data.
		$this->push_error_data     = [];
		$this->push_initiated_data = [];
		$this->push_success_data   = [];
		$this->push_completed_data = [];

		// Try pushing again without any changes.
		do_push();

		// Verify if actions were triggered.
		$this->assertEquals( count( $expedition_post_ids ), did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 2, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( 5, did_action( 'quark_ingestor_push_success' ) ); // Old one.
		$this->assertEquals( 2, did_action( 'quark_ingestor_push_completed' ) );

		// Verify push initiated data.
		$this->assertNotEmpty( $this->push_initiated_data );

		// Prepare expected push initiated data.
		$expected_push_initiated_data = [
			[
				'expedition_post_ids' => $expedition_post_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $expedition_post_ids ),
			],
		];

		// Verify push initiated data.
		$this->assertEquals(
			$expected_push_initiated_data,
			$this->push_initiated_data
		);

		// Assert success data - no new data pushed.
		$this->assertEmpty( $this->push_success_data );
		$this->assertSame( [], $this->push_success_data );

		// Prepare expected push completed data.
		$expected_push_completed_data = [
			[
				'expedition_post_ids' => $expedition_post_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $expedition_post_ids ),
				'success_count'       => 0,
			],
		];

		// Verify push completed data.
		$this->assertNotEmpty( $this->push_completed_data );
		$this->assertEquals(
			$expected_push_completed_data,
			$this->push_completed_data
		);

		// Prepare expected error data.
		$expected_push_error_data = [];

		// Prepare expected push error data.
		foreach ( $expedition_post_ids as $expedition_post_id ) {
			$expected_push_error_data[] = [
				'error'              => 'No changes detected.',
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => 'manually',
			];
		}

		// Verify push error data.
		$this->assertNotEmpty( $this->push_error_data );
		$this->assertEquals(
			$expected_push_error_data,
			$this->push_error_data
		);

		// Create a draft expedition post.
		$draft_expedition_post_id = $this->factory()->post->create(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_status' => 'draft',
			]
		);
		$this->assertIsInt( $draft_expedition_post_id );

		// Reset error data.
		$this->push_error_data = [];

		// Do push.
		do_push( [], true );

		// Verify if actions were triggered.
		$this->assertEquals( 10, did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 3, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( 6, did_action( 'quark_ingestor_push_success' ) );
		$this->assertEquals( 3, did_action( 'quark_ingestor_push_completed' ) );

		// Verify push initiated data.
		$this->assertNotEmpty( $this->push_initiated_data );

		// Add draft to expedition post ids.
		$new_post_expedition_ids = $expedition_post_ids;
		array_unshift( $new_post_expedition_ids, $draft_expedition_post_id );

		// Prepare expected push initiated data.
		$expected_push_initiated_data = [
			[
				'expedition_post_ids' => $expedition_post_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $expedition_post_ids ),
			],
			[
				'expedition_post_ids' => $new_post_expedition_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $new_post_expedition_ids ),
			],
		];

		// Verify push initiated data.
		$this->assertEquals(
			$expected_push_initiated_data,
			$this->push_initiated_data
		);

		// Prepare expected push completed data.
		$expected_push_completed_data = [
			[
				'expedition_post_ids' => $expedition_post_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $expedition_post_ids ),
				'success_count'       => 0,
			],
			[
				'expedition_post_ids' => $new_post_expedition_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $new_post_expedition_ids ),
				'success_count'       => 1,
			],
		];

		// Verify push completed data.
		$this->assertNotEmpty( $this->push_completed_data );
		$this->assertEquals(
			$expected_push_completed_data,
			$this->push_completed_data
		);

		// Prepare expected error data.
		$expected_push_error_data = [];

		// Prepare expected push error data.
		foreach ( $expedition_post_ids as $expedition_post_id ) {
			$expected_push_error_data[] = [
				'error'              => 'No changes detected.',
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => 'manually',
			];
		}

		// Verify push error data.
		$this->assertNotEmpty( $this->push_error_data );
		$this->assertEquals(
			$expected_push_error_data,
			$this->push_error_data
		);

		// Data hash.
		$hash = get_post_meta( $draft_expedition_post_id, DATA_HASH_KEY, true );

		// Prepare expected success data.
		$expected_push_success_data = [
			[
				'expedition_post_id' => $draft_expedition_post_id,
				'initiated_via'      => 'manually',
				'changed_only'       => true,
				'hash'               => $hash,
			],
		];

		// Verify push success data.
		$this->assertNotEmpty( $this->push_success_data );

		// Unset file_name from success data.
		$actual_push_success_data = $this->push_success_data;

		// Loop through success data.
		foreach ( $actual_push_success_data as $key => $data ) {
			// Unset file_name from success data.
			if ( is_array( $actual_push_success_data[ $key ] ) && ! empty( $actual_push_success_data[ $key ]['file_name'] ) ) {
				unset( $actual_push_success_data[ $key ]['file_name'] );
			}
		}

		// Verify push success data.
		$this->assertEquals(
			$expected_push_success_data,
			$actual_push_success_data
		);

		// Reset all data.
		$this->push_error_data     = [];
		$this->push_initiated_data = [];
		$this->push_success_data   = [];
		$this->push_completed_data = [];

		// Let's try pushing all without concerning of changes.
		do_push( [], false );

		// Verify if actions were triggered.
		$this->assertEquals( 10, did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 4, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( 12, did_action( 'quark_ingestor_push_success' ) );
		$this->assertEquals( 4, did_action( 'quark_ingestor_push_completed' ) );

		// Verify push initiated data.
		$this->assertNotEmpty( $this->push_initiated_data );

		// Prepare expected push initiated data.
		$expected_push_initiated_data = [
			[
				'expedition_post_ids' => $new_post_expedition_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => false,
				'total_count'         => count( $new_post_expedition_ids ),
			],
		];

		// Verify push initiated data.
		$this->assertEquals(
			$expected_push_initiated_data,
			$this->push_initiated_data
		);

		// Verify push success data.
		$this->assertNotEmpty( $this->push_success_data );

		// Prepare expected push success data.
		$expected_push_success_data = [];

		// Prepare expected push success data.
		foreach ( $new_post_expedition_ids as $expedition_post_id ) {
			// Data hash.
			$hash = get_post_meta( $expedition_post_id, DATA_HASH_KEY, true );

			// Add to expected push success data.
			$expected_push_success_data[] = [
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => 'manually',
				'changed_only'       => false,
				'hash'               => $hash,
			];
		}

		// Unset file_name from success data.
		$actual_push_success_data = $this->push_success_data;

		// Loop through success data.
		foreach ( $actual_push_success_data as $key => $data ) {
			// Unset file_name from success data.
			if ( is_array( $actual_push_success_data[ $key ] ) && ! empty( $actual_push_success_data[ $key ]['file_name'] ) ) {
				unset( $actual_push_success_data[ $key ]['file_name'] );
			}
		}

		// Verify push success data.
		$this->assertEquals(
			$expected_push_success_data,
			$actual_push_success_data
		);

		// Prepare expected push completed data.
		$expected_push_completed_data = [
			[
				'expedition_post_ids' => $new_post_expedition_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => false,
				'total_count'         => count( $new_post_expedition_ids ),
				'success_count'       => count( $new_post_expedition_ids ),
			],
		];

		// Verify push completed data.
		$this->assertNotEmpty( $this->push_completed_data );

		// Get a cabin category post.
		$cabin_category_post = get_posts(
			[
				'post_type'              => CABIN_CATEGORY_POST_TYPE,
				'meta_key'               => 'cabin_category_id',
				'meta_value'             => 'OEX-SGL',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);

		// Convert to int.
		$this->assertNotEmpty( $cabin_category_post );
		$cabin_category_post = array_map( 'absint', $cabin_category_post );
		$cabin_category_post = $cabin_category_post[0];

		// Update cabin category post title.
		wp_update_post(
			[
				'ID'         => $cabin_category_post,
				'post_title' => 'Updated Cabin Category',
			]
		);

		// Reset all data.
		$this->push_error_data     = [];
		$this->push_initiated_data = [];
		$this->push_success_data   = [];
		$this->push_completed_data = [];

		// Let's try pushing all with only changed ones.
		do_push( [], true );

		// Verify if actions were triggered.
		$this->assertEquals( 14, did_action( 'quark_ingestor_push_error' ) );
		$this->assertEquals( 5, did_action( 'quark_ingestor_push_initiated' ) );
		$this->assertEquals( 14, did_action( 'quark_ingestor_push_success' ) ); // Should be 13.
		$this->assertEquals( 5, did_action( 'quark_ingestor_push_completed' ) );

		// Verify push initiated data.
		$this->assertNotEmpty( $this->push_initiated_data );

		// Prepare expected push initiated data.
		$expected_push_initiated_data = [
			[
				'expedition_post_ids' => $new_post_expedition_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $new_post_expedition_ids ),
			],
		];

		// Verify push initiated data.
		$this->assertEquals(
			$expected_push_initiated_data,
			$this->push_initiated_data
		);

		// Verify push success data.
		$this->assertNotEmpty( $this->push_success_data );

		// Prepare expected push success data.
		$expected_push_success_data = [];

		// Actual push success data.
		$actual_push_success_data = $this->push_success_data;

		// Updated expedition post id.
		$updated_post_ids = [ $new_post_expedition_ids[1], $new_post_expedition_ids[2] ];

		// Non-updated.
		$non_updated_post_ids = array_diff( $new_post_expedition_ids, $updated_post_ids );

		// Prepare expected push success data.
		foreach ( $updated_post_ids as $expedition_post_id ) {
			// Data hash.
			$hash = get_post_meta( $expedition_post_id, DATA_HASH_KEY, true );

			// Add to expected push success data.
			$expected_push_success_data[] = [
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => 'manually',
				'changed_only'       => true,
				'hash'               => $hash,
			];
		}

		// Verify push success data.
		$this->assertEquals(
			$expected_push_success_data,
			$actual_push_success_data
		);

		// Prepare expected push completed data.
		$expected_push_completed_data = [
			[
				'expedition_post_ids' => $new_post_expedition_ids,
				'initiated_via'       => 'manually',
				'changed_only'        => true,
				'total_count'         => count( $new_post_expedition_ids ),
				'success_count'       => 2,
			],
		];

		// Verify push completed data.
		$this->assertNotEmpty( $this->push_completed_data );

		// Verify push completed data.
		$this->assertEquals(
			$expected_push_completed_data,
			$this->push_completed_data
		);

		// Verify error data.
		$this->assertNotEmpty( $this->push_error_data );

		// Prepare expected push error data.
		$expected_push_error_data = [];

		// Prepare expected push error data.
		foreach ( $non_updated_post_ids as $expedition_post_id ) {
			$expected_push_error_data[] = [
				'error'              => 'No changes detected.',
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => 'manually',
			];
		}

		// Verify push error data.
		$this->assertEquals(
			$expected_push_error_data,
			$this->push_error_data
		);

		// Remove filter.
		remove_filter( 'pre_http_request', '\Quark\Tests\Ingestor\mock_ingestor_http_request', 10 );

		// Reset.
		$this->push_completed_data = [];
		$this->push_error_data     = [];
		$this->push_initiated_data = [];
		$this->push_success_data   = [];
	}

	/**
	 * Listen for error during push.
	 *
	 * @param mixed[] $data The data.
	 *
	 * @return void
	 */
	public function ingestor_push_error( array $data = [] ): void {
		// Add data to the array.
		$this->push_error_data[] = $data;
	}

	/**
	 * Listen for push initiated.
	 *
	 * @param mixed[] $data The data.
	 *
	 * @return void
	 */
	public function ingestor_push_initiated( array $data = [] ): void {
		// Add data to the array.
		$this->push_initiated_data[] = $data;
	}

	/**
	 * Listen for push success.
	 *
	 * @param mixed[] $data The data.
	 *
	 * @return void
	 */
	public function ingestor_push_success( array $data = [] ): void {
		// Add data to the array.
		$this->push_success_data[] = $data;
	}

	/**
	 * Listen for push completed.
	 *
	 * @param mixed[] $data The data.
	 *
	 * @return void
	 */
	public function ingestor_push_completed( array $data = [] ): void {
		// Add data to the array.
		$this->push_completed_data[] = $data;
	}

	/**
	 * Test push expedition data.
	 *
	 * @covers \Quark\Ingestor\push_expedition_data
	 *
	 * @return void
	 */
	public function test_push_expedition_data(): void {
		// Test with no arguments.
		$actual = push_expedition_data();
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_ingestor_invalid_expedition_id', $actual->get_error_code() );
		$this->assertSame( 'Invalid expedition post ID.', $actual->get_error_message() );

		// Test with invalid post id.
		$actual = push_expedition_data( 0 );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_ingestor_invalid_expedition_id', $actual->get_error_code() );
		$this->assertSame( 'Invalid expedition post ID.', $actual->get_error_message() );

		// Test with no expedition data.
		$actual = push_expedition_data( 999999 );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_ingestor_invalid_expedition_data', $actual->get_error_code() );
		$this->assertSame( 'Invalid expedition data.', $actual->get_error_message() );

		// Test with empty expedition data.
		$actual = push_expedition_data( 999999, '' );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_ingestor_invalid_expedition_data', $actual->get_error_code() );
		$this->assertSame( 'Invalid expedition data.', $actual->get_error_message() );

		// Test: Check for API credentials.
		$this->assertTrue( defined( 'QUARK_INGESTOR_BASE_URL' ) );
		$this->assertTrue( defined( 'QUARK_INGESTOR_API_KEY' ) );
		$this->assertNotEmpty( QUARK_INGESTOR_BASE_URL );
		$this->assertNotEmpty( QUARK_INGESTOR_API_KEY );

		// Sample expedition data.
		$expedition_data = [
			'id'           => 123,
			'name'         => 'Test Expedition',
			'published'    => true,
			'description'  => 'Test description.',
			'images'       => [
				[
					'id'           => 1,
					'fullSizeUrl'  => 'https://example.com/image1.jpg',
					'thumbnailUrl' => 'https://example.com/image1-thumbnail.jpg',
					'alt'          => 'Image 1',
				],
				[
					'id'           => 2,
					'fullSizeUrl'  => 'https://example.com/image2.jpg',
					'thumbnailUrl' => 'https://example.com/image2-thumbnail.jpg',
					'alt'          => 'Image 2',
				],
			],
			'destinations' => [
				[
					'id'     => 1,
					'name'   => 'Destination 1',
					'region' => [
						'name' => 'Region 1',
						'code' => '123',
					],
				],
				[
					'id'     => 2,
					'name'   => 'Destination 2',
					'region' => [
						'name' => 'Region 2',
						'code' => '456',
					],
				],
			],
			'itineraries'  => [
				[
					'id'            => 1,
					'packageId'     => 'UNQ-123',
					'name'          => 'Itinerary 1',
					'published'     => true,
					'startLocation' => 'Start Location 1',
					'endLocation'   => 'End Location 1',
					'departures'    => [],
				],
				[
					'id'            => 2,
					'packageId'     => 'UNQ-456',
					'name'          => 'Itinerary 2',
					'published'     => true,
					'startLocation' => 'Start Location 2',
					'endLocation'   => 'End Location 2',
					'departures'    => [],
				],
			],
		];

		// Json encoded data.
		$expedition_data_json = strval( wp_json_encode( $expedition_data ) );

		// Setup mock response.
		add_filter( 'pre_http_request', [ $this, 'mock_ingestor_http_request' ], 10, 3 );

		// Test with valid expedition data.
		$actual = push_expedition_data( 123, $expedition_data_json );
		$this->assertTrue( $actual );
		$this->assertSame( 'POST', $this->push_method );
		$this->assertStringContainsString( QUARK_INGESTOR_BASE_URL, $this->push_url );

		// Test with invalid expedition data.
		$actual = push_expedition_data( 123, 'invalid-json' );
		$this->assertTrue( $actual instanceof WP_Error );
		$this->assertSame( 'qrk_ingestor_invalid_response', $actual->get_error_code() );
		$this->assertSame( 'Bad Request', $actual->get_error_message() );
		$this->assertStringContainsString( QUARK_INGESTOR_BASE_URL, $this->push_url );

		// Remove filter.
		remove_filter( 'pre_http_request', [ $this, 'mock_ingestor_http_request' ], 10 );
	}

	/**
	 * Mock the HTTP request.
	 *
	 * @param mixed[]|false $response    The response.
	 * @param mixed[]       $parsed_args The parsed args.
	 * @param string|null   $url         The URL.
	 *
	 * @return false|array{}|array{
	 *    body: string|false,
	 *    response: array{
	 *      code: int,
	 *      message: string,
	 *    },
	 *    headers: array{},
	 * }
	 */
	public function mock_ingestor_http_request( array|false $response = [], array $parsed_args = [], string $url = null ): false|array {
		// Validate URL.
		if ( empty( $url ) ) {
			return $response;
		}

		// Bail if base URL is not defined.
		if ( ! defined( 'QUARK_INGESTOR_BASE_URL' ) || empty( QUARK_INGESTOR_BASE_URL )
		|| ! defined( 'QUARK_INGESTOR_API_KEY' ) || empty( QUARK_INGESTOR_API_KEY )
		) {
			return $response;
		}

		// Check if the URL is the one we want to mock.
		if ( ! str_contains( $url, QUARK_INGESTOR_BASE_URL ) ) {
			return $response;
		}

		// Set the push URL and method.
		$this->push_url    = $url;
		$this->push_method = strval( $parsed_args['method'] );

		// Check if the request is a PUT request.
		if ( 'POST' !== $parsed_args['method'] ) {
			return [
				'response' => [
					'code'    => 405,
					'message' => 'Method Not Allowed',
				],
				'headers'  => [],
				'body'     => '',
			];
		}

		// Check if the request has the correct headers.
		if ( QUARK_INGESTOR_API_KEY !== $parsed_args['headers']['x-api-key'] ) {
			return [
				'response' => [
					'code'    => 403,
					'message' => 'Missing Authentication Token',
				],
				'headers'  => [],
				'body'     => '',
			];
		}

		// If body contains invalid JSON.
		if ( ! is_array( json_decode( $parsed_args['body'], true ) ) ) {
			return [
				'response' => [
					'code'    => 400,
					'message' => 'Bad Request',
				],
				'headers'  => [],
				'body'     => '',
			];
		}

		// Return success response.
		return [
			'body'     => wp_json_encode( [ 'status' => 'success' ] ),
			'response' => [
				'code'    => 200,
				'message' => 'OK',
			],
			'headers'  => [],
		];
	}

	/**
	 * Test get all data when there are no expeditions.
	 *
	 * @covers \Quark\Ingestor\get_all_data
	 *
	 * @return void
	 */
	public function test_get_all_data_no_expeditions(): void {
		// Get all expeditions.
		$expedition_posts = get_posts(
			[
				'post_type'      => EXPEDITION_POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
			]
		);

		// Convert to int.
		$expedition_posts = array_map( 'absint', $expedition_posts );

		// Delete all expedition posts.
		foreach ( $expedition_posts as $expedition_post_id ) {
			wp_delete_post( $expedition_post_id, true );
		}

		// Test without any expedition posts.
		$expected = [];
		$actual   = get_all_data();
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get all data.
	 *
	 * @covers \Quark\Ingestor\get_all_data
	 *
	 * @return void
	 */
	public function test_get_all_data(): void {
		// Create an expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create draft expedition post.
		$expedition_post_id2 = $this->factory()->post->create(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_status' => 'draft',
			]
		);
		$this->assertIsInt( $expedition_post_id2 );

		// Test with expedition posts - should also include draft one.
		$actual = get_all_data();
		$this->assertIsArray( $actual );
		$this->assertArrayHasKey( $expedition_post_id, $actual );
		$this->assertArrayHasKey( $expedition_post_id2, $actual );

		// Get first expedition data.
		$expedition_data = $actual[ $expedition_post_id ];
		$this->assertIsArray( $expedition_data );
		$this->assertEquals(
			[
				'id'           => $expedition_post_id,
				'name'         => get_raw_text_from_html( get_the_title( $expedition_post_id ) ),
				'published'    => true,
				'description'  => '',
				'images'       => [],
				'destinations' => [],
				'itineraries'  => [],
				'heroImage'    => [],
				'modified'     => get_post_modified_time( $expedition_post_id ),
				'highlights'   => [],
				'url'          => get_permalink( $expedition_post_id ),
				'drupalId'     => 0,
			],
			$expedition_data
		);

		// Get second expedition data.
		$expedition_data2 = $actual[ $expedition_post_id2 ];
		$this->assertIsArray( $expedition_data2 );
		$this->assertEquals(
			[
				'id'           => $expedition_post_id2,
				'name'         => get_raw_text_from_html( get_the_title( $expedition_post_id2 ) ),
				'published'    => false,
				'description'  => '',
				'images'       => [],
				'destinations' => [],
				'itineraries'  => [],
				'heroImage'    => [],
				'modified'     => get_post_modified_time( $expedition_post_id2 ),
				'highlights'   => [],
				'url'          => get_permalink( $expedition_post_id2 ),
				'drupalId'     => 0,
			],
			$expedition_data2
		);
	}
}
