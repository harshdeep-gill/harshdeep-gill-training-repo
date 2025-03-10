<?php
/**
 * Plugin Name: QuarkExpedition Phone Numbers
 * Description: Phone Numbers feature for QuarkExpedition.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-office-phone-numbers
 */

namespace Quark\OfficePhoneNumbers;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
