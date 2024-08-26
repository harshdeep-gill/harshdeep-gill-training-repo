<?php
/**
 * Test suite for cleanup functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Cleanup;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\Cleanup\is_scheduled;
use function Quark\Softrip\Cleanup\schedule_cleanup;

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
}
