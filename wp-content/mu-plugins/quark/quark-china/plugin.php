<?php
/**
 * Plugin Name: Quark China.
 * Description:  Quark China site functionality.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-china
 */

namespace Quark\China;

use function Quark\Core\is_china_website;

// Load the plugin only if the current site is China site.
if ( ! is_china_website() ) {
	return;
}

// Load the plugin files.
require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'muplugins_loaded', __NAMESPACE__ . '\\bootstrap' );
