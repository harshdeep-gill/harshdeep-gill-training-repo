<?php
/**
 * Softrip: DB.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\WP_CLI;

use cli\progress\Bar;
use Quark\Softrip\Softrip_DB;
use WP_CLI;
use WP_CLI\ExitException;

/**
 * Class DB.
 */
class DB {

	/**
	 * Softrip install tables.
	 *
	 * @subcommand install
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function install(): void {
		// Include DB functions.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YInstall DB Tables...%n' ) );

		// Get the DB object.
		$softrip_db = new Softrip_DB();

		// Get SQL array.
		$tables = $softrip_db->get_db_tables_sql();

		// Initialize progress bar.
		$progress = new Bar( 'Setting up tables', count( $tables ), 100 );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start table creation.
		foreach ( $tables as $name => $sql ) {
			$table_name = $softrip_db->prefix_table_name( $name );
			maybe_create_table( $table_name, $sql );
			$progress->tick();
		}

		// End bar.
		$progress->finish();

		// End notice.
		WP_CLI::success( 'DB Tables created.' );
	}
}
