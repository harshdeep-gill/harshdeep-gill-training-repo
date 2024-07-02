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

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
