<?php
/**
 * Plugin Name: Quark Search
 * Description: Search feature for QuarkExpedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-search
 */

namespace Quark\Search;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/departures/namespace.php';
require_once __DIR__ . '/inc/departures/class-search.php';
require_once __DIR__ . '/inc/filters/namespace.php';
require_once __DIR__ . '/inc/filters/legacy/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Departures\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Filters\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Filters\\Legacy\\bootstrap' );
