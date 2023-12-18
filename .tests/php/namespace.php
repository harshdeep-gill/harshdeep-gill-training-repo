<?php
/**
 * Tests bootstrap.
 *
 * @package quark
 */

namespace Quark\Tests;

/**
 * Bootstrap the tests.
 *
 * @return void
 */
function bootstrap(): void {
	// Load environment.
	tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\\load_environment' );
}

/**
 * Load test environment.
 *
 * @return void
 */
function load_environment(): void {
	// Create "uploads" needed for tests.
	if ( ! is_dir( WP_CONTENT_DIR . '/uploads' ) ) {
		mkdir( WP_CONTENT_DIR . '/uploads' ); // phpcs:ignore
	}

	// Table prefix for test database.
	global $table_prefix;
	$table_prefix = 'wptests_'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	// Options.
	update_option( 'permalink_structure', '/%postname%' );
	update_option( 'blogname', 'Quark' );
	update_option( 'admin_email', 'admin@test.quarkexpeditions.com' );

	// Activate plugins.
	$plugins_to_activate = [
	];

	// Update active plugins.
	update_option( 'active_plugins', $plugins_to_activate );

	// Switch theme.
	add_filter(
		'stylesheet',
		function () {
			return 'quark';
		}
	);
}
