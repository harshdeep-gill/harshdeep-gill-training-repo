<?php
/**
 * Test suite for ingestor.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Ingestor;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Error;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\cron_is_scheduled;
use function Quark\Ingestor\cron_schedule_push;
use function Quark\Ingestor\do_push;
use function Quark\Softrip\AdventureOptions\update_adventure_options;
use function Quark\Ingestor\AdventureOptions\get_adventure_option_category_data_from_meta;
use function Quark\Ingestor\get_all_data;
use function Quark\Ingestor\Cabins\get_cabins_data;
use function Quark\Ingestor\Departures\get_departures_data;
use function Quark\Ingestor\Expeditions\get_destination_terms;
use function Quark\Ingestor\Expeditions\get_expedition_data;
use function Quark\Ingestor\AdventureOptions\get_included_adventure_options_data;
use function Quark\Ingestor\Itineraries\get_itineraries;
use function Quark\Ingestor\Occupancies\get_occupancies_data;
use function Quark\Ingestor\AdventureOptions\get_paid_adventure_options_data;
use function Quark\Ingestor\push_expedition_data;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\update_occupancies;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTION_POST_TYPE;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\USD_CURRENCY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Ingestor\DATA_HASH_KEY;
use const Quark\Ingestor\SCHEDULE_HOOK;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as DECK_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

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
	 * Test get expedition data.
	 *
	 * @covers \Quark\Ingestor\get_expedition_data
	 *
	 * @return void
	 */
	public function test_get_expedition_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_expedition_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_expedition_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_expedition_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Test without assigning any data.
		$expected =
			[
				'id'           => $expedition_post_id,
				'name'         => get_raw_text_from_html( get_the_title( $expedition_post_id ) ),
				'published'    => true,
				'description'  => '',
				'images'       => [],
				'destinations' => [],
				'itineraries'  => [],
			];
		$actual   = get_expedition_data( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Add itinerary to the expedition post.
		$itinerary_post_id = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => 'UNQ-123',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id );

		// Assign itinerary to the expedition post.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

		// Create some media post.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id2 );

		// Get alt text for media post.
		$alt_text1 = get_post_meta( $media_post_id1, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text1 ) ) {
			$alt_text1 = get_the_title( $media_post_id1 );
		}

		// Get alt text for media post.
		$alt_text2 = get_post_meta( $media_post_id2, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text2 ) ) {
			$alt_text2 = get_the_title( $media_post_id2 );
		}

		// Post content.
		$post_content = sprintf(
			'<!-- wp:paragraph -->
			<p>On this extraordinary journey, you will pack all the excitement of an epic Arctic cruise into just seven days, experience incredible Arctic wilderness you never dreamt possible. Though this rocky island is covered in mountains and glaciers, the towering cliffs and fjords play host to a thriving and diverse ecosystem. Exploring as much of the area as possible, you will enjoy maximum opportunities to spot, among other wildlife, the walrus with its long tusks and distinctive whiskers, the resilient and Arctic birds in all their varied majesty, and that most iconic of Arctic creatures, the polar bear.</p>
			<!-- /wp:paragraph -->

			<!-- wp:quark/expedition-hero -->
			<!-- wp:quark/expedition-hero-content -->
			<!-- wp:quark/expedition-hero-content-left -->
			<!-- wp:quark/expedition-details /-->
			<!-- /wp:quark/expedition-hero-content-left -->

			<!-- wp:quark/expedition-hero-content-right -->
			<!-- wp:quark/hero-card-slider {"items":[{"id":%1$s,"src":"%3$s","width":300,"height":200,"alt":"","caption":"","size":"medium"},{"id":%2$s,"src":"%4$s","width":300,"height":200,"alt":"","caption":"","size":"medium"},{"id":6592,"src":"https://local.quarkexpeditions.com/wp-content/uploads/2024/08/strote-jared-201809-214x300.jpg","width":214,"height":300,"alt":"","caption":"","size":"medium"},{"id":6594,"src":"https://local.quarkexpeditions.com/wp-content/uploads/2024/08/white-andrew-202102-300x200.jpg","width":300,"height":200,"alt":"","caption":"","size":"medium"}]} /-->
			<!-- /wp:quark/expedition-hero-content-right -->
			<!-- /wp:quark/expedition-hero-content -->
			<!-- /wp:quark/expedition-hero -->

			<!-- wp:quark/book-departures-expeditions /-->',
			$media_post_id1,
			$media_post_id2,
			wp_get_attachment_image_url( $media_post_id1, 'medium' ),
			wp_get_attachment_image_url( $media_post_id2, 'full' )
		);

		// Update post content.
		wp_update_post(
			[
				'ID'           => $expedition_post_id,
				'post_content' => $post_content,
				'meta_input'   => [
					'overview' => 'Here is the overview. <h1>Surfing</h1> You never know the world until you explore it.',
				],
			]
		);

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itinerary.
		$expected =
			[
				'id'           => $expedition_post_id,
				'name'         => get_raw_text_from_html( get_the_title( $expedition_post_id ) ),
				'published'    => true,
				'description'  => 'Here is the overview. Surfing You never know the world until you explore it.',
				'images'       => [
					[
						'id'           => $media_post_id1,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id1 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
						'alt'          => $alt_text1,
					],
					[
						'id'           => $media_post_id2,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id2 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
						'alt'          => $alt_text2,
					],
				], // @todo Get description after parsing post content.
				'destinations' => [],
				'itineraries'  => [
					[
						'id'             => $itinerary_post_id,
						'packageId'      => 'UNQ-123',
						'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id ) ),
						'published'      => true,
						'startLocation'  => '',
						'endLocation'    => '',
						'departures'     => [],
						'durationInDays' => 0,
					],
				],
			];
		$actual   = get_expedition_data( $expedition_post_id );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get destination terms.
	 *
	 * @covers \Quark\Ingestor\get_destination_terms
	 *
	 * @return void
	 */
	public function test_get_destination_terms(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_destination_terms();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_destination_terms( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_destination_terms( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create a expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create a destination term.
		$destination_term_id1 = $this->factory()->term->create( [ 'taxonomy' => DESTINATION_TAXONOMY ] );
		$this->assertIsInt( $destination_term_id1 );
		$destination_term1 = get_term( $destination_term_id1, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $destination_term1 );
		$this->assertArrayHasKey( 'name', $destination_term1 );
		$destination_term1_name = $destination_term1['name'];

		// Test without assigning any destination term.
		$expected = [];
		$actual   = get_destination_terms( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a child term but without softrip id.
		$destination_term_id2 = $this->factory()->term->create(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $destination_term_id1,
			]
		);
		$this->assertIsInt( $destination_term_id2 );
		$destination_term2 = get_term( $destination_term_id2, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $destination_term2 );
		$this->assertArrayHasKey( 'name', $destination_term2 );
		$destination_term2_name = $destination_term2['name'];

		// Assign child term to the expedition post.
		wp_set_post_terms( $expedition_post_id, [ $destination_term_id2 ], DESTINATION_TAXONOMY );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned child term but without softrip id.
		$expected = [];
		$actual   = get_destination_terms( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Add softrip id to parent term meta.
		update_term_meta( $destination_term_id1, 'softrip_id', '123' );

		// Test with assigned child term and parent term with softrip id.
		$actual   = get_destination_terms( $expedition_post_id );
		$expected = [
			[
				'id'     => $destination_term_id2,
				'name'   => $destination_term2_name,
				'region' => [
					'name' => $destination_term1_name,
					'code' => '123',
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add one more child term with softrip id.
		$destination_term_id3 = $this->factory()->term->create(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $destination_term_id1,
			]
		);
		$this->assertIsInt( $destination_term_id3 );
		$destination_term3 = get_term( $destination_term_id3, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $destination_term3 );
		$this->assertArrayHasKey( 'name', $destination_term3 );
		$destination_term3_name = $destination_term3['name'];
		update_term_meta( $destination_term_id3, 'softrip_id', '456' );

		// Assign child term to the expedition post.
		wp_set_post_terms( $expedition_post_id, [ $destination_term_id2, $destination_term_id3 ], DESTINATION_TAXONOMY );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned child term and parent term with softrip id.
		$actual   = get_destination_terms( $expedition_post_id );
		$expected = [
			[
				'id'     => $destination_term_id2,
				'name'   => $destination_term2_name,
				'region' => [
					'name' => $destination_term1_name,
					'code' => '123',
				],
			],
			[
				'id'     => $destination_term_id3,
				'name'   => $destination_term3_name,
				'region' => [
					'name' => $destination_term1_name,
					'code' => '123',
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get itineraries.
	 *
	 * @covers \Quark\Ingestor\get_itineraries
	 *
	 * @return void
	 */
	public function test_get_itineraries(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_itineraries();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_itineraries( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_itineraries( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create a expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Test without assigning any itinerary.
		$expected = [];
		$actual   = get_itineraries( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a itinerary post without softrip code.
		$itinerary_post_id1 = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id1 );

		// Assign itinerary to the expedition post.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id1 ] );

		// Test with assigned itinerary that has no softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Add softrip code to the itinerary post.
		update_post_meta( $itinerary_post_id1, 'softrip_package_code', 'UNQ-123' );

		// Add duration in days.
		update_post_meta( $itinerary_post_id1, 'duration_in_days', 7 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itinerary that has softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'             => $itinerary_post_id1,
				'packageId'      => 'UNQ-123',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'      => true,
				'startLocation'  => '',
				'endLocation'    => '',
				'departures'     => [],
				'durationInDays' => 7,
			],
		];
		$this->assertEquals( $expected, $actual );

		// Create location term.
		$start_location_term_id = $this->factory()->term->create( [ 'taxonomy' => DEPARTURE_LOCATION_TAXONOMY ] );
		$this->assertIsInt( $start_location_term_id );
		$start_location_term = get_term( $start_location_term_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $start_location_term );
		$this->assertArrayHasKey( 'name', $start_location_term );
		$start_location_term_name = $start_location_term['name'];

		// Create end location term.
		$end_location_term_id = $this->factory()->term->create( [ 'taxonomy' => DEPARTURE_LOCATION_TAXONOMY ] );
		$this->assertIsInt( $end_location_term_id );
		$end_location_term = get_term( $end_location_term_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $end_location_term );
		$this->assertArrayHasKey( 'name', $end_location_term );
		$end_location_term_name = $end_location_term['name'];

		// Add start and end to itinerary meta.
		update_post_meta( $itinerary_post_id1, 'start_location', $start_location_term_id );
		update_post_meta( $itinerary_post_id1, 'end_location', $end_location_term_id );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itinerary that has softrip code and start/end location.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'             => $itinerary_post_id1,
				'packageId'      => 'UNQ-123',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'      => true,
				'startLocation'  => $start_location_term_name,
				'endLocation'    => $end_location_term_name,
				'departures'     => [],
				'durationInDays' => 7,
			],
		];
		$this->assertEquals( $expected, $actual );

		// Create one more itinerary post with softrip code.
		$itinerary_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => 'UNQ-456',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id2 );

		// Assign both itineraries to the expedition post.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id1, $itinerary_post_id2 ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itineraries that have softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'             => $itinerary_post_id1,
				'packageId'      => 'UNQ-123',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'      => true,
				'startLocation'  => $start_location_term_name,
				'endLocation'    => $end_location_term_name,
				'departures'     => [],
				'durationInDays' => 7,
			],
			[
				'id'             => $itinerary_post_id2,
				'packageId'      => 'UNQ-456',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id2 ) ),
				'published'      => true,
				'startLocation'  => '',
				'endLocation'    => '',
				'departures'     => [],
				'durationInDays' => 0,
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get departures data.
	 *
	 * @covers \Quark\Ingestor\get_departures_data
	 *
	 * @return void
	 */
	public function test_get_departures_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_departures_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_departures_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_departures_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create a itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Test without assigning any departure.
		$expected = [];
		$actual   = get_departures_data( $itinerary_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a departure post without softrip id.
		$departure_post_id1 = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-123:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned departure that has no softrip id.
		$actual   = get_departures_data( $expedition_post_id, $itinerary_post_id );
		$expected = [
			[
				'id'               => 'UNQ-123:2025-01-01',
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id1 ) ),
				'published'        => true,
				'startDate'        => '',
				'endDate'          => '',
				'durationInDays'   => 0,
				'ship'             => [],
				'languages'        => '',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add start date to departure meta.
		update_post_meta( $departure_post_id1, 'start_date', '2025-01-01' );

		// Add end date to departure meta.
		update_post_meta( $departure_post_id1, 'end_date', '2025-01-02' );

		// Add duration to departure meta.
		update_post_meta( $departure_post_id1, 'duration', 2 );

		// Create language term.
		$language_term_id = $this->factory()->term->create( [ 'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY ] );
		$this->assertIsInt( $language_term_id );

		// Add language code to term meta.
		update_term_meta( $language_term_id, 'language_code', 'EN' );

		// Assign language to the departure post.
		wp_set_post_terms( $departure_post_id1, [ $language_term_id ], SPOKEN_LANGUAGE_TAXONOMY );

		// Create ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'OQP',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Add ship to departure meta.
		update_post_meta( $departure_post_id1, 'related_ship', $ship_post_id );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned departure that has softrip id, start/end date, duration, language and ship.
		$actual   = get_departures_data( $expedition_post_id, $itinerary_post_id );
		$expected = [
			[
				'id'               => 'UNQ-123:2025-01-01',
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id1 ) ),
				'published'        => true,
				'startDate'        => '2025-01-01',
				'endDate'          => '2025-01-02',
				'durationInDays'   => 2,
				'ship'             => [
					'code' => 'OQP',
					'id'   => $ship_post_id,
					'name' => get_raw_text_from_html( get_the_title( $ship_post_id ) ),
				],
				'languages'        => 'EN',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add one more departure post with softrip id.
		$departure_post_id2 = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-456:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'start_date'         => '2025-01-01',
					'end_date'           => '2025-01-02',
					'duration'           => 2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Create language term.
		$language_term_id2 = $this->factory()->term->create( [ 'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY ] );
		$this->assertIsInt( $language_term_id2 );

		// Add language code to term meta.
		update_term_meta( $language_term_id2, 'language_code', 'FR' );

		// Assign language to the departure post.
		wp_set_post_terms( $departure_post_id2, [ $language_term_id2 ], SPOKEN_LANGUAGE_TAXONOMY );

		// Create ship post.
		$ship_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'LOP',
				],
			]
		);
		$this->assertIsInt( $ship_post_id2 );

		// Add ship to departure meta.
		update_post_meta( $departure_post_id2, 'related_ship', $ship_post_id2 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned departures that have softrip id, start/end date, duration, language and ship.
		$actual   = get_departures_data( $expedition_post_id, $itinerary_post_id );
		$expected = [
			[
				'id'               => 'UNQ-456:2025-01-01',
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id2 ) ),
				'published'        => true,
				'startDate'        => '2025-01-01',
				'endDate'          => '2025-01-02',
				'durationInDays'   => 2,
				'ship'             => [
					'code' => 'LOP',
					'id'   => $ship_post_id2,
					'name' => get_raw_text_from_html( get_the_title( $ship_post_id2 ) ),
				],
				'languages'        => 'FR',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
			],
			[
				'id'               => 'UNQ-123:2025-01-01',
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id1 ) ),
				'published'        => true,
				'startDate'        => '2025-01-01',
				'endDate'          => '2025-01-02',
				'durationInDays'   => 2,
				'ship'             => [
					'code' => 'OQP',
					'id'   => $ship_post_id,
					'name' => get_raw_text_from_html( get_the_title( $ship_post_id ) ),
				],
				'languages'        => 'EN',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get cabins data.
	 *
	 * @covers \Quark\Ingestor\get_cabins_data
	 *
	 * @return void
	 */
	public function test_get_cabins_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_cabins_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_cabins_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_cabins_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Update related itinerary.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-123:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'start_date'         => '2025-01-01',
					'end_date'           => '2025-01-02',
					'duration'           => 2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Create ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'POQ',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Test without assigning any cabin.
		$expected = [];
		$actual   = get_cabins_data( $ship_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a cabin post without softrip id.
		$cabin_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORY_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'POQ-SGL',
				],
			]
		);
		$this->assertIsInt( $cabin_post_id1 );

		// Insert occupancies for this cabin.
		$raw_cabins_data = [
			[
				'id'          => 'UNQ-123:2025-01-01:POQ-SGL',
				'code'        => 'POQ-SGL',
				'name'        => 'Explorer Single',
				'departureId' => 'UNQ-123:2025-01-01',
				'occupancies' => [
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'mask'           => 'A',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'mask'           => 'AA',
						'saleStatusCode' => 'S',
						'saleStatus'     => 'Sold Out',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'mask'           => 'SA',
						'saleStatusCode' => 'N',
						'saleStatus'     => 'No display',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'           => 'SAA',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [
							'AUD' => [
								'currencyCode'   => 'AUD',
								'pricePerPerson' => 1000,
							],
						],
					],
				],
			],
		];
		$is_updated      = update_occupancies( $raw_cabins_data, $departure_post_id );
		$this->assertTrue( $is_updated );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned cabin that has no softrip id.
		$actual   = get_cabins_data( $expedition_post_id, $itinerary_post_id, $departure_post_id );
		$expected = [
			[
				'id'             => 'UNQ-123:2025-01-01:POQ-SGL',
				'name'           => get_raw_text_from_html( get_the_title( $cabin_post_id1 ) ),
				'code'           => 'POQ-SGL',
				'description'    => get_raw_text_from_html( get_the_content( null, false, $cabin_post_id1 ) ),
				'bedDescription' => '',
				'type'           => '',
				'location'       => '',
				'size'           => '',
				'occupancySize'  => '',
				'media'          => [],
				'occupancies'    => [
					[
						'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'                    => 'SAA',
						'description'             => get_description_and_pax_count_by_mask( 'SAA' )['description'],
						'availabilityStatus'      => 'O',
						'availabilityDescription' => 'Open',
						'spacesAvailable'         => 0,
						'prices'                  => [
							'AUD' => [
								'currencyCode'                    => 'AUD',
								'pricePerPerson'                  => 1000,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'USD' => [
								'currencyCode'                    => 'USD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'EUR' => [
								'currencyCode'                    => 'EUR',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'GBP' => [
								'currencyCode'                    => 'GBP',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'CAD' => [
								'currencyCode'                    => 'CAD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
						],
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add cabin_bed configuration to cabin meta.
		update_post_meta( $cabin_post_id1, 'cabin_bed_configuration', 'Twin' );

		// Create cabin class term.
		$cabin_class_term_id = $this->factory()->term->create( [ 'taxonomy' => CABIN_CLASS_TAXONOMY ] );
		$this->assertIsInt( $cabin_class_term_id );
		$cabin_class_term = get_term( $cabin_class_term_id, CABIN_CLASS_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $cabin_class_term );
		$this->assertArrayHasKey( 'name', $cabin_class_term );
		$cabin_class_term_name = $cabin_class_term['name'];

		// Create one more cabin class term.
		$cabin_class_term_id2 = $this->factory()->term->create( [ 'taxonomy' => CABIN_CLASS_TAXONOMY ] );
		$this->assertIsInt( $cabin_class_term_id2 );
		$cabin_class_term2 = get_term( $cabin_class_term_id2, CABIN_CLASS_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $cabin_class_term2 );
		$this->assertArrayHasKey( 'name', $cabin_class_term2 );
		$cabin_class_term_name2 = $cabin_class_term2['name'];

		// Assign these cabin class terms to the cabin post.
		wp_set_post_terms( $cabin_post_id1, [ $cabin_class_term_id, $cabin_class_term_id2 ], CABIN_CLASS_TAXONOMY );

		// Create a deck post.
		$deck_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => DECK_POST_TYPE,
				'meta_input' => [
					'deck_name' => 'Deck 1',
				],
			]
		);
		$this->assertIsInt( $deck_post_id1 );

		// Create one more deck post.
		$deck_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => DECK_POST_TYPE,
				'meta_input' => [
					'deck_name' => 'Deck 2',
				],
			]
		);
		$this->assertIsInt( $deck_post_id2 );

		// Add these two decks to the cabin post in related_decks meta.
		update_post_meta( $cabin_post_id1, 'related_decks', [ $deck_post_id1, $deck_post_id2 ] );

		// Add from and to size on cabin meta.
		update_post_meta( $cabin_post_id1, 'cabin_category_size_range_from', '100' );
		update_post_meta( $cabin_post_id1, 'cabin_category_size_range_to', '200' );

		// Add from and to occupancy size on cabin meta.
		update_post_meta( $cabin_post_id1, 'cabin_occupancy_pax_range_from', '1' );
		update_post_meta( $cabin_post_id1, 'cabin_occupancy_pax_range_to', '2' );

		// Create two media posts.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/cabin.jpg' );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/cabin.jpg' );
		$this->assertIsInt( $media_post_id2 );

		// Get alt text for media post.
		$alt_text1 = get_post_meta( $media_post_id1, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text1 ) ) {
			$alt_text1 = get_post_field( 'post_title', $media_post_id1 );
		}

		// Set alt text on second media.
		update_post_meta( $media_post_id2, '_wp_attachment_image_alt', 'Cabin 2' );

		// Add these media posts to the cabin post in cabin_images meta.
		update_post_meta( $cabin_post_id1, 'cabin_images', [ $media_post_id1, $media_post_id2 ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned cabin that has softrip id, bed configuration, cabin class, decks and occupancies.
		$actual   = get_cabins_data( $expedition_post_id, $itinerary_post_id, $departure_post_id );
		$expected = [
			[
				'id'             => 'UNQ-123:2025-01-01:POQ-SGL',
				'name'           => get_raw_text_from_html( get_the_title( $cabin_post_id1 ) ),
				'code'           => 'POQ-SGL',
				'description'    => get_raw_text_from_html( get_the_content( null, false, $cabin_post_id1 ) ),
				'bedDescription' => 'Twin',
				'type'           => implode( ', ', [ $cabin_class_term_name, $cabin_class_term_name2 ] ),
				'location'       => implode( ', ', [ 'Deck 1', 'Deck 2' ] ),
				'size'           => '100 - 200',
				'occupancySize'  => '1 - 2',
				'media'          => [
					[
						'id'           => $media_post_id1,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id1 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
						'alt'          => $alt_text1,
					],
					[
						'id'           => $media_post_id2,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id2 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
						'alt'          => 'Cabin 2',
					],
				],
				'occupancies'    => [
					[
						'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'                    => 'SAA',
						'description'             => get_description_and_pax_count_by_mask( 'SAA' )['description'],
						'availabilityStatus'      => 'O',
						'availabilityDescription' => 'Open',
						'spacesAvailable'         => 0,
						'prices'                  => [
							'AUD' => [
								'currencyCode'                    => 'AUD',
								'pricePerPerson'                  => 1000,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'USD' => [
								'currencyCode'                    => 'USD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'EUR' => [
								'currencyCode'                    => 'EUR',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'GBP' => [
								'currencyCode'                    => 'GBP',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'CAD' => [
								'currencyCode'                    => 'CAD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
						],
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Delete media files.
		wp_delete_attachment( $media_post_id1, true );
		wp_delete_attachment( $media_post_id2, true );
	}

	/**
	 * Test get occupancies data.
	 *
	 * @covers \Quark\Ingestor\get_occupancies_data
	 *
	 * @return void
	 */
	public function test_get_occupancies_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_occupancies_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_occupancies_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_occupancies_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Update related itinerary.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-123:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'start_date'         => '2025-01-01',
					'end_date'           => '2025-01-02',
					'duration'           => 2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Create ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'POQ',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Create a cabin post without softrip id.
		$cabin_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORY_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'POQ-SGL',
				],
			]
		);
		$this->assertIsInt( $cabin_post_id1 );

		// Insert some promotions.
		$raw_promotions = [
			[
				'endDate'       => '2025-03-01',
				'startDate'     => '2025-01-01',
				'description'   => 'Promotion 1',
				'discountType'  => 'percentage',
				'discountValue' => '0.1',
				'promotionCode' => '10PROMO',
				'isPIF'         => false,
			],
			[
				'endDate'       => '2025-04-01',
				'startDate'     => '2025-02-01',
				'description'   => 'Promotion 2',
				'discountType'  => 'fixed',
				'discountValue' => '0.1',
				'promotionCode' => '10PIF',
				'isPIF'         => true,
			],
			[
				'endDate'       => '2025-03-22',
				'startDate'     => '2025-01-12',
				'description'   => 'Promotion 3',
				'discountType'  => 'percentage',
				'discountValue' => '0.2',
				'promotionCode' => '20PROMO',
				'isPIF'         => false,
			],
		];
		$is_success     = update_promotions( $raw_promotions, $departure_post_id );
		$this->assertTrue( $is_success );

		// Get promotion by code - 10PROMO.
		$promotions = get_promotions_by_code( '10PROMO' );
		$this->assertIsArray( $promotions );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );
		$promotion1 = $promotions[0];
		$this->assertIsArray( $promotion1 );
		$this->assertArrayHasKey( 'id', $promotion1 );
		$promotion_id1 = $promotion1['id'];

		// Get promotion by code - 20PROMO.
		$promotions = get_promotions_by_code( '20PROMO' );
		$this->assertIsArray( $promotions );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );
		$promotion2 = $promotions[0];
		$this->assertIsArray( $promotion2 );
		$this->assertArrayHasKey( 'id', $promotion2 );
		$promotion_id2 = $promotion2['id'];

		// Get promotion by code - 10PIF.
		$promotions = get_promotions_by_code( '10PIF' );
		$this->assertIsArray( $promotions );
		$this->assertNotEmpty( $promotions );
		$this->assertCount( 1, $promotions );
		$promotion3 = $promotions[0];
		$this->assertIsArray( $promotion3 );
		$this->assertArrayHasKey( 'id', $promotion3 );
		$promotion_id3 = $promotion3['id'];

		// Insert occupancies for this cabin.
		$raw_cabins_data = [
			[
				'id'          => 'UNQ-123:2025-01-01:POQ-SGL',
				'code'        => 'POQ-SGL',
				'name'        => 'Explorer Single',
				'departureId' => 'UNQ-123:2025-01-01',
				'occupancies' => [
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'mask'           => 'A',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [
							'AUD' => [
								'currencyCode'   => 'AUD',
								'pricePerPerson' => 1000,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 900,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 800,
									],
								],
							],
							'USD' => [
								'currencyCode'   => 'USD',
								'pricePerPerson' => 8176,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 7360,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 6544,
									],
									'10PROMO' => [
										'promoPricePerPerson' => 5360,
									],
								],
							],
							'CAD' => [
								'currencyCode'   => 'CAD',
								'pricePerPerson' => 1000,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 900,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 800,
									],
								],
							],
							'EUR' => [
								'currencyCode'   => 'EUR',
								'pricePerPerson' => 780,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 900,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 800,
									],
									'10PROMO' => [
										'promoPricePerPerson' => 800,
									],
								],
							],
							'GBP' => [
								'currencyCode'   => 'GBP',
								'pricePerPerson' => 18722,
								'promos'         => [
									'10PIF'   => [
										'promoPricePerPerson' => 16850,
									],
									'20PROMO' => [
										'promoPricePerPerson' => 14978,
									],
									'10PROMO' => [
										'promoPricePerPerson' => 12300,
									],
								],
							],
						],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'mask'           => 'AA',
						'saleStatusCode' => 'S',
						'saleStatus'     => 'Sold Out',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'mask'           => 'SA',
						'saleStatusCode' => 'N',
						'saleStatus'     => 'No display',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'           => 'SAA',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [
							'AUD' => [
								'currencyCode'   => 'AUD',
								'pricePerPerson' => 1000,
							],
						],
					],
				],
			],
		];

		// Insert occupancies.
		$is_updated = update_occupancies( $raw_cabins_data, $departure_post_id );
		$this->assertTrue( $is_updated );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned occupancy that has no softrip id.
		$actual   = get_occupancies_data( $itinerary_post_id, $departure_post_id, $cabin_post_id1 );
		$expected = [
			[
				'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:A',
				'mask'                    => 'A',
				'description'             => get_description_and_pax_count_by_mask( 'A' )['description'],
				'availabilityStatus'      => 'O',
				'availabilityDescription' => 'Open',
				'spacesAvailable'         => 0,
				'prices'                  => [
					'AUD' => [
						'currencyCode'                    => 'AUD',
						'pricePerPerson'                  => 1000,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 900,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 800,
							],
						],
					],
					'USD' => [
						'currencyCode'                    => 'USD',
						'pricePerPerson'                  => 8176,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 7360,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 6544,
							],
							[
								'id'                  => $promotion_id1,
								'promotionCode'       => '10PROMO',
								'promoPricePerPerson' => 5360,
							],
						],
					],
					'CAD' => [
						'currencyCode'                    => 'CAD',
						'pricePerPerson'                  => 1000,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 900,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 800,
							],
						],
					],
					'EUR' => [
						'currencyCode'                    => 'EUR',
						'pricePerPerson'                  => 780,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 900,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 800,
							],
							[
								'id'                  => $promotion_id1,
								'promotionCode'       => '10PROMO',
								'promoPricePerPerson' => 800,
							],
						],
					],
					'GBP' => [
						'currencyCode'                    => 'GBP',
						'pricePerPerson'                  => 18722,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [
							[
								'id'                  => $promotion_id3,
								'promotionCode'       => '10PIF',
								'promoPricePerPerson' => 16850,
							],
							[
								'id'                  => $promotion_id2,
								'promotionCode'       => '20PROMO',
								'promoPricePerPerson' => 14978,
							],
							[
								'id'                  => $promotion_id1,
								'promotionCode'       => '10PROMO',
								'promoPricePerPerson' => 12300,
							],
						],
					],
				],
			],
			[
				'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
				'mask'                    => 'SAA',
				'description'             => get_description_and_pax_count_by_mask( 'SAA' )['description'],
				'availabilityStatus'      => 'O',
				'availabilityDescription' => 'Open',
				'spacesAvailable'         => 0,
				'prices'                  => [
					'AUD' => [
						'currencyCode'                    => 'AUD',
						'pricePerPerson'                  => 1000,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'USD' => [
						'currencyCode'                    => 'USD',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'EUR' => [
						'currencyCode'                    => 'EUR',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'GBP' => [
						'currencyCode'                    => 'GBP',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
					'CAD' => [
						'currencyCode'                    => 'CAD',
						'pricePerPerson'                  => 0,
						'mandatoryTransferPricePerPerson' => 0,
						'supplementalPricePerPerson'      => 0,
						'promotionsApplied'               => [],
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get included adventure options data.
	 *
	 * @covers \Quark\Ingestor\get_included_adventure_options_data
	 *
	 * @return void
	 */
	public function test_get_included_adventure_options_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_included_adventure_options_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_included_adventure_options_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_included_adventure_options_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create adventure option post.
		$adventure_option_post_id1 = $this->factory()->post->create(
			[
				'post_type' => ADVENTURE_OPTION_POST_TYPE,
			]
		);
		$this->assertIsInt( $adventure_option_post_id1 );

		// Test with expedition that has no related adventure options.
		$actual   = get_included_adventure_options_data( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Update related adventure options.
		update_post_meta( $expedition_post_id, 'included_activities', [ $adventure_option_post_id1 ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with expedition that has related adventure options but no assigned category.
		$actual   = get_included_adventure_options_data( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Create adventure option category term.
		$adventure_option_category_term_id = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id );
		$adventure_option_category = get_term( $adventure_option_category_term_id, ADVENTURE_OPTION_CATEGORY, ARRAY_A );
		$this->assertIsArray( $adventure_option_category );
		$this->assertArrayHasKey( 'name', $adventure_option_category );
		$adventure_option_category_name = $adventure_option_category['name'];

		// Assign this category to the adventure option post.
		wp_set_post_terms( $adventure_option_post_id1, [ $adventure_option_category_term_id ], ADVENTURE_OPTION_CATEGORY );

		// Test without departure id.
		$actual   = get_included_adventure_options_data( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_id' => 'UNQ-123:2025-01-01',
					'ship_code'  => 'POQ',
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Flush the cache.
		wp_cache_flush();

		// Test with expedition that has related adventure options and assigned category.
		$actual   = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$expected = [
			[
				'id'        => $adventure_option_category_term_id,
				'name'      => $adventure_option_category_name,
				'icon'      => '',
				'optionIds' => '',
			],
		];
		$this->assertEquals( $expected, $actual );

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );

		// Update icon on adventure option category.
		update_term_meta( $adventure_option_category_term_id, 'icon', $media_post_id1 );

		// Test with expedition that has related adventure options and assigned category with icon.
		$actual   = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$expected = [
			[
				'id'        => $adventure_option_category_term_id,
				'name'      => $adventure_option_category_name,
				'icon'      => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
				'optionIds' => '',
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add option ids to the category.
		update_term_meta( $adventure_option_category_term_id, 'softrip_0_id', 'ABC' );
		update_term_meta( $adventure_option_category_term_id, 'softrip_1_id', 'DEF' );

		// Test with expedition that has related adventure options and assigned category with icon and option ids.
		$actual   = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$expected = [
			[
				'id'        => $adventure_option_category_term_id,
				'name'      => $adventure_option_category_name,
				'icon'      => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
				'optionIds' => 'ABC, DEF',
			],
		];
		$this->assertEquals( $expected, $actual );

		// Delete media files.
		wp_delete_attachment( $media_post_id1, true );
	}

	/**
	 * Test get adventure option category data from meta.
	 *
	 * @covers \Quark\Ingestor\get_adventure_option_category_data_from_meta
	 *
	 * @return void
	 */
	public function test_get_adventure_option_category_data_from_meta(): void {
		// Default expected.
		$default_expected = [
			'icon'      => '',
			'optionIds' => [],
			'images'    => [],
		];

		// Test with no arguments.
		$actual = get_adventure_option_category_data_from_meta();
		$this->assertEquals( $default_expected, $actual );

		// Test with default arg.
		$actual = get_adventure_option_category_data_from_meta( 0 );
		$this->assertEquals( $default_expected, $actual );

		// Test with invalid term id.
		$actual = get_adventure_option_category_data_from_meta( 999999 );
		$this->assertEquals( $default_expected, $actual );

		// Create adventure option category term.
		$adventure_option_category_term_id = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id );

		// Test with term id that has no meta.
		$actual = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$this->assertEquals( $default_expected, $actual );

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );

		// Update icon on adventure option category.
		update_term_meta( $adventure_option_category_term_id, 'icon', $media_post_id1 );

		// Test with term id that has icon meta.
		$actual   = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$expected = [
			'icon'      => wp_get_attachment_image_url( $media_post_id1, 'full' ),
			'optionIds' => [],
			'images'    => [],
		];
		$this->assertEquals( $expected, $actual );

		// Add option ids to the category.
		update_term_meta( $adventure_option_category_term_id, 'softrip_0_id', 'ABC' );
		update_term_meta( $adventure_option_category_term_id, 'softrip_1_id', 'DEF' );

		// Test with term id that has icon and option ids meta.
		$actual   = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$expected = [
			'icon'      => wp_get_attachment_image_url( $media_post_id1, 'full' ),
			'optionIds' => [ 'ABC', 'DEF' ],
			'images'    => [],
		];
		$this->assertEquals( $expected, $actual );

		// Create attachment.
		$media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id2 );
		$alt_text = get_post_meta( $media_post_id2, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text ) ) {
			$alt_text = get_post_field( 'post_title', $media_post_id2 );
		}

		// Update images on adventure option category.
		update_term_meta( $adventure_option_category_term_id, 'image', $media_post_id2 );

		// Test with term id that has icon, option ids and image meta.
		$actual   = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$expected = [
			'icon'      => wp_get_attachment_image_url( $media_post_id1, 'full' ),
			'optionIds' => [ 'ABC', 'DEF' ],
			'images'    => [
				[
					'id'           => $media_post_id2,
					'fullSizeUrl'  => wp_get_attachment_url( $media_post_id2 ),
					'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
					'alt'          => $alt_text,
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get paid adventure options data.
	 *
	 * @covers \Quark\Ingestor\get_paid_adventure_options_data
	 *
	 * @return void
	 */
	public function test_get_paid_adventure_options_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_paid_adventure_options_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_paid_adventure_options_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_paid_adventure_options_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create adventure option category terms.
		$adventure_option_category_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id1 );
		$adventure_option_category_term1 = get_term( $adventure_option_category_term_id1, ADVENTURE_OPTION_CATEGORY, ARRAY_A );
		$this->assertIsArray( $adventure_option_category_term1 );
		$this->assertArrayHasKey( 'name', $adventure_option_category_term1 );
		$adventure_option_category_name1 = $adventure_option_category_term1['name'];

		// Create second adventure option category term.
		$adventure_option_category_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id2 );
		$adventure_option_category_term2 = get_term( $adventure_option_category_term_id2, ADVENTURE_OPTION_CATEGORY, ARRAY_A );
		$this->assertIsArray( $adventure_option_category_term2 );
		$this->assertArrayHasKey( 'name', $adventure_option_category_term2 );
		$adventure_option_category_name2 = $adventure_option_category_term2['name'];

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id2 );
		$media_post_id3 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id3 );

		// Update alt text on media 2.
		update_post_meta( $media_post_id2, '_wp_attachment_image_alt', 'Alt text 2' );

		// Alt text.
		$alt_text1 = get_post_meta( $media_post_id1, '_wp_attachment_image_alt', true );
		$alt_text2 = get_post_meta( $media_post_id2, '_wp_attachment_image_alt', true );
		$alt_text3 = get_post_meta( $media_post_id3, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text1 ) ) {
			$alt_text1 = get_post_field( 'post_title', $media_post_id1 );
		}

		// If empty alt, see title.
		if ( empty( $alt_text2 ) ) {
			$alt_text2 = get_post_field( 'post_title', $media_post_id2 );
		}

		// If empty alt, see title.
		if ( empty( $alt_text3 ) ) {
			$alt_text3 = get_post_field( 'post_title', $media_post_id3 );
		}

		// Update icon on adventure option category.
		update_term_meta( $adventure_option_category_term_id1, 'icon', $media_post_id1 );
		update_term_meta( $adventure_option_category_term_id2, 'icon', $media_post_id3 );

		// Update images on adventure option category.
		update_term_meta( $adventure_option_category_term_id1, 'image', $media_post_id2 );
		update_term_meta( $adventure_option_category_term_id2, 'image', $media_post_id3 );

		// Add softrip ids to the categories.
		update_term_meta( $adventure_option_category_term_id1, 'softrip_0_id', 'ABC' );
		update_term_meta( $adventure_option_category_term_id1, 'softrip_1_id', 'DEF' );
		update_term_meta( $adventure_option_category_term_id2, 'softrip_0_id', 'GHI' );
		update_term_meta( $adventure_option_category_term_id2, 'softrip_1_id', 'JKL' );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// Add softrip_package_code to the departure.
		update_post_meta( $departure_post_id, 'softrip_package_code', 'UNQ-123-11D2025' );

		// Create adventure option rows.
		$raw_adventure_options_data = [
			[
				'id'              => 'UNQ-123-11D2025:2025-08-26:KAYAK',
				'spacesAvailable' => 10,
				'serviceIds'      => [ 'ABC', 'DEF' ],
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 1000,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 8176,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 1000,
					],
				],
			],
			[
				'id'              => 'UNQ-123-11D2025:2025-08-26:HIKE',
				'spacesAvailable' => 0,
				'serviceIds'      => [ 'GHI', 'JKL' ],
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 234,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 235456,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 29332,
					],
				],
			],
		];

		// Insert adventure options.
		$is_success = update_adventure_options( $raw_adventure_options_data, $departure_post_id );
		$this->assertTrue( $is_success );

		// Flush the cache.
		wp_cache_flush();

		// Test with departure that has softrip_package_code.
		$actual   = get_paid_adventure_options_data( $departure_post_id );
		$expected = [
			[
				'id'              => $adventure_option_category_term_id1,
				'name'            => get_raw_text_from_html( $adventure_option_category_name1 ),
				'icon'            => wp_get_attachment_image_url( $media_post_id1, 'full' ),
				'optionIds'       => 'ABC, DEF',
				'images'          => [
					[
						'id'           => $media_post_id2,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id2 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
						'alt'          => $alt_text2,
					],
				],
				'spacesAvailable' => 10,
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 1000,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 8176,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 1000,
					],
					GBP_CURRENCY => [
						'currencyCode'   => GBP_CURRENCY,
						'pricePerPerson' => 0,
					],
					EUR_CURRENCY => [
						'currencyCode'   => EUR_CURRENCY,
						'pricePerPerson' => 0,
					],
				],
			],
			[
				'id'              => $adventure_option_category_term_id2,
				'name'            => get_raw_text_from_html( $adventure_option_category_name2 ),
				'icon'            => wp_get_attachment_image_url( $media_post_id3, 'full' ),
				'optionIds'       => 'GHI, JKL',
				'images'          => [
					[
						'id'           => $media_post_id3,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id3 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id3, 'thumbnail' ),
						'alt'          => $alt_text3,
					],
				],
				'spacesAvailable' => 0,
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 234,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 235456,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 29332,
					],
					GBP_CURRENCY => [
						'currencyCode'   => GBP_CURRENCY,
						'pricePerPerson' => 0,
					],
					EUR_CURRENCY => [
						'currencyCode'   => EUR_CURRENCY,
						'pricePerPerson' => 0,
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );
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
			],
			$expedition_data2
		);
	}
}
