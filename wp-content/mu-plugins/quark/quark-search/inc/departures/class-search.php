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

/**
 * Class Search
 */
class Search {

	/**
	 * Field mapping.
	 *
	 * @var string[] Field mapping.
	 */
	private array $field_mapping = [
		'durations'            => 'duration_i',
		'start_date' => 'start_date_i',
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
			'terms'            => $adventure_option_ids,
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
			'key'     => 'expedition',
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
			'key'     => 'ship',
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
			'compare' => 'IN',
		];
	}

	/**
	 * Set Departure Months.
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

		// If mata query array not present, create it.
		if ( ! is_array( $this->args['meta_query'] ) ) {
			$this->args['meta_query'] = [];
		}

		// Set search by destinations parameters in search arguments.
		foreach ( array_unique( $months ) as $departure ) {
			// Validate departure format.
			preg_match( '/^(?<month>\d{2})-(?<year>\d{4})$/', $departure, $match );

			// Continue if departure format is invalid.
			if ( empty( $match['year'] ) || empty( $match['month'] ) ) {
				continue;
			}

			// Convert departure to timestamp.
			$departure = absint( mktime( 0, 0, 0, absint( $match['month'] ), 01, absint( $match['year'] ) ) );

			// Set departure meta query (date Format: Ymd).
			$this->args['meta_query'][] = [
				'key'     => 'start_date',
				'value'   => [ gmdate( 'Y-m-01', $departure ), gmdate( 'Y-m-t', $departure ) ],
				'type'    => 'DATE',
				'compare' => 'BETWEEN',
			];
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

		// Set search by seasons parameters in search arguments.
		foreach ( array_unique( $seasons ) as $season ) {
			// Set Season meta query.
			$this->args['meta_query'][] = [
				'key'     => 'region_season',
				'value'   => $season,
				'compare' => '=',
			];
		}
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
	 * @param string $sort Sort.
	 * @param string $order Order.
	 *
	 * @return void
	 */
	public function set_sort( string $sort = '', string $order = 'ASC' ): void {
		// Return early if sort is not set or field mapping is not set.
		if ( empty( $sort ) || empty( $this->field_mapping[ $sort ] ) ) {
			return;
		}

		// Set sort.
		$this->sorts[ $this->field_mapping[ $sort ] ] = $order;
	}

	/**
	 * Get search args.
	 *
	 * @return mixed[]
	 */
	public function get_args(): array {
		// Get the args.
		$args = $this->args;

		// Set tax-query relation parameter.
		if ( is_array( $args['tax_query'] ) && ! empty( $args['tax_query'] ) ) {
			$args['tax_query']['relation'] = 'AND';
		} else {
			unset( $args['tax_query'] );
		}

		// Set meta-query relation parameter.
		if ( is_array( $args['meta_query'] ) && ! empty( $args['meta_query'] ) ) {
			$args['meta_query']['relation'] = 'OR';
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

		// Return posts.
		return $filtered_posts;
	}
}
