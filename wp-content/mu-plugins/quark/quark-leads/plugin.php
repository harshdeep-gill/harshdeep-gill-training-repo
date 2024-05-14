<?php
/**
 * Plugin Name: Quark Landing Pages
 * Description: Landing Pages features for Quark.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-leads
 */

namespace Quark\Leads;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/forms/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
