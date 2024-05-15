<?php
/**
 * Plugin Name: Quark World Travel Staff Members
 * Description: Staff Members feature for Quark World Travel.
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0.0
 *
 * @package quark-staff-members
 */

namespace Quark\StaffMembers;

require_once __DIR__ . '/inc/namespace.php';

// Kick it off.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
