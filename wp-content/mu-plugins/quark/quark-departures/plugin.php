<?php
/**
 * Plugin Name: Quark Expedition Departures
 * Description: Departure Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-departures
 */

namespace Quark\Departures;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
