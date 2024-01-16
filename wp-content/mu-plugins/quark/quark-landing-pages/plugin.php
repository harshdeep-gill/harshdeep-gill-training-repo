<?php
/**
 * Plugin Name: Quark Landing Pages
 * Description: Landing Pages features for Quark.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-landing-pages
 */

namespace Quark\LandingPages;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
