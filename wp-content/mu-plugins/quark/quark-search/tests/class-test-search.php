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

use function Quark\Search\solr_scheme;
use function Quark\Search\Departures\parse_filters;
use function Quark\Search\Departures\get_filters_from_url;
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
				'sort'              => [ 'date-now' ],
				'currency'          => 'USD',
				'destinations'      => [],
				'languages'         => [],
				'itinerary_lengths' => [],
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
				'sort'              => [ 'date-now' ],
				'currency'          => 'USD',
				'destinations'      => [],
				'languages'         => [],
				'itinerary_lengths' => [],
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
				'sort'              => [ 'date-now' ],
				'currency'          => EUR_CURRENCY,
				'destinations'      => [],
				'languages'         => [],
				'itinerary_lengths' => [],
			],
			$filters
		);

		// Add languages and destinations.
		$query_vars['languages']         = '21,44';
		$query_vars['destinations']      = '281,291';
		$query_vars['itinerary_lengths'] = '1,2,3';

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
				'sort'              => [ 'date-now' ],
				'currency'          => EUR_CURRENCY,
				'destinations'      => [
					281,
					291,
				],
				'languages'         => [
					21,
					44,
				],
				'itinerary_lengths' => [
					1,
					2,
					3,
				],
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
				'meta_query'             => [
					[
						'key'     => 'related_expedition',
						'compare' => 'EXISTS',
					],
				],
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
					'relation' => 'AND',
					[
						'key'     => 'related_expedition',
						'compare' => 'EXISTS',
					],
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
						'compare' => 'EXISTS',
					],
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
					'relation' => 'AND',
					[
						'key'     => 'related_expedition',
						'compare' => 'EXISTS',
					],
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
		$set_solr_sort = $class->getMethod( 'set_sorts' );
		$set_solr_sort->invokeArgs( $solr_search, [ [ 'date-now' ] ] );
		$set_solr_sort->invokeArgs( $solr_search, [ [ 'duration-long' ] ] );
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
		$set_solr_sort = $class->getMethod( 'set_sorts' );

		// Without empty sort args.
		$set_solr_sort->invokeArgs( $solr_search, [ [ '' ] ] );
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
		$set_solr_sort = $class->getMethod( 'set_sorts' );

		// Pass price based sorting, but without any currency - should sort by USD.
		$set_solr_sort->invokeArgs( $solr_search, [ [ 'price-low' ] ] );

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
		$set_solr_sort->invokeArgs( $solr_search, [ [ 'price-low' ], 'EUR' ] );

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
		$set_solr_sort->invokeArgs( $solr_search, [ [ 'price-low' ], 'invalid' ] );

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
				'meta_query'             => [
					[
						'key'     => 'related_expedition',
						'compare' => 'EXISTS',
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
						'compare' => 'EXISTS',
					],
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
		$solr_search->set_sorts( [ 'price-low' ], 'EUR' );
		$solr_search->set_seasons( [ '2024', '2025' ] );
		$solr_search->set_destinations( [ 1, 2, 3 ] );
		$solr_search->set_languages( [ 1, 2, 3 ] );
		$solr_search->set_itinerary_lengths( [ 91, 22, 33 ] );

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
					[
						'taxonomy'         => SPOKEN_LANGUAGE_TAXONOMY,
						'field'            => 'term_id',
						'terms'            => [ 1, 2, 3 ],
						'include_children' => false,
					],
				],
				'meta_query'             => [
					'relation' => 'AND',
					[
						'key'     => 'related_expedition',
						'compare' => 'EXISTS',
					],
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
					[
						'key'     => 'duration',
						'value'   => [
							22,
							91,
						],
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC',
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
			'/' . REST_API_NAMESPACE . '/filter-options/by-expedition',
		];
		$actual   = public_rest_api_routes();
		$this->assertEquals( $expected, $actual );
	}
}
