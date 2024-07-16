<?php
/**
 * Softtrip Database Object Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Post;

/**
 * Object Class.
 */
abstract class Data_Object extends Softrip_Object {

	/**
	 * Holds the specific entry data.
	 *
	 * @var mixed[]
	 */
	protected array $entry_data = [];

	/**
	 * Flag to indicate data changed.
	 *
	 * @var bool
	 */
	protected bool $changed = false;

	/**
	 * Flag if children have been loaded.
	 *
	 * @var bool
	 */
	protected bool $children_loaded = false;

	/**
	 * Holds the children objects
	 *
	 * @var self[]
	 */
	protected array $children = [];

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	protected function get_table_name(): string {
		// Meant to be defined in extension.
		return '';
	}

	/**
	 * Get the children table name.
	 *
	 * @return string
	 */
	protected function get_child_table_name(): string {
		// Meant to be defined in extension.
		return '';
	}

	/**
	 * Get the child relation field.
	 *
	 * @return string
	 */
	protected function get_relation_field(): string {
		// Meant to be defined in extension.
		return 'field';
	}

	/**
	 * Get the children index field.
	 *
	 * @return string
	 */
	protected function get_index_field(): string {
		// Meant to be defined in extension.
		return 'index';
	}

	/**
	 * Load the data - unused.
	 *
	 * @param int $post_id The object post ID.
	 *
	 * @return void
	 */
	public function load( int $post_id = 0 ): void {
		// Unused in data object.
	}

	/**
	 * Save to database table.
	 *
	 * @return void
	 */
	public function save(): void {
		// Check if changes are made.
		if ( false === $this->changed ) {
			return;
		}

		// Get the global DB object.
		global $wpdb;

		// Get the table name.
		$table_name = $this->get_table_name();

		// Update or insert depending on ID present.
		if ( ! empty( $this->entry_data['id'] ) ) {
			$wpdb->update( $table_name, $this->entry_data, [ 'id' => $this->entry_data['id'] ] );
		} else {
			$saved = $wpdb->insert( $table_name, $this->entry_data );

			// if successful, get the id.
			if ( $saved ) {
				$this->entry_data['id'] = $wpdb->insert_id;
			}
		}
	}

	/**
	 * Get entry data.
	 *
	 * @param string $key The key of the data to get.
	 *
	 * @return mixed
	 */
	public function get_entry_data( string $key = '' ): mixed {
		// if empty key, return all.
		if ( empty( $key ) ) {
			return $this->entry_data;
		}

		// Check if key exists.
		if ( isset( $this->entry_data[ $key ] ) ) {
			return $this->entry_data[ $key ];
		}

		// return an empty if not found.
		return '';
	}

	/**
	 * Load children items.
	 *
	 * @return void
	 */
	protected function load_children(): void {
		// Get the global DB object.
		global $wpdb;

		// Set the table name and relation.
		$table_name = $this->get_child_table_name();
		$relation   = $this->get_relation_field();

		// Get entries for the child items.
		$entries = $wpdb->get_results(
			$wpdb->prepare(
				'
			SELECT
				*
			FROM
				%i
			WHERE
				%i = %d
			',
				[
					$table_name,
					$relation,
					$this->get_entry_data( 'id' ),
				]
			),
			ARRAY_A
		);

		// Build children.
		$this->build_children( $entries );

		// Flag loaded.
		$this->children_loaded = true;
	}

	/**
	 * Build the child objects.
	 *
	 * @param mixed[] $children The child entries to build from.
	 *
	 * @return void
	 */
	protected function build_children( array $children = [] ): void {
		// Load each occupancy.
		foreach ( $children as $child_data ) {
			if ( ! is_array( $child_data ) ) {
				continue;
			}
			$this->add_child( $child_data );
		}
	}

	/**
	 * Add a child object.
	 *
	 * @param mixed[] $child_data The data for the child.
	 *
	 * @return void
	 */
	abstract protected function add_child( array $child_data = [] ): void;

	/**
	 * Format incoming data.
	 *
	 * @param mixed[] $data The data to format.
	 *
	 * @return mixed[]
	 */
	abstract protected function format_data( array $data = [] ): array;
}
