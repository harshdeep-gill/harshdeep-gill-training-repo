<?php
/**
 * Cabin Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use function Quark\CabinCategories\get as get_cabin_category;
use function Quark\ShipDecks\get as get_deck;

use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY as CABIN_CLASS;

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
	 * Get the cabin departure.
	 *
	 * @return Departure
	 */
	public function get_departure(): Departure {
		// Return the departure object.
		return $this->departure;
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
	 * @param array<string, mixed> $data The data to format.
	 *
	 * @return array{
	 *      id: mixed,
	 *      title: string,
	 *      departure: int,
	 *      cabin_category: int,
	 *      package_id: string,
	 *      departure_id: string,
	 *      ship_id: string,
	 *      cabin_category_id: string,
	 *      availability_status: string,
	 *      spaces_available: string,
	 *      occupancies?: array{},
	 * }
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
			'title'               => strval( $data['id'] ),
			'departure'           => $this->departure->get_id(),
			'cabin_category'      => $this->get_id(),
			'package_id'          => strval( $this->departure->get_post_meta( 'softrip_package_id' ) ),
			'departure_id'        => strval( $this->departure->get_post_meta( 'softrip_departure_id' ) ),
			'ship_id'             => strval( $this->departure->get_post_meta( 'ship_id' ) ),
			'cabin_category_id'   => strval( $data['code'] ),
			'availability_status' => 'C',
			'spaces_available'    => '0',
			'occupancies'         => is_array( $data['occupancies'] ) ? $data['occupancies'] : [],
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

	/**
	 * Get the lowest price per person for the cabin.
	 *
	 * @param string $currency The currency code to get.
	 *
	 * @return array{
	 *   discounted_price: float,
	 *   original_price: float,
	 * }
	 */
	public function get_lowest_price( string $currency = 'USD' ): array {
		// Set up the lowest variable.
		$lowest         = 0;
		$original_price = 0;

		// Iterate over the occupancies.
		foreach ( $this->get_occupancies() as $occupancy ) {
			// Get the price per person.
			$test_price = $occupancy->get_price_per_person( $currency, true );

			// Check if lowest is set and is lower than the previous price.
			if ( empty( $lowest ) || $lowest > $test_price ) {
				// Use the price as it's lower.
				$lowest         = $test_price;
				$original_price = $occupancy->get_price_per_person( $currency );
			}
		}

		// Return the lowest found.
		return [
			'discounted_price' => $lowest,
			'original_price'   => $original_price,
		];
	}

	/**
	 * Get the lowest prices per person for the cabin.
	 *
	 * @return array<string, array<string, float>>
	 */
	public function get_lowest_prices(): array {
		// Set up the lowest variable.
		$lowest = [];

		// Set default array.
		$currencies = [];

		// Iterate over the occupancies.
		foreach ( $this->get_occupancies() as $occupancy ) {
			// currencies.
			$currencies = array_merge( $occupancy->get_currencies(), $currencies );
		}

		// Get unique items only.
		$currencies = array_unique( $currencies );

		// Iterate over currencies.
		foreach ( $currencies as $currency ) {
			$lowest[ $currency ] = $this->get_lowest_price( $currency );
		}

		// Return the lowest found.
		return $lowest;
	}

	/**
	 * Get cabin availability description.
	 *
	 * @return string
	 */
	public function get_availability_description(): string {
		// Set the types.
		$types = [
			'O' => 'Open',
			'S' => 'Sold out',
			'N' => 'No display',
			'C' => 'Unavailable',
		];

		// Get status and space.
		$status = $this->get_entry_data( 'availability_status' );
		$spaces = $this->get_entry_data( 'spaces_available' );

		// If is O and no spaces, return 'Please call'.
		if ( empty( $spaces ) && 'O' === $status ) {
			return 'Please call';
		}

		// Return type from list.
		return $types[ $status ] ?? '';
	}

	/**
	 * Get the cabin class.
	 *
	 * @return string
	 */
	public function get_cabin_class(): string {
		// Set default string.
		$class = '';

		// Get taxonomy.
		$taxonomy_data = $this->get_data()['post_taxonomies'][ CABIN_CLASS ] ?? [];

		// Check if the class taxonomy is set.
		if ( is_array( $taxonomy_data ) && ! empty( $taxonomy_data ) ) {
			// Get the name of the first item.
			$item  = array_shift( $taxonomy_data );
			$class = $item['name'];
		}

		// Return the class.
		return $class;
	}

	/**
	 * Get the cabin location.
	 *
	 * @return string
	 */
	public function get_location(): string {
		// Set default.
		$location = '';

		// Get decks.
		$decks = $this->get_post_meta( 'related_decks' );

		// Check we have decks and is array.
		if ( ! empty( $decks ) && is_array( $decks ) ) {
			// Get the first deck.
			$deck_id = array_shift( $decks );
			$deck    = get_deck( $deck_id );

			// Get the deck name.
			if ( isset( $deck['post_meta']['deck_name'] ) ) {
				$location = strval( $deck['post_meta']['deck_name'] );
			}
		}

		// Return the location.
		return $location;
	}

	/**
	 * Get the size using from_size and to_size.
	 *
	 * @param string $from_size The from size.
	 * @param string $to_size   The to size.
	 *
	 * @return string
	 */
	public function validate_the_sizes( string $from_size = '', string $to_size = '' ): string {
		// Validate the sizes.
		$from_size = empty( $from_size ) ? 0 : $from_size;
		$to_size   = empty( $to_size ) ? 0 : $to_size;

		// Check if both sizes are available and are numeric.
		if ( $from_size && $to_size ) {
			// Return the range.
			return $from_size . '-' . $to_size;
		} elseif ( $from_size ) {
			// Return the from size.
			return $from_size;
		} elseif ( $to_size ) {
			// Return the to size.
			return $to_size;
		}

		// Return empty if no sizes are available.
		return '';
	}

	/**
	 * Get cabin pax range.
	 *
	 * @return string
	 */
	public function get_pax_range(): string {
		// Get range values.
		$from = $this->get_post_meta( 'cabin_occupancy_pax_range_from' );
		$to   = $this->get_post_meta( 'cabin_occupancy_pax_range_to' );

		// validate the sizes.
		return $this->validate_the_sizes( strval( $from ), strval( $to ) );
	}

	/**
	 * Get cabin size.
	 *
	 * @return string
	 */
	public function get_size(): string {
		// Get value.
		$from_size = $this->get_post_meta( 'cabin_category_size_range_from' );
		$to_size   = $this->get_post_meta( 'cabin_category_size_range_to' );

		// validate the sizes.
		return $this->validate_the_sizes( strval( $from_size ), strval( $to_size ) );
	}
}
