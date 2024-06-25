<?php
/**
 * Departure Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Post;
use WP_Error;

use function Quark\Departures\get as get_departure;
use function Quark\Departures\bust_post_cache;

use function Quark\Ships\code_to_id as get_ship_post_id;

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
	 * Constructor.
	 *
	 * @param Itinerary|null $itinerary The parent Itinerary.
	 */
	public function __construct( Itinerary $itinerary = null ) {
		// If Itinerary supplied, initialise it.
		if ( $itinerary instanceof Itinerary ) {
			// Set the parent.
			$this->set_itinerary( $itinerary );
		}
	}

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
	 * @return bool
	 */
	public function save(): bool {
		// If no data, bail.
		if ( empty( $this->pre_save ) ) {
			return false;
		}

		// If existing post exists, use the ID as an update.
		if ( $this->is_valid() ) {
			// Get the pre-save data.
			$save_data = $this->pre_save;

			// Add the ID data.
			$save_data['ID'] = $this->id();

			// Update post.
			$departure = wp_update_post( $save_data, true );
		} else {
			// Create a new post.
			$departure = wp_insert_post( $this->pre_save, true );
		}

		// Bail if error.
		if ( $departure instanceof WP_Error ) {
			return false;
		}

		// Bust cache.
		bust_post_cache( $departure );

		// Load the departure.
		$this->load( $departure );

		// Clear pre-save data.
		$this->pre_save = [];

		// Return success.
		return true;
	}

	/**
	 * Set the departure data.
	 *
	 * @param mixed[] $data The data to be set.
	 *
	 * @return void
	 */
	public function set( array $data = [] ): void {
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
				'itinerary'            => $this->itinerary->id(),
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
		$data['post_parent']  = $this->itinerary->id();

		// Assign the pre-save data.
		$this->pre_save = $data;
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
			'shipCode'         => '',
			'packageCode'      => '',
			'startDate'        => current_time( 'mysql' ),
			'endDate'          => '',
			'duration'         => 0,
			'cabins'           => [],
			'adventureOptions' => [],
		];

		// Apply default structures.
		$data = wp_parse_args( $data, $default );

		// Return the formatted structure.
		return [
			'post_title'  => $data['id'],
			'post_status' => $this->departure_status( $data['startDate'] ),
			'meta_input'  => [
				'related_expedition'   => $this->itinerary->post_meta( 'related_expedition' ),
				'related_ship'         => get_ship_post_id( strval( $data['shipCode'] ) ),
				'softrip_departure_id' => $data['id'],
				'softrip_package_id'   => $data['packageCode'],
				'departure_start_date' => $data['startDate'],
				'departure_end_date'   => $data['endDate'],
				'duration'             => $data['duration'],
				'itinerary'            => $this->itinerary->id(),
			],
		];
	}

	/**
	 * Get the departure status based on start date.
	 *
	 * @param string $date The start date.
	 *
	 * @return string
	 */
	protected function departure_status( string $date = '' ): string {
		// Convert time to timestamp.
		$check_stamp   = strtotime( $date );
		$current_stamp = time();
		$status        = 'draft';

		// Check if start date within the last day.
		if ( $check_stamp <= ( $current_stamp + DAY_IN_SECONDS ) ) {
			$status = 'publish';
		}

		// Return the status.
		return $status;
	}
}
