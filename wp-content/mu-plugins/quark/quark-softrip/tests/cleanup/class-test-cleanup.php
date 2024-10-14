<?php
/**
 * Test suite for cleanup functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Cleanup;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Departures\get_card_data;
use function Quark\Softrip\Cleanup\do_cleanup;
use function Quark\Softrip\Cleanup\is_scheduled;
use function Quark\Softrip\Cleanup\schedule_cleanup;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\Occupancies\get_occupancies_by_departure;

use const Quark\Departures\POST_TYPE;
use const Quark\Softrip\Cleanup\SCHEDULE_HOOK;

/**
 * Class Test_Cleanup
 */
class Test_Cleanup extends Softrip_TestCase {
	/**
	 * Test is scheduled.
	 *
	 * @covers \Quark\Softrip\Cleanup\is_scheduled
	 *
	 * @return void
	 */
	public function test_is_scheduled(): void {
		// Clear any existing scheduled events.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );

		// Test : cron should not be scheduled.
		$this->assertFalse( is_scheduled() );

		// Schedule the cron task.
		schedule_cleanup();

		// Test : cron should be scheduled.
		$this->assertTrue( is_scheduled() );
	}

	/**
	 * Test schedule cleanup.
	 *
	 * @covers \Quark\Softrip\Cleanup\schedule_cleanup
	 *
	 * @return void
	 */
	public function test_schedule_cleanup(): void {
		// Clear any existing scheduled events.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );

		// It should not be scheduled.
		$timestamp = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertFalse( $timestamp );

		// Schedule the cron task.
		schedule_cleanup();
		$timestamp = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertNotFalse( $timestamp );

		// Verify the schedule interval.
		$schedule = wp_get_schedule( SCHEDULE_HOOK );
		$this->assertEquals( 'daily', $schedule );

		// Test: if repeated call should not schedule again.
		schedule_cleanup();
		$timestamp2 = wp_next_scheduled( SCHEDULE_HOOK );
		$this->assertNotFalse( $timestamp2 );
		$this->assertEquals( $timestamp, $timestamp2 );

		// Cleanup.
		wp_clear_scheduled_hook( SCHEDULE_HOOK );
	}

	/**
	 * Test do cleanup.
	 *
	 * @covers \Quark\Softrip\Cleanup\do_cleanup
	 *
	 * @return void
	 */
	public function test_do_cleanup(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Do sync.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Fetch draft departure posts - there should be no draft.
		$departures = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'draft',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertEmpty( $departures );

		// Get a published departure post.
		$departures = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => 3,
				'post_status'            => 'publish',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotEmpty( $departures );
		$this->assertCount( 3, $departures );

		// First departure post.
		$departure_id1 = $departures[0];
		$this->assertIsInt( $departure_id1 );

		// Second departure post.
		$departure_id2 = $departures[1];
		$this->assertIsInt( $departure_id2 );

		// Third departure post.
		$departure_id3 = $departures[2];
		$this->assertIsInt( $departure_id3 );

		// Run cleanup.
		do_cleanup();

		// All three departure should still be present as all above are published - shouldn't affect published ones.
		$departure_ids1 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => 3,
				'post_status'            => 'publish',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotEmpty( $departure_ids1 );
		$this->assertContains( $departure_id1, $departure_ids1 );
		$this->assertContains( $departure_id2, $departure_ids1 );
		$this->assertContains( $departure_id3, $departure_ids1 );

		// Mark first departure post as draft and set start date to tomorrow.
		wp_update_post(
			[
				'ID'          => $departure_id1,
				'post_status' => 'draft',
				'meta_input'  => [
					'start_date' => gmdate( 'Y-m-d', strtotime( '+1 days' ) ),
				],
			]
		);

		// Run cleanup.
		do_cleanup();

		// First departure should be present as start date is not past 4 months.
		$departure_ids2 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'draft',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotEmpty( $departure_ids2 );
		$this->assertContains( $departure_id1, $departure_ids2 );

		// Mark second departure post as draft and set start date to a little less than 4 months.
		wp_update_post(
			[
				'ID'          => $departure_id2,
				'post_status' => 'draft',
				'meta_input'  => [
					'start_date' => gmdate( 'Y-m-d', strtotime( '-3 months 30 days' ) ),
				],
			]
		);

		// Run cleanup.
		do_cleanup();

		// Second departure should still be present as it is not past 4 months.
		$departure_ids3 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'draft',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertContains( $departure_id2, $departure_ids3 );

		// Mark third departure post as draft and set start date to exact 4 months.
		wp_update_post(
			[
				'ID'          => $departure_id3,
				'post_status' => 'draft',
				'meta_input'  => [
					'start_date' => gmdate( 'Y-m-d', strtotime( '-4 months' ) ),
				],
			]
		);

		// Run cleanup.
		do_cleanup();

		// Third departure should be present as it's exactly 4 months but not past 4 months.
		$departure_ids4 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'draft',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertContains( $departure_id3, $departure_ids4 );

		// Occupancies for each departure should still be present.
		$occupancies1 = get_occupancies_by_departure( $departure_id1 );
		$this->assertNotEmpty( $occupancies1 );
		$occupancies2 = get_occupancies_by_departure( $departure_id2 );
		$this->assertNotEmpty( $occupancies2 );
		$occupancies3 = get_occupancies_by_departure( $departure_id3 );
		$this->assertNotEmpty( $occupancies3 );

		// Mark third departure post as draft and set start date to past 4 months.
		wp_update_post(
			[
				'ID'          => $departure_id3,
				'post_status' => 'draft',
				'meta_input'  => [
					'start_date' => gmdate( 'Y-m-d', strtotime( '-4 months -1 day' ) ),
				],
			]
		);

		// Run cleanup.
		do_cleanup();

		// Third departure should not be present as it's past 4 months.
		$departure_ids5 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'draft',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotContains( $departure_id3, $departure_ids5 );
		$this->assertContains( $departure_id1, $departure_ids5 );
		$this->assertContains( $departure_id2, $departure_ids5 );

		// Flush the cache.
		wp_cache_flush();

		// Occupancies for each departure should still be present except for third departure.
		$occupancies1 = get_occupancies_by_departure( $departure_id1 );
		$this->assertNotEmpty( $occupancies1 );
		$occupancies2 = get_occupancies_by_departure( $departure_id2 );
		$this->assertNotEmpty( $occupancies2 );
		$occupancies3 = get_occupancies_by_departure( $departure_id3 );
		$this->assertEmpty( $occupancies3 );

		// Mark second departure post as draft and set start date to past 5 months.
		wp_update_post(
			[
				'ID'          => $departure_id2,
				'post_status' => 'draft',
				'meta_input'  => [
					'start_date' => gmdate( 'Y-m-d', strtotime( '-5 months' ) ),
				],
			]
		);

		// Run cleanup.
		do_cleanup();

		// Second departure should not be present as it's past 4 months.
		$departure_ids6 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'draft',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotContains( $departure_id2, $departure_ids6 );

		// Flush the cache.
		wp_cache_flush();

		// Occupancies for second departure should not be present.
		$occupancies2 = get_occupancies_by_departure( $departure_id2 );
		$this->assertEmpty( $occupancies2 );

		// Create departure post.
		$departure_id4 = $this->factory()->post->create(
			[
				'post_type'    => POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => 'Test Departure 4',
				'post_content' => 'Test Departure 4',
			]
		);
		$this->assertIsInt( $departure_id4 );

		// Try deleting a published departure post.
		do_cleanup( [ $departure_id4 ] );

		// Departure should still be present as it's published.
		$departure_ids7 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'post_status'            => 'publish',
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotContains( $departure_id4, $departure_ids7 );

		// Create departure post.
		$departure_id4 = $this->factory()->post->create(
			[
				'post_type'    => POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => 'Test Departure 4',
				'post_content' => 'Test Departure 4',
			]
		);
		$this->assertIsInt( $departure_id4 );

		// Mark this departure as trash.
		wp_update_post(
			[
				'ID'          => $departure_id4,
				'post_status' => 'trash',
			]
		);

		// Run cleanup.
		do_cleanup( [ $departure_id4 ] );

		// Departure should not be present as it's trashed and cleaned up.
		$departure_ids8 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'post__in'               => [ $departure_id4 ],
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotContains( $departure_id4, $departure_ids8 );

		// Get departure by softrip id - JKL-012.
		$departure_ids5 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'posts_per_page'         => -1,
				'meta_query'             => [
					[
						'key'     => 'softrip_package_code',
						'value'   => 'HIJ-456',
						'compare' => '=',
					],
				],
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertNotEmpty( $departure_ids5 );

		// Convert to int.
		$departure_ids5 = array_map( 'absint', $departure_ids5 );

		// Prepare cards data.
		$card_data = get_card_data( $departure_ids5[0] );
		$this->assertNotEmpty( $card_data );

		// Do cleanup for this departure.
		do_cleanup( [ $departure_ids5[0] ] );

		// Departure should not be present as it's cleaned up.
		$departure_ids6 = get_posts(
			[
				'post_type'              => POST_TYPE,
				'post__in'               => [ $departure_ids5[0] ],
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);
		$this->assertEmpty( $departure_ids6 );

		// Get card data again - should be empty.
		$card_data = get_card_data( $departure_ids5[0] );
		$this->assertEmpty( $card_data );

		// Card data for second departure should still be present.
		$card_data2 = get_card_data( $departure_ids5[1] );
		$this->assertNotEmpty( $card_data2 );
	}
}
