<?php
/**
 * Namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search;

use SolrPower_Sync;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Filers.
	add_filter( 'solr_scheme', __NAMESPACE__ . '\\solr_scheme' );
	add_filter( 'solr_post_types', __NAMESPACE__ . '\\solr_post_types' );
}

/**
 * Set Solr scheme based on environment.
 *
 * @param string $scheme Scheme.
 *
 * @return string
 */
function solr_scheme( string $scheme = '' ): string {
	// Use http if on local development environment.
	if ( 'local' === wp_get_environment_type() ) {
		return 'http';
	}

	// Return scheme.
	return $scheme;
}

/**
 * Post types to index.
 *
 * @return string[] Post types.
 */
function solr_post_types(): array {
	// Return post types.
	return [
		DEPARTURE_POST_TYPE => DEPARTURE_POST_TYPE,
	];
}

/**
 * Update a post manually in search index.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function update_post_in_index( int $post_id = 0 ): void {
	// Bail if SolrPower_Sync class doesn't exist.
	if ( ! class_exists( 'SolrPower_Sync' ) ) {
		return;
	}

	// Update post in index.
	$sync = SolrPower_Sync::get_instance();
	$sync->handle_modified( $post_id );
}
