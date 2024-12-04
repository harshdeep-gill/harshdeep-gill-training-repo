<?php
/**
 * Plugin Name: Quark Softrip.
 * Description: Pulls data from Softrip API and stores it in the WP database.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

require_once __DIR__ . '/inc/autoload.php';
require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/itineraries/namespace.php';
require_once __DIR__ . '/inc/departures/namespace.php';
require_once __DIR__ . '/inc/adventure-options/namespace.php';
require_once __DIR__ . '/inc/promotions/namespace.php';
require_once __DIR__ . '/inc/occupancies/namespace.php';
require_once __DIR__ . '/inc/occupancy-promotions/namespace.php';
require_once __DIR__ . '/inc/manual-sync/namespace.php';
require_once __DIR__ . '/inc/cleanup/namespace.php';
require_once __DIR__ . '/inc/admin/namespace.php';

// Register Autoloader.
spl_autoload_register( __NAMESPACE__ . '\\autoload' );

// Load the plugin only if the constant is not set to the china blog id.
if ( defined( 'QUARK_CHINA_SITE_BLOG_ID' ) && QUARK_CHINA_SITE_BLOG_ID === get_current_blog_id() ) {
	return;
}

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\ManualSync\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Cleanup\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Admin\\bootstrap' );
