<?php
/**
 * Plugin Name: Quark World Travel Ship Decks
 * Description: Ship Decks feature for Quark World Travel.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-ship-decks
 */

namespace Quark\ShipDecks;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
