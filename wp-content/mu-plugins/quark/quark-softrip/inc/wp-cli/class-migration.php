<?php
/**
 * WP CLI for doing migration.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\WP_CLI;

use WP_CLI;

use function Quark\Softrip\Promotions\get_table_name;
use function WP_CLI\Utils\get_flag_value;

/**
 * Handles migration-related operations.
 */
class Migration {

	/**
	 * Adds a currency column to the promotions table.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand add-currency-column
	 *
	 * ## OPTIONS
	 * [--dry-run]
	 * : Whether to run the command in dry-run mode. Default is false.
	 *
	 * @synopsis [--dry-run]
	 *
	 * @return void
	 */
	public function add_currency_column( array $args = [], array $args_assoc = [] ): void {
		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'dry-run' => false,
			]
		);

		// Get the dry-run flag.
		$dry_run = (bool) get_flag_value( $options, 'dry-run', false );

		// Global WPDB.
		global $wpdb;

		// Get promotions table name.
		$promotions_table = get_table_name();

		// Define the column to add.
		$currency_column = 'currency';

		// Check if the column already exists.
		$column_exists = $wpdb->get_results(
			$wpdb->prepare(
				'SHOW COLUMNS FROM %i LIKE %s',
				[
					$promotions_table,
					$currency_column,
				]
			)
		);

		// If dry-run mode is enabled, just output the status.
		if ( $dry_run ) {
			// Output the status.
			if ( $column_exists ) {
				WP_CLI::success( "'$currency_column' column already exists in the '$promotions_table' table." );
			} else {
				WP_CLI::success( "'$currency_column' column does not exist in the '$promotions_table' table." );
			}

			// Return early.
			return;
		}

		// If the column already exists, return early.
		if ( $column_exists ) {
			WP_CLI::success( "'$currency_column' column already exists in the '$promotions_table' table." );

			// Return early.
			return;
		}

		// Add the column.
		$result = $wpdb->get_results(
			$wpdb->prepare(
				'ALTER TABLE %i ADD %i VARCHAR(255) NULL',
				[
					$promotions_table,
					$currency_column,
				]
			)
		);

		// Output the status.
		if ( false === $result ) {
			WP_CLI::error( "Failed to add '$currency_column' column to the '$promotions_table' table." );
		} else {
			WP_CLI::success( "Successfully added '$currency_column' column to the '$promotions_table' table." );
		}
	}
}
