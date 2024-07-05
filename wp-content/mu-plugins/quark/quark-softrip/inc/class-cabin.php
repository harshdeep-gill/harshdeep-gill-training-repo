<?php
/**
 * Cabin Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Post;

use function Quark\CabinCategories\get as get_cabin_category;

/**
 * Cabin class.
 */
class Cabin extends Data_Object {

	/**
	 * Holds the Parent Departure object.
	 *
	 * @var Departure
	 */
	protected Departure $departure;

	/**
	 * Flag to indicate data changed.
	 *
	 * @var bool
	 */
	protected bool $changed = false;

	/**
	 * Holds the children objects
	 *
	 * @var Occupancy[]
	 */
	protected array $children = [];

	/**
	 * Flag if occupancies have been loaded.
	 *
	 * @var bool
	 */
	private bool $occupancies_loaded = false;

	/**
	 * Load cabin data.
	 *
	 * @param int $post_id The cabin_category post object.
	 *
	 * @return void
	 */
	public function load( int $post_id = 0 ): void {
		// Load the basic cabin category data.
		$this->data = get_cabin_category( $post_id );
	}

	/**
	 * Set the cabin departure.
	 *
	 * @param Departure|null $departure The cabin departure object to set.
	 *
	 * @return void
	 */
	public function set_departure( Departure $departure = null ): void {
		// If valid, set departure.
		if ( $departure instanceof Departure ) {
			$this->departure = $departure;
		}
	}

	/**
	 * Set Cabin data.
	 *
	 * @param mixed[] $data The cabin data to set.
	 * @param bool    $save Flag to determine auto save.
	 *
	 * @return void
	 */
	public function set( array $data = [], bool $save = false ): void {
		// No point in assigning an empty array.
		if ( empty( $data ) || empty( $this->departure ) ) {
			return;
		}

		// Create a test format.
		$structure = $this->format_data();

		// Format the data if needed.
		if ( array_keys( $structure ) !== array_keys( $data ) ) {
			$data = $this->format_data( $data );
		}

		// Set absolutes.
		$data['departure']      = $this->departure->get_id();
		$data['cabin_category'] = $this->get_id();

		// Set flag if data changed.
		if ( empty( $data['id'] ) || ( ! empty( $this->entry_data ) && $this->entry_data !== $data ) ) {
			$this->changed = true;
		}

		// Prep occupancies.
		$occupancies = [];

		// Use if found.
		if ( isset( $data['occupancies'] ) ) {
			$occupancies = $data['occupancies'];
			unset( $data['occupancies'] );
		}

		// Set the cabin data.
		$this->entry_data = $data;

		// If auto save, save.
		if ( ! empty( $save ) ) {
			$this->save();
		}

		// Set occupancies.
		if ( ! empty( $occupancies ) ) {
			foreach ( $occupancies as $occupancy_data ) {
				$occupancy = $this->get_occupancy( $occupancy_data['id'] );
				$occupancy->set( $occupancy_data, true );
			}
		}
	}

	/**
	 * Format incoming data.
	 *
	 * @param mixed[] $data The data to format.
	 *
	 * @return mixed[]
	 */
	protected function format_data( array $data = [] ): array {
		// Setup defaults.
		$default = [
			'id'          => '',
			'code'        => '',
			'name'        => '',
			'departureId' => '',
			'occupancies' => [],
			'promotions'  => [],
		];

		// Apply defaults.
		$data = wp_parse_args( $data, $default );

		// Setup formatted data.
		$formatted = [
			'id'                  => $this->entry_data['id'] ?? null,
			'title'               => $data['id'],
			'departure'           => $this->departure->get_id(),
			'cabin_category'      => $this->get_id(),
			'package_id'          => $this->departure->get_post_meta( 'softrip_package_id' ),
			'departure_id'        => $this->departure->get_post_meta( 'softrip_departure_id' ),
			'ship_id'             => $this->departure->get_post_meta( 'ship_id' ),
			'cabin_category_id'   => $data['code'],
			'availability_status' => 'C',
			'spaces_available'    => '0',
			'occupancies'         => $data['occupancies'],
		];

		// Check Availability status.
		if ( ! empty( $data['occupancies'] ) ) {
			$count                         = $this->get_availability_count( $data['occupancies'] );
			$formatted['spaces_available'] = strval( $count );

			// Update availability if space.
			if ( ! empty( $count ) ) {
				$formatted['availability_status'] = 'O';
			}
		}

		// Remove occupancies if empty.
		if ( empty( $formatted['occupancies'] ) ) {
			unset( $formatted['occupancies'] );
		}

		// Return the formatted data.
		return $formatted;
	}

	/**
	 * Get the availability status.
	 *
	 * @param mixed[] $data The occupancies data to check from.
	 *
	 * @return int
	 */
	protected function get_availability_count( array $data = [] ): int {
		// Set zero.
		$available = 0;

		// Go over each to find the status.
		foreach ( $data as $occupancy ) {
			if ( is_array( $occupancy ) && ! empty( $occupancy['spacesAvailable'] ) ) {
				$available = $occupancy['spacesAvailable'];
			}
		}

		// Return our counted.
		return $available;
	}

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	protected function get_table_name(): string {
		// Return prefixed.
		return 'qrk_cabin_categories';
	}

	/**
	 * Get the children table name.
	 *
	 * @return string
	 */
	protected function get_child_table_name(): string {
		// Meant to be defined in extension.
		return 'qrk_occupancies';
	}

	/**
	 * Get relation field name.
	 *
	 * @return string
	 */
	protected function get_relation_field(): string {
		// Return relation field.
		return 'cabin_category';
	}

	/**
	 * Get the children index field.
	 *
	 * @return string
	 */
	protected function get_index_field(): string {
		// Meant to be defined in extension.
		return 'title';
	}

	/**
	 * Ensure occupancies have been loaded.
	 *
	 * @return void
	 */
	protected function ensure_occupancies_loaded(): void {
		// Check occupancies are loaded.
		if ( false === $this->occupancies_loaded ) {
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

		// Create the object.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $this );
		$occupancy->set( $child_data );

		// Add to internal.
		$this->children[ $child_data[ $index_key ] ] = $occupancy;
	}

	/**
	 * Get occupancies.
	 *
	 * @return Occupancy[]
	 */
	public function get_occupancies(): array {
		// Ensure departures loaded.
		$this->ensure_occupancies_loaded();

		// Return all occupancies.
		return $this->children;
	}

	/**
	 * Get an occupancy.
	 *
	 * @param string $code The occupancy code to get.
	 *
	 * @return Occupancy
	 */
	public function get_occupancy( string $code = '' ): Occupancy {
		// Ensure departures loaded.
		$this->ensure_occupancies_loaded();

		// Create if not existing.
		if ( ! isset( $this->children[ $code ] ) ) {
			// Create a new occupancy.
			$occupancy = new Occupancy();

			// Set departure, and assign to occupancies.
			$occupancy->set_cabin( $this );
			$this->children[ $code ] = $occupancy;
		}

		// Return the occupancy object.
		return $this->children[ $code ];
	}
}
