<?php
/**
 * Plugin Name: Quark Softrip API.
 * Description: API Class for retrieving data from Softrip.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

require_once __DIR__ . '/inc/autoload.php';
require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
