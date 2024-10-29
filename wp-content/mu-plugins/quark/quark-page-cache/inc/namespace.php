<?php
/**
 * Namespaces functions.
 *
 * @package quark-page-cache
 */

namespace Quark\PageCache;

use WP_CLI;
use WP_Post;
use WP_Query;

use function Quark\Core\doing_tests;
use function Travelopia\Cache\clear_edge_paths_by_post_id;

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

// Pricing blocks.
const PRICING_BLOCKS = [
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
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-cache pricing-information', __NAMESPACE__ . '\\WP_CLI\\Pricing_Information' );
	}

	// Register Stream log connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );

	// Set meta for pricing block page.
	add_action( 'save_post', __NAMESPACE__ . '\\set_meta_for_pricing_block_posts' );
}

/**
 * Flush page cache and warm it up.
 *
 * @param bool $invalidate_pricing_posts Invalidate all posts edge cache.
 *
 * @return void
 */
function flush_and_warm_up_page_cache( bool $invalidate_pricing_posts = true ): void {
	// Start time.
	$start_time = microtime( true );

	// Prepare query args.
	$args = [
		'no_found_rows'          => true,
		'posts_per_page'         => -1,
		'fields'                 => 'ids',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	];

	// Loop through post types.
	foreach ( CACHED_POST_TYPE_SLUGS as $post_type ) {
		$args['post_type'] = $post_type;

		// Add meta query if invalidating for pricing posts only.
		if ( true === $invalidate_pricing_posts ) {
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
			continue;
		}

		// Convert to integers.
		$post_ids = array_map( 'absint', $post_ids );

		// Skip if doing tests.
		if ( doing_tests() ) {
			continue;
		}

		// Get permalink.
		foreach ( $post_ids as $post_id ) {
			$permalink = get_permalink( $post_id );

			// Bail if no permalink.
			if ( ! $permalink ) {
				continue;
			}

			// Clear cache.
			clear_edge_paths_by_post_id( $post_id );

			// Make request to warm cache.
			wp_remote_get(
				$permalink,
				[
					'blocking' => false,
				]
			);
		}
	}

	// End time.
	$end_time       = microtime( true );
	$execution_time = $end_time - $start_time;

	// Log action.
	do_action(
		'quark_page_cache_flushed',
		[
			'time_taken' => $execution_time,
		]
	);
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
	$connectors['quark_page_cache'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
}

/**
 * Set meta for pricing block page.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if meta set, false otherwise.
 */
function set_meta_for_pricing_block_posts( int $post_id = 0 ): bool {
	// Bail if saving a revision OR Bail if doing autosave.
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return false;
	}

	// Get post ID.
	if ( 0 === $post_id ) {
		$post_id = absint( get_the_ID() );
	}

	// Get post.
	$post = get_post( $post_id );

	// Bail if no post.
	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, CACHED_POST_TYPE_SLUGS, true ) ) {
		return false;
	}

	// get post content.
	$content = get_post_field( 'post_content', $post_id );

	// Parse blocks.
	$blocks = parse_blocks( $content );

	// Skip if we don't have any blocks.
	if ( empty( $blocks ) ) {
		return false;
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
		if ( in_array( $block['blockName'], PRICING_BLOCKS, true ) ) {
			// Set flag to true.
			$has_pricing_block = true;

			// break the loop.
			break;
		}
	}

	// Update post meta if pricing block found.
	if ( true === $has_pricing_block ) {
		// Set pricing block meta.
		update_post_meta( $post_id, '_has_a_block_with_pricing_information', true );

		// Return true if pricing block found.
		return true;
	} else {
		delete_post_meta( $post_id, '_has_a_block_with_pricing_information' );
	}

	// Return false if no pricing block found.
	return false;
}
