<?php
/**
 * Migrate: Blog Posts.
 *
 * @package quark-migration
 */

namespace Quark\PageCache\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_CLI\ExitException;
use WP_Query;

use function WP_CLI\Utils\make_progress_bar;

use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTIONS_POST_TYPE;
use const Quark\Blog\POST_TYPE as BLOG_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITIONS_POST_TYPE;
use const Quark\LandingPages\POST_TYPE as LANDING_PAGES_POST_TYPE;
use const Quark\Offers\POST_TYPE as OFFERS_POST_TYPE;
use const Quark\Pages\POST_TYPE as PAGE_POST_TYPE;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGES_POST_TYPE;
use const Quark\PressReleases\POST_TYPE as PRESS_RELEASES_POST_TYPE;
use const Quark\Regions\POST_TYPE as REGIONS_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIPS_POST_TYPE;
use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBERS_POST_TYPE;

const CACHED_POST_TYPE_SLUGS = [
	PAGE_POST_TYPE,
	EXPEDITIONS_POST_TYPE,
	SHIPS_POST_TYPE,
	OFFERS_POST_TYPE,
	REGIONS_POST_TYPE,
	LANDING_PAGES_POST_TYPE,
	ADVENTURE_OPTIONS_POST_TYPE,
	POLICY_PAGES_POST_TYPE,
	BLOG_POST_TYPE,
	PRESS_RELEASES_POST_TYPE,
	STAFF_MEMBERS_POST_TYPE,
];

/**
 * Class Pricing.
 */
class Pricing {
	/**
	 * Set pricing block meta.
	 *
	 * @subcommand set-meta
	 *
	 * Example: wp quark-cache pricing set-meta
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

		// $count Total count.
		// $found Found count.
		$count = 0;
		$found = 0;

		// Pricing blocks list.
		$pricing_blocks = [
			'quark/book-departures-expeditions',
			'quark/book-departures-ships',
			'quark/dates-and-rates',
			'quark/detailed-expeditions-carousel',
			'quark/expedition-details',
			'quark/expedition-search',
			'quark/expeditions',
			'quark/itineraries',
			'quark/product-cards',
			'quark/product-departures-card',
		];

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
				// get post content.
				$content = get_post_field( 'post_content', $post_id );

				// Set progress tick.
				$progress->tick();
				++$count;

				// Parse blocks.
				$blocks = parse_blocks( $content );

				// Skip if we don't have any blocks.
				if ( empty( $blocks ) ) {
					continue;
				}

				// Flatten blocks.
				$blocks            = _flatten_blocks( $blocks );
				$has_pricing_block = false;

				// Loop through blocks to find the pricing blocks.
				foreach ( $blocks as $block ) {
					// Skip if we don't have a block name.
					if ( empty( $block['blockName'] ) ) {
						continue;
					}

					// Skip if block name is not in the pricing blocks.
					if ( in_array( $block['blockName'], $pricing_blocks, true ) ) {
						// Set flag to true.
						$has_pricing_block = true;

						// Update found count.
						++$found;

						// break the loop.
						break;
					}
				}

				// Update post meta if pricing block found.
				if ( true === $has_pricing_block ) {
					// Set pricing block meta.
					update_post_meta( $post_id, '_has_quark_pricing_block', true );
				} else {
					delete_post_meta( $post_id, '_has_quark_pricing_block' );
				}
			}

			// All done!
			$progress->finish();

			// Let sleep for a sec.
			sleep( 1 );
		}

		// Success message.
		WP_CLI::success( "Added meta for $found posts out of $count." );
	}
}
