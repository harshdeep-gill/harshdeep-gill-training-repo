<?php
/**
 * Plugin Name: Quark Softrip API.
 * Description: API Class for retrieving data from Softrip.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

require_once __DIR__ . '/inc/autoload.php';
require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/departure/namespace.php';
require_once __DIR__ . '/inc/adventure-options/namespace.php';
require_once __DIR__ . '/inc/promotions/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

// Register Autoloader.
spl_autoload_register( __NAMESPACE__ . '\\autoload' );
