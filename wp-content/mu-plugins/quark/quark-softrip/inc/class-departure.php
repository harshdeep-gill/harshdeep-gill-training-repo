<?php
/**
 * Departure Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Post;
use WP_Error;

use function Quark\CabinCategories\get_id_from_cabin_code;
use function Quark\Departures\get as get_departure;
use function Quark\Departures\bust_post_cache;
use function Quark\Ships\get_id_from_ship_code;
use function Quark\Ships\get as get_ship;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Departure class.
 */
class Departure extends Softrip_Object {
	/**
	 * Holds the parent Itinerary object.
	 *
	 * @var Itinerary
	 */
	private Itinerary $itinerary;

	/**
	 * Holds pre-save departure data.
	 *
	 * @var mixed[]
	 */
	private array $pre_save = [];

	/**
	 * Flag if cabins have been loaded.
	 *
	 * @var bool
	 */
	private bool $cabins_loaded = false;

	/**
	 * Holds the departure cabins.
	 *
	 * @var Cabin[]
	 */
	protected array $cabins = [];

	/**
	 * Set itinerary.
	 *
	 * @param Itinerary|null $itinerary The itinerary object to set.
	 *
	 * @return void
	 */
	public function set_itinerary( Itinerary $itinerary = null ): void {
		// Set the parent object if valid.
		if ( $itinerary instanceof Itinerary ) {
			$this->itinerary = $itinerary;
		}
	}

	/**
	 * Load a departure.
	 *
	 * @param int $post_id The departure post ID.
	 *
	 * @return void
	 */
	public function load( int $post_id = 0 ): void {
		// Get the Departure.
		$this->data = get_departure( $post_id );
	}

	/**
	 * Save departure.
	 *
	 * @return void
	 */
	public function save(): void {
		// If no data, bail.
		if ( empty( $this->pre_save ) ) {
			return;
		}

		// Localise the pre-save data.
		$save_data = $this->pre_save;

		// If existing post exists, use the ID as an update.
		if ( $this->is_valid() ) {
			// Get the pre-save data.
			$save_data = $this->pre_save;

			// Add the ID data.
			$save_data['ID'] = $this->get_id();

			// Update post.
			$departure = wp_update_post( $save_data, true );
		} else {
			// Create a new post.
			$departure = wp_insert_post( $this->pre_save, true );
		}

		// Bail if error.
		if ( $departure instanceof WP_Error ) {
			return;
		}

		// Save cabins if data is available.
		foreach ( $this->cabins as $cabin ) {
			$cabin->save();
		}

		// Bust cache.
		bust_post_cache( $departure );

		// Load the departure.
		$this->load( $departure );

		// Clear pre-save data.
		$this->pre_save = [];
	}

	/**
	 * Set the departure data.
	 *
	 * @param mixed[] $data The data to be set.
	 * @param bool    $save Flag to save on set in the case of a new departure.
	 *
	 * @return void
	 */
	public function set( array $data = [], bool $save = false ): void {
		// No point in assigning an empty array.
		if ( empty( $data ) || empty( $this->itinerary ) ) {
			return;
		}

		// Format the departure data.
		$data = $this->format_departure_data( $data );

		// Define the defaults.
		$default = [
			'post_title'  => '',
			'post_status' => 'draft',
			'meta_input'  => [
				'related_expedition'   => 0,
				'related_ship'         => '',
				'softrip_departure_id' => '',
				'softrip_package_id'   => '',
				'departure_start_date' => '',
				'departure_end_date'   => null,
				'duration'             => null,
				'itinerary'            => $this->itinerary->get_id(),
			],
		];

		// If previously been set, use the preset array.
		if ( ! empty( $this->pre_save ) ) {
			$default = $this->pre_save;
		}

		// Apply defaults.
		$data = wp_parse_args( $data, $default );

		// Ensure post_type, post_content, and post_parent cannot be altered.
		$data['post_type']    = DEPARTURE_POST_TYPE;
		$data['post_content'] = '';
		$data['post_parent']  = $this->itinerary->get_id();

		// Assign the pre-save data.
		$this->pre_save = $data;

		// Auto safe if flagged.
		if ( ! empty( $save ) ) {
			$this->save();
		}

		// Set Cabin data if found.
		if ( ! empty( $data['cabins'] ) ) {
			foreach ( $data['cabins'] as $cabin_data ) {
				$cabin = $this->get_cabin( $cabin_data['code'] );
				$cabin->set( (array) $cabin_data, $save );
			}
		}
	}

	/**
	 * Format departure data.
	 *
	 * @param array<string, mixed> $data The departure data.
	 *
	 * @return array<string, string|array<string, mixed>>
	 */
	private function format_departure_data( array $data = [] ): array {
		// Set data defaults.
		$default = [
			'id'               => 0,
			'code'             => 0,
			'shipCode'         => '',
			'packageCode'      => '',
			'startDate'        => current_time( 'mysql' ),
			'endDate'          => '',
			'marketCode'       => '',
			'duration'         => 0,
			'cabins'           => [],
			'adventureOptions' => [],
		];

		// Apply default structures.
		$data = wp_parse_args( $data, $default );

		// Structure the formatted data.
		$return_data = [
			'post_title'  => $data['id'],
			'post_status' => $this->get_departure_status( $data['startDate'] ),
			'meta_input'  => [
				'related_expedition'   => $this->itinerary->get_post_meta( 'related_expedition' ),
				'related_ship'         => get_id_from_ship_code( strval( $data['shipCode'] ) ),
				'softrip_departure_id' => $data['code'],
				'departure_unique_id'  => $data['id'],
				'softrip_package_id'   => $data['packageCode'],
				'departure_start_date' => $data['startDate'],
				'departure_end_date'   => $data['endDate'],
				'duration'             => $data['duration'],
				'itinerary'            => $this->itinerary->get_id(),
				'ship_id'              => $data['shipCode'],
				'region'               => $data['marketCode'],
			],
		];

		// If we have cabins, set cabin data.
		if ( ! empty( $data['cabins'] ) ) {
			$return_data['cabins'] = $data['cabins'];
		}

		// Return the structured array.
		return $return_data;
	}

	/**
	 * Get the departure status based on start date.
	 *
	 * @param string $date The start date.
	 *
	 * @return string
	 */
	protected function get_departure_status( string $date = '' ): string {
		// Convert time to timestamp.
		$check_stamp   = strtotime( $date );
		$current_stamp = time();
		$status        = 'draft';

		// Check if start date within the last day.
		if ( $check_stamp >= ( $current_stamp + DAY_IN_SECONDS ) ) {
			$status = 'publish';
		}

		// Return the status.
		return $status;
	}

	/**
	 * Ensure cabins have been loaded.
	 *
	 * @return void
	 */
	protected function ensure_cabins_loaded(): void {
		// Check cabins are loaded.
		if ( false === $this->cabins_loaded ) {
			$this->load_cabins();
		}
	}

	/**
	 * Load cabins.
	 *
	 * @return void
	 */
	protected function load_cabins(): void {
		// Get the global DB object.
		global $wpdb;

		// Set the table name.
		$table_name = 'qrk_cabin_categories';

		// Load the cabins.
		$cabins = $wpdb->get_results(
			$wpdb->prepare(
				'
			SELECT
				*
			FROM
				%i
			WHERE
				departure = %d
			',
				[
					$table_name,
					$this->get_id(),
				]
			),
			ARRAY_A
		);

		// Load each cabin.
		foreach ( $cabins as $cabin_data ) {
			$cabin = new Cabin();
			$cabin->set_departure( $this );
			$cabin->load( $cabin_data['cabin_category'] );
			$cabin->set( $cabin_data );
			$this->cabins[ $cabin_data['cabin_category_id'] ] = $cabin;
		}

		// Flag loaded.
		$this->cabins_loaded = true;
	}

	/**
	 * Get cabins.
	 *
	 * @return Cabin[]
	 */
	public function get_cabins(): array {
		// Ensure departures loaded.
		$this->ensure_cabins_loaded();

		// Return all cabins.
		return $this->cabins;
	}

	/**
	 * Get a cabin.
	 *
	 * @param string $code The cabin code to get.
	 *
	 * @return Cabin
	 */
	public function get_cabin( string $code = '' ): Cabin {
		// Ensure departures loaded.
		$this->ensure_cabins_loaded();

		// Create if not existing.
		if ( ! isset( $this->cabins[ $code ] ) ) {
			// Create a new cabin.
			$cabin = new Cabin();

			// Get the cabin category post id.
			$cabin_post_id = get_id_from_cabin_code( $code );

			// Load if found.
			if ( ! empty( $cabin_post_id ) ) {
				$cabin->load( $cabin_post_id );
			}

			// Set departure, and assign to cabins.
			$cabin->set_departure( $this );
			$this->cabins[ $code ] = $cabin;
		}

		// Return the cabin object.
		return $this->cabins[ $code ];
	}

	/**
	 * Get the lowest price per person for the departure.
	 *
	 * @param string $currency The currency code to get.
	 *
	 * @return float
	 */
	public function get_lowest_price( string $currency = 'USD' ): float {
		// Set up the lowest variable.
		$lowest = 0;

		// Check Current departure status is published.
		if ( 'publish' !== $this->get_status() ) {
			return $lowest;
		}

		// Iterate over the cabins.
		foreach ( $this->get_cabins() as $cabin ) {
			// Get the price per person.
			$test_price = $cabin->get_lowest_price( $currency );

			// Check if lowest is set and is lower than the previous price.
			if ( empty( $lowest ) || $lowest > $test_price ) {
				// Use the price as it's lower.
				$lowest = $test_price;
			}
		}

		// Return the lowest found.
		return $lowest;
	}

	/**
	 * Get the ship.
	 *
	 * @return array{} | array{
	 *     post: WP_Post|null,
	 *     permalink: string,
	 *     post_meta: mixed[],
	 * }
	 */
	public function get_ship(): array {
		// Check Current departure status is published.
		if ( 'publish' !== $this->get_status() ) {
			return [];
		}

		// Get the ship code.
		$ship_code = $this->get_post_meta( 'ship_id' );

		// Validate ship code.
		if ( empty( $ship_code ) || ! is_string( $ship_code ) ) {
			return [];
		}

		// Get the ship object.
		$ship_id = get_id_from_ship_code( $ship_code );
		$ship    = get_ship( $ship_id );

		// Validate ship.
		if ( ! $ship['post'] instanceof WP_Post ) {
			return [];
		}

		// Return the ship object.
		return $ship;
	}

	/**
	 * Get the starting date.
	 *
	 * @return string The starting date.
	 */
	public function get_starting_date(): string {
		// Check Current departure status is published.
		if ( 'publish' !== $this->get_status() ) {
			return '';
		}

		// Get the departure start date.
		$departure_start_date = $this->get_post_meta( 'departure_start_date' );

		// Validate the start date.
		if ( empty( $departure_start_date ) || ! is_string( $departure_start_date ) ) {
			return '';
		}

		// Return the start date.
		return $departure_start_date;
	}

	/**
	 * Get the ending date.
	 *
	 * @return string The ending date.
	 */
	public function get_ending_date(): string {
		// Check Current departure status is published.
		if ( 'publish' !== $this->get_status() ) {
			return '';
		}

		// Get the departure end date.
		$departure_end_date = $this->get_post_meta( 'departure_end_date' );

		// Validate the end date.
		if ( empty( $departure_end_date ) || ! is_string( $departure_end_date ) ) {
			return '';
		}

		// Return the end date.
		return $departure_end_date;
	}
}
