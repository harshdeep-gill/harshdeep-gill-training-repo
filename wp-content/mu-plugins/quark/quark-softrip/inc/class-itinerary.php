<?php
/**
 * Itinerary Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Error;
use WP_Query;

use function Quark\Itineraries\bust_post_cache;
use function Quark\Itineraries\get as get_itinerary;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Itinerary API.
 */
class Itinerary extends Softrip_Object {
	/**
	 * Holds the departures.
	 *
	 * @var Departure[]
	 */
	protected array $departures = [];

	/**
	 * Flag if departures have been loaded.
	 *
	 * @var bool
	 */
	private bool $departures_loaded = false;

	/**
	 * Constructor.
	 *
	 * @param int $post_id The itinerary post ID.
	 */
	public function __construct( int $post_id = 0 ) {
		// If provided with a post_id, load it.
		if ( ! empty( $post_id ) ) {
			$this->load( $post_id );
		}
	}

	/**
	 * Load the data.
	 *
	 * @param int $post_id The object post ID.
	 *
	 * @return void
	 */
	public function load( int $post_id = 0 ): void {
		// Get the Itinerary.
		$this->data = get_itinerary( $post_id );
	}

	/**
	 * Ensure that departures are loaded.
	 *
	 * @return void
	 */
	private function ensure_departures_loaded(): void {
		// Check departures are loaded.
		if ( false === $this->departures_loaded ) {
			$this->load_departures();
		}
	}

	/**
	 * Load itinerary departures.
	 *
	 * @return void
	 */
	private function load_departures(): void {
		// Get the departure posts.
		$posts = new WP_Query(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => 100,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'parent_in'              => $this->get_id(),
				'fields'                 => 'ids',
				'post_status'            => 'draft,publish',
			]
		);

		// Departures.
		foreach ( $posts->posts as $post_id ) {
			$departure = new Departure();
			$departure->set_itinerary( $this );
			$departure->load( absint( $post_id ) );
			$this->departures[ $departure->get_post_meta( 'softrip_departure_id' ) ] = $departure;
		}

		// Set departures loaded.
		$this->departures_loaded = true;
	}

	/**
	 * Get itinerary departures.
	 *
	 * @return Departure[]
	 */
	public function get_departures(): array {
		// Ensure departures loaded.
		$this->ensure_departures_loaded();

		// Check last update time.
		$last_update = $this->get_post_meta( 'last_updated' );

		// Update if older than 4 hours.
		if ( empty( $last_update ) || time() > $last_update + ( HOUR_IN_SECONDS * 4 ) ) {
			$this->update_departures();
		}

		// Return the list of departures.
		return $this->departures;
	}

	/**
	 * Get a departure by id.
	 *
	 * @param string|null $id Departure ID.
	 *
	 * @return Departure
	 */
	public function get_departure( string|null $id = null ): Departure {
		// Ensure departures loaded.
		$this->ensure_departures_loaded();

		// Create if not existing.
		if ( ! isset( $this->departures[ $id ] ) ) {
			$departure = new Departure();
			$departure->set_itinerary( $this );
			$this->departures[ $id ] = $departure;
		}

		// Return the departure.
		return $this->departures[ $id ];
	}

	/**
	 * Update departures.
	 *
	 * @return void
	 */
	private function update_departures(): void {
		// Get the Softrip ID and request the departures from the middleware.
		$softrip_id     = strval( $this->get_post_meta( 'softrip_package_id' ) );
		$raw_departures = request_departures( [ $softrip_id ] );

		// Check if is valid.
		if ( $raw_departures instanceof WP_Error ) {
			return;
		}

		// Use the departures for the softrip ID.
		$departures = (array) $raw_departures[ $softrip_id ];

		// Bail if departures are missing.
		if ( ! is_array( $departures['departures'] ) ) {
			return;
		}

		// Go over each departure and create a new Departure post for each.
		foreach ( $departures['departures'] as $raw_departure ) {
			$departure = $this->get_departure( strval( $raw_departure['id'] ) );
			$departure->set( $raw_departure );
			$departure->save();
		}

		// Update last updated timestamp.
		update_post_meta( $this->get_id(), 'last_updated', time() );

		// Reload data.
		bust_post_cache( $this->get_id() );

		// Reload data.
		$this->load( $this->get_id() );
	}
}
