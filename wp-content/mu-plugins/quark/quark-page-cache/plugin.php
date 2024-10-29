<?php
/**
 * Plugin Name: Quark Page Cache
 * Description: Page Cache feature for Quark World Travel.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-page-cache
 */

namespace Quark\PageCache;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/wp-cli/class-pricing-information.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
