<?php
/**
 * Softrip: DB.
 *
 * @package quark-softrip
 */

namespace Quark\softrip\WP_CLI;

use cli\progress\Bar;
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

		// Get SQL array.
		$tables = [
			'adventure_options' => $this->get_adventure_table_sql(),
			'cabin_categories'  => $this->get_cabin_table_sql(),
			'occupancies'       => $this->get_occupancies_table_sql(),
			'occupancy_prices'  => $this->get_occupancy_prices_table_sql(),
			'promos'            => $this->get_promos_table_sql(),
		];

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
			$table_name = $this->prefix_table_name( $name );
			maybe_create_table( $table_name, $sql );
			$progress->tick();
		}

		// End bar.
		$progress->finish();

		// End notice.
		WP_CLI::success( 'DB Tables created.' );
	}

	/**
	 * Get the engine and collate.
	 *
	 * @return string
	 */
	private function engine_collate(): string {
		// Get the $wpdb object.
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// Return the engine and collate string.
		return "ENGINE=InnoDB $charset_collate";
	}

	/**
	 * Get the Table Name with prefix.
	 *
	 * @param string $name The table name to prefix.
	 *
	 * @return string
	 */
	private function prefix_table_name( string $name = '' ): string {
		// Get the $wpdb object.
		global $wpdb;

		// Return the prefixed name.
		return $wpdb->prefix . $name;
	}

	/**
	 * Get the cabins table create SQL.
	 *
	 * @return string
	 */
	private function get_cabin_table_sql(): string {
		// Get the table name.
		$table_name = $this->prefix_table_name( 'cabin_categories' );

		// Get the engine collate.
		$engine_collate = $this->engine_collate();

		// Build the SQL query.
		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			title VARCHAR(255) NOT NULL,
			departure BIGINT NOT NULL,
			cabin_category BIGINT NOT NULL,
			package_id VARCHAR(20) NOT NULL,
			departure_id VARCHAR(20) NOT NULL,
			Ship_id VARCHAR(10) NOT NULL,
			cabin_category_id VARCHAR(10) NOT NULL,
			availability_status VARCHAR(1) NOT NULL,
			spaces_available INT NOT NULL,
			UNIQUE KEY cabin_category_title_unique (title)
		) $engine_collate";

		// return the SQL.
		return $sql;
	}

	/**
	 * Get the adventure_options table create SQL.
	 *
	 * @return string
	 */
	private function get_adventure_table_sql(): string {
		// Get the table name.
		$table_name = $this->prefix_table_name( 'adventure_options' );

		// Get the engine collate.
		$engine_collate = $this->engine_collate();

		// Build the SQL query.
		$sql = "CREATE TABLE $table_name (
		    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		    title VARCHAR(255) NOT NULL,
		    Departure BIGINT NOT NULL,
		    departure_id VARCHAR(20) NOT NULL,
		    package_id VARCHAR(20) NOT NULL,
		    option_id VARCHAR(10) NOT NULL,
		    spaces_available BIGINT NOT NULL,
		    adventure_option_term BIGINT NOT NULL,
		    price_per_person_usd BIGINT NOT NULL,
		    price_per_person_cad BIGINT NOT NULL,
		    price_per_person_aud BIGINT NOT NULL,
		    price_per_person_gbp BIGINT NOT NULL,
		    price_per_person_eur BIGINT NOT NULL
		) $engine_collate";

		// return the SQL.
		return $sql;
	}

	/**
	 * Get the promos table create SQL.
	 *
	 * @return string
	 */
	private function get_promos_table_sql(): string {
		// Get the table name.
		$table_name = $this->prefix_table_name( 'promos' );

		// Get the engine collate.
		$engine_collate = $this->engine_collate();

		// Build the SQL query.
		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			promotion_code VARCHAR(255) NOT NULL,
		    start_date DATETIME NOT NULL,
		    end_date DATETIME NOT NULL,
		    description VARCHAR(255) NOT NULL,
		    discount_type VARCHAR(255) NOT NULL,
		    discount_value VARCHAR(255) NOT NULL,
		    pif TINYINT(1) NOT NULL
		) $engine_collate";

		// return the SQL.
		return $sql;
	}

	/**
	 * Get the occupancies table create SQL.
	 *
	 * @return string
	 */
	private function get_occupancies_table_sql(): string {
		// Get the table name.
		$table_name = $this->prefix_table_name( 'occupancies' );

		// Get the engine collate.
		$engine_collate = $this->engine_collate();

		// Build the SQL query.
		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    		cabin_category BIGINT NOT NULL,
		    title VARCHAR(255) NOT NULL,
    		occupancy_mask VARCHAR(12) NOT NULL
		) $engine_collate";

		// return the SQL.
		return $sql;
	}

	/**
	 * Get the occupancy_prices table create SQL.
	 *
	 * @return string
	 */
	private function get_occupancy_prices_table_sql(): string {
		// Get the table name.
		$table_name = $this->prefix_table_name( 'occupancy_prices' );

		// Get the engine collate.
		$engine_collate = $this->engine_collate();

		// Build the SQL query.
		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    		occupancy_id BIGINT NOT NULL,
    		currency_code VARCHAR(3) NOT NULL,
    		price_per_person DECIMAL(8, 2) NOT NULL,
    		total_price_per_person DECIMAL(8, 2) NOT NULL,
    		promotion_code VARCHAR(255) NOT NULL,
    		promo_price_per_person DECIMAL(8, 2) NOT NULL
		) $engine_collate";

		// return the SQL.
		return $sql;
	}
}
