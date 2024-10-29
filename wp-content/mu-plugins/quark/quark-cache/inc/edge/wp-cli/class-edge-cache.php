<?php
/**
 * Edge cache CLI.
 *
 * @package quark-cache
 */

namespace Quark\Cache\Edge\WP_CLI;

use WP_CLI;

use function Quark\Cache\Edge\flush_and_warm_edge_cache;
use function WP_CLI\Utils\get_flag_value;

/**
 * Edge Cache CLI commands.
 */
class Edge_Cache {
	/**
	 * Flush Edge cache and warm it up.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand flush-and-warm
	 *
	 * ## OPTIONS
	 *
	 * [--pricing-pages-only]
	 * : Only flush and warm pages with pricing blocks. Default is false.
	 *
	 * @synopsis [--pricing-pages-only]
	 *
	 * Example: wp quark-cache edge flush-and-warm
	 * @return void
	 */
	public function flush_and_warm( array $args = [], array $args_assoc = [] ): void {
		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'pricing-pages-only' => false,
			]
		);

		// Get pricing pages only flag.
		$pricing_pages_only = (bool) get_flag_value( $options, 'pricing-pages-only', false );

		// Prepare welcome message.
		if ( $pricing_pages_only ) {
			WP_CLI::log( WP_CLI::colorize( '%YFlushing and warming edge cache for pricing pages only...%n' ) );
		} else {
			WP_CLI::log( WP_CLI::colorize( '%YFlushing and warming edge cache for all pages...%n' ) );
		}

		// Flush and warm edge cache.
		flush_and_warm_edge_cache( $pricing_pages_only );

		// Success message.
		WP_CLI::success( 'Edge cache flushed and warmed.' );
	}
}
