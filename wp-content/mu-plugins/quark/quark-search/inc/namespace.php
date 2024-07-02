<?php
/**
 * Namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search;

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Filers.
	add_filter( 'solr_scheme', __NAMESPACE__ . '\\solr_scheme' );
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
