<?php
/**
 * Plugin Name: Quark Expedition Adventure Options
 * Description: Adventure Option Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-adventure-options
 */

namespace Quark\AdventureOptions;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
