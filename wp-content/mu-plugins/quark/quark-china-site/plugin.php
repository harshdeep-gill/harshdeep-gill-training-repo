<?php
/**
 * Plugin Name: Quark China site.
 * Description:  Quark China site functionality.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-china-site
 */

namespace Quark\ChinaSite;

// Load the plugin only if the China site blog ID constant is set to the current blog id.
if ( ! defined( 'QUARK_CHINA_SITE_BLOG_ID' ) || QUARK_CHINA_SITE_BLOG_ID !== get_current_blog_id() ) {
	return;
}

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
