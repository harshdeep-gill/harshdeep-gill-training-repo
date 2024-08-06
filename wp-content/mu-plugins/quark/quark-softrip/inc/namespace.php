<?php
/**
 * Namespace functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_Query;

use function Quark\Softrip\Departure\update_departures;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;

const SCHEDULE_RECURRENCE       = 'qrk_softrip_4_hourly';
const SCHEDULE_HOOK             = 'qrk_softrip_sync';
const ITINERARY_SYNC_BATCH_SIZE = 5;

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
 * @param int[] $itinerary_post_ids Itinerary post IDs.
 *
 * @return void.
 */
function do_sync( $itinerary_post_ids = [] ): void {
	// Get all itinerary post ids.
	$args = [
		'post_type'              => ITINERARY_POST_TYPE,
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_term_meta_cache' => false,
		'ignore_sticky_posts'    => true,
		'post_status'            => [ 'draft', 'publish' ],
	];

	// If no itinerary post ids are provided, get all.
	if ( empty( $itinerary_post_ids ) ) {
		$args['posts_per_page'] = 1;
	} else {
		$args['post__in'] = $itinerary_post_ids;
	}

	// Run WP_Query.
	$query = new WP_Query( $args );

	// Initialize package codes.
	$package_codes = [];

	// Initialize CLI variables.
	$is_in_cli = defined( 'WP_CLI' ) && true === WP_CLI;
	$progress = null;

	// Get all package codes.
	foreach ( $query->posts as $post_id ) {
		$package_code = get_post_meta( absint( $post_id ), 'softrip_package_id', true );

		if ( ! empty( $package_code ) && is_string( $package_code ) ) {
			$package_codes[] = $package_code;
		}
	}

	// Bail if no package codes found.
	if ( empty( $package_codes ) ) {
		// Log CLI message.
		if ( $is_in_cli ) {
			WP_CLI::error( 'No package codes found' );
		}

		// Bail out.
		return;
	}

	// Total count.
	$total = count( $package_codes );

	// Log CLI message.
	if ( $is_in_cli ) {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( 'Syncing Itineraries...' ) );

		// Initialize progress bar.
		$progress = new Bar( 'Softrip sync', $total, 100 );		
	}

	// Log the sync initiated.
	do_action(
		'quark_softrip_sync_initiated',
		[
			'count' => $total,
			'via'   => 'cron',
		]
	);

	// Create batches.
	$batches = array_chunk( $package_codes, ITINERARY_SYNC_BATCH_SIZE );

	// Set up a counter for successful.
	$counter = 0;

	// Iterate over the batches.
	foreach ( $batches as $softrip_codes ) {
		// Get the raw departure data for the IDs.
		$raw_departures = synchronize_itinerary_departures( $softrip_codes );

		// Handle if an error is found.
		if ( ! is_array( $raw_departures ) || empty( $raw_departures ) ) {
			// Update progress bar.
			if ( $is_in_cli ) {
				$progress->tick( count( $softrip_codes ) );
			}

			// Skip since there was an error.
			continue;
		}

		// Process each departure.
		foreach ( $raw_departures as $softrip_package_code => $departures ) {
			// Validate is array and not empty.
			if ( ! is_string( $softrip_package_code ) || ! is_array( $departures ) || empty( $departures ) || empty( $departures['departures'] ) ) {
				// Update progress bar.
				if ( $is_in_cli ) {
					$progress->tick();
				}

				// Skip since there was an error, or departures are empty.
				continue;
			}

			// Update departure data.
			$success = update_departures( $departures['departures'], $softrip_package_code );

			// Update progress bar.
			if ( $is_in_cli ) {
				$progress->tick();
			}

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

	// End progress bar.
	if ( $is_in_cli ) {
		$progress->finish();

		// End notice.
		WP_CLI::success( sprintf( 'Completed %d items with %d failed items', $counter, ( $total - $counter ) ) );
	}
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

/**
 * Get the latest departure data from Softrip.
 *
 * @param string[] $codes Softrip codes.
 *
 * @return mixed[]|WP_Error
 */
function synchronize_itinerary_departures( array $codes = [] ): array|WP_Error {
	// Get unique codes.
	$codes = array_unique( $codes );

	if ( empty( $codes ) ) {
		return new WP_Error( 'qrk_softrip_no_codes', 'No Softrip codes provided' );
	}

	if ( ITINERARY_SYNC_BATCH_SIZE < count( $codes ) ) {
		return new WP_Error( 'qrk_softrip_departures_limit', sprintf( 'The maximum number of codes allowed is %d', ITINERARY_SYNC_BATCH_SIZE ) );
	}

	// Get API adapter.
	$softrip = new Softrip_Data_Adapter();

	// Implode IDs into a string.
	$code_string = implode( ',', $codes );

	// Do request and return the result.
	return $softrip->do_request( 'departures', [ 'productCodes' => $code_string ] );
}
