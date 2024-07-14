<?php
/**
 * Softrip DB Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

/**
 * Class Softrip_DB.
 */
class Softrip_DB {

	/**
	 * Get the DB Tables array.
	 *
	 * @return array{
	 *      adventure_options: string,
	 *      cabin_categories: string,
	 *      occupancies: string,
	 *      occupancy_prices: string,
	 *      promos: string,
	 * }
	 */
	public function get_db_tables(): array {
		// Return the list of tables used.
		return [
			'adventure_options' => $this->get_adventure_table_sql(),
			'cabin_categories'  => $this->get_cabin_table_sql(),
			'occupancies'       => $this->get_occupancies_table_sql(),
			'occupancy_prices'  => $this->get_occupancy_prices_table_sql(),
			'promos'            => $this->get_promos_table_sql(),
		];
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
	public function prefix_table_name( string $name = '' ): string {
		// Return the prefixed name.
		return 'qrk_' . $name;
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
			package_id VARCHAR(45) NOT NULL,
			departure_id VARCHAR(45) NOT NULL,
			ship_id VARCHAR(10) NOT NULL,
			cabin_category_id VARCHAR(45) NOT NULL,
			availability_status VARCHAR(4) NOT NULL,
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
