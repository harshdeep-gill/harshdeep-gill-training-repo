<?php
/**
 * Softrip: Sync.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\WP_CLI;

use WP_CLI;
use WP_CLI\ExitException;
use WP_Query;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;

use function Quark\Softrip\do_sync;

const BATCH_SIZE = 5;

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

		// Run sync.
		do_sync( $options['ids'] );
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
		$args = [
			'post_type'      => ITINERARY_POST_TYPE,
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'update_post_meta_cache' => false,
			'update_term_meta_cache' => false,
			'ignore_sticky_posts'    => true,
			'post_status'            => [ 'draft', 'publish' ],
		];

		// Run WP_Query.
		$query = new WP_Query( $args );

		// Get all itinerary post ids.
		$ids = $query->posts;

		// Validate IDs.
		$ids = array_map( 'absint', $ids );

		// Implode and run sync.
		$this->do_sync( [], [ 'ids' => $ids ] );
	}
}
