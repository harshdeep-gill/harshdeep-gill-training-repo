<?php
/**
 * Occupancy Pricing Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

/**
 * Occupancy Pricing class.
 */
class Occupancy_Pricing extends Data_Object {

	/**
	 * Holds the Parent object.
	 *
	 * @var Occupancy
	 */
	protected Occupancy $occupancy;

	/**
	 * Flag to indicate data changed.
	 *
	 * @var bool
	 */
	protected bool $changed = false;

	/**
	 * Set the occupancy.
	 *
	 * @param Occupancy|null $occupancy The Occupancy object to set.
	 *
	 * @return void
	 */
	public function set_occupancy( Occupancy $occupancy = null ): void {
		// If valid, set Occupancy.
		if ( $occupancy instanceof Occupancy ) {
			$this->occupancy = $occupancy;
		}
	}

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	protected function get_table_name(): string {
		// Meant to be defined in extension.
		return 'qrk_occupancy_prices';
	}

	/**
	 * Get the children table name.
	 *
	 * @return string
	 */
	protected function get_child_table_name(): string {
		// Meant to be defined in extension.
		return 'qrk_promos';
	}

	/**
	 * Get the child relation field.
	 *
	 * @return string
	 */
	protected function get_relation_field(): string {
		// Meant to be defined in extension.
		return 'promotion_code';
	}

	/**
	 * Set occupancy data.
	 *
	 * @param mixed[] $data The data to set.
	 * @param bool    $save Flag to determine auto save.
	 *
	 * @return void
	 */
	public function set( array $data = [], bool $save = false ): void {
		// No point in assigning an empty array.
		if ( empty( $data ) || empty( $this->occupancy ) ) {
			return;
		}

		// Create a test format.
		$structure = $this->format_data();

		// Format the data if needed.
		if ( array_keys( $structure ) !== array_keys( $data ) ) {
			$data = $this->format_data( $data );
		}

		// Set flag if data changed.
		if ( empty( $data['id'] ) || ( ! empty( $this->entry_data ) && $this->entry_data !== $data ) ) {
			$this->changed = true;
		}

		// Set the data.
		$this->entry_data = $data;

		// Auto save.
		if ( ! empty( $save ) ) {
			$this->save();
		}
	}

	/**
	 * Format incoming data.
	 *
	 * @param mixed[] $data The data to format.
	 *
	 * @return array{
	 *      id: mixed,
	 *      occupancy_id: string,
	 *      price_per_person: double,
	 *      total_price_per_person: double,
	 *      promotion_code: string,
	 *      promo_price_per_person: string,
	 * }
	 */
	protected function format_data( array $data = [] ): array {
		// Setup defaults.
		$default = [
			'currencyCode'   => '',
			'pricePerPerson' => 0,
			'promos'         => [],
		];

		// Apply defaults.
		$data = wp_parse_args( $data, $default );

		// Setup formatted data.
		$formatted = [
			'id'                     => $this->entry_data['id'] ?? null,
			'occupancy_id'           => strval( $this->occupancy->get_entry_data( 'id' ) ),
			'currency_code'          => strval( $data['currencyCode'] ),
			'price_per_person'       => doubleval( $data['pricePerPerson'] ),
			'total_price_per_person' => doubleval( $data['pricePerPerson'] ),
			'promotion_code'         => '',
			'promo_price_per_person' => '',
		];

		// Return the formatted data.
		return $formatted;
	}

	/**
	 * Add a child object.
	 *
	 * @param mixed[] $child_data The data for the child.
	 *
	 * @return void
	 */
	protected function add_child( array $child_data = [] ): void {
		// Not used yet.
	}
}
