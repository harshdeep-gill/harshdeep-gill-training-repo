<?php
/**
 * Plugin Name: Quark Expeditions Press Releases
 * Description: Press Releases for Quark Expeditions.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-press-releases
 */

namespace Quark\PressReleases;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
