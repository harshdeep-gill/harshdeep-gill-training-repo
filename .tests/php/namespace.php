<?php
/**
 * Tests bootstrap.
 *
 * @package quark
 */

namespace Quark\Tests;

use function Quark\Tests\Ingestor\setup_ingestor_integration;
use function Quark\Tests\Softrip\drop_softrip_db_tables;
use function Quark\Tests\Softrip\setup_softrip_integration;

/**
 * Bootstrap the tests.
 *
 * @return void
 */
function bootstrap(): void {
	// Load environment.
	tests_add_filter( 'muplugins_loaded', __NAMESPACE__ . '\\load_environment' );

	// Destroy environment.
	tests_add_filter( 'shutdown', __NAMESPACE__ . '\\destroy_environment' );
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
		'wordpress-seo/wp-seo.php',
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

	// Setup Softrip database tables.
	setup_softrip_integration();

	// Setup DataEngine/Ingestor.
	setup_ingestor_integration();
}

/**
 * Destroy test environment.
 *
 * @return void
 */
function destroy_environment(): void {
	// Drop Softrip database tables.
	drop_softrip_db_tables();
}
