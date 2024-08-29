<?php
/**
 * Softrip: Cleanup CLI commands.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Cleanup\WP_CLI;

use WP_CLI;

use function Quark\Softrip\Cleanup\do_cleanup;

/**
 * Class Cleanup.
 */
class Cleanup {

	/**
	 * Softrip cleanup specific departure.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand items
	 *
	 * ## OPTIONS
	 * [<ids>]
	 * : The ID's to cleanup.
	 *
	 * @synopsis [--ids=<1,2>]
	 *
	 * @return void
	 */
	public function do_cleanup( array $args = [], array $args_assoc = [] ): void {
		// Check for id.
		if ( empty( $args_assoc['ids'] ) ) {
			WP_CLI::error( "ID's are required: --ids=<1,1>" );
		}

		// Skip if not string.
		if ( ! is_string( $args_assoc['ids'] ) ) {
			WP_CLI::error( 'IDs must be integer separate by comma' );
		}

		// Validate ids.
		$args_assoc['ids'] = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', strval( $args_assoc['ids'] ) ) ) ) );

		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'ids' => [],
			]
		);

		// Check if ID's are empty.
		if ( empty( $options['ids'] ) ) {
			WP_CLI::error( 'No ID\'s found' );
		}

		// Run cleanup.
		do_cleanup( $options['ids'] );
	}

	/**
	 * Softrip cleanup all departures.
	 *
	 * @subcommand all
	 *
	 * @return void
	 */
	public function all(): void {
		// Run cleanup.
		do_cleanup();
	}
}
