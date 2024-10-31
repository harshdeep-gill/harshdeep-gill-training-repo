<?php
/**
 * Namespaces functions.
 *
 * @package quark-cache
 */

namespace Quark\Cache\Edge;

use cli\progress\Bar;
use WP_CLI;
use WP_Query;

use function Quark\Core\doing_tests;
use function Quark\Softrip\get_initiated_via;
use function Travelopia\Cache\clear_edge_cache_paths;

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

// Post types to cache.
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
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register Stream log connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );

	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-cache edge', __NAMESPACE__ . '\\WP_CLI\\Edge_Cache' );
	}

	// Flush page cache on sync completion.
	add_action( 'quark_softrip_sync_completed', __NAMESPACE__ . '\\flush_page_cache', 9999 );
}

/**
 * Flush page cache and warm it up.
 *
 * @param bool $pricing_pages_only Invalidate pricing pages only. Default is false.
 *
 * @return void
 */
function flush_and_warm_edge_cache( bool $pricing_pages_only = true ): void {
	// Start time.
	$start_time = microtime( true );

	// Check if in CLI.
	$is_in_cli = defined( 'WP_CLI' ) && true === WP_CLI;
	$progress  = null;

	// Prepare query args.
	$args = [
		'no_found_rows'          => true,
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'ignore_sticky_posts'    => true,
	];

	// Get environment.
	$environment = wp_get_environment_type();

	// Log message in CLI.
	if ( $is_in_cli ) {
		// Welcome message.
		if ( $pricing_pages_only ) {
			WP_CLI::log( WP_CLI::colorize( '%YFlushing and warming edge cache for pricing pages only...%n' ) );
		} else {
			WP_CLI::log( WP_CLI::colorize( '%YFlushing and warming edge cache for all pages...%n' ) );
		}

		// New line.
		WP_CLI::log( '' );
	}

	// Loop through post types.
	foreach ( CACHED_POST_TYPE_SLUGS as $post_type ) {
		$args['post_type'] = $post_type;

		// Add meta query if invalidating for pricing posts only.
		if ( true === $pricing_pages_only ) {
			$args['meta_query'] = [
				[
					'key'   => '_has_a_block_with_pricing_information',
					'value' => '1',
				],
			];
		}

		// Run query.
		$query    = new WP_Query( $args );
		$post_ids = $query->posts;

		// Bail if no posts.
		if ( empty( $post_ids ) ) {
			// Log action.
			if ( $is_in_cli ) {
				WP_CLI::log( WP_CLI::colorize( '%YNo ' . $post_type . ' posts found.%n' ) );
			}

			// Continue to next post type.
			continue;
		}

		// Convert to integers.
		$post_ids = array_map( 'absint', $post_ids );

		// Skip if doing tests.
		if ( doing_tests() || 'local' === $environment ) {
			continue;
		}

		// Total posts.
		$total_posts     = count( $post_ids );
		$counter         = 0;
		$post_start_time = microtime( true );

		// Initialize progress bar.
		if ( $is_in_cli ) {
			WP_CLI::log( WP_CLI::colorize( '%GFound ' . $total_posts . ' ' . $post_type . ' posts.%n' ) );
			$progress = new Bar( "Processing $post_type", $total_posts );
		}

		// Get permalink.
		foreach ( $post_ids as $post_id ) {
			$permalink = get_permalink( $post_id );

			// Bail if no permalink.
			if ( ! $permalink ) {
				// Update progress bar.
				if ( $progress ) {
					$progress->tick();
				}

				// Continue to next post.
				continue;
			}

			// Clear cache.
			$paths = wp_parse_url( $permalink );

			// Validate $paths.
			if ( ! is_array( $paths ) || ! isset( $paths['path'] ) ) {
				// Update progress bar.
				if ( $progress ) {
					$progress->tick();
				}

				// Continue to next post.
				continue;
			}

			// edge cache.
			clear_edge_cache_paths( [ $paths['path'] ] );

			// delay for 200 milliseconds.
			usleep( 200000 );

			// Make request to warm cache.
			wp_remote_get(
				$permalink,
				[
					'timeout' => '0.5',
				]
			);

			// Update counter.
			++$counter;

			// Update progress bar.
			if ( $progress ) {
				$progress->tick();
			}
		}

		// End time.
		$post_end_time = microtime( true );

		// Finish progress bar.
		if ( $progress ) {
			$progress->finish();
		}

		// Log action.
		if ( $is_in_cli ) {
			WP_CLI::log( WP_CLI::colorize( '%GProcessed ' . $counter . ' out of ' . $total_posts . ' ' . $post_type . ' posts.%n' ) );
			WP_CLI::log( WP_CLI::colorize( '%GTime taken: ' . ( $post_end_time - $post_start_time ) . ' seconds.%n' ) );
		}
	}

	// End time.
	$end_time       = microtime( true );
	$execution_time = $end_time - $start_time;

	// Log action.
	do_action(
		'quark_edge_cache_flushed',
		[
			'time_taken'         => $execution_time,
			'pricing_pages_only' => $pricing_pages_only,
		]
	);

	// Log action.
	if ( $is_in_cli ) {
		// Success message.
		WP_CLI::log( '' );
		WP_CLI::success( 'Edge cache flushed and warmed.' );
		WP_CLI::log( WP_CLI::colorize( '%GTotal time taken: ' . $execution_time . ' seconds.%n' ) );
		WP_CLI::log( '' );
	}
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
	$connectors['quark_edge_cache'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
}

/**
 * Flush edge page cache.
 *
 * @param mixed[] $data Data args.
 *
 * @return void
 */
function flush_page_cache( array $data = [] ): void {
	// Validate data.
	if ( empty( $data['success'] ) ) {
		// Bail out.
		return;
	}

	// Get initiated via.
	$initiated_via = get_initiated_via();

	// Bail if initiated manually.
	if ( 'manually' === $initiated_via ) {
		// @todo Implement a GitHub action to flush cache - https://tuispecialist.atlassian.net/browse/QE-1035.
		return;
	}

	// Flush cache and warm up.
	flush_and_warm_edge_cache();
}
