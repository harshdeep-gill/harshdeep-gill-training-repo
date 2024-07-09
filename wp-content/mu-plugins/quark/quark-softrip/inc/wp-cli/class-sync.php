<?php
/**
 * Softrip: Sync.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\WP_CLI;

use cli\progress\Bar;
use Quark\Softrip\Itinerary;
use WP_CLI;
use WP_CLI\ExitException;
use WP_Query;
use WP_Error;

use function Quark\Softrip\request_departures;

use const Quark\Itineraries\POST_TYPE;

/**
 * Class Sync.
 */
class Sync {
	/**
	 * Softrip sync itineraries.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand items
	 * @synopsis [--ids=<1,2>]
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function sync( array $args = [], array $args_assoc = [] ): void {
		// Explode.
		if ( ! empty( $args_assoc['ids'] ) ) {
			$args_assoc['ids'] = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', strval( $args_assoc['ids'] ) ) ) ) );
		}

		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'ids' => [],
			]
		);

		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( 'Syncing Itineraries...' ) );

		// Initialize progress bar.
		$progress = new Bar( 'Softrip sync', count( $options['ids'] ), 100 );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start sync items.
		$to_sync = [];

		// Create packages.
		foreach ( $options['ids'] as $id ) {
			$softrip_package_id = get_post_meta( $id, 'softrip_package_id', true );

			// Capture if a package id is found.
			if ( ! empty( $softrip_package_id ) ) {
				$to_sync[ $softrip_package_id ] = $id;
			}
		}

		// Chunk to sync into packages.
		$parts = array_chunk( array_keys( $to_sync ), 5 );

		// Process each part.
		foreach ( $parts as $softrip_ids ) {
			// Get the raw departure data for the IDs.
			$raw_departures = request_departures( $softrip_ids );

			// Handle if an error is found.
			if ( $raw_departures instanceof WP_Error ) {
				continue;
			}

			// Process each departure.
			foreach ( $raw_departures as $softrip_id => $departures ) {
				// Validate is array and not empty.
				if ( ! is_array( $departures ) || empty( $departures ) ) {
					continue;
				}

				// Get itinerary and update data.
				$post_id   = $to_sync[ $softrip_id ];
				$itinerary = new Itinerary( $post_id );
				$itinerary->update_departures( (array) $departures );
				$progress->tick();
			}
		}

		// End bar.
		$progress->finish();

		// End notice.
		WP_CLI::success( 'Completed.' );
	}

	/**
	 * Softrip sync itineraries.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function all(): void {
		// Args to get items.
		$args = [
			'post_type'      => POST_TYPE,
			'posts_per_page' => 100,
			'fields'         => 'ids',
			'offset'         => 0,
		];

		// Run the query.
		$query = new WP_Query( $args );

		// Set the count.
		$found_posts = $query->found_posts;
		$processed   = 0;
		$ids         = [];

		// Get the post ids.
		while ( $processed < $found_posts ) {
			// Loop over the posts.
			foreach ( $query->posts as $post ) {
				$ids[] = absint( $post );
				++$processed;
			}
			$args['offset'] = $processed;
			$query          = new WP_Query( $args );
		}

		// Implode and run sync.
		$list = implode( ',', $ids );
		$this->sync( [], [ 'ids' => $list ] );
	}
}
