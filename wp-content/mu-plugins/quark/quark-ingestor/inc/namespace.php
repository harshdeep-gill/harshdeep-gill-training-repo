<?php
/**
 * Namespace for the Softrip Ingestor.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_Post;
use WP_Query;

use function Quark\Core\init_auto_cloudinary;
use function Quark\Ingestor\Expeditions\get_expedition_data;
use function Quark\Softrip\get_initiated_via;
use function get_post_modified_time as wp_get_post_modified_time;

use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;

const SCHEDULE_HOOK                 = 'qrk_ingestor_push';
const DATA_HASH_KEY                 = '_ingestor_data_hash';
const START_ID                      = 130000;
const VIRTUAL_DRUPAL_ID_COUNTER_KEY = 'qrk_ingestor_virtual_drupal_id_counter';

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		// Require cli file.
		require_once __DIR__ . '/wp-cli/class-push.php';

		// Add CLI command.
		WP_CLI::add_command( 'quark-ingestor push', __NAMESPACE__ . '\\WP_CLI\\Push' );
	}

	// Push cron handler.
	add_action( SCHEDULE_HOOK, __NAMESPACE__ . '\\do_push' );

	// Register cron.
	add_action( 'init', __NAMESPACE__ . '\\cron_schedule_push' );

	// Register Stream log connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );
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
	$connectors['quark_ingestor_push'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
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
 * Register cron.
 *
 * @return void
 */
function cron_schedule_push(): void {
	// Check if scheduled.
	if ( cron_is_scheduled() ) {
		return;
	}

	// Set a time + 1 hour + 90 seconds.
	$next_time = time() + HOUR_IN_SECONDS + 90;

	// Schedule the event. in 4 hours time.
	wp_schedule_event( $next_time, 'hourly', SCHEDULE_HOOK );
}

/**
 * Push data to the ingestor.
 *
 * @param int[] $expedition_post_ids Expedition post IDs.
 * @param bool  $changed_only        Only push changed expeditions.
 *
 * @return void
 */
function do_push( array $expedition_post_ids = [], bool $changed_only = true ): void {
	// Initialize Cloudinary. This ensures that media URLs are of Cloudinary.
	init_auto_cloudinary();

	// If no expedition post IDs, get all.
	if ( empty( $expedition_post_ids ) ) {
		// Prepare args.
		$args = [
			'post_type'              => EXPEDITION_POST_TYPE,
			'posts_per_page'         => -1,
			'post_status'            => [ 'publish', 'draft' ],
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
		];

		// Get all expedition IDs.
		$expeditions         = new WP_Query( $args );
		$expedition_post_ids = $expeditions->posts;
		$expedition_post_ids = array_map( 'absint', $expedition_post_ids );
	} else {
		// Remove duplicates.
		$expedition_post_ids = array_unique( $expedition_post_ids );

		// Validate expedition post IDs.
		$expedition_post_ids = array_filter(
			$expedition_post_ids,
			function ( $expedition_post_id ) {
				return get_post_type( $expedition_post_id ) === EXPEDITION_POST_TYPE;
			}
		);
	}

	// Initialize CLI variables.
	$is_in_cli = defined( 'WP_CLI' ) && true === WP_CLI;
	$progress  = null;

	// Initiated via.
	$initiated_via = get_initiated_via();

	// If empty expedition posts.
	if ( empty( $expedition_post_ids ) ) {
		// Check if in CLI.
		if ( $is_in_cli ) {
			// Output message.
			WP_CLI::log( 'No expeditions found.' );
		}

		// Log error.
		do_action(
			'quark_ingestor_push_error',
			[
				'error'         => 'No expeditions found.',
				'initiated_via' => $initiated_via,
			]
		);

		// Return.
		return;
	}

	// Total count.
	$total_count = count( $expedition_post_ids );

	// Success count.
	$success_count = 0;

	// Log CLI message.
	if ( $is_in_cli ) {
		// Output message.
		WP_CLI::log( 'Pushing expeditions to ingestor...' );

		// Initialize progress.
		$progress = new Bar( 'Ingestor push', $total_count );
	}

	// Log push initiated.
	do_action(
		'quark_ingestor_push_initiated',
		[
			'expedition_post_ids' => $expedition_post_ids,
			'changed_only'        => $changed_only,
			'initiated_via'       => $initiated_via,
			'total_count'         => $total_count,
		]
	);

	// Get data for each expedition.
	foreach ( $expedition_post_ids as $expedition_post_id ) {
		// Get expedition data.
		$expedition_data = get_expedition_data( $expedition_post_id );

		// Check for expedition data.
		if ( empty( $expedition_data ) ) {
			// Increment progress.
			if ( $is_in_cli ) {
				$progress->tick();
			}

			// Log error.
			do_action(
				'quark_ingestor_push_error',
				[
					'expedition_post_id' => $expedition_post_id,
					'error'              => 'Expedition data is empty.',
					'initiated_via'      => $initiated_via,
				]
			);

			// Continue.
			continue;
		}

		// JSON encode expedition data.
		$json_expedition_data = wp_json_encode( $expedition_data, JSON_UNESCAPED_UNICODE );

		// Validate JSON.
		if ( empty( $json_expedition_data ) ) {
			// Increment progress.
			if ( $is_in_cli ) {
				$progress->tick();
			}

			// Log error.
			do_action(
				'quark_ingestor_push_error',
				[
					'expedition_post_id' => $expedition_post_id,
					'error'              => 'Invalid JSON data.',
					'initiated_via'      => $initiated_via,
				]
			);

			// Continue.
			continue;
		}

		// Hash expedition data.
		$new_hash = md5( $json_expedition_data );

		// Check for changed only.
		if ( $changed_only ) {
			// Get old hash.
			$old_hash = get_post_meta( $expedition_post_id, DATA_HASH_KEY, true );

			// Compare hash.
			if ( $new_hash === $old_hash ) {
				// Increment progress.
				if ( $is_in_cli ) {
					$progress->tick();
				}

				// Log error.
				do_action(
					'quark_ingestor_push_error',
					[
						'expedition_post_id' => $expedition_post_id,
						'error'              => 'No changes detected.',
						'initiated_via'      => $initiated_via,
					]
				);

				// Continue.
				continue;
			}
		}

		// Push expedition data.
		$push_result = push_expedition_data( $expedition_post_id, $json_expedition_data );

		// Check for WP_Error.
		if ( $push_result instanceof WP_Error ) {
			// Log error.
			do_action(
				'quark_ingestor_push_error',
				[
					'expedition_post_id' => $expedition_post_id,
					'error'              => $push_result->get_error_message(),
					'initiated_via'      => $initiated_via,
				]
			);
		} elseif ( ! empty( $push_result ) ) {
				// Update hash if successful.
				update_post_meta( $expedition_post_id, DATA_HASH_KEY, $new_hash );

				// Log success.
				do_action(
					'quark_ingestor_push_success',
					[
						'expedition_post_id' => $expedition_post_id,
						'initiated_via'      => $initiated_via,
						'changed_only'       => $changed_only,
						'hash'               => $new_hash,
					]
				);

				// Increment success count.
				++$success_count;
		} else {
			// Log error.
			do_action(
				'quark_ingestor_push_error',
				[
					'expedition_post_id' => $expedition_post_id,
					'error'              => 'Failed to push data.',
					'initiated_via'      => $initiated_via,
				]
			);
		}

		// Increment progress.
		if ( $is_in_cli ) {
			$progress->tick();
		}
	}

	// Log CLI message.
	if ( $is_in_cli ) {
		// Finish progress.
		$progress->finish();

		// Output message.
		WP_CLI::success( sprintf( 'Pushed %d of %d expeditions.', $success_count, $total_count ) );
	}

	// Log push completed.
	do_action(
		'quark_ingestor_push_completed',
		[
			'expedition_post_ids' => $expedition_post_ids,
			'changed_only'        => $changed_only,
			'initiated_via'       => $initiated_via,
			'success_count'       => $success_count,
			'total_count'         => $total_count,
		]
	);
}

/**
 * Push data to ingestor.
 *
 * @param int    $expedition_post_id   Expedition post ID.
 * @param string $json_expedition_data JSON expedition data.
 *
 * @return bool|WP_Error
 */
function push_expedition_data( int $expedition_post_id = 0, string $json_expedition_data = '' ): bool|WP_Error {
	// Check for expedition post ID.
	if ( empty( $expedition_post_id ) ) {
		return new WP_Error( 'qrk_ingestor_invalid_expedition_id', 'Invalid expedition post ID.' );
	}

	// Check for expedition data.
	if ( empty( $json_expedition_data ) ) {
		return new WP_Error( 'qrk_ingestor_invalid_expedition_data', 'Invalid expedition data.' );
	}

	// Validate credentials.
	if (
		! defined( 'QUARK_INGESTOR_BASE_URL' ) ||
		! defined( 'QUARK_INGESTOR_API_KEY' ) ||
		empty( QUARK_INGESTOR_BASE_URL ) ||
		empty( QUARK_INGESTOR_API_KEY )
	) {
		return new WP_Error( 'qrk_ingestor_no_auth', 'Ingestor credentials missing' );
	}

	// Construct URL.
	$url = trailingslashit( QUARK_INGESTOR_BASE_URL );

	// Set request args.
	$args = [
		'method'  => 'POST',
		'timeout' => 20,
		'headers' => [
			'x-api-key'    => QUARK_INGESTOR_API_KEY,
			'Content-Type' => 'application/json',
		],
		'body'    => $json_expedition_data,
	];

	// Do request.
	$request = wp_remote_request( $url, $args );

	// Return WP_Error if failed.
	if ( $request instanceof WP_Error ) {
		return $request;
	}

	// Check response code.
	if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
		return new WP_Error( 'qrk_ingestor_invalid_response', wp_remote_retrieve_response_message( $request ) );
	}

	// Return url.
	return true;
}

/**
 * Get all data to be sent to ingestor.
 *
 * @return array{}|array<int,
 *   array{
 *       id: int,
 *       name: string,
 *       description: string,
 *       images: array{}|array<int,
 *         array{
 *           id: int,
 *           fullSizeUrl: string,
 *           thumbnailUrl: string,
 *           alt: string,
 *         }
 *       >,
 *       destinations: array{}|array<int,
 *          array{
 *             id: int,
 *             name: string,
 *             region: array{
 *             name: string,
 *             code: string,
 *            }
 *          }
 *       >,
 *       itineraries: array{}|array<int,
 *         array{
 *           id: int,
 *           packageId: string,
 *           name: string,
 *           startLocation: string,
 *           endLocation: string,
 *           departures: mixed[],
 *         }
 *       >
 *   }
 * >
 */
function get_all_data(): array {
	// Prepare args.
	$args = [
		'post_type'              => EXPEDITION_POST_TYPE,
		'posts_per_page'         => -1,
		'post_status'            => [ 'publish', 'draft' ],
		'fields'                 => 'ids',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'no_found_rows'          => true,
	];

	// Get all expedition IDs.
	$expeditions         = new WP_Query( $args );
	$expedition_post_ids = $expeditions->posts;
	$expedition_post_ids = array_map( 'absint', $expedition_post_ids );

	// Initialize results.
	$results = [];

	// Get data for each expedition.
	foreach ( $expedition_post_ids as $expedition_post_id ) {
		// Get expedition data.
		$expedition_data = get_expedition_data( $expedition_post_id );

		// Check for expedition data.
		if ( empty( $expedition_data ) ) {
			continue;
		}

		// Add expedition data to results.
		$results[ $expedition_post_id ] = $expedition_data;
	}

	// Return results.
	return $results;
}

/**
 * Get image details.
 *
 * @param int $image_id Image ID.
 *
 * @return array{}|array{
 *   id: int,
 *   fullSizeUrl: string,
 *   thumbnailUrl: string,
 *   alt: string,
 *   title: string,
 *   dimensions: array{
 *     width: int,
 *     height: int,
 *   }
 * }
 */
function get_image_details( int $image_id = 0 ): array {
	// Validate id.
	if ( empty( $image_id ) ) {
		return [];
	}

	// Skip if not id of an image attachment.
	if ( ! wp_attachment_is( 'image', $image_id ) ) {
		return [];
	}

	// Full size image.
	$full_size_image = wp_get_attachment_image_src( $image_id, 'full' );

	// Validate image.
	if ( empty( $full_size_image ) ) {
		return [];
	}

	// Thumbnail url.
	$thumbnail_url = strval( wp_get_attachment_image_url( $image_id, 'thumbnail' ) );

	// Alt text.
	$alt_text = strval( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );

	// Title text.
	$title_text = get_post_field( 'post_title', $image_id );

	// Return image details.
	return [
		'type'         => 'image',
		'id'           => $image_id,
		'fullSizeUrl'  => $full_size_image[0],
		'thumbnailUrl' => $thumbnail_url,
		'alt'          => $alt_text,
		'title'        => $title_text,
		'dimensions'   => [
			'width'  => $full_size_image[1],
			'height' => $full_size_image[2],
		],
	];
}

/**
 * Get modified time of a post.
 *
 * @param int|WP_Post $post Post ID or WP_Post object.
 *
 * @return string
 */
function get_post_modified_time( int|WP_Post $post = 0 ): string {
	// Get modified time.
	$modified_time = wp_get_post_modified_time( 'c', true, $post );

	// Validate modified time.
	if ( ! is_string( $modified_time ) ) {
		return '';
	}

	// Return modified time.
	return $modified_time;
}

/**
 * Get drupal ID from post meta. If empty, generate a new one.
 * This is done to let content API queryable by legacy drupal ID.
 *
 * @param int $post_id Post ID.
 *
 * @return int
 */
function get_id( int $post_id = 0 ): int {
	// Validate post id.
	if ( empty( $post_id ) ) {
		return 0;
	}

	// Check if post exists.
	if ( false === get_post_status( $post_id ) ) {
		return 0;
	}

	// Get drupal ID.
	$drupal_id = absint( get_post_meta( $post_id, 'drupal_id', true ) );

	// Generate new ID if empty.
	if ( empty( $drupal_id ) ) {
		$drupal_id = generate_virtual_drupal_id();

		// Update post meta.
		update_post_meta( $post_id, 'drupal_id', $drupal_id );
	}

	// Return drupal ID.
	return $drupal_id;
}

/**
 * Generate a virtual Drupal ID.
 *
 * @return int
 */
function generate_virtual_drupal_id(): int {
	// Get current counter from options.
	$counter = get_option( VIRTUAL_DRUPAL_ID_COUNTER_KEY, START_ID );

	// Increment counter.
	++$counter;

	// Update counter.
	update_option( VIRTUAL_DRUPAL_ID_COUNTER_KEY, $counter );

	// Return counter.
	return $counter;
}
