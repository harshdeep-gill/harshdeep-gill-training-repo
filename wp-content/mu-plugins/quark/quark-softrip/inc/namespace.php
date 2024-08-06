<?php
/**
 * Namespace functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_CLI;
use WP_Error;

const SCHEDULE_RECURRENCE = 'qrk_softrip_4_hourly';
const SCHEDULE_HOOK       = 'qrk_softrip_sync';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-softrip db', __NAMESPACE__ . '\\WP_CLI\\DB' );
		WP_CLI::add_command( 'quark-softrip sync', __NAMESPACE__ . '\\WP_CLI\\Sync' );
	}

	// Add in filter to add in our sync schedule.
	add_filter( 'cron_schedules', __NAMESPACE__ . '\\cron_add_schedule' ); // phpcs:ignore WordPress.WP.CronInterval -- Verified > 4 Hour.

	// Schedule our sync task.
	add_filter( 'init', __NAMESPACE__ . '\\cron_schedule_sync' );

	// Register our sync hook.
	add_action( SCHEDULE_HOOK, __NAMESPACE__ . '\\do_sync' );

	// Register Stream log connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );
}

/**
 * Request departures for an array of Softrip IDs.
 *
 * @param array<int, mixed> $codes Softrip ID array, max 5.
 *
 * @return mixed[]|WP_Error
 */
function request_departures( array $codes = [] ): array|WP_Error {
	// Strip out duplicates.
	$codes = array_unique( $codes );

	// Check if less than 5 IDs.
	if ( empty( $codes ) || 5 < count( $codes ) ) {
		return new WP_Error( 'qrk_softrip_departures_limit', 'The maximum number of codes allowed is 5' );
	}

	// Get API.
	$softrip = new Softrip_Data_Adapter();

	// Implode IDs into a string.
	$code_string = implode( ',', $codes );

	// Do request and return the result.
	return $softrip->do_request( 'departures', [ 'productCodes' => $code_string ] );
}

/**
 * Registers our custom schedule.
 *
 * @param array<string, array<int|string, int|string>> $schedules The current schedules to add to.
 *
 * @return array<string, array<int|string, int|string>>
 */
function cron_add_schedule( array $schedules = [] ): array {
	// Explicitly define the interval in seconds.
	$interval = 4 * HOUR_IN_SECONDS; // 4 hours.

	// Create our schedule.
	$schedules[ SCHEDULE_RECURRENCE ] = [
		'interval' => $interval,
		'display'  => 'Once every 4 hours',
	];

	// return with custom added.
	return $schedules;
}

/**
 * Check if the cron task is already scheduled.
 *
 * @return bool
 */
function cron_is_scheduled(): bool {
	// Check if the schedule exists or not.
	return ! empty( wp_next_scheduled( SCHEDULE_HOOK ) );
}

/**
 * Schedule the sync cron task.
 *
 * @return void
 */
function cron_schedule_sync(): void {
	// Check if scheduled.
	if ( cron_is_scheduled() ) {
		return;
	}

	// Set a time + 4 hours.
	$next_time = time() + ( HOUR_IN_SECONDS * 4 );

	// Schedule the event. in 4 hours time.
	wp_schedule_event( $next_time, SCHEDULE_RECURRENCE, SCHEDULE_HOOK );
}

/**
 * Do the sync.
 *
 * @return void
 */
function do_sync(): void {
	// Get the sync object.
	$sync = new Softrip_Sync();

	// Get the ID's to sync.
	$ids = $sync->get_all_itinerary_ids();

	// Get the total count.
	$total = count( $ids );

	// Log the sync initiated.
	do_action(
		'quark_softrip_sync_initiated',
		[
			'count' => $total,
			'via'   => 'cron',
		]
	);

	// Create batches.
	$batches = $sync->prepare_batch_ids( $ids );

	// Set up a counter for successful.
	$counter = 0;

	// Iterate over the batches.
	foreach ( $batches as $softrip_ids ) {
		// Get the raw departure data for the IDs.
		$raw_departures = $sync->batch_request( $softrip_ids );

		// Handle if an error is found.
		if ( empty( $raw_departures ) ) {
			// Skip since there was an error.
			continue;
		}

		// Process each departure.
		foreach ( $raw_departures as $softrip_id => $departures ) {
			// Validate is array and not empty.
			if ( ! is_array( $departures ) || empty( $departures ) ) {
				// Skip since there was an error, or departures are empty.
				continue;
			}

			// Sync the code.
			$success = $sync->sync_softrip_code( $softrip_id, $departures );

			// Check if successful.
			if ( $success ) {
				// Update counter.
				++$counter;
			}
		}
	}

	// Log the sync completed.
	do_action(
		'quark_softrip_sync_completed',
		[
			'success' => $counter,
			'failed'  => $total - $counter,
			'via'     => 'cron',
		]
	);
}

/**
 * Register custom stream connectors for Softrip sync.
 *
 * @param array<string, mixed> $connectors Connectors.
 *
 * @return array<string, mixed>
 */
function setup_stream_connectors( array $connectors = [] ): array {
	// Load Stream connector file.
	require_once __DIR__ . '/class-stream-connector.php';

	// Add our connector.
	$connectors['quark_softrip_sync'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
}
