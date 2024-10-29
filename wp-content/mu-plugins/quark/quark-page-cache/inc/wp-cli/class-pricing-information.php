<?php
/**
 * CLI: pricing information.
 *
 * @package quark-page-cache
 */

namespace Quark\PageCache\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_CLI\ExitException;
use WP_Query;

use function WP_CLI\Utils\make_progress_bar;
use function Quark\PageCache\set_meta_for_pricing_block_posts;

use const Quark\PageCache\CACHED_POST_TYPE_SLUGS;

/**
 * Class Pricing_Information.
 */
class Pricing_Information {
	/**
	 * Set pricing block meta.
	 *
	 * @subcommand set-meta
	 *
	 * Example: wp quark-cache pricing-information set-meta
	 * @return void
	 *
	 * @throws ExitException Exception on error.
	 */
	public function set_pricing_block_meta(): void {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YStarting pricing block search...%n' ) );

		// Prepare query args.
		$args = [
			'no_found_rows'          => true,
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		];

		// count - Total count.
		$count = 0;

		// found - Found count.
		$found = 0;

		// Loop through post types.
		foreach ( CACHED_POST_TYPE_SLUGS as $post_type ) {
			$args['post_type'] = $post_type;

			// Run query.
			$query    = new WP_Query( $args );
			$post_ids = $query->posts;

			// Convert to integers.
			$post_ids = array_map( 'absint', $post_ids );

			// Bail if no posts.
			if ( empty( $post_ids ) ) {
				continue;
			}

			// Progress bar.
			$total_posts = count( $post_ids );
			$progress    = make_progress_bar( "Processing $post_type", $total_posts );
			WP_CLI::log( WP_CLI::colorize( "%GFound $post_type posts: %n" . $total_posts ) );

			// Bail out if progress bar is not found.
			if ( ! $progress instanceof Bar ) {
				WP_CLI::error( 'Progress bar not found!' );

				// Bail out.
				return;
			}

			// Get permalink.
			foreach ( $post_ids as $post_id ) {
				// Set progress tick.
				$progress->tick();
				++$count;

				// update_post_meta if pricing block is found.
				if ( set_meta_for_pricing_block_posts( $post_id ) ) {
					++$found;
				}
			}

			// All done!
			$progress->finish();
		}

		// Success message.
		WP_CLI::success( "Added meta for $found posts out of $count." );
	}
}
