<?php
/**
 * Plugin Name: Quark Expedition - Exclusion Sets
 * Description: Exclusion Set Posts for Quark Expedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-exclusion-sets
 */

namespace Quark\ExclusionSets;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
