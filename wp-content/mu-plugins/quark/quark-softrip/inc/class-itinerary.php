<?php
/**
 * Itinerary Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Error;
use WP_Query;
use WP_Post;

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
				'post_parent'            => $this->get_id(),
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

		// Return the list of departures.
		return $this->departures;
	}

	/**
	 * Get itinerary published departures.
	 *
	 * @return Departure[]
	 */
	public function get_published_departures(): array {
		// Ensure departures loaded.
		$this->ensure_departures_loaded();

		// Return the list of departures.
		return array_filter(
			$this->departures,
			function ( Departure $departure ) {
				return 'publish' === $departure->get_status();
			}
		);
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
	 * @param mixed[] $departures Departures data from Softrip to update with.
	 *
	 * @return void
	 */
	public function update_departures( array $departures = [] ): void {
		// If no data is supplied, attempt to get it.
		if ( empty( $departures ) ) {
			// Get the Softrip ID and request the departures from the middleware.
			$softrip_id     = strval( $this->get_post_meta( 'softrip_package_id' ) );
			$raw_departures = request_departures( [ $softrip_id ] );

			// Check if is valid.
			if ( $raw_departures instanceof WP_Error ) {
				return;
			}

			// Use the departures for the softrip ID.
			$departures = (array) $raw_departures[ $softrip_id ];
		}

		// Bail if departures are missing.
		if ( ! is_array( $departures['departures'] ) ) {
			return;
		}

		// Go over each departure and create a new Departure post for each.
		foreach ( $departures['departures'] as $raw_departure ) {
			$departure = $this->get_departure( strval( $raw_departure['id'] ) );
			$departure->set( $raw_departure, true );
		}

		// Update last updated timestamp.
		update_post_meta( $this->get_id(), 'last_updated', time() );

		// Reload data.
		bust_post_cache( $this->get_id() );

		// Reload data.
		$this->load( $this->get_id() );
	}

	/**
	 * Get the lowest price per person for the itinerary.
	 *
	 * @param string $currency The currency code to get.
	 *
	 * @return float
	 */
	public function get_lowest_price( string $currency = 'USD' ): float {
		// Set up the lowest variable.
		$lowest = 0;

		// Iterate over the departures.
		foreach ( $this->get_departures() as $departure ) {
			// Get the price per person.
			$test_price = $departure->get_lowest_price( $currency );

			// Check if lowest is set and is lower than the previous price.
			if ( ! empty( $test_price ) && ( empty( $lowest ) || $lowest > $test_price ) ) {
				// Use the price as it's lower.
				$lowest = $test_price;
			}
		}

		// Return the lowest found.
		return $lowest;
	}

	/**
	 * Get the related ships.
	 *
	 * @return mixed[]
	 */
	public function get_related_ships(): array {
		// Init Ships var.
		$ships = [];

		// Iterate over the departures.
		foreach ( $this->get_departures() as $departure ) {
			// Get the ship.
			$ship = $departure->get_ship();

			// Check if ship is valid.
			if ( empty( $ship['post'] ) || ! $ship['post'] instanceof WP_Post ) {
				continue;
			}

			// Add the ship to the list.
			$ships[ $ship['post']->ID ] = $ship;
		}

		// Return the ships.
		return $ships;
	}

	/**
	 * Get the starting date for the Itinerary.
	 *
	 * @return string
	 */
	public function get_starting_date(): string {
		// Set up the lowest variable.
		$start_date = '';

		// Iterate over the departures.
		foreach ( $this->get_departures() as $departure ) {
			// Get the price per person.
			$test_date = $departure->get_starting_date();

			// Check if start date is set.
			if ( empty( $test_date ) ) {
				continue;
			}

			// Check if start date is set and is newer than the previous date.
			if ( empty( $start_date ) || strtotime( $start_date ) < strtotime( $test_date ) ) {
				// Use the date as it's newer.
				$start_date = $test_date;
			}
		}

		// Return the newest found.
		return $start_date;
	}

	/**
	 * Get the ending date for the Itinerary.
	 *
	 * @return string
	 */
	public function get_ending_date(): string {
		// Set up the lowest variable.
		$end_date = '';

		// Iterate over the departures.
		foreach ( $this->get_departures() as $departure ) {
			// Get the price per person.
			$test_date = $departure->get_ending_date();

			// Check if start date is set.
			if ( empty( $test_date ) ) {
				continue;
			}

			// Check if start date is set and is newer than the previous date.
			if ( empty( $end_date ) || strtotime( $end_date ) < strtotime( $test_date ) ) {
				// Use the date as it's newer.
				$end_date = $test_date;
			}
		}

		// Return the newest found.
		return $end_date;
	}
}
