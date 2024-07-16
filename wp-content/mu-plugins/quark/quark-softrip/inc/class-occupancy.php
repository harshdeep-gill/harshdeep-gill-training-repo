<?php
/**
 * Occupancy Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Post;

use function Quark\CabinCategories\get as get_cabin_category;

/**
 * Occupancy class.
 */
class Occupancy extends Data_Object {

	/**
	 * Holds the Parent Cabin object.
	 *
	 * @var Cabin
	 */
	protected Cabin $parent;

	/**
	 * Holds the prices.
	 *
	 * @var Occupancy_Pricing[]
	 */
	protected array $children = [];

	/**
	 * Get the table name.
	 *
	 * @return string
	 */
	protected function get_table_name(): string {
		// Return prefixed table name.
		return 'qrk_occupancies';
	}

	/**
	 * Get the children table name.
	 *
	 * @return string
	 */
	protected function get_child_table_name(): string {
		// Meant to be defined in extension.
		return 'qrk_occupancy_prices';
	}

	/**
	 * Get the child relation field.
	 *
	 * @return string
	 */
	protected function get_relation_field(): string {
		// Meant to be defined in extension.
		return 'occupancy_id';
	}

	/**
	 * Get the children index field.
	 *
	 * @return string
	 */
	protected function get_index_field(): string {
		// Meant to be defined in extension.
		return 'currency_code';
	}

	/**
	 * Set the occupancy cabin.
	 *
	 * @param Cabin|null $cabin The cabin object to set.
	 *
	 * @return void
	 */
	public function set_cabin( Cabin $cabin = null ): void {
		// If valid, set cabin.
		if ( $cabin instanceof Cabin ) {
			$this->parent = $cabin;
		}
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
		if ( empty( $data ) || empty( $this->parent ) ) {
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

		// Setup prices.
		$prices = [];

		// If have prices, use them.
		if ( ! empty( $data['prices'] ) ) {
			$prices = $data['prices'];
			unset( $data['prices'] );
		}

		// Set the data.
		$this->entry_data = $data;

		// Auto save.
		if ( ! empty( $save ) ) {
			$this->save();
		}

		// Do prices.
		if ( ! empty( $prices ) ) {
			foreach ( $prices as $price_data ) {
				$price = $this->get_occupancy_price( $price_data['currencyCode'] );
				$price->set_occupancy( $this );
				$price->set( $price_data, $save );
			}
		}
	}

	/**
	 * Format incoming data.
	 *
	 * @param mixed[] $data The data to format.
	 *
	 * @return array{
	 *      id: mixed,
	 *      cabin_category: string,
	 *      title: string,
	 *      occupancy_mask: string,
	 *      prices?: mixed[]
	 * }
	 */
	protected function format_data( array $data = [] ): array {
		// Setup defaults.
		$default = [
			'id'                      => '',
			'name'                    => '',
			'mask'                    => '',
			'availabilityStatus'      => '',
			'availabilityDescription' => '',
			'spacesAvailable'         => '',
			'seq'                     => '',
			'prices'                  => [],
		];

		// Apply defaults.
		$data = wp_parse_args( $data, $default );

		// Setup formatted data.
		$formatted = [
			'id'             => $this->entry_data['id'] ?? null,
			'cabin_category' => strval( $this->parent->get_entry_data( 'id' ) ),
			'title'          => strval( $data['id'] ),
			'occupancy_mask' => strval( $data['mask'] ),
			'prices'         => [],
		];

		// Add prices.
		if ( ! empty( $data['prices'] ) ) {
			$formatted['prices'] = $data['prices'];
		} else {
			// Unset as un-needed.
			unset( $formatted['prices'] );
		}

		// Return the formatted data.
		return $formatted;
	}

	/**
	 * Ensure occupancy prices have been loaded.
	 *
	 * @return void
	 */
	protected function ensure_pricings_loaded(): void {
		// Check occupancies are loaded.
		if ( false === $this->children_loaded ) {
			$this->load_children();
		}
	}

	/**
	 * Add a child object.
	 *
	 * @param mixed[] $child_data The data for the child.
	 *
	 * @return void
	 */
	protected function add_child( array $child_data = [] ): void {
		// Get the index key.
		$index_key = $this->get_index_field();

		// Add object.
		$occupancy_price = new Occupancy_Pricing();
		$occupancy_price->set_occupancy( $this );
		$occupancy_price->set( $child_data );
		$this->children[ $child_data[ $index_key ] ] = $occupancy_price;
	}

	/**
	 * Get occupancies prices.
	 *
	 * @return Occupancy_Pricing[]
	 */
	public function get_occupancy_prices(): array {
		// Ensure pricings loaded.
		$this->ensure_pricings_loaded();

		// Return all occupancies.
		return $this->children;
	}

	/**
	 * Get an occupancy price.
	 *
	 * @param string $code The occupancy code to get.
	 *
	 * @return Occupancy_Pricing
	 */
	public function get_occupancy_price( string $code = '' ): Occupancy_Pricing {
		// Ensure departures loaded.
		$this->ensure_pricings_loaded();

		// Create if not existing.
		if ( ! isset( $this->children[ $code ] ) ) {
			// Create a new occupancy.
			$occupancy_price = new Occupancy_Pricing();

			// Set departure, and assign to occupancies.
			$occupancy_price->set_occupancy( $this );
			$this->children[ $code ] = $occupancy_price;
		}

		// Return the occupancy object.
		return $this->children[ $code ];
	}

	/**
	 * Get the price per person for the occupancy in specified currency.
	 *
	 * @param string $currency The currency code to get.
	 *
	 * @return string
	 */
	public function get_price_per_person( string $currency = 'USD' ): string {
		// Iterate over the occupancy prices.
		foreach ( $this->get_occupancy_prices() as $price ) {
			// Check the price is the correct currency.
			if ( $currency === $price->get_entry_data( 'currency_code' ) ) {
				// Get the price per person.
				return strval( $price->get_entry_data( 'price_per_person' ) );
			}
		}

		// Return nothing as it's not found.
		return '0.00';
	}
}
