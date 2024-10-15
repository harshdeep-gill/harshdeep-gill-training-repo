<?php
/**
 * Namespace functions for cleanup.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Cleanup;

use cli\progress\Bar;
use WP_CLI;
use WP_Query;

use function Quark\Departures\bust_post_cache as bust_departure_post_cache;
use function Quark\Softrip\AdventureOptions\delete_adventure_option_by_id;
use function Quark\Softrip\AdventureOptions\get_adventure_option_by_departure_post_id;
use function Quark\Softrip\get_initiated_via;
use function Quark\Softrip\Occupancies\delete_occupancy_by_id;
use function Quark\Softrip\Occupancies\get_occupancies_by_departure;
use function Quark\Softrip\OccupancyPromotions\delete_occupancy_promotions_by_occupancy_id;

use const Quark\Departures\POST_TYPE;

const SCHEDULE_HOOK = 'qrk_softrip_cleanup';

/**
 * Bootstrap the namespace.
 *
 * @return void
 */
function bootstrap(): void {
	// Schedule the cleanup.
	add_action( 'init', __NAMESPACE__ . '\\schedule_cleanup' );

	// Add the cron task.
	add_action( SCHEDULE_HOOK, __NAMESPACE__ . '\\do_cleanup' );

	// Register the stream connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\register_stream_connector' );

	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-softrip cleanup', __NAMESPACE__ . '\\WP_CLI\Cleanup' );
	}
}

/**
 * Schedule the cleanup.
 *
 * @return void
 */
function schedule_cleanup(): void {
	// Check if the cron task is scheduled.
	if ( is_scheduled() ) {
		return;
	}

	// Schedule the cron task.
	wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', SCHEDULE_HOOK );
}

/**
 * Check if cleanup cron task is scheduled.
 *
 * @return bool
 */
function is_scheduled(): bool {
	// Check if the cron task is scheduled.
	return wp_next_scheduled( SCHEDULE_HOOK ) ? true : false;
}

/**
 * Do Cleanup.
 *
 * @param int[] $departure_post_ids Departure post IDs.
 * @param bool  $delete_departure_post Delete departure post or not.
 *
 * @return void
 */
function do_cleanup( array $departure_post_ids = [], bool $delete_departure_post = true ): void {
	// Initialize CLI variables.
	$is_in_cli = defined( 'WP_CLI' ) && true === WP_CLI;
	$progress  = null;

	// Prepare args. Start date is past 4 months.
	$args = [
		'post_type'              => POST_TYPE,
		'posts_per_page'         => -1,
		'post_status'            => [
			'publish',
			'future',
			'private',
			'pending',
			'draft',
			'trash',
			'auto-draft',
			'inherit',
		],
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'ignore_sticky_posts'    => true,
	];

	// If departure post IDs are empty - get expired departures, else get the departures by IDs.
	if ( empty( $departure_post_ids ) ) {
		// Add meta query.
		$args['meta_query'] = [
			[
				'key'     => 'start_date',
				'value'   => gmdate( 'Y-m-d', strtotime( '-4 months' ) ),
				'compare' => '<',
			],
		];

		// Add post status.
		$args['post_status'] = [
			'draft',
			'trash',
		];
	} else {
		$args['post__in'] = $departure_post_ids;
	}

	// Get the posts.
	$query = new WP_Query( $args );

	// Validate departure post IDs.
	$departure_post_ids = array_map( 'absint', $query->posts );

	// Total posts found.
	$total_posts = count( $departure_post_ids );

	// Bail if empty.
	if ( empty( $total_posts ) ) {
		// Log message if in CLI.
		if ( $is_in_cli ) {
			WP_CLI::success( 'No posts found to cleanup.' );
		}

		// Bail out.
		return;
	}

	// Log CLI message.
	if ( $is_in_cli ) {
		WP_CLI::line( sprintf( 'Found %d departures to cleanup.', $total_posts ) );

		// Progress bar.
		$progress = new Bar( 'Cleaning up departures', $total_posts, 100 );
	}

	// Initiated via.
	$initiated_via = get_initiated_via();

	// Counter for successful.
	$success = 0;

	// Log initiated action.
	do_action(
		'quark_softrip_cleanup_initiated',
		[
			'total' => $total_posts,
			'via'   => $initiated_via,
		]
	);

	// Delete occupancies.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Get occupancies.
		$occupancies = get_occupancies_by_departure( $departure_post_id );

		// Flag if all occupancies are deleted.
		$all_occupancies_deleted = true;

		// Delete occupancies.
		foreach ( $occupancies as $occupancy ) {
			// Check if valid occupancy.
			if ( ! is_array( $occupancy ) || empty( $occupancy['id'] ) ) {
				continue;
			}

			// Get cabin category ID.
			$cabin_category_id = $occupancy['cabin_category_post_id'];

			// Delete occupancy promotion row.
			$is_occupancy_promotions_deleted = delete_occupancy_promotions_by_occupancy_id( $occupancy['id'] );

			// If occupancy promotions are not deleted, then skip the deletion of occupancy.
			if ( ! $is_occupancy_promotions_deleted ) {
				$all_occupancies_deleted = false;
				continue;
			}

			// Delete occupancy.
			$is_occupancy_deleted = delete_occupancy_by_id( $occupancy['id'] );

			// If occupancy is not deleted, then skip the deletion of cabin category.
			if ( ! $is_occupancy_deleted ) {
				$all_occupancies_deleted = false;
				continue;
			}

			// Bust caches.
			delete_post_meta( $departure_post_id, 'cabin_spaces_available_' . $cabin_category_id );
		}

		// Skip the deletion of departure post if any occupancy is not deleted.
		if ( ! $all_occupancies_deleted ) {
			// Update progress.
			if ( $is_in_cli ) {
				WP_CLI::log( sprintf( 'All occupancies are not deleted for departure %d.', $departure_post_id ) );
				$progress->tick();
			}

			// Log failed action.
			do_action(
				'quark_softrip_cleanup_failed',
				[
					'departure_post_id' => $departure_post_id,
					'message'           => 'All occupancies are not deleted.',
					'via'               => $initiated_via,
				]
			);

			// Bust departure cache.
			bust_departure_post_cache( $departure_post_id );

			// Bail out.
			continue;
		}

		// Get adventure options by departure.
		$adventure_options = get_adventure_option_by_departure_post_id( $departure_post_id );

		// Flag if all adventure options are deleted.
		$all_adventure_options_deleted = true;

		// Delete adventure options.
		foreach ( $adventure_options as $adventure_option ) {
			// Check if valid adventure option.
			if ( ! is_array( $adventure_option ) || empty( $adventure_option['id'] ) ) {
				continue;
			}

			// Delete adventure option.
			$is_adventure_option_deleted = delete_adventure_option_by_id( $adventure_option['id'] );

			// If adventure option is not deleted.
			if ( ! $is_adventure_option_deleted ) {
				$all_adventure_options_deleted = false;
				continue;
			}
		}

		// Skip the deletion of departure post if any adventure option is not deleted.
		if ( ! $all_adventure_options_deleted ) {
			// Update progress.
			if ( $is_in_cli ) {
				WP_CLI::log( sprintf( 'All adventure options are not deleted for departure %d.', $departure_post_id ) );
				$progress->tick();
			}

			// Log failed action.
			do_action(
				'quark_softrip_cleanup_failed',
				[
					'departure_post_id' => $departure_post_id,
					'message'           => 'All adventure options are not deleted.',
					'via'               => $initiated_via,
				]
			);

			// Bust departure cache.
			bust_departure_post_cache( $departure_post_id );

			// Bail out.
			continue;
		}

		// Delete departure post if flag is set.
		if ( $delete_departure_post ) {
			// Delete departure post.
			$is_departure_deleted = wp_delete_post( $departure_post_id, true );

			// Skip if not deleted.
			if ( empty( $is_departure_deleted ) ) {
				// Update progress.
				if ( $is_in_cli ) {
					WP_CLI::log( sprintf( 'Departure %d post is not deleted, but it\'s all occupancies and adventure options are deleted.', $departure_post_id ) );
					$progress->tick();
				}

				// Log failed action.
				do_action(
					'quark_softrip_cleanup_failed',
					[
						'departure_post_id' => $departure_post_id,
						'message'           => 'Departure post is not deleted, but it\'s all occupancies and adventure options are deleted.',
						'via'               => $initiated_via,
					]
				);

				// Bail out.
				continue;
			}
		}

		// Bust departure cache.
		bust_departure_post_cache( $departure_post_id );

		// Increment success counter.
		++$success;

		// Update progress.
		if ( $is_in_cli ) {
			$progress->tick();
		}
	}

	// Log completed action.
	do_action(
		'quark_softrip_cleanup_completed',
		[
			'total'   => $total_posts,
			'success' => $success,
			'via'     => $initiated_via,
		]
	);

	// Log message if in CLI.
	if ( $is_in_cli ) {
		$progress->finish();

		// End notice.
		WP_CLI::success( sprintf( 'Completed %d items with %d failed items', $success, ( $total_posts - $success ) ) );
	}
}

/**
 * Register custom stream connectors for Softrip sync.
 *
 * @param array<string, mixed> $connectors Connectors.
 *
 * @return array<string, mixed>
 */
function register_stream_connector( array $connectors = [] ): array {
	// Load Stream connector file.
	require_once __DIR__ . '/class-stream-connector.php';

	// Add our connector.
	$connectors['quark_softrip_cleanup'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
}
