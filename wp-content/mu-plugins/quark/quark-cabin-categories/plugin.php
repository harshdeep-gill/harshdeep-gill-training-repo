<?php
/**
 * Plugin Name: Quark Expedition Cabin Categories
 * Description: Cabin Category Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-cabin-categories
 */

namespace Quark\CabinCategories;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
