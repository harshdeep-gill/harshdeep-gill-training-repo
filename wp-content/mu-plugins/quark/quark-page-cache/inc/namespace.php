<?php
/**
 * Namespaces functions.
 *
 * @package quark-page-cache
 */

namespace Quark\PageCache;

use WP_Query;

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
}

/**
 * Flush page cache.
 *
 * @return void
 */
function flush_page_cache(): void {
    // Start time.
    $start_time = microtime( true );

    // Prepare query args.
    $args = [
        'no_found_rows' => true,
        'posts_per_page' => -1,
        'fields' => 'ids',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ];

    // Loop through post types.
    foreach ( CACHED_POST_TYPE_SLUGS as $post_type ) {
        $args['post_type'] = $post_type;

        // Run query.
        $query = new WP_Query( $args );
        $post_ids = $query->posts;

        // Bail if no posts.
        if ( empty( $post_ids ) ) {
            continue;
        }

        // Convert to integers.
        $post_ids = array_map( 'absint', $post_ids );

        // Get permalink.
        foreach ( $post_ids as $post_id ) {
            $permalink = get_permalink( $post_id );

            // Bail if no permalink.
            if ( ! $permalink ) {
                continue;
            }

            // Clear cache.
            clear_edge_paths_by_post_id( $post_id );

            // // Make request to warm cache.
            wp_remote_get( $permalink, [
                'blocking' => false,
            ] );
        }
    }

    $end_time = microtime( true );
    $execution_time = $end_time - $start_time;

    // Log action.
    do_action( 'quark_page_cache_flushed', [
        'time_took' => $execution_time,
    ] );
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
