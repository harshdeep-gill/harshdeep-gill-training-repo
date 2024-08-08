<?php
/**
 * Softrip DB Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use function Quark\Softrip\AdventureOptions\get_adventure_table_sql;
use function Quark\Softrip\Cabins\get_table_sql as get_cabins_table_sql;
use function Quark\Softrip\Promotions\get_table_sql as get_promotions_table_sql;

/**
 * Class Softrip_DB.
 */
class Softrip_DB {

	/**
	 * Get the DB Tables array.
	 *
	 * @return array{
	 *      adventure_options: string,
	 *      cabins: string,
	 *      promotions: string,
	 * }
	 */
	public function get_db_tables_sql(): array {
		// Return the list of tables used.
		return [
			'adventure_options' => get_adventure_table_sql(),
			'promotions'        => get_promotions_table_sql(),
			'cabins'            => get_cabins_table_sql()
		];
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
}
