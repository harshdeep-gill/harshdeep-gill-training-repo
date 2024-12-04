<?php
/**
 * Plugin Name: Quark Expedition Departures
 * Description: Departure Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-departures
 */

namespace Quark\Departures;

require_once __DIR__ . '/inc/namespace.php';

// Load the plugin only if the constant is not set to the china blog id.
if ( defined( 'QUARK_CHINA_SITE_BLOG_ID' ) && QUARK_CHINA_SITE_BLOG_ID === get_current_blog_id() ) {
	return;
}

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
