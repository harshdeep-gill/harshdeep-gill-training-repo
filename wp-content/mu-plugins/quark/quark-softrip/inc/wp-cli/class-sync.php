<?php
/**
 * Softrip: Sync.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\WP_CLI;

use cli\progress\Bar;
use Quark\Softrip\Softrip_Sync;
use WP_CLI;
use WP_CLI\ExitException;

const BATCH_SIZE = 5;

/**
 * Class Sync.
 */
class Sync {

	/**
	 * Holds the Sync object.
	 *
	 * @var Softrip_Sync
	 */
	protected Softrip_Sync $sync;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Init the sync object.
		$this->sync = new Softrip_Sync();
	}

	/**
	 * Softrip sync itineraries.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand items
	 *
	 * ## OPTIONS
	 * [<ids>]
	 * : The ID's to sync.
	 *
	 * @synopsis [--ids=<1,2>]
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function do_sync( array $args = [], array $args_assoc = [] ): void {
		// Check for ID's.
		if ( empty( $args_assoc['ids'] ) ) {
			WP_CLI::error( "ID's are required: --ids=<1,1>" );
		}

		// Check if is a string and explode.
		if ( is_string( $args_assoc['ids'] ) ) {
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
		$total    = count( $options['ids'] );
		$progress = new Bar( 'Softrip sync', $total, 100 );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// split batches.
		$parts = $this->sync->prepare_batch_ids( $options['ids'], BATCH_SIZE );

		// Set up a counter for how many we're successful.
		$counter = 0;

		// Get the list of prepared codes.
		$to_sync = $this->sync->get_prepared_codes();

		// Process each part.
		foreach ( $parts as $softrip_ids ) {
			// Get the raw departure data for the IDs.
			$raw_departures = $this->sync->batch_request( $softrip_ids );

			// Handle if an error is found.
			if ( empty( $raw_departures ) ) {
				// If this fails, it fails for the 5 items.
				$progress->tick( 5 );
				continue;
			}

			// Process each departure.
			foreach ( $raw_departures as $softrip_id => $departures ) {
				// Validate is array and not empty.
				if ( ! is_array( $departures ) || empty( $departures ) ) {
					$progress->tick();
					continue;
				}

				// Sync the code.
				$success = $this->sync->sync_softrip_code( $softrip_id, $departures );
				$progress->tick();

				// Check if successful.
				if ( $success ) {
					// Update counter.
					++$counter;
				}
			}
		}

		// End bar.
		$progress->finish();

		// Check if failed any.
		$if_failed = '.';

		// Compare counter with total.
		if ( $counter < $total ) {
			$if_failed = ' with ' . ( $total - $counter ) . ' failed items.';
		}

		// End notice.
		WP_CLI::success( 'Completed ' . $counter . ' items' . $if_failed );
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
		// Get Itinerary ID's.
		$ids = $this->sync->get_itinerary_ids();

		// Implode and run sync.
		$this->do_sync( [], [ 'ids' => $ids ] );
	}
}
