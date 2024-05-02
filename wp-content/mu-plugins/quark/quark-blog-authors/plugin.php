<?php
/**
 * Plugin Name: Quark Expeditions Blog Authors.
 * Description: Blog Authors post type for Quark Expeditions.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-blog-authors
 */

namespace Quark\BlogAuthors;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
