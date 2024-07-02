<?php
/**
 * Softrip: Sync.
 *
 * @package quark-softrip
 */

namespace Quark\softrip\WP_CLI;

use cli\progress\Bar;
use Quark\Softrip\Itinerary;
use WP_CLI;
use WP_CLI\ExitException;

use const Quark\Itineraries\POST_TYPE;

//use function Quark\Softrip\get_itinerary;

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
			$args_assoc['ids'] = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $args_assoc['ids'] ) ) ) );
		}

		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'ids' => [],
			]
		);

		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( 'Syncing...' . count( $options['ids'] ) ) );

		// Initialize progress bar.
		$progress = new Bar( 'Starting', count( $options['ids'] ), 100 );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start.
		foreach ( $options['ids'] as $id ) {
			// Get Itinerary and update.
			$itinerary = new Itinerary( $id );
			$itinerary->update_departures();
			$progress->tick( 1, $id );
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
			'posts_per_page' => 10,
			'fields'         => 'ids',
		];

		$query = new \WP_Query( $args );
		$ids   = [];
		foreach ( $query->posts as $post ) {
			$ids[] = $post;
		}

		$list = implode( ',', $ids );
		$this->sync( [], [ 'ids' => $list ] );
	}
}
