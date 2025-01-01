<?php
/**
 * WordPress Configuration.
 *
 * @package WordPress
 */

use function Env\env;

/**
 * Composer Autoloader.
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Set cache.
 */
define( 'WP_CACHE', true );
define( 'WP_REDIS_USE_CACHE_GROUPS', true );

/**
 * Use Dotenv to set required environment variables and load .env file in root.
 */
if ( file_exists( __DIR__ . '/.env' ) ) {
	$dotenv = Dotenv\Dotenv::createUnsafeImmutable( __DIR__, [ '.env' ], false );

	$dotenv->load();
	$dotenv->required( [ 'WP_HOME', 'WP_SITEURL' ] );
	if ( ! env( 'DATABASE_URL' ) ) {
		$dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD' ] );
	}
}

/**
 * Set up our global environment constant and load its config first.
 * Default: development
 */
define( 'WP_ENVIRONMENT_TYPE', env( 'WP_ENVIRONMENT_TYPE' ) ? env( 'WP_ENVIRONMENT_TYPE' ) : 'development' );

/**
 * URLs.
 */
if ( isset( $_SERVER['HTTP_HOST'] ) ) {
	define( 'WP_HOME', 'https://' . $_SERVER['HTTP_HOST'] );
	define( 'WP_SITEURL', 'https://' . $_SERVER['HTTP_HOST'] . '/wp' );
} else {
	define( 'WP_HOME', env( 'WP_HOME' ) );
	define( 'WP_SITEURL', env( 'WP_SITEURL' ) );
}

/**
 * Custom Content Directory.
 */
define( 'CONTENT_DIR', '/wp-content' );
define( 'WP_CONTENT_DIR', __DIR__ . CONTENT_DIR );
define( 'WP_CONTENT_URL', WP_HOME . CONTENT_DIR );

/**
 * DB settings.
 */
define( 'DB_NAME', env( 'DB_NAME' ) );
define( 'DB_USER', env( 'DB_USER' ) );
define( 'DB_PASSWORD', env( 'DB_PASSWORD' ) );
define( 'DB_HOST', env( 'DB_HOST' ) ? env( 'DB_HOST' ) : 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
$table_prefix = env( 'DB_PREFIX' ) ?: 'wp_'; // phpcs:ignore

if ( env( 'DATABASE_URL' ) ) {
	$dsn = (object) wp_parse_url( strval( env( 'DATABASE_URL' ) ) );

	define( 'DB_NAME', substr( $dsn->path, 1 ) );
	define( 'DB_USER', $dsn->user );
	define( 'DB_PASSWORD', isset( $dsn->pass ) ? $dsn->pass : null );
	define( 'DB_HOST', isset( $dsn->port ) ? "{$dsn->host}:{$dsn->port}" : $dsn->host );
}

// Migration database.
define( 'QUARK_MIGRATION_DB_USER', env( 'QUARK_MIGRATION_DB_USER' ) );
define( 'QUARK_MIGRATION_DB_PASSWORD', env( 'QUARK_MIGRATION_DB_PASSWORD' ) );
define( 'QUARK_MIGRATION_DB_NAME', env( 'QUARK_MIGRATION_DB_NAME' ) );
define( 'QUARK_MIGRATION_DB_HOST', env( 'QUARK_MIGRATION_DB_HOST' ) );
define( 'QUARK_MIGRATION_MEDIA_PATH', env( 'QUARK_MIGRATION_MEDIA_PATH' ) );

/**
 * Authentication Unique Keys and Salts.
 */
define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
define( 'NONCE_SALT', env( 'NONCE_SALT' ) );

/**
 * Local Settings.
 */
define( 'SAVEQUERIES', true );
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'WP_DEBUG_LOG', true );
define( 'SCRIPT_DEBUG', true );
define( 'DISALLOW_WP_CRON', false );
define( 'WP_MEMORY_LIMIT', '512M' );
ini_set( 'display_errors', '1' ); // phpcs:ignore

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer.
 *
 * @see https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' === $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
	$_SERVER['HTTPS'] = 'on';
}

/**
 * Redis Cache.
 */
$redis_server = array(
	'host' => env( 'CACHE_HOST' ),
	'port' => env( 'CACHE_PORT' ),
);

/**
 * Application Settings.
 */
// ACF.
define( 'ACF_PRO_LICENSE', env( 'ACF_PRO_LICENSE' ) );

// Solr config.
define( 'PANTHEON_INDEX_HOST', env( 'PANTHEON_INDEX_HOST' ) );
define( 'PANTHEON_INDEX_PORT', env( 'PANTHEON_INDEX_PORT' ) );
define( 'SOLR_PATH', env( 'SOLR_PATH' ) );
define( 'SOLRPOWER_DISABLE_AUTOCOMMIT', false );

// S3 Uploads.
define( 'S3_UPLOADS_BUCKET', env( 'S3_UPLOADS_BUCKET' ) );
define( 'S3_UPLOADS_REGION', env( 'S3_UPLOADS_REGION' ) );
define( 'S3_UPLOADS_KEY', env( 'S3_UPLOADS_KEY' ) );
define( 'S3_UPLOADS_SECRET', env( 'S3_UPLOADS_SECRET' ) );
define( 'S3_UPLOADS_HTTP_CACHE_CONTROL', 90 * 24 * 60 * 60 );
define( 'S3_UPLOADS_HTTP_EXPIRES', gmdate( 'D, d M Y H:i:s', time() + ( 10 * 365 * 24 * 60 * 60 ) ) . ' GMT' );
define( 'S3_UPLOADS_OBJECT_ACL', 'private' );

// AWS SES.
define( 'AWS_SES_WP_MAIL_REGION', env( 'AWS_SES_WP_MAIL_REGION' ) );
define( 'AWS_SES_WP_MAIL_KEY', env( 'AWS_SES_WP_MAIL_KEY' ) );
define( 'AWS_SES_WP_MAIL_SECRET', env( 'AWS_SES_WP_MAIL_SECRET' ) );

// reCAPTCHA.
define( 'TRAVELOPIA_RECAPTCHA_SITE_KEY', env( 'TRAVELOPIA_RECAPTCHA_SITE_KEY' ) );
define( 'TRAVELOPIA_RECAPTCHA_SECRET_KEY', env( 'TRAVELOPIA_RECAPTCHA_SECRET_KEY' ) );

// Automated tests.
define( 'QUARK_AUTOMATED_TEST_USER_AGENT', env( 'QUARK_AUTOMATED_TEST_USER_AGENT' ) );

// Salesforce.
define( 'TRAVELOPIA_SALESFORCE_KEY_PATH', env( 'TRAVELOPIA_SALESFORCE_KEY_PATH' ) );
define( 'TRAVELOPIA_SALESFORCE_OAUTH_URL', env( 'TRAVELOPIA_SALESFORCE_OAUTH_URL' ) );
define( 'TRAVELOPIA_SALESFORCE_CONSUMER_KEY', env( 'TRAVELOPIA_SALESFORCE_CONSUMER_KEY' ) );
define( 'TRAVELOPIA_SALESFORCE_USER_NAME', env( 'TRAVELOPIA_SALESFORCE_USER_NAME' ) );
define( 'TRAVELOPIA_SALESFORCE_TOKEN_EXPIRY_SECONDS', env( 'TRAVELOPIA_SALESFORCE_TOKEN_EXPIRY_SECONDS' ) );

// Softrip Middleware.
define( 'QUARK_SOFTRIP_ADAPTER_BASE_URL', env( 'QUARK_SOFTRIP_ADAPTER_BASE_URL' ) );
define( 'QUARK_SOFTRIP_ADAPTER_API_KEY', env( 'QUARK_SOFTRIP_ADAPTER_API_KEY' ) );

// Disable Softrip Middleware sync.
define( 'QUARK_SOFTRIP_SYNC_DISABLE', env( 'QUARK_SOFTRIP_SYNC_DISABLE' ) );

// Checkout.
define( 'QUARK_CHECKOUT_BASE_URL', env( 'QUARK_CHECKOUT_BASE_URL' ) );

// Ingestor.
define( 'QUARK_INGESTOR_BASE_URL', env( 'QUARK_INGESTOR_BASE_URL' ) );
define( 'QUARK_INGESTOR_API_KEY', env( 'QUARK_INGESTOR_API_KEY' ) );

// Disable Ingestor push.
define( 'QUARK_INGESTOR_PUSH_DISABLE', env( 'QUARK_INGESTOR_PUSH_DISABLE' ) );

// Github Actions.
define( 'QUARK_GITHUB_ACTIONS_TOKEN', env( 'QUARK_GITHUB_ACTIONS_TOKEN' ) );
define( 'QUARK_GITHUB_API_DISPATCH_URL', env( 'QUARK_GITHUB_API_DISPATCH_URL' ) );
define( 'QUARK_GITHUB_ACTIONS_REF', env( 'QUARK_GITHUB_ACTIONS_REF' ) );

// China Site blog ID.
define( 'QUARK_CHINA_SITE_BLOG_ID', env( 'QUARK_CHINA_SITE_BLOG_ID' ) );

// DeepL.
define( 'TRAVELOPIA_TRANSLATION_DEEPL_AUTH_KEY', env( 'TRAVELOPIA_TRANSLATION_DEEPL_AUTH_KEY' ) );
/**
 * Multisite.
 */
define( 'WP_ALLOW_MULTISITE', true );
define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE',  env( 'DOMAIN_CURRENT_SITE' ) );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/wp/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
