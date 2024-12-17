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

// Skip sync class init if SOFTRIP PUSH disabled.
if ( ! defined( 'QUARK_SOFTRIP_SYNC_DISABLE' ) || true !== QUARK_SOFTRIP_SYNC_DISABLE ) {
	// Kick it off.
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\ManualSync\\bootstrap' );
}

// Kick off other classes.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Cleanup\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Admin\\bootstrap' );
