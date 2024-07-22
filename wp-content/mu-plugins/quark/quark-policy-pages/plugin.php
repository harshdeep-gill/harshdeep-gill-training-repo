<?php
/**
 * Plugin Name: Quark World Travel Policy Pages
 * Description: Policy Pages feature for Quark World Travel.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-policy-pages
 */

namespace Quark\PolicyPages;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
