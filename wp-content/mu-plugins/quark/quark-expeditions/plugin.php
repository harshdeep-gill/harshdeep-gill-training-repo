<?php
/**
 * Plugin Name: Quark Expedition - Expedition Posts
 * Description: Expedition Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-expeditions
 */

namespace Quark\Expeditions;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/pre-post-trip-options/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\PrePostTripOptions\\bootstrap' );
