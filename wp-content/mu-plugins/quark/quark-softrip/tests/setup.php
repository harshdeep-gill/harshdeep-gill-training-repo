<?php
/**
 * Softrip test functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

/**
 * Setup Softrip DB.
 *
 * @return void
 */
function setup_softrip_db(): void {
	// Set run status.
	static $run;

	// End if run.
	if ( ! empty( $run ) ) {
		return;
	}

	// Include DB functions.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	// Init DB object.
	$db = new Softrip_DB();

	// Get SQL array.
	$tables = $db->get_db_tables();

	// Start table creation.
	foreach ( $tables as $name => $sql ) {
		$table_name = $db->prefix_table_name( $name );
		maybe_create_table( $table_name, $sql );
	}

	// Flag as run.
	$run = true;
}
