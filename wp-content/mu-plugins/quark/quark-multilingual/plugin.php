<?php
/**
 * Plugin Name: Quarkexpeditions Multilingual
 * Description: Multilingual feature for Quarkexpeditions.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
