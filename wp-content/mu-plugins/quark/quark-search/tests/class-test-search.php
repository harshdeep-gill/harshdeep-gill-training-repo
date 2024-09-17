<?php
/**
 * Search test suite.
 *
 * @package quark-search
 */

namespace Quark\Search\Tests;

use WP_UnitTestCase;
use ReflectionClass;
use ReflectionException;

use Quark\Search\Departures\Search;

use function Quark\Departures\bust_post_cache;
use function Quark\Expeditions\bust_post_cache as bust_expedition_post_cache;
use function Quark\Search\Departures\get_destination_and_month_search_filter_data;
use function Quark\Search\Departures\get_destination_search_filter_data;
use function Quark\Search\solr_scheme;
use function Quark\Search\Departures\parse_filters;
use function Quark\Search\Departures\get_filters_from_url;
use function Quark\Search\Departures\get_itinerary_length_search_filter_data;
use function Quark\Search\Departures\get_language_search_filter_data;
use function Quark\Search\Departures\reindex_departures;
use function Quark\Search\public_rest_api_routes;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Localization\CURRENCY_COOKIE;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Search\Departures\FACET_TYPE_FIELD;
use const Quark\Search\Departures\FACET_TYPE_RANGE;
use const Quark\Search\Departures\REINDEX_POST_IDS_OPTION_KEY;
use const Quark\Search\Departures\SCHEDULE_REINDEX_HOOK;
use const Quark\Search\REST_API_NAMESPACE;

/**
 * Class Test_Search.
 */
class Test_Search extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Search\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if filters are registered.
		$this->assertEquals( 10, has_filter( 'solr_scheme', 'Quark\Search\solr_scheme' ) );

		// Test if REST API is registered.
		$this->assertEquals( 10, has_action( 'rest_api_init', 'Quark\Search\register_rest_endpoints' ) );
	}

	/**
	 * Test solr_scheme().
	 *
	 * @covers \Quark\Search\solr_scheme()
	 *
	 * @return void
	 */
	public function test_solr_scheme(): void {
		// Test if scheme is http on local environment.
		if ( 'local' === wp_get_environment_type() ) {
			$this->assertEquals( 'http', solr_scheme( 'https' ) );
		} else {
			// Test if scheme is https on production environment.
			$this->assertEquals( 'https', solr_scheme( 'https' ) );
		}
	}

	/**
	 * Test parse_filters().
	 *
	 * @covers \Quark\Search\Departures\parse_filters()
	 * @covers \Quark\Search\Departures\get_filters_from_url()
	 *
	 * @return void
	 */
	public function test_parse_filters(): void {
		// Redirect to custom URL.
		$query_vars = [
			'adventure_options' => 'tracking,photo',
			'expeditions'       => 123,
			'durations'         => 12,
			'seasons'           => '2021',
			'ships'             => '99,22',
			'months'            => 'may,june',
		];

		// Redirect to custom URL.
		$this->go_to( add_query_arg( $query_vars, home_url() ) );

		// Test if filters are parsed correctly.
		$filters = parse_filters( get_filters_from_url() );

		// Test if filters are parsed correctly.
		$this->assertEquals(
			[
				'adventure_options' => [
					'tracking',
					'photo',
				],
				'expeditions'       => [ 123 ],
				'durations'         => [ 12 ],
				'seasons'           => [ '2021' ],
				'ships'             => [
					'99',
					'22',
				],
				'months'            => [
					'may',
					'june',
				],
				'page'              => 1,
				'posts_per_load'    => 10,
				'sort'              => 'date-now',
				'currency'          => 'USD',
				'destinations'      => [],
			],
			$filters
		);

		// Add invalid currency.
		$_COOKIE[ CURRENCY_COOKIE ] = 'INVALID';

		// Redirect to custom URL.
		$this->go_to( add_query_arg( $query_vars, home_url() ) );

		// Test if filters are parsed correctly.
		$filters = parse_filters( get_filters_from_url() );

		// Test if filters are parsed correctly.
		$this->assertEquals(
			[
				'adventure_options' => [
					'tracking',
					'photo',
				],
				'expeditions'       => [ 123 ],
				'durations'         => [ 12 ],
				'seasons'           => [ '2021' ],
				'ships'             => [
					'99',
					'22',
				],
				'months'            => [
					'may',
					'june',
				],
				'page'              => 1,
				'posts_per_load'    => 10,
				'sort'              => 'date-now',
				'currency'          => 'USD',
				'destinations'      => [],
			],
			$filters
		);

		// Set new currency cookie.
		$_COOKIE[ CURRENCY_COOKIE ] = 'EUR';

		// Redirect to custom URL.
		$this->go_to( add_query_arg( $query_vars, home_url() ) );

		// Test if filters are parsed correctly.
		$filters = parse_filters( get_filters_from_url() );

		// Test if filters are parsed correctly.
		$this->assertEquals(
			[
				'adventure_options' => [
					'tracking',
					'photo',
				],
				'expeditions'       => [ 123 ],
				'durations'         => [ 12 ],
				'seasons'           => [ '2021' ],
				'ships'             => [
					'99',
					'22',
				],
				'months'            => [
					'may',
					'june',
				],
				'page'              => 1,
				'posts_per_load'    => 10,
				'sort'              => 'date-now',
				'currency'          => EUR_CURRENCY,
				'destinations'      => [],
			],
			$filters
		);
	}

	/**
	 * Test Search class.
	 *
	 * @covers \Quark\Search\Departures\Search
	 *
	 * @return void
	 * @throws ReflectionException Reflection exception.
	 */
	public function test_search_class(): void {
		// Include Search class.
		require_once __DIR__ . '/../inc/departures/class-search.php';

		// Test if class exists.
		$this->assertTrue( class_exists( 'Quark\Search\Departures\Search' ) );

		// Test Solr Search default arguments.
		$solr_search = new Search();
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 10,
			],
			$solr_search->get_args(),
			'Failed to test Solr Search default arguments.'
		);

		// Test Solr Search arguments with tax_query ADVENTURE_OPTION_CATEGORY.
		$solr_search = new Search();
		$solr_search->set_posts_per_page( 5 );
		$solr_search->set_page( 3 );
		$solr_search->set_adventure_options( [ 272 ] );
		$solr_search->set_seasons( [ '2024', '2025' ] );
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 5,
				'paged'                  => 3,
				'tax_query'              => [
					[
						'taxonomy'         => ADVENTURE_OPTION_CATEGORY,
						'field'            => 'term_id',
						'terms'            => [ 272 ],
						'include_children' => false,
					],
				],
				'meta_query'             => [
					[
						'key'     => 'region_season',
						'value'   => [ 2024, 2025 ],
						'compare' => 'IN',
					],
				],
			],
			$solr_search->get_args(),
			'Failed to test Solr Search arguments with tax_query ADVENTURE_OPTION_CATEGORY.'
		);

		// Test Solr Search arguments with expeditions.
		$solr_search = new Search();
		$solr_search->set_expeditions( [ 20, 15, 20, 25 ] );
		$solr_search->set_ships( [ 2, 1, 2, 5 ] );
		$solr_search->set_durations( [ [ 12, 15 ] ] );
		$solr_search->set_order( 'DESC' );
		$solr_search->set_order( 'DESC', 'meta_value_num', 'duration' );
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'DESC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 10,
				'orderby'                => 'meta_value_num',
				'meta_key'               => 'duration',
				'meta_query'             => [
					[
						'key'     => 'related_expedition',
						'value'   => array_unique( [ 20, 15, 20, 25 ] ),
						'compare' => 'IN',
					],
					[
						'key'     => 'related_ship',
						'value'   => array_unique( [ 2, 1, 2, 5 ] ),
						'compare' => 'IN',
					],
					[
						'key'     => 'duration',
						'value'   => array_unique( [ 12, 15 ] ),
						'type'    => 'NUMERIC',
						'compare' => 'BETWEEN',
					],
					'relation' => 'AND',
				],
			],
			$solr_search->get_args(),
			'Failed to test Solr Search arguments with meta_query departure months.'
		);

		// Test Solr Search default arguments.
		$solr_search = new Search();
		$solr_search->set_months( [ '10-2024', '04-2025' ] );
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 10,
				'meta_query'             => [
					[
						'relation' => 'OR',
						[
							'key'     => 'start_date',
							'value'   => [ '2024-10-01', '2024-10-31' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'start_date',
							'value'   => [ '2025-04-01', '2025-04-30' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
					],
				],
			],
			$solr_search->get_args(),
			'Failed to test Solr Search default arguments.'
		);

		// Test solr sort arguments.
		$solr_search = new Search();

		// Make private method accessible.
		$class         = new ReflectionClass( $solr_search );
		$set_solr_sort = $class->getMethod( 'set_sort' );
		$set_solr_sort->invokeArgs( $solr_search, [ 'date-now' ] );
		$set_solr_sort->invokeArgs( $solr_search, [ 'duration-long' ] );
		$sorts = $class->getProperty( 'sorts' );

		// Make private property accessible and test.
		$this->assertEquals(
			[
				'duration_i'   => 'desc',
				'start_date_s' => 'asc',
			],
			$sorts->getValue( $solr_search ),
		);

		// Test solr sort with empty args.
		$solr_search = new Search();

		// Make private method accessible.
		$class         = new ReflectionClass( $solr_search );
		$set_solr_sort = $class->getMethod( 'set_sort' );

		// Without empty sort args.
		$set_solr_sort->invokeArgs( $solr_search, [ '' ] );
		$sorts = $class->getProperty( 'sorts' );

		// Assert empty sort.
		$this->assertEquals(
			[],
			$sorts->getValue( $solr_search ),
		);

		// Test sort for currency and price.
		$solr_search = new Search();

		// Make private method accessible.
		$class         = new ReflectionClass( $solr_search );
		$set_solr_sort = $class->getMethod( 'set_sort' );

		// Pass price based sorting, but without any currency - should sort by USD.
		$set_solr_sort->invokeArgs( $solr_search, [ 'price-low' ] );

		// Assert sort by price USD.
		$this->assertEquals(
			[
				'lowest_price_usd_i' => 'asc',
			],
			$sorts->getValue( $solr_search ),
		);

		// Test sort for currency and price for EUR.
		$solr_search = new Search();
		$class       = new ReflectionClass( $solr_search );

		// Pass EUR price based sorting.
		$set_solr_sort->invokeArgs( $solr_search, [ 'price-low', 'EUR' ] );

		// Assert sort by price EUR.
		$this->assertEquals(
			[
				'lowest_price_eur_i' => 'asc',
			],
			$sorts->getValue( $solr_search ),
		);

		// Test sort for currency and price for invalid currency.
		$solr_search = new Search();
		$class       = new ReflectionClass( $solr_search );

		// Pass invalid currency.
		$set_solr_sort->invokeArgs( $solr_search, [ 'price-low', 'invalid' ] );

		// Assert sort by price USD.
		$this->assertEmpty(
			$sorts->getValue( $solr_search ),
		);

		// Test destination search.
		$solr_search = new Search();
		$class       = new ReflectionClass( $solr_search );

		// Set destinations.
		$solr_search->set_destinations( [ 1, 2, 3 ] );

		// Assert destination search.
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 10,
				'tax_query'              => [
					[
						'taxonomy'         => DESTINATION_TAXONOMY,
						'field'            => 'term_id',
						'terms'            => [ 1, 2, 3 ],
						'include_children' => false,
					],
				],
			],
			$solr_search->get_args(),
		);

		// Test with multiple filters.
		$solr_search = new Search();
		$class       = new ReflectionClass( $solr_search );

		// Set multiple filters.
		$solr_search->set_adventure_options( [ 1, 2, 3 ] );
		$solr_search->set_expeditions( [ 4, 5, 6 ] );
		$solr_search->set_ships( [ 7, 8, 9 ] );
		$solr_search->set_durations( [ [ 10, 11 ], [ 12 ] ] );
		$solr_search->set_months( [ '01-2024', '02-2024', '03-2024' ] );

		// Assert multiple filters.
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 10,
				'tax_query'              => [
					[
						'taxonomy'         => ADVENTURE_OPTION_CATEGORY,
						'field'            => 'term_id',
						'terms'            => [ 1, 2, 3 ],
						'include_children' => false,
					],
				],
				'meta_query'             => [
					'relation' => 'AND',
					[
						'key'     => 'related_expedition',
						'value'   => [ 4, 5, 6 ],
						'compare' => 'IN',
					],
					[
						'key'     => 'related_ship',
						'value'   => [ 7, 8, 9 ],
						'compare' => 'IN',
					],
					[
						'relation' => 'OR',
						[
							'key'     => 'duration',
							'value'   => [ 10, 11 ],
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'duration',
							'value'   => [ 12 ],
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN',
						],
					],
					[
						'relation' => 'OR',
						[
							'key'     => 'start_date',
							'value'   => [ '2024-01-01', '2024-01-31' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'start_date',
							'value'   => [ '2024-02-01', '2024-02-29' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'start_date',
							'value'   => [ '2024-03-01', '2024-03-31' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
					],
				],
			],
			$solr_search->get_args()
		);

		// Test with all filters and sort as well.
		$solr_search = new Search();
		$class       = new ReflectionClass( $solr_search );

		// Set multiple filters.
		$solr_search->set_adventure_options( [ 1, 2, 3 ] );
		$solr_search->set_expeditions( [ 4, 5, 6 ] );
		$solr_search->set_ships( [ 7, 8, 9 ] );
		$solr_search->set_durations( [ [ 10, 11 ], [ 12, 15 ] ] );
		$solr_search->set_months( [ '01-2024', '02-2024', '03-2024' ] );
		$solr_search->set_sort( 'price-low', 'EUR' );
		$solr_search->set_seasons( [ '2024', '2025' ] );
		$solr_search->set_destinations( [ 1, 2, 3 ] );

		// Assert multiple filters.
		$this->assertEquals(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'post_status'            => 'publish',
				'solr_integrate'         => true,
				'order'                  => 'ASC',
				'fields'                 => 'ids',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'posts_per_page'         => 10,
				'tax_query'              => [
					'relation' => 'AND',
					[
						'taxonomy'         => ADVENTURE_OPTION_CATEGORY,
						'field'            => 'term_id',
						'terms'            => [ 1, 2, 3 ],
						'include_children' => false,
					],
					[
						'taxonomy'         => DESTINATION_TAXONOMY,
						'field'            => 'term_id',
						'terms'            => [ 1, 2, 3 ],
						'include_children' => false,
					],
				],
				'meta_query'             => [
					'relation' => 'AND',
					[
						'key'     => 'related_expedition',
						'value'   => [ 4, 5, 6 ],
						'compare' => 'IN',
					],
					[
						'key'     => 'related_ship',
						'value'   => [ 7, 8, 9 ],
						'compare' => 'IN',
					],
					[
						'relation' => 'OR',
						[
							'key'     => 'duration',
							'value'   => [ 10, 11 ],
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'duration',
							'value'   => [ 12, 15 ],
							'type'    => 'NUMERIC',
							'compare' => 'BETWEEN',
						],
					],
					[
						'relation' => 'OR',
						[
							'key'     => 'start_date',
							'value'   => [ '2024-01-01', '2024-01-31' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'start_date',
							'value'   => [ '2024-02-01', '2024-02-29' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
						[
							'key'     => 'start_date',
							'value'   => [ '2024-03-01', '2024-03-31' ],
							'type'    => 'DATE',
							'compare' => 'BETWEEN',
						],
					],
					[
						'key'     => 'region_season',
						'value'   => [ 2024, 2025 ],
						'compare' => 'IN',
					],
				],
			],
			$solr_search->get_args()
		);

		/**
		 * Test facets.
		 */

		// Test with no facets.
		$solr_search = new Search();
		$class       = new ReflectionClass( $solr_search );

		// Assert no facets.
		$this->assertEmpty( $solr_search->facet_results );
		$facets = $class->getProperty( 'facet_queries' );
		$facets->setAccessible( true );
		$this->assertEmpty( $facets->getValue( $solr_search ) );

		// Set empty facets.
		$solr_search->set_facets();
		$this->assertEmpty( $solr_search->facet_results );
		$this->assertEmpty( $facets->getValue( $solr_search ) );

		// Set invalid facets.
		$solr_search->set_facets( [ 'invalid' ] );
		$this->assertEmpty( $solr_search->facet_results );
		$this->assertEmpty( $facets->getValue( $solr_search ) );

		// Set facet with key and invalid type.
		$solr_search->set_facets(
			[
				'key'  => 'test',
				'type' => 'string',
			]
		);
		$this->assertEquals(
			[],
			$solr_search->facet_results
		);
		$this->assertEmpty( $facets->getValue( $solr_search ) );

		// Set field type facet.
		$solr_search->set_facets(
			[
				[
					'key'  => 'test',
					'type' => FACET_TYPE_FIELD,
				],
			]
		);
		$this->assertEquals(
			[],
			$solr_search->facet_results
		);
		$this->assertEquals(
			[
				'test' => [
					'key'  => 'test',
					'type' => FACET_TYPE_FIELD,
					'args' => [],
				],
			],
			$facets->getValue( $solr_search )
		);

		// Set range type facet.
		$solr_search->set_facets(
			[
				[
					'key'  => 'test',
					'type' => FACET_TYPE_RANGE,
				],
			]
		);
		$this->assertEquals(
			[],
			$solr_search->facet_results
		);
		$this->assertEquals(
			[
				'test' => [
					'key'  => 'test',
					'type' => FACET_TYPE_RANGE,
					'args' => [],
				],
			],
			$facets->getValue( $solr_search )
		);

		// Set start, end, gap.
		$solr_search->set_facets(
			[
				[
					'key'  => 'test',
					'type' => FACET_TYPE_RANGE,
					'args' => [
						'start' => 0,
						'end'   => 100,
						'gap'   => 10,
					],
				],
			]
		);
		$this->assertEquals(
			[],
			$solr_search->facet_results
		);
		$this->assertEquals(
			[
				'test' => [
					'key'  => 'test',
					'type' => FACET_TYPE_RANGE,
					'args' => [
						'start' => 0,
						'end'   => 100,
						'gap'   => 10,
					],
				],
			],
			$facets->getValue( $solr_search )
		);
	}

	/**
	 * Get destination search filter data.
	 *
	 * @covers \Quark\Search\Departures\get_destination_search_filter_data()
	 *
	 * @return void
	 */
	public function test_get_destination_search_filter_data(): void {
		// Test with no destinations.
		$expected = [];
		$actual   = get_destination_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create destination taxonomy terms.
		$term1 = wp_insert_term( 'Destination 1', DESTINATION_TAXONOMY );
		$this->assertIsArray( $term1 );
		$term1 = get_term( $term1['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term1 );
		$term2 = wp_insert_term( 'Destination 2', DESTINATION_TAXONOMY );
		$this->assertIsArray( $term2 );
		$term2 = get_term( $term2['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term2 );

		// Test without any association of these term to any expedition.
		$expected = [];
		$actual   = get_destination_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create an expedition post.
		$post_id = $this->factory()->post->create(
			[
				'post_type' => EXPEDITION_POST_TYPE,
			]
		);
		$this->assertIsInt( $post_id );

		// Assign destination terms to the expedition post.
		wp_set_object_terms( $post_id, [ $term1['term_id'], $term2['term_id'] ], DESTINATION_TAXONOMY );

		// Test with destination terms assigned to the expedition post, but without any children.
		$expected = [
			[
				'id'       => $term1['term_id'],
				'slug'     => $term1['slug'],
				'name'     => $term1['name'],
				'children' => [],
			],
			[
				'id'       => $term2['term_id'],
				'slug'     => $term2['slug'],
				'name'     => $term2['name'],
				'children' => [],
			],
		];
		$actual   = get_destination_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create some child terms for term1.
		$child_term1 = wp_insert_term( 'Child Destination 1', DESTINATION_TAXONOMY, [ 'parent' => $term1['term_id'] ] );
		$this->assertIsArray( $child_term1 );
		$child_term1 = get_term( $child_term1['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $child_term1 );
		$child_term2 = wp_insert_term( 'Child Destination 2', DESTINATION_TAXONOMY, [ 'parent' => $term1['term_id'] ] );
		$this->assertIsArray( $child_term2 );
		$child_term2 = get_term( $child_term2['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $child_term2 );

		// Create some child terms for term2.
		$child_term3 = wp_insert_term( 'Child Destination 3', DESTINATION_TAXONOMY, [ 'parent' => $term2['term_id'] ] );
		$this->assertIsArray( $child_term3 );
		$child_term3 = get_term( $child_term3['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $child_term3 );

		// Test with destination terms assigned to the expedition post, with children but without any children associated to the expedition post. So, children should still be empty.
		$expected = [
			[
				'id'       => $term1['term_id'],
				'slug'     => $term1['slug'],
				'name'     => $term1['name'],
				'children' => [],
			],
			[
				'id'       => $term2['term_id'],
				'slug'     => $term2['slug'],
				'name'     => $term2['name'],
				'children' => [],
			],
		];
		$actual   = get_destination_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Assign child terms to the expedition post along with parent terms.
		wp_set_object_terms( $post_id, [ $child_term1['term_id'], $child_term2['term_id'], $child_term3['term_id'] ], DESTINATION_TAXONOMY );

		// Test with destination terms assigned to the expedition post, with children and with children associated to the expedition post.
		$expected = [
			[
				'id'       => $term1['term_id'],
				'slug'     => $term1['slug'],
				'name'     => $term1['name'],
				'children' => [
					[
						'id'        => $child_term1['term_id'],
						'slug'      => $child_term1['slug'],
						'name'      => $child_term1['name'],
						'parent_id' => $term1['term_id'],
					],
					[
						'id'        => $child_term2['term_id'],
						'slug'      => $child_term2['slug'],
						'name'      => $child_term2['name'],
						'parent_id' => $term1['term_id'],
					],
				],
			],
			[
				'id'       => $term2['term_id'],
				'slug'     => $term2['slug'],
				'name'     => $term2['name'],
				'children' => [
					[
						'id'        => $child_term3['term_id'],
						'slug'      => $child_term3['slug'],
						'name'      => $child_term3['name'],
						'parent_id' => $term2['term_id'],
					],
				],
			],
		];
		$actual   = get_destination_search_filter_data();
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test getting itinerary length search filter data.
	 *
	 * @covers \Quark\Search\Departures\get_itinerary_length_search_filter_data()
	 *
	 * @return void
	 */
	public function test_get_itinerary_length_search_filter_data(): void {
		// Test with no departure post.
		$expected = [];
		$actual   = get_itinerary_length_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create a departure post.
		$post_id = $this->factory()->post->create(
			[
				'post_type' => DEPARTURE_POST_TYPE,
			]
		);
		$this->assertIsInt( $post_id );

		// Test with no itinerary length.
		$expected = [];
		$actual   = get_itinerary_length_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Set itinerary length meta.
		update_post_meta( $post_id, 'duration', 10 );

		// Flush departure post.
		bust_post_cache( $post_id );

		// Test with itinerary length.
		$expected = [
			'10' => '10 Days',
		];
		$actual   = get_itinerary_length_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create another departure post.
		$post_id = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'duration' => 15,
				],
			]
		);
		$this->assertIsInt( $post_id );

		// Get itinerary length meta.
		$expected = [
			'10' => '10 Days',
			'15' => '15 Days',
		];
		$actual   = get_itinerary_length_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create another departure post.
		$post_id = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'duration' => 5,
				],
			]
		);
		$this->assertIsInt( $post_id );

		// Orders should be ascending.
		$expected = [
			'5'  => '5 Days',
			'10' => '10 Days',
			'15' => '15 Days',
		];
		$actual   = get_itinerary_length_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Test for duplicate.
		$post_id = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'duration' => 5,
				],
			]
		);
		$this->assertIsInt( $post_id );

		// 5 should not be duplicated.
		$expected = [
			'5'  => '5 Days',
			'10' => '10 Days',
			'15' => '15 Days',
		];
		$actual   = get_itinerary_length_search_filter_data();
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Get language search filter data.
	 *
	 * @covers \Quark\Search\Departures\get_language_search_filter_data()
	 *
	 * @return void
	 */
	public function test_get_language_search_filter_data(): void {
		// Test with no departure post.
		$expected = [];
		$actual   = get_language_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create a departure post.
		$post_id = $this->factory()->post->create(
			[
				'post_type' => DEPARTURE_POST_TYPE,
			]
		);
		$this->assertIsInt( $post_id );

		// Test with no language.
		$expected = [];
		$actual   = get_language_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create spoken language terms.
		$term1 = wp_insert_term( 'Language 1', SPOKEN_LANGUAGE_TAXONOMY );
		$this->assertIsArray( $term1 );
		$term1 = get_term( $term1['term_id'], SPOKEN_LANGUAGE_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term1 );
		$term2 = wp_insert_term( 'Language 2', SPOKEN_LANGUAGE_TAXONOMY );
		$this->assertIsArray( $term2 );
		$term2 = get_term( $term2['term_id'], SPOKEN_LANGUAGE_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term2 );

		// Associate spoken language terms to the departure post.
		wp_set_object_terms( $post_id, [ $term1['term_id'], $term2['term_id'] ], SPOKEN_LANGUAGE_TAXONOMY );

		// Test with language.
		$expected = [
			$term1['term_id'] => $term1['name'],
			$term2['term_id'] => $term2['name'],
		];
		$actual   = get_language_search_filter_data();
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test if posts are tracked to be re-indexed.
	 * On update of expedition and itinerary posts, the post ids should be tracked to be re-indexed.
	 *
	 * @covers \Quark\Search\Departures\track_posts_to_be_reindexed()
	 * @covers \Quark\Search\Departures\reindex_departures()
	 *
	 * @return void
	 */
	public function test_track_posts_to_be_reindexed(): void {
		// Clear any existing tracked post ids.
		delete_option( REINDEX_POST_IDS_OPTION_KEY );
		wp_unschedule_hook( SCHEDULE_REINDEX_HOOK );

		// Create a non-supported post.
		$post_id = $this->factory()->post->create();
		$this->assertIsInt( $post_id );

		// Update the post.
		wp_update_post(
			[
				'ID'         => $post_id,
				'post_title' => 'Test Post',
			]
		);

		// Option should still be empty.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertEmpty( $option );

		// Cron should not be scheduled.
		$timestamp = wp_next_scheduled( SCHEDULE_REINDEX_HOOK );
		$this->assertFalse( $timestamp );

		// Create a expedition post.
		$expedition_post_id = $this->factory()->post->create(
			[
				'post_type' => EXPEDITION_POST_TYPE,
			]
		);
		$this->assertIsInt( $expedition_post_id );

		// Update the expedition post.
		wp_update_post(
			[
				'ID'         => $expedition_post_id,
				'post_title' => 'Test Expedition Post',
			]
		);

		// Option should not be empty anymore.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertNotEmpty( $option );
		$this->assertContains( $expedition_post_id, $option );

		// Cron should be scheduled.
		$timestamp = wp_next_scheduled( SCHEDULE_REINDEX_HOOK );
		$this->assertNotFalse( $timestamp );

		// Time should be equal to 1 hour.
		$this->assertEquals( time() + HOUR_IN_SECONDS, $timestamp );

		// Try updating the expedition post again.
		wp_update_post(
			[
				'ID'         => $expedition_post_id,
				'post_title' => 'Test Expedition Post Updated',
			]
		);

		// Option should not contain duplicate post id.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertNotEmpty( $option );
		$this->assertCount( 1, $option );
		$this->assertContains( $expedition_post_id, $option );

		// Cron should not be scheduled again.
		$timestamp = wp_next_scheduled( SCHEDULE_REINDEX_HOOK );
		$this->assertNotFalse( $timestamp );

		// Time should be under or equal to 1 hour.
		$this->assertLessThanOrEqual( time() + HOUR_IN_SECONDS, $timestamp );

		// Create another expedition post.
		$expedition_post_id2 = $this->factory()->post->create(
			[
				'post_type' => EXPEDITION_POST_TYPE,
			]
		);
		$this->assertIsInt( $expedition_post_id2 );

		// Option should not be updated on creation of post.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertNotEmpty( $option );
		$this->assertCount( 1, $option );
		$this->assertContains( $expedition_post_id, $option );

		// Update the expedition post.
		wp_update_post(
			[
				'ID'         => $expedition_post_id2,
				'post_title' => 'Test Expedition Post 2',
			]
		);

		// Option should be updated with new post id.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertNotEmpty( $option );
		$this->assertCount( 2, $option );
		$this->assertContains( $expedition_post_id, $option );
		$this->assertContains( $expedition_post_id2, $option );

		// Create itinerary post.
		$itinerary_post_id = $this->factory()->post->create(
			[
				'post_type' => ITINERARY_POST_TYPE,
			]
		);
		$this->assertIsInt( $itinerary_post_id );

		// Update the itinerary post.
		wp_update_post(
			[
				'ID'         => $itinerary_post_id,
				'post_title' => 'Test Itinerary Post',
			]
		);

		// Option should not be updated on creation of post.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertNotEmpty( $option );
		$this->assertCount( 3, $option );
		$this->assertEquals( [ $expedition_post_id, $expedition_post_id2, $itinerary_post_id ], $option );

		// Cron should be scheduled.
		$timestamp = wp_next_scheduled( SCHEDULE_REINDEX_HOOK );
		$this->assertNotFalse( $timestamp );

		// Time should be equal to 1 hour.
		$this->assertLessThanOrEqual( time() + HOUR_IN_SECONDS, $timestamp );

		// Update the itinerary post.
		wp_update_post(
			[
				'ID'         => $itinerary_post_id,
				'post_title' => 'Test Itinerary Post Updated',
			]
		);

		// Option should not contain duplicate post id.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertNotEmpty( $option );
		$this->assertCount( 3, $option );
		$this->assertEquals( [ $expedition_post_id, $expedition_post_id2, $itinerary_post_id ], $option );

		// Execute the re-indexing.
		reindex_departures();

		// Option should be empty after re-indexing.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertEmpty( $option );

		// Action for re-index initiation should be fired.
		$this->assertTrue( did_action( 'quark_search_reindex_initiated' ) > 0 );

		// Action for re-index completion should be fired.
		$this->assertTrue( did_action( 'quark_search_reindex_completed' ) > 0 );

		// There should be no more cron scheduled.
		$timestamp = wp_next_scheduled( SCHEDULE_REINDEX_HOOK );
		$this->assertFalse( $timestamp );

		// Add a non-supported post id to option and check if it is removed.
		update_option( REINDEX_POST_IDS_OPTION_KEY, [ $post_id ] );

		// Execute the re-indexing.
		reindex_departures();

		// Option should be empty after re-indexing.
		$option = get_option( REINDEX_POST_IDS_OPTION_KEY, [] );
		$this->assertIsArray( $option );
		$this->assertEmpty( $option );

		// Action for re-index initiation should be again fired.
		$this->assertTrue( did_action( 'quark_search_reindex_initiated' ) > 1 );

		// Action for re-index completion should be again fired.
		$this->assertTrue( did_action( 'quark_search_reindex_completed' ) > 1 );

		// Action for re-index failed should be fired.
		$this->assertTrue( did_action( 'quark_search_reindex_failed' ) > 0 );

		// There should be no more cron scheduled.
		$timestamp = wp_next_scheduled( SCHEDULE_REINDEX_HOOK );
		$this->assertFalse( $timestamp );
	}

	/**
	 * Test if correct REST API routes are public.
	 *
	 * @covers \Quark\Search\public_rest_api_routes()
	 *
	 * @return void
	 */
	public function test_public_rest_api_routes(): void {
		// Test.
		$expected = [
			'/' . REST_API_NAMESPACE . '/filter-options/by-destination-and-month',
		];
		$actual   = public_rest_api_routes();
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Get destination and month filter options.
	 *
	 * @covers \Quark\Search\Departures\get_destination_and_month_search_filter_data()
	 *
	 * @return void
	 */
	public function test_get_destination_and_month_search_filter_data(): void {
		// Default expected.
		$expected_default = [
			'destinations' => [],
			'months'       => [],
		];

		// Test with no departure post.
		$actual = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected_default, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type' => DEPARTURE_POST_TYPE,
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Test with no destination and month.
		$actual = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected_default, $actual );

		// Add month meta.
		update_post_meta( $departure_post_id, 'start_date', '2026-01-01' );

		// Bust cache.
		bust_post_cache( $departure_post_id );

		// Test with month but no destination.
		$expected = [
			'destinations' => [],
			'months'       => [
				'01-2026' => 'January 2026',
			],
		];
		$actual   = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected_default, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create(
			[
				'post_type' => EXPEDITION_POST_TYPE,
			]
		);
		$this->assertIsInt( $expedition_post_id );

		// Update related expedition meta.
		update_post_meta( $departure_post_id, 'related_expedition', $expedition_post_id );

		// Bust cache.
		bust_post_cache( $departure_post_id );

		// Test with month and destination.
		$expected = [
			'destinations' => [],
			'months'       => [
				[
					'label' => 'January 2026',
					'value' => '01-2026',
				],
			],
		];
		$actual   = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create destination taxonomy terms.
		$term1 = wp_insert_term( 'Destination 1', DESTINATION_TAXONOMY );
		$this->assertIsArray( $term1 );
		$term1 = get_term( $term1['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term1 );

		// Assign destination terms to the expedition post.
		wp_set_object_terms( $expedition_post_id, [ $term1['term_id'] ], DESTINATION_TAXONOMY );

		// Bust cache.
		bust_post_cache( $departure_post_id );
		bust_expedition_post_cache( $expedition_post_id );

		// Test with month and destination.
		$expected = [
			'destinations' => [
				[
					'label'    => $term1['name'],
					'id'       => $term1['term_id'],
					'value'    => $term1['term_id'],
					'image_id' => 0,
					'children' => [],
				],
			],
			'months'       => [
				[
					'label' => 'January 2026',
					'value' => '01-2026',
				],
			],
		];
		$actual   = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create one more destination taxonomy term.
		$term2 = wp_insert_term( 'Destination 2', DESTINATION_TAXONOMY );
		$this->assertIsArray( $term2 );
		$term2 = get_term( $term2['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term2 );

		// Create some media post.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );

		// Update term1 with image.
		update_term_meta( $term1['term_id'], 'destination_image', $media_post_id1 );

		// Assign destination terms to the expedition post.
		wp_set_object_terms( $expedition_post_id, [ $term1['term_id'], $term2['term_id'] ], DESTINATION_TAXONOMY );

		// Bust cache.
		bust_post_cache( $departure_post_id );
		bust_expedition_post_cache( $expedition_post_id );

		// Test with month and destination.
		$expected = [
			'destinations' => [
				[
					'label'    => $term1['name'],
					'id'       => $term1['term_id'],
					'value'    => $term1['term_id'],
					'image_id' => $media_post_id1,
					'children' => [],
				],
				[
					'label'    => $term2['name'],
					'id'       => $term2['term_id'],
					'value'    => $term2['term_id'],
					'image_id' => 0,
					'children' => [],
				],
			],
			'months'       => [
				[
					'label' => 'January 2026',
					'value' => '01-2026',
				],
			],
		];
		$actual   = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create child terms of term1 and term2.
		$child_term1 = wp_insert_term( 'Child Destination 1', DESTINATION_TAXONOMY, [ 'parent' => $term1['term_id'] ] );
		$this->assertIsArray( $child_term1 );
		$child_term1 = get_term( $child_term1['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $child_term1 );
		$child_term2 = wp_insert_term( 'Child Destination 2', DESTINATION_TAXONOMY, [ 'parent' => $term2['term_id'] ] );
		$this->assertIsArray( $child_term2 );
		$child_term2 = get_term( $child_term2['term_id'], DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $child_term2 );

		// Add image to child terms.
		update_term_meta( $child_term1['term_id'], 'destination_image', $media_post_id1 );
		update_term_meta( $child_term2['term_id'], 'destination_image', $media_post_id1 );

		// Assign child terms to the expedition post.
		wp_set_object_terms( $expedition_post_id, [ $term1['term_id'], $term2['term_id'], $child_term1['term_id'], $child_term2['term_id'] ], DESTINATION_TAXONOMY );

		// Bust cache.
		bust_post_cache( $departure_post_id );
		bust_expedition_post_cache( $expedition_post_id );

		// Test with month and destination.
		$expected = [
			'destinations' => [
				[
					'label'    => $term1['name'],
					'id'       => $term1['term_id'],
					'value'    => $term1['term_id'],
					'image_id' => $media_post_id1,
					'children' => [
						[
							'label'     => $child_term1['name'],
							'id'        => $child_term1['term_id'],
							'value'     => $child_term1['term_id'],
							'parent_id' => $term1['term_id'],
							'image_id'  => $media_post_id1,
						],
					],
				],
				[
					'label'    => $term2['name'],
					'id'       => $term2['term_id'],
					'value'    => $term2['term_id'],
					'image_id' => $media_post_id1,
					'children' => [
						[
							'label'     => $child_term2['name'],
							'id'        => $child_term2['term_id'],
							'value'     => $child_term2['term_id'],
							'parent_id' => $term2['term_id'],
							'image_id'  => $media_post_id1,
						],
					],
				],
			],
			'months'       => [
				[
					'label' => 'January 2026',
					'value' => '01-2026',
				],
			],
		];
		$actual   = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Create another expedition post.
		$expedition_post_id2 = $this->factory()->post->create(
			[
				'post_type' => EXPEDITION_POST_TYPE,
			]
		);
		$this->assertIsInt( $expedition_post_id2 );

		// Create another departure post.
		$departure_post_id2 = $this->factory()->post->create(
			[
				'post_type' => DEPARTURE_POST_TYPE,
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Update related expedition meta.
		update_post_meta( $departure_post_id2, 'related_expedition', $expedition_post_id2 );

		// Update start date.
		update_post_meta( $departure_post_id2, 'start_date', '2026-02-01' );

		// Bust cache.
		bust_post_cache( $departure_post_id2 );
		bust_expedition_post_cache( $expedition_post_id2 );

		// Test with month and destination.
		$expected = [
			'destinations' => [
				[
					'label'    => $term1['name'],
					'id'       => $term1['term_id'],
					'value'    => $term1['term_id'],
					'image_id' => $media_post_id1,
					'children' => [
						[
							'label'     => $child_term1['name'],
							'id'        => $child_term1['term_id'],
							'value'     => $child_term1['term_id'],
							'parent_id' => $term1['term_id'],
							'image_id'  => $media_post_id1,
						],
					],
				],
				[
					'label'    => $term2['name'],
					'id'       => $term2['term_id'],
					'value'    => $term2['term_id'],
					'image_id' => $media_post_id1,
					'children' => [
						[
							'label'     => $child_term2['name'],
							'id'        => $child_term2['term_id'],
							'value'     => $child_term2['term_id'],
							'parent_id' => $term2['term_id'],
							'image_id'  => $media_post_id1,
						],
					],
				],
			],
			'months'       => [
				[
					'label' => 'January 2026',
					'value' => '01-2026',
				],
				[
					'label' => 'February 2026',
					'value' => '02-2026',
				],
			],
		];
		$actual   = get_destination_and_month_search_filter_data();
		$this->assertEquals( $expected, $actual );

		// Test filter by month.
		$expected = [
			'destinations' => [
				[
					'label'    => $term1['name'],
					'id'       => $term1['term_id'],
					'value'    => $term1['term_id'],
					'image_id' => $media_post_id1,
					'children' => [
						[
							'label'     => $child_term1['name'],
							'id'        => $child_term1['term_id'],
							'value'     => $child_term1['term_id'],
							'parent_id' => $term1['term_id'],
							'image_id'  => $media_post_id1,
						],
					],
				],
				[
					'label'    => $term2['name'],
					'id'       => $term2['term_id'],
					'value'    => $term2['term_id'],
					'image_id' => $media_post_id1,
					'children' => [
						[
							'label'     => $child_term2['name'],
							'id'        => $child_term2['term_id'],
							'value'     => $child_term2['term_id'],
							'parent_id' => $term2['term_id'],
							'image_id'  => $media_post_id1,
						],
					],
				],
			],
			'months'       => [
				[
					'label' => 'January 2026',
					'value' => '01-2026',
				],
			],
		];
		$actual   = get_destination_and_month_search_filter_data( 0, '01-2026' );
		$this->assertEquals( $expected, $actual );

		/**
		 * We can't test by destination term id as destination is linked to departure in Solr but not in WP.
		 */
	}
}
