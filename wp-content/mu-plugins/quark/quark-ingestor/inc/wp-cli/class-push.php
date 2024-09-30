<?php
/**
 * Ingestor: Push.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\WP_CLI;

use WP_CLI;
use WP_CLI\ExitException;

use function Quark\Ingestor\do_push;
use function WP_CLI\Utils\get_flag_value;

/**
 * Push expeditions to the Ingestor.
 */
class Push extends WP_CLI {
	/**
	 * Ingestor push expeditions.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand items
	 *
	 * ## OPTIONS
	 *
	 * [<ids>]
	 * : The ID's to push.
	 *
	 * [--changed-only]
	 * : Only push changed items. Default is false.
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function do_push( array $args = [], array $args_assoc = [] ): void {
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
				'ids'          => [],
				'changed-only' => false,
			]
		);

		// Get changed only flag.
		$push_changed_only = (bool) get_flag_value( $options, 'changed-only', false );

		// Do push.
		do_push( $options['ids'], $push_changed_only );
	}

	/**
	 * Ingestor push all expeditions.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand all
	 *
	 * ## OPTIONS
	 *
	 * [--changed-only]
	 * : Only push changed items. Default is false.
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function all( array $args = [], array $args_assoc = [] ): void {
		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'changed-only' => false,
			]
		);

		// Get changed only flag.
		$push_changed_only = (bool) get_flag_value( $options, 'changed-only', false );

		// Do push.
		do_push( [], $push_changed_only );
	}
}
