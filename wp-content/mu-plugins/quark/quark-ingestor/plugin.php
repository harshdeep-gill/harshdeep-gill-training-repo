<?php
/**
 * Plugin Name: Quark Ingestor.
 * Description: Sends data to Ingestor API.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/urgent/namespace.php';

// Check if INGESTOR PUSH disabled.
if ( defined( 'QUARK_INGESTOR_PUSH_DISABLE' ) && true === QUARK_INGESTOR_PUSH_DISABLE ) {
	return;
}

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\Urgent\\bootstrap' );
