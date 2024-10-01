<?php
/**
 * Bootstrap for unit tests.
 *
 * @package quark
 */

namespace Quark\Tests;

use Dotenv;

use function Env\env;

// Composer Autoloader.
require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Use Dotenv to set required environment variables and load .env file in root.
 */
// Load secret environment variables from .env file.
if ( file_exists( __DIR__ . '/../../.env' ) ) {
	$dotenv = Dotenv\Dotenv::createUnsafeImmutable( __DIR__ . '/../../', [ '.env' ], false );
	$dotenv->load();
	$dotenv->required( [ 'WP_HOME', 'WP_SITEURL' ] );

	// Check if tests related environment variables are set and load them.
	if ( ! env( 'TESTS_DATABASE_URL' ) ) {
		$dotenv->required( [ 'TESTS_DB_NAME', 'TESTS_DB_USER', 'TESTS_DB_PASSWORD' ] );
	}
}

// Load PHPUnit functions.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

// Require the various namespace files.
require_once __DIR__ . '/namespace.php';
require_once __DIR__ . '/softrip/namespace.php';
require_once __DIR__ . '/ingestor/namespace.php';

// Bootstrap the tests.
bootstrap();

// Bootstrap PHPUnit tests.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';

// Require the base test case class.
require_once __DIR__ . '/softrip/class-softrip-testcase.php';
