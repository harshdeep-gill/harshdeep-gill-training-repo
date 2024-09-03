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

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Core\EUR_CURRENCY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;

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
		$query_vars['currency'] = 'invalid';

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

		// Test for other currency.
		$query_vars['currency'] = EUR_CURRENCY;

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
					'relation' => 'AND',
					[
						'taxonomy'         => ADVENTURE_OPTION_CATEGORY,
						'field'            => 'term_id',
						'terms'            => [ 272 ],
						'include_children' => false,
					],
				],
				'meta_query'             => [
					[
						'relation' => 'OR',
						[
							'key'     => 'region_season',
							'value'   => 2024,
							'compare' => '=',
						],
						[
							'key'     => 'region_season',
							'value'   => 2025,
							'compare' => '=',
						],
					],
					'relation' => 'AND',
				],
			],
			$solr_search->get_args(),
			'Failed to test Solr Search arguments with tax_query ADVENTURE_OPTION_CATEGORY.'
		);

		// Test Solr Search arguments with expeditions.
		$solr_search = new Search();
		$solr_search->set_expeditions( [ 20, 15, 20, 25 ] );
		$solr_search->set_ships( [ 2, 1, 2, 5 ] );
		$solr_search->set_durations( [ 12, 15 ] );
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
					'relation' => 'AND',
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
					'relation' => 'AND',
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
	}
}
