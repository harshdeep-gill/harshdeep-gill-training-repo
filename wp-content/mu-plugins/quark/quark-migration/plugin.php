<?php
/**
 * Plugin Name: Quark Migration from Drupal.
 * Description: Migrate data from Drupal into WordPress.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-migration
 */

namespace Quark\Migration;

require_once __DIR__ . '/inc/autoload.php';
require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/drupal/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
