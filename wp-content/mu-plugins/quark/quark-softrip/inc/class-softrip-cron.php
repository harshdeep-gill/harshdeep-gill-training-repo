<?php
/**
 * Softrip Cron Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Query;
use WP_Error;

use const Quark\Itineraries\POST_TYPE;

/**
 * Class Softrip_Cron.
 */
class Softrip_Cron {

	/**
	 * Holds the schedule recurrence key.
	 *
	 * @var string
	 */
	protected static string $schedule_recurrence = 'qrk_softrip_4_hourly';

	/**
	 * Holds the schedule hook.
	 *
	 * @var string
	 */
	protected static string $schedule_hook = 'qrk_softrip_sync';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add in filter to add in our sync schedule.
		add_filter( 'cron_schedules', [ $this, 'add_cron_schedule' ] );

		// Schedule our sync task.
		add_filter( 'admin_init', [ $this, 'schedule_sync' ] );

		// Register our sync hook.
		add_action( self::$schedule_hook, [ $this, 'do_sync' ] );
	}

	/**
	 * Registers our custom schedule.
	 *
	 * @param array<string, array<int|string, int|string>> $schedules The current schedules to add to.
	 *
	 * @return array<string, array<int|string, int|string>>
	 */
	public function add_cron_schedule( array $schedules = [] ): array {
		// Create our schedule.
		$schedules[ self::$schedule_recurrence ] = [
			'interval' => 4 * HOUR_IN_SECONDS,
			'display'  => __( 'Once every 4 hours' ),
		];

		// return with custom added.
		return $schedules;
	}

	/**
	 * Check if the cron task is already scheduled.
	 *
	 * @return bool
	 */
	protected function is_scheduled(): bool {
		// Check if the schedule exists or not.
		return ! empty( wp_next_scheduled( self::$schedule_hook ) );
	}

	/**
	 * Schedule the sync cron task.
	 *
	 * @return void
	 */
	public function schedule_sync(): void {
		// Check if scheduled.
		if ( $this->is_scheduled() ) {
			return;
		}

		// Set a time + 4 hours.
		$next_time = time() + ( HOUR_IN_SECONDS * 4 );

		// Schedule the event. in 4 hours time.
		wp_schedule_event( $next_time, self::$schedule_recurrence, self::$schedule_hook );
	}

	/**
	 * Do the sync.
	 *
	 * @return void
	 */
	public function do_sync(): void {
		// Get the sync object.
		$sync = new Softrip_Sync();

		// Get the ID's to sync.
		$ids = $sync->get_itinerary_ids();

		// Create batches.
		$batches = $sync->prepare_batch_ids( $ids );

		// Iterate over the batches.
		foreach ( $batches as $softrip_ids ) {
			// Get the raw departure data for the IDs.
			$raw_departures = $sync->batch_request( $softrip_ids );

			// Handle if an error is found.
			if ( empty( $raw_departures ) ) {
				// @todo: add in debug logging for failed cron tasks.
				continue;
			}

			// Process each departure.
			foreach ( $raw_departures as $softrip_id => $departures ) {
				// Validate is array and not empty.
				if ( ! is_array( $departures ) || empty( $departures ) ) {
					// @todo: add in debug logging for failed cron tasks.
					continue;
				}

				// Sync the code.
				$sync->sync_softrip_code( $softrip_id, $departures );
			}
		}
	}
}
