<?php
/**
 * Softrip: DB.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\WP_CLI;

use WP_CLI;
use WP_CLI\ExitException;

use function Quark\Softrip\create_custom_db_tables;

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

		// Create the DB tables.
		create_custom_db_tables();

		// End notice.
		WP_CLI::success( 'DB Tables created.' );
	}
}
