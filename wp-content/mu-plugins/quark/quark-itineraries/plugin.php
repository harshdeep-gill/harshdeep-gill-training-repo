<?php
/**
 * Plugin Name: Quark Expedition - Itineraries
 * Description: Itinerary Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-itineraries
 */

namespace Quark\Itineraries;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
