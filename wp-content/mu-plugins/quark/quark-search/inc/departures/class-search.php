<?php
/**
 * Search class.
 *
 * @package quark-search
 */

namespace Quark\Search\Departures;

use WP_Query;
use Solarium\QueryType\Select\Query\Query;
use SolrPower_WP_Query;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Localization\DEFAULT_CURRENCY;

const FACET_TYPE_FIELD = 'field';
const FACET_TYPE_RANGE = 'range';

const FACET_FIELD_TYPES = [
	FACET_TYPE_FIELD,
	FACET_TYPE_RANGE,
];

/**
 * Class Search
 */
class Search {

	/**
	 * Sort Options.
	 *
	 * @var array{
	 *     'date-now': array{
	 *         key: string,
	 *         order: string
	 *     },
	 *     'date-later': array{
	 *         key: string,
	 *         order: string
	 *     },
	 *     'duration-short': array{
	 *         key: string,
	 *         order: string
	 *     },
	 *     'duration-long': array{
	 *         key: string,
	 *         order: string
	 *     },
	 *    'price-low-usd': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-high-usd': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-low-gbp': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-high-gbp': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-low-aud': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-high-aud': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-low-cad': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-high-cad': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-low-eur': array{
	 *         key: string,
	 *         order: string
	 *    },
	 *    'price-high-eur': array{
	 *         key: string,
	 *         order: string
	 *    },
	 * }
	 */
	private array $sort_options = [
		'date-now'       => [
			'key'   => 'start_date_s',
			'order' => 'asc',
		],
		'date-later'     => [
			'key'   => 'start_date_s',
			'order' => 'desc',
		],
		'duration-short' => [
			'key'   => 'duration_i',
			'order' => 'asc',
		],
		'duration-long'  => [
			'key'   => 'duration_i',
			'order' => 'desc',
		],
		'price-low-usd'  => [
			'key'   => 'lowest_price_usd_i',
			'order' => 'asc',
		],
		'price-high-usd' => [
			'key'   => 'lowest_price_usd_i',
			'order' => 'desc',
		],
		'price-low-gbp'  => [
			'key'   => 'lowest_price_gbp_i',
			'order' => 'asc',
		],
		'price-high-gbp' => [
			'key'   => 'lowest_price_gbp_i',
			'order' => 'desc',
		],
		'price-low-eur'  => [
			'key'   => 'lowest_price_eur_i',
			'order' => 'asc',
		],
		'price-high-eur' => [
			'key'   => 'lowest_price_eur_i',
			'order' => 'desc',
		],
		'price-low-aud'  => [
			'key'   => 'lowest_price_aud_i',
			'order' => 'asc',
		],
		'price-high-aud' => [
			'key'   => 'lowest_price_aud_i',
			'order' => 'desc',
		],
		'price-low-cad'  => [
			'key'   => 'lowest_price_cad_i',
			'order' => 'asc',
		],
		'price-high-cad' => [
			'key'   => 'lowest_price_cad_i',
			'order' => 'desc',
		],
	];

	/**
	 * Post per page.
	 *
	 * @var int Post per page.
	 */
	public int $posts_per_page = 10;

	/**
	 * Search results.
	 *
	 * @var int[] Search results.
	 */
	public array $results = [];

	/**
	 * Has next page.
	 *
	 * @var bool Has next page.
	 */
	public bool $has_next_page = false;

	/**
	 * Number of results fetched.
	 *
	 * @var int Results count.
	 */
	public int $result_count = 0;

	/**
	 * Remaining count.
	 *
	 * @var int Remaining count.
	 */
	public int $remaining_count = 0;

	/**
	 * Current page number.
	 *
	 * @var int Current page number.
	 */
	public int $current_page = 1;

	/**
	 * Next page number.
	 *
	 * @var int Next page number.
	 */
	public int $next_page = 1;

	/**
	 * Facet queries.
	 *
	 * @var array{}|array<string, array{key: string, type: string, args: mixed[]}> Facets.
	 */
	protected array $facet_queries = [];

	/**
	 * Query object.
	 *
	 * @var WP_Query|null Query object.
	 */
	protected ?WP_Query $query_object = null;

	/**
	 * Sorts
	 *
	 * @var array<string, string> Sorts.
	 */
	protected array $sorts = [];

	/**
	 * Facet results
	 *
	 * @var mixed[] Facet values.
	 */
	public array $facet_results = [];

	/**
	 * Search arguments.
	 *
	 * @var mixed[] Arguments.
	 */
	protected array $args = [
		'post_type'              => DEPARTURE_POST_TYPE,
		'post_status'            => 'publish',
		'solr_integrate'         => true,
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'tax_query'              => [],
		'meta_query'             => [],
	];

	/**
	 * Set posts per page.
	 *
	 * @param int $posts_per_page Post per page.
	 *
	 * @return void
	 */
	public function set_posts_per_page( int $posts_per_page = 10 ): void {
		// Set post per page.
		$this->posts_per_page = $posts_per_page;
	}

	/**
	 * Set page.
	 *
	 * @param int $page Page.
	 *
	 * @return void
	 */
	public function set_page( int $page = 1 ): void {
		// Cap minimum page value to 1.
		$page = max( 1, $page );

		// Set page numbers.
		$this->current_page = $page;
		$this->next_page    = $this->current_page + 1;

		// Set page parameter.
		$this->args['paged'] = $page;
	}

	/**
	 * Set Adventure Options.
	 *
	 * @param int[] $adventure_option_ids Adventure Option category taxonomy IDs.
	 *
	 * @return void
	 */
	public function set_adventure_options( array $adventure_option_ids = [] ): void {
		// Return early if no Adventure Option IDs are passed.
		if ( empty( $adventure_option_ids ) ) {
			return;
		}

		// If tax query array not present, create it.
		if ( ! is_array( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = [];
		}

		// Set search by destinations parameters in search arguments.
		$this->args['tax_query'][] = [
			'taxonomy'         => ADVENTURE_OPTION_CATEGORY,
			'field'            => 'term_id',
			'terms'            => array_unique( $adventure_option_ids ),
			'include_children' => false,
		];
	}

	/**
	 * Set Expeditions.
	 *
	 * @param int[] $expeditions Expedition post IDs.
	 *
	 * @return void
	 */
	public function set_expeditions( array $expeditions = [] ): void {
		// Return early if no Expeditions are passed.
		if ( empty( $expeditions ) ) {
			return;
		}

		// If meta query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Set Expedition meta query.
		$this->args['meta_query'][] = [
			'key'     => 'related_expedition',
			'value'   => array_unique( $expeditions ),
			'compare' => 'IN',
		];
	}

	/**
	 * Set Ships.
	 *
	 * @param int[] $ships Ship post IDs.
	 *
	 * @return void
	 */
	public function set_ships( array $ships = [] ): void {
		// Return early if no Ship are passed.
		if ( empty( $ships ) ) {
			return;
		}

		// If meta query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Set Ship meta query.
		$this->args['meta_query'][] = [
			'key'     => 'related_ship',
			'value'   => array_unique( $ships ),
			'compare' => 'IN',
		];
	}

	/**
	 * Set Duration.
	 * Example - [ [1, 7], [8, 14] ].
	 *
	 * @param array<int, int[]> $durations Duration.
	 *
	 * @return void
	 */
	public function set_durations( array $durations = [] ): void {
		// Return early if no durations are passed.
		if ( empty( $durations ) ) {
			return;
		}

		// If meta query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Initialize meta query.
		$meta_query = [];

		// Loop through durations.
		foreach ( $durations as $duration ) {
			// Set duration meta query.
			$meta_query[] = [
				'key'     => 'duration',
				'value'   => $duration,
				'type'    => 'NUMERIC',
				'compare' => 'BETWEEN',
			];
		}

		// Add relation if more than one meta query.
		if ( 1 < count( $meta_query ) ) {
			$meta_query['relation'] = 'OR';

			// Set meta query.
			$this->args['meta_query'][] = $meta_query;
		} else {
			// Set meta query.
			$this->args['meta_query'][] = $meta_query[0];
		}
	}

	/**
	 * Set Departure Months.
	 * The meta query is set as OR relation.
	 * Example - [ '2021-01', '2021-02' ].
	 *
	 * @param string[] $months Months. Format: Y-m.
	 *
	 * @return void
	 */
	public function set_months( array $months = [] ): void {
		// Return early if months are not set.
		if ( empty( $months ) ) {
			return;
		}

		// If meta query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Remove duplicate months.
		$months = array_unique( $months );

		// Initialize meta query.
		$meta_query = [];

		// Set search by destinations parameters in search arguments.
		foreach ( $months as $departure ) {
			// Validate departure format.
			preg_match( '/^(?<month>\d{2})-(?<year>\d{4})$/', $departure, $match );

			// Continue if departure format is invalid.
			if ( empty( $match['year'] ) || empty( $match['month'] ) ) {
				continue;
			}

			// Convert departure to timestamp.
			$departure = absint( mktime( 0, 0, 0, absint( $match['month'] ), 01, absint( $match['year'] ) ) );

			// Set departure meta query (date Format: Ymd).
			$meta_query[] = [
				'key'     => 'start_date',
				'value'   => [ gmdate( 'Y-m-01', $departure ), gmdate( 'Y-m-t', $departure ) ],
				'type'    => 'DATE',
				'compare' => 'BETWEEN',
			];
		}

		// Add relation if more than one meta query.
		if ( 1 < count( $meta_query ) ) {
			$meta_query['relation'] = 'OR';

			// Set meta query.
			$this->args['meta_query'][] = $meta_query;
		} else {
			// Set meta query.
			$this->args['meta_query'][] = $meta_query[0];
		}
	}

	/**
	 * Set region and season.
	 * Example - [ 'ANT-2025', 'ACT-2026' ].
	 *
	 * @param string[] $seasons Season slug.
	 *
	 * @return void
	 */
	public function set_seasons( array $seasons = [] ): void {
		// Return early if no seasons are passed.
		if ( empty( $seasons ) ) {
			return;
		}

		// If tax query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Unique seasons.
		$seasons = array_unique( $seasons );

		// Set meta query.
		$this->args['meta_query'][] = [
			'key'     => 'region_season',
			'value'   => $seasons,
			'compare' => 'IN',
		];
	}

	/**
	 * Set destinations.
	 *
	 * @param int[] $destination_ids Destination taxonomy IDs.
	 *
	 * @return void
	 */
	public function set_destinations( array $destination_ids = [] ): void {
		// Return early if no destinations are passed.
		if ( empty( $destination_ids ) ) {
			return;
		}

		// If tax query array not present, create it.
		if ( ! is_array( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = [];
		}

		// Set search by destinations parameters in search arguments.
		$this->args['tax_query'][] = [
			'taxonomy'         => DESTINATION_TAXONOMY,
			'field'            => 'term_id',
			'terms'            => array_unique( $destination_ids ),
			'include_children' => false,
		];
	}

	/**
	 * Set languages.
	 *
	 * @param int[] $language_ids Language taxonomy IDs.
	 *
	 * @return void
	 */
	public function set_languages( array $language_ids = [] ): void {
		// Return early if no languages are passed.
		if ( empty( $language_ids ) ) {
			return;
		}

		// If tax query array not present, create it.
		if ( ! is_array( $this->args['tax_query'] ) ) {
			$this->args['tax_query'] = [];
		}

		// Set search by languages parameters in search arguments.
		$this->args['tax_query'][] = [
			'taxonomy'         => SPOKEN_LANGUAGE_TAXONOMY,
			'field'            => 'term_id',
			'terms'            => array_unique( $language_ids ),
			'include_children' => false,
		];
	}

	/**
	 * Set itinerary length.
	 *
	 * @param int[] $itinerary_lengths Itinerary length.
	 *
	 * @return void
	 */
	public function set_itinerary_lengths( array $itinerary_lengths = [] ): void {
		// Return early if no itinerary lengths are passed.
		if ( empty( $itinerary_lengths ) || 2 > count( $itinerary_lengths ) ) {
			return;
		}

		// Sort.
		ksort( $itinerary_lengths );

		// Start and end.
		$start = $itinerary_lengths[0];
		$end   = absint( end( $itinerary_lengths ) );

		// If meta query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Set meta query.
		$this->args['meta_query'][] = [
			'key'     => 'itinerary_length',
			'value'   => [
				$start,
				$end,
			],
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		];
	}

	/**
	 * Set Order.
	 *
	 * @param string $order Order.
	 * @param string $order_by Order by.
	 * @param string $meta_key Meta key.
	 *
	 * @return void
	 */
	public function set_order( string $order = 'ASC', string $order_by = 'ids', string $meta_key = '' ): void {
		// Set order.
		$this->args['order'] = $order;

		// Set order by.
		$this->args['orderby'] = $order_by;

		// Set meta key.
		if ( ! empty( $meta_key ) ) {
			$this->args['meta_key'] = $meta_key;
		}
	}

	/**
	 * Set Sort.
	 *
	 * @param string $sort     Sort.
	 * @param string $currency Currency.
	 *
	 * @return void
	 */
	public function set_sort( string $sort = '', string $currency = DEFAULT_CURRENCY ): void {
		// Return early if sort is empty.
		if ( empty( $sort ) ) {
			return;
		}

		// Check if sort is for price.
		if ( strpos( $sort, 'price-' ) !== false ) {
			// Set currency.
			$currency = strtolower( $currency );

			// Update the sort key.
			$sort = $sort . '-' . $currency;
		}

		// Check if sort is valid.
		if ( empty( $this->sort_options[ $sort ] ) ) {
			return;
		}

		// Set sort.
		$this->sorts[ $this->sort_options[ $sort ]['key'] ] = $this->sort_options[ $sort ]['order'];
	}

	/**
	 * Set facets.
	 * Example: [ [ 'key' => 'duration_i', 'type' => 'range', 'args' => [ 'start' => 1, 'end' => 7, 'gap' => 7 ] ] ].
	 *
	 * @param mixed[] $facets Facets.
	 *
	 * @return void
	 */
	public function set_facets( array $facets = [] ): void {
		// Bail if facets are empty.
		if ( empty( $facets ) || ! is_array( $facets ) ) {
			return;
		}

		// Loop through facets.
		foreach ( $facets as $facet ) {
			// Validate facet.
			if ( ! is_array( $facet ) || empty( $facet['key'] ) || empty( $facet['type'] ) ) {
				continue;
			}

			// Set facet key and type.
			$facet_key  = strval( $facet['key'] );
			$facet_type = strval( $facet['type'] );

			// Validate type.
			if ( ! in_array( $facet['type'], FACET_FIELD_TYPES, true ) ) {
				continue;
			}

			// Set facet.
			$this->facet_queries[ $facet_key ] = [
				'key'  => $facet_key,
				'type' => $facet_type,
				'args' => $facet['args'] ?? [],
			];
		}
	}

	/**
	 * Get search query args.
	 * Combines multiple tax queries and meta queries using AND relation.
	 * This is to ensure that different filters are combined using AND.
	 * While the same filter is combined using OR/IN in their respective functions.
	 *
	 * @return mixed[]
	 */
	public function get_args(): array {
		// Get the args.
		$args = $this->args;

		// Set tax-query relation parameter. Various filters (tax queries) are combined using AND.
		if ( is_array( $args['tax_query'] ) && ! empty( $args['tax_query'] ) ) {
			// Add relation parameter if more than one tax query.
			if ( 1 < count( $args['tax_query'] ) ) {
				$args['tax_query']['relation'] = 'AND';
			}
		} else {
			unset( $args['tax_query'] );
		}

		// Set meta-query relation parameter. Various filters (meta queries) are combined using AND.
		if ( is_array( $args['meta_query'] ) && ! empty( $args['meta_query'] ) ) {
			// Add relation parameter if more than one meta query.
			if ( 1 < count( $args['meta_query'] ) ) {
				$args['meta_query']['relation'] = 'AND';
			}
		} else {
			unset( $args['meta_query'] );
		}

		// Set post per page.
		$args['posts_per_page'] = $this->posts_per_page;

		// Set current page number if not set.
		if ( ! isset( $args['paged'] ) ) {
			$this->current_page = 1;
		}

		// Set next page number.
		$this->next_page = $this->current_page + 1;

		// Return the query args.
		return $args;
	}

	/**
	 * Modify Solr query.
	 *
	 * @param Query|null $query Query.
	 *
	 * @return Query|null
	 */
	public function modify_solr_query( Query $query = null ): Query|null {
		// Return early if query is not set.
		if ( ! $query instanceof Query ) {
			return $query;
		}

		// Set sorts.
		if ( ! empty( $this->sorts ) ) {
			// Merge existing sort with new to preserve previous ones.
			$query->setSorts(
				array_merge(
					$this->sorts,
					$query->getSorts()
				)
			);
		}

		/**
		 * Add facet queries if available.
		 *
		 * 1. Loop through all facets.
		 * 2. Determine their type and set them using facet set instance.
		 */

		// Return early if no facets are set.
		if ( empty( $this->facet_queries ) ) {
			return $query;
		}

		// Get facet set instance.
		$facet_set = $query->getFacetSet();

		// Loop through facets and set them.
		foreach ( $this->facet_queries as $facet ) {
			// Validate facet.
			if ( empty( $facet['key'] ) || empty( $facet['type'] ) ) {
				continue;
			}

			// Validate type.
			if ( ! in_array( $facet['type'], FACET_FIELD_TYPES, true ) ) {
				continue;
			}

			// Get facet field instance.
			switch ( $facet['type'] ) {
				// Set range facet.
				case 'range':
					// Validate if start, end and gap are set. No barrier on data type.
					if ( ! is_array( $facet['args'] ) || empty( $facet['args']['start'] ) || empty( $facet['args']['end'] ) || empty( $facet['args']['gap'] ) ) {
						break;
					}

					// Get facet range instance.
					$facet_field = $facet_set->createFacetRange( $facet['key'] )->setField( $facet['key'] );

					// Set facet start, end and gap.
					$facet_field->setStart( $facet['args']['start'] )->setEnd( $facet['args']['end'] )->setGap( $facet['args']['gap'] );
					break;

				// Set field facet.
				default:
					// Get facet field instance.
					$facet_field = $facet_set->createFacetField( $facet['key'] )->setField( $facet['key'] );
					break;
			}
		}

		// Return the query.
		return $query;
	}

	/**
	 * Run the search.
	 *
	 * @return int[]
	 */
	public function search(): array {
		// Get query args.
		$args = $this->get_args();

		// Add filter to modify solr query before executing WP_Query.
		add_filter( 'solr_query', [ $this, 'modify_solr_query' ] );

		// Run the search and return results.
		$this->query_object = new WP_Query( $args );

		// Remove filter to avoid adding filter multiple times.
		remove_filter( 'solr_query', [ $this, 'modify_solr_query' ] );

		// Ensure all of those are integers, as we expect return type to be list of IDs.
		$filtered_posts = array_map( 'absint', $this->query_object->posts );

		// Set has next page.
		$this->has_next_page = $this->query_object->max_num_pages > ( ! empty( $this->query_object->query_vars['paged'] ) ? $this->query_object->query_vars['paged'] : 1 );

		// Set next page to zero if there is no other page.
		if ( ! $this->has_next_page ) {
			$this->next_page = 0;
		}

		// Store the result count.
		$this->result_count = $this->query_object->found_posts;

		// Set the result.
		$this->results = $filtered_posts;

		// Count number of posts for previous load.
		$previous_load_count = ( $this->current_page - 1 ) * $this->posts_per_page;

		// Set the remaining count, including previous pagination.
		$this->remaining_count = $this->result_count - $previous_load_count - count( $filtered_posts );

		// Check if class exists.
		if ( ! class_exists( 'SolrPower_WP_Query' ) ) {
			return $filtered_posts;
		}

		/**
		 * Set facet results.
		 *
		 * 1. Get Solr facets from the executed Solr query.
		 * 2. Loop through Solr facets.
		 * 3. Validate facet key and facet result object.
		 * 4. Get facet values.
		 * Format: [
		 *   'duration_i' => [
		 *      'key'    => 'duration_i',
		 *      'values' => [
		 *         1 => 23, // 1-7 days. Key = filter value, Value = count
		 *         8 => 12, // 8-14 days
		 *       ]
		 *   ],
		 * ]
		 */

		// Solr query instance.
		$solr_query = SolrPower_WP_Query::get_instance();

		// Get Solr facets from query.
		$solr_facets = $solr_query->facets;

		// Bail if facets are empty.
		if ( empty( $solr_facets ) ) {
			return $filtered_posts;
		}

		// Initialize facets.
		$facet_results = [];

		// Loop through Solr facets.
		foreach ( $solr_facets as $facet_key => $facet_result_object ) {
			// Validate facet key.
			if ( empty( $facet_key ) || ! in_array( $facet_key, array_keys( $this->facet_queries ), true ) ) {
				continue;
			}

			// Validate facet result object.
			if ( ! method_exists( $facet_result_object, 'getValues' ) ) {
				continue;
			}

			// Get facet values. The next line is a false positive.
			$facet_values = $facet_result_object->getValues(); // @phpstan-ignore-line

			// Continue if facet values are empty.
			if ( empty( $facet_values ) ) {
				continue;
			}

			// Set facet values.
			$facet_results[ $facet_key ] = [
				'key'    => $facet_key,
				'values' => $facet_values,
			];
		}

		// Set facet results.
		$this->facet_results = $facet_results;

		// Return posts.
		return $filtered_posts;
	}
}
