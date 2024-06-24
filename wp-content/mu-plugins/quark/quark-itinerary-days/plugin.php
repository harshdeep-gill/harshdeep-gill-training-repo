<?php
/**
 * Plugin Name: Quark Expedition Itinerary Days
 * Description: Itinerary Days Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-itinerary-days
 */

namespace Quark\ItineraryDays;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
