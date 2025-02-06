<?php
/**
 * Plugin Name: Quarkexpeditions Multilingual
 * Description: Multilingual feature for Quarkexpeditions.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/wp-cli/class-site-clone.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

/**
 * Filter MultilingualPress modules.
 *
 * This plugin is a must-use plugin, but it still loads after the MultilingualPress plugin.
 * The MultilingualPress plugin is loaded on the `plugins_loaded` hook at priority 0, while this plugin is loaded at
 * priority 10. This means that the `multilingualpress.modules` filter is fired before this plugin is bootstrapped. In
 * order to make sure we are filtering the modules at the right time, we need to hook it here.
 */
add_filter( 'multilingualpress.modules', __NAMESPACE__ . '\\multilingualpress_modules' );
