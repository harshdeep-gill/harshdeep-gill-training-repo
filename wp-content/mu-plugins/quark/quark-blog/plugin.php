<?php
/**
 * Plugin Name: Quark Expedition Blog
 * Description: Blog Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-blog
 */

namespace Quark\Blog;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/authors/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Authors\\bootstrap' );
