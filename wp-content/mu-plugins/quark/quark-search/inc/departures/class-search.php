<?php
/**
 * Search class.
 *
 * @package quark-search
 */

namespace Quark\Search\Departures;

use WP_Query;
use Solarium\QueryType\Select\Query\Query;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;

/**
 * Class Search
 */
class Search {

	/**
	 * Field mapping.
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
	 * } Field mapping.
	 */
	private array $field_mapping = [
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
	 * @param int[] $adventure_option_ids Adventure Option IDs.
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
	 * @param int[] $expeditions Expeditions IDs.
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
	 * @param int[] $ships Ship IDs.
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
	 *
	 * @param int[] $durations Duration.
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

		// Set durations meta query.
		$this->args['meta_query'][] = [
			'key'     => 'duration',
			'value'   => array_unique( $durations ),
			'type'    => 'NUMERIC',
			'compare' => 'BETWEEN',
		];
	}

	/**
	 * Set Departure Months.
	 * The meta query is set as OR relation.
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
	 * Set season.
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

		// Initialize meta query.
		$meta_query = [];

		// Set search by seasons parameters in search arguments.
		foreach ( $seasons as $season ) {
			// Set Season meta query.
			$meta_query[] = [
				'key'     => 'region_season',
				'value'   => $season,
				'compare' => '=',
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
	 * Set destinations.
	 *
	 * @param int[] $destination_ids Destination IDs.
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
	public function set_sort( string $sort = '', string $currency = 'USD' ): void {
		// Return early if sort is not set or field mapping is not set.
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
		if ( empty( $this->field_mapping[ $sort ] ) ) {
			return;
		}

		// Set sort.
		$this->sorts[ $this->field_mapping[ $sort ]['key'] ] = $this->field_mapping[ $sort ]['order'];
	}

	/**
	 * Get search args.
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
	 * Filter solr sort.
	 *
	 * @param Query|null $query Query.
	 *
	 * @return Query|null
	 */
	public function filter_solr_sort( Query $query = null ): Query|null {
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
		add_filter( 'solr_query', [ $this, 'filter_solr_sort' ] );

		// Run the search and return results.
		$this->query_object = new WP_Query( $args );

		// Remove filter to avoid adding filter multiple times.
		remove_filter( 'solr_query', [ $this, 'filter_solr_sort' ] );

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

		// Return posts.
		return $filtered_posts;
	}
}
