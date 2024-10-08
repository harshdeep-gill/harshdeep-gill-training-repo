<?php
/**
 * Namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search;

use SolrPower_Sync;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

const REST_API_NAMESPACE = 'quark-search/v1';

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Filers.
	add_filter( 'solr_scheme', __NAMESPACE__ . '\\solr_scheme' );
	add_filter( 'solr_post_types', __NAMESPACE__ . '\\solr_post_types' );

	// REST API.
	add_action( 'rest_api_init', __NAMESPACE__ . '\\register_rest_endpoints' );
	add_filter( 'travelopia_security_public_rest_api_routes', __NAMESPACE__ . '\\public_rest_api_routes' );

	// Frontend data.
	add_action( 'quark_front_end_data', __NAMESPACE__ . '\\front_end_data' );
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

/**
 * Register REST API endpoints.
 *
 * @return void
 */
function register_rest_endpoints(): void {
	// Require REST API classes.
	require_once __DIR__ . '/rest-api/class-destination-month-filters.php';

	// REST API endpoints.
	$endpoints = [
		new REST_API\Destination_Month_Filters(),
	];

	// Register routes.
	foreach ( $endpoints as $endpoint ) {
		$endpoint->register_routes();
	}
}

/**
 * Register public REST API routes.
 *
 * @param string[] $routes Public routes.
 *
 * @return string[]
 */
function public_rest_api_routes( array $routes = [] ): array {
	// Add REST API routes.
	$routes[] = '/' . REST_API_NAMESPACE . '/filter-options/by-destination-and-month';

	// Return routes.
	return $routes;
}

/**
 * Front-end data.
 *
 * @param mixed[] $data Front-end data.
 *
 * @return mixed[]
 */
function front_end_data( array $data = [] ): array {
	// Default currency.
	$data['filters_api_url'] = home_url( 'wp-json/' . REST_API_NAMESPACE . '/filter-options/by-destination-and-month' );

	// Return data.
	return $data;
}
