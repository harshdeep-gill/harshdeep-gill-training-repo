<?php
/**
 * Test Config.
 *
 * @package quark
 */

// phpcs:ignoreFile PSR1.Files.SideEffects.FoundWithSymbols

// Modify the $_SERVER global for testing environment.
$_SERVER['HTTP_HOST'] = 'test.quarkexpeditions.com';

// Define database constants for GH Actions or local testing.
if ( ! empty( getenv( 'GITHUB_ACTIONS' ) ) ) {
	define( 'DB_NAME', getenv( 'DB_NAME' ) );
	define( 'DB_USER', getenv( 'DB_USER' ) );
	define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) );
	define( 'DB_HOST', getenv( 'DB_HOST' ) );
} else {
	define( 'DB_NAME', getenv( 'TESTS_DB_NAME' ) );
	define( 'DB_USER', getenv( 'TESTS_DB_USER' ) );
	define( 'DB_PASSWORD', getenv( 'TESTS_DB_PASSWORD' ) );
	define( 'DB_HOST', getenv( 'TESTS_DB_HOST' ) );
}

// Defined required constants for testing environment.
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_TESTS_DOMAIN', 'localhost' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );
define( 'WP_ENVIRONMENT_TYPE', 'development' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WP_TESTS_MULTISITE', false );
define( 'WP_TESTS', true );

// Define Site URL.
if ( ! defined( 'WP_SITEURL' ) ) {
	define( 'WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] );
}

// Define Home URL.
if ( ! defined( 'WP_HOME' ) ) {
	define( 'WP_HOME', 'http://' . $_SERVER['HTTP_HOST'] );
}

// Define Path & URL for Content.
define( 'WP_CONTENT_DIR', dirname( __DIR__ ) . '/../wp-content' );
define( 'WP_CONTENT_URL', WP_HOME . '/wp-content' );

// Create "uploads" needed for tests.
if ( ! is_dir( WP_CONTENT_DIR . '/uploads' ) ) {
	mkdir( WP_CONTENT_DIR . '/uploads' );
}

// Somehow WP is not creating directories recursively.
// Add Month and Year directories.
if ( ! is_dir( WP_CONTENT_DIR . '/uploads/' . gmdate( 'Y' ) ) ) {
	mkdir( WP_CONTENT_DIR . '/uploads/' . gmdate( 'Y' ) );
}

// Add Month directory.
if ( ! is_dir( WP_CONTENT_DIR . '/uploads/' . gmdate( 'Y' ) . '/' . gmdate( 'm' ) ) ) {
	mkdir( WP_CONTENT_DIR . '/uploads/' . gmdate( 'Y' ) . '/' . gmdate( 'm' ) );
}

// Prevent editing of files through WP Admin.
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

// Absolute path to the WordPress directory.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/../wp/' );
}

// Softrip API credentials.
define( 'QUARK_SOFTRIP_ADAPTER_BASE_URL', 'https://softrip-adapter.dev' );
define( 'QUARK_SOFTRIP_ADAPTER_USERNAME', 'test' );
define( 'QUARK_SOFTRIP_ADAPTER_PASSWORD', 'test' );
