<?php
/**
 * Plugin Name: Quark Expeditions Brochures.
 * Description: Brochures for Quark Expeditions.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-brochures
 */

namespace Quark\Brochures;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
