<?php
/**
 * Plugin Name: Quark Checkout.
 * Description:  Quark checkout functionality.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-checkout
 */

namespace Quark\Checkout;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
