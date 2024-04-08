<?php
/**
 * Plugin Name: Quark World Travel Press Releases
 * Description: Press Releases for Quark World Travel.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-press-release
 */

namespace Quark\PressRelease;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
