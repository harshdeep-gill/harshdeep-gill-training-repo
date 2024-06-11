<?php
/**
 * Itinerary Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use Quark\Softrip\Departure;
use WP_Error;
use WP_Post;

use function Quark\Itineraries\get;
use function Quark\Ships\code_to_id;

/**
 * Itinerary API.
 */
class Itinerary {
	/**
	 * Holds the itinerary data.
	 *
	 * @var array{
	 *     post: WP_Post|null,
	 *     post_meta: mixed[],
	 *     post_taxonomies: mixed[]
	 * }
	 */
	private array $itinerary;

	/**
	 * Constructor.
	 *
	 * @param int $post_id The itinerary post ID.
	 */
	public function __construct( int $post_id = 0 ) {
		// Get the Itinerary.
		$this->itinerary = get( $post_id );
	}

	/**
	 * Get the itinerary post id.
	 *
	 * @return int
	 */
	public function id(): int {
		// If valid post, return ID.
		if ( $this->itinerary['post'] instanceof WP_Post ) {
			return $this->itinerary['post']->ID;
		}

		// Return a 0 if not.
		return 0;
	}

	/**
	 * Check if the post is valid.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		// Check if $itinerary is correct.
		return $this->itinerary['post'] instanceof WP_Post;
	}

	/**
	 * Get post meta from the post object.
	 *
	 * @param string $name The metadata to call from the post object.
	 *
	 * @return mixed
	 */
	public function post_meta( string $name = '' ): mixed {
		// Check if the post is valid.
		if ( ! $this->is_valid() ) {
			// Not valid post, so bail with expected empty meta.
			return '';
		}

		// if not specified, return all.
		if ( empty( $name ) ) {
			return $this->itinerary['post_meta'];
		}

		// Return post meta.
		return $this->itinerary['post_meta'][ $name ] ?? '';
	}

	/**
	 * Get itinerary departures.
	 *
	 * @return Departure[]
	 */
	public function departures(): array {
		// @todo Departure objects.
		return [];
	}

	/**
	 * Build departures.
	 *
	 * @return bool
	 */
	public function build_departures(): bool {
		// Check if departures have been built already.
		$has_built = $this->post_meta( 'built_departures' );

		// Return if already built.
		if ( ! empty( $has_built ) ) {
			return true;
		}

		// Get the Softrip ID and request the departures from the middleware.
		$softrip_id     = strval( $this->post_meta( 'softrip_package_id' ) );
		$raw_departures = request_departures( [ $softrip_id ] );
		$departures     = $raw_departures[ $softrip_id ];

		// Go over each departure and create a new Departure post for each.
		foreach ( $departures['departures'] as $departure ) {
			$this->create_departure( $departure );
		}

		// Return a true value.
		return true;
	}

	/**
	 * Create a departure.
	 *
	 * @param array<string, string> $data The departure data.
	 *
	 * @return bool|WP_Error
	 */
	public function create_departure( array $data = [] ): bool|WP_Error {
		// Set the post structure.
		$args = [
			'post_type'    => 'qrk_departure',
			'post_title'   => $data['id'],
			'post_content' => '',
			'post_status'  => 'publish',
			'post_parent'  => $this->id(),
			'meta_input'   => [
				'related_expedition'   => $this->post_meta( 'related_expedition' ),
				'related_ship'         => strval( code_to_id( $data['shipCode'] ) ),
				'softrip_departure_id' => $data['id'],
				'softrip_package_id'   => $data['packageCode'],
				'departure_start_date' => $data['startDate'],
				'departure_end_date'   => $data['endDate'],
				'duration'             => $data['duration'],
				'itinerary'            => $this->id(),
			],
		];

		// Create the departure post item.
		$departure_id = wp_insert_post( $args );

		// Return if error.
		if ( $departure_id instanceof WP_Error ) {
			return $departure_id;
		}

		// @Todo: Create a new Departure object and return it.
		return true;
	}
}
