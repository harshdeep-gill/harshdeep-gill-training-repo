<?php
/**
 * Departure test suite.
 *
 * @package quark-departures
 */

namespace Quark\Departures\Tests;

use WP_Post;
use WP_Term;
use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Departures\bust_card_data_cache_on_expedition_update;
use function Quark\Departures\bust_post_cache;
use function Quark\Departures\get;
use function Quark\Departures\get_paid_adventure_options;
use function Quark\Departures\get_languages;
use function Quark\Departures\get_promotion_tags;
use function Quark\Departures\get_start_end_departure_date;
use function Quark\Departures\sort_promotions_by_type_and_value;
use function Quark\Localization\get_currencies;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\do_sync;

use const Quark\Departures\POST_TYPE;
use const Quark\Departures\PROMOTION_TAG;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;

/**
 * Class Test_Departure.
 */
class Test_Departures extends Softrip_TestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Departures\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_action( 'init', 'Quark\Departures\register_departure_post_type' ) );
		$this->assertEquals( 10, has_action( 'init', 'Quark\Departures\register_spoken_language_taxonomy' ) );
		$this->assertEquals( 10, has_filter( 'qe_adventure_options_taxonomy_post_types', 'Quark\Departures\opt_in' ) );
		$this->assertEquals( 10, has_filter( 'qe_spoken_language_taxonomy_post_types', 'Quark\Departures\opt_in' ) );
	}

	/**
	 * Test get().
	 *
	 * @covers \Quark\Departures\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Set qrk_spoken_language taxonomy term.
		$spoken_language_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
				'name'     => 'spoken_language_1',
			]
		);

		// Assert term is created.
		$this->assertTrue( $spoken_language_term instanceof WP_Term );

		// Assign term to post.
		wp_set_object_terms( $post_1->ID, $spoken_language_term->term_id, SPOKEN_LANGUAGE_TAXONOMY );

		// Bust post cache after setting term.
		bust_post_cache( $post_1->ID );

		// Get post.
		$the_post = get( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				'post'            => $post_1,
				'post_meta'       => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
				'post_taxonomies' => [
					SPOKEN_LANGUAGE_TAXONOMY => [
						[
							'term_id'     => strval( $spoken_language_term->term_id ),
							'name'        => $spoken_language_term->name,
							'slug'        => $spoken_language_term->slug,
							'taxonomy'    => $spoken_language_term->taxonomy,
							'description' => $spoken_language_term->description,
							'parent'      => $spoken_language_term->parent,
							'term_group'  => $spoken_language_term->term_group,
						],
					],
				],
			],
			$the_post
		);

		// clean up.
		wp_delete_post( $post_1->ID, true );
	}

	/**
	 * Test get_paid_adventure_options.
	 *
	 * @covers \Quark\Departures\get_paid_adventure_options()
	 *
	 * @return void
	 */
	public function test_get_paid_adventure_options(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get post.
		$paid_adventure_options = get_paid_adventure_options( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEmpty( $paid_adventure_options );

		// Create term of Adventure Options.
		$adventure_option_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option_1',
			]
		);

		// Create term of Adventure Options.
		$adventure_option_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option_2',
			]
		);

		// Create term of Adventure Options.
		$adventure_option_term_3 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option_3',
			]
		);

		// Assert term is created.
		$this->assertTrue( $adventure_option_term_1 instanceof WP_Term );
		$this->assertTrue( $adventure_option_term_2 instanceof WP_Term );
		$this->assertTrue( $adventure_option_term_3 instanceof WP_Term );

		// Set post meta.
		add_post_meta(
			$post_1->ID,
			'adventure_options',
			[
				$adventure_option_term_1->term_id,
				$adventure_option_term_3->term_id,
			]
		);

		// Bust post cache after setting post meta.
		bust_post_cache( $post_1->ID );

		// Get paid adventure options.
		$paid_adventure_options = get_paid_adventure_options( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				$adventure_option_term_1->term_id => $adventure_option_term_1->name,
				$adventure_option_term_3->term_id => $adventure_option_term_3->name,
			],
			$paid_adventure_options
		);
	}

	/**
	 * Test get_languages.
	 *
	 * @covers \Quark\Departures\get_languages()
	 *
	 * @return void
	 */
	public function test_get_languages(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get languages.
		$languages = get_languages( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEmpty( $languages );

		// Create term of Spoken Language.
		$spoken_language_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
				'name'     => 'spoken_language_1',
			]
		);

		// Create term of Spoken Language.
		$spoken_language_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
				'name'     => 'spoken_language_2',
			]
		);

		// Create term of Spoken Language.
		$spoken_language_term_3 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
				'name'     => 'spoken_language_3',
			]
		);

		// Assert term is created.
		$this->assertTrue( $spoken_language_term_1 instanceof WP_Term );
		$this->assertTrue( $spoken_language_term_2 instanceof WP_Term );
		$this->assertTrue( $spoken_language_term_3 instanceof WP_Term );

		// Set terms.
		wp_set_object_terms(
			$post_1->ID,
			[
				$spoken_language_term_3->term_id,
				$spoken_language_term_1->term_id,
			],
			SPOKEN_LANGUAGE_TAXONOMY
		);

		// Bust post cache.
		bust_post_cache( $post_1->ID );

		// Get languages.
		$languages = get_languages( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				$spoken_language_term_1->name,
				$spoken_language_term_3->name,
			],
			$languages
		);
	}

	/**
	 * Test get_promotion_tags.
	 *
	 * @covers \Quark\Departures\get_promotion_tags()
	 *
	 * @return void
	 */
	public function test_get_promotion_tags(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get promotion tags.
		$promotion_tags = get_promotion_tags( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEmpty( $promotion_tags );

		// Create term of Promotion Tags.
		$promotion_tag_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => PROMOTION_TAG,
				'name'     => 'promotion_tag_1',
			]
		);

		// Create term of Promotion Tags.
		$promotion_tag_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => PROMOTION_TAG,
				'name'     => 'promotion_tag_2',
			]
		);

		// Create term of Promotion Tags.
		$promotion_tag_term_3 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => PROMOTION_TAG,
				'name'     => 'promotion_tag_3',
			]
		);

		// Assert term is created.
		$this->assertTrue( $promotion_tag_term_1 instanceof WP_Term );
		$this->assertTrue( $promotion_tag_term_2 instanceof WP_Term );
		$this->assertTrue( $promotion_tag_term_3 instanceof WP_Term );

		// Set terms.
		wp_set_object_terms(
			$post_1->ID,
			[
				$promotion_tag_term_3->term_id,
				$promotion_tag_term_1->term_id,
			],
			PROMOTION_TAG
		);

		// Add non-array promotion tag to post meta.
		update_post_meta(
			$post_1->ID,
			'related_promotion_tags',
			'abc'
		);

		// Flush cache.
		bust_post_cache( $post_1->ID );

		// Get promotion tags.
		$promotion_tags = get_promotion_tags( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals( [], $promotion_tags );

		// Add array promotion tag to post meta.
		update_post_meta(
			$post_1->ID,
			'related_promotion_tags',
			[
				$promotion_tag_term_1->term_id,
				$promotion_tag_term_3->term_id,
			]
		);

		// Flush cache.
		bust_post_cache( $post_1->ID );

		// Get promotion tags.
		$promotion_tags = get_promotion_tags( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				$promotion_tag_term_1->name,
				$promotion_tag_term_3->name,
			],
			$promotion_tags
		);
	}

	/**
	 * Test get_start_end_departure_date.
	 *
	 * @covers \Quark\Departures\get_start_end_departure_date()
	 *
	 * @return void
	 */
	public function test_get_start_end_departure_date(): void {
		// Create a departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Departure Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'start_date' => '2021-01-01',
					'end_date'   => '2021-01-10',
				],
			]
		);

		// Make sure post is created.
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Get start and end date.
		$start_end_date = get_start_end_departure_date( $departure_post->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals( 'January 1-10, 2021', $start_end_date );

		// Update dates.
		update_post_meta( $departure_post->ID, 'start_date', '2021-02-25' );
		update_post_meta( $departure_post->ID, 'end_date', '2021-03-15' );

		// Bust post cache after updating post meta.
		bust_post_cache( $departure_post->ID );

		// Get start and end date.
		$start_end_date = get_start_end_departure_date( $departure_post->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals( 'February 25 - March 15, 2021', $start_end_date );

		// Update dates.
		update_post_meta( $departure_post->ID, 'start_date', '2020-12-21' );
		update_post_meta( $departure_post->ID, 'end_date', '2021-01-05' );

		// Bust post cache after updating post meta.
		bust_post_cache( $departure_post->ID );

		// Get start and end date.
		$start_end_date = get_start_end_departure_date( $departure_post->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals( 'December 21, 2020 - January 5, 2021', $start_end_date );
	}

	/**
	 * Test bust_card_data_cache.
	 *
	 * @covers \Quark\Departures\bust_card_data_cache()
	 *
	 * @return void
	 */
	public function test_bust_card_data_cache(): void {
		// Create a departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Departure Post',
				'post_status' => 'publish',
			]
		);

		// Make sure post is created.
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Get currency list.
		$currencies = get_currencies();

		// Loop through currencies.
		foreach ( $currencies as $currency ) {
			// Set cache key.
			$cache_key = 'departure_card_data_' . $departure_post->ID . '_' . $currency;

			// Set cache data.
			wp_cache_set( $cache_key, 'test data_' . $departure_post->ID . $currency, POST_TYPE );
		}

		// Verify cache data is set.
		foreach ( $currencies as $currency ) {
			// Set cache key.
			$cache_key = 'departure_card_data_' . $departure_post->ID . '_' . $currency;

			// Get cache data.
			$cache_data = wp_cache_get( $cache_key, POST_TYPE );

			// Assert cache data is set.
			$this->assertEquals( 'test data_' . $departure_post->ID . $currency, $cache_data );
		}

		// Bust cache.
		bust_post_cache( $departure_post->ID );

		// Verify cache data is deleted.
		foreach ( $currencies as $currency ) {
			// Set cache key.
			$cache_key = 'departure_card_data_' . $departure_post->ID . '_' . $currency;

			// Get cache data.
			$cache_data = wp_cache_get( $cache_key, POST_TYPE );

			// Assert cache data is deleted.
			$this->assertFalse( $cache_data );
		}
	}

	/**
	 * Test bust card cache on itinerary and expedition update.
	 *
	 * @covers \Quark\Departures\bust_card_data_cache_on_itinerary_update()
	 * @covers \Quark\Departures\bust_card_data_cache_on_expedition_update()
	 *
	 * @return void
	 */
	public function test_bust_card_data_cache_on_expedition_update(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync departure posts.
		do_sync();

		// Get Itinerary posts.
		$itinerary_query_args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'meta_query'             => [
				[
					'key'     => 'softrip_package_code',
					'value'   => [
						'ABC-123',
						'PQR-345',
						'JKL-012',
					],
					'compare' => 'IN',
				],
			],
		];

		// Get Itinerary posts.
		$itinerary_posts = get_posts( $itinerary_query_args );

		// Assert fetched posts are not empty.
		$this->assertEquals( 3, count( $itinerary_posts ) );

		// Create an Expedition post.
		$expedition_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_title'  => 'Test Expedition Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_itineraries' => $itinerary_posts,
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $expedition_post instanceof WP_Post );

		// Get Departure posts by itinerary.
		foreach ( $itinerary_posts as $itinerary_post_id ) {
			$departure_post_ids = get_departures_by_itinerary( absint( $itinerary_post_id ) );

			// Loop through departure posts.
			foreach ( $departure_post_ids as $departure_post_id ) {
				// Get currency list.
				$currencies = get_currencies();

				// Loop through currencies.
				foreach ( $currencies as $currency ) {
					// Set cache key.
					$cache_key = 'departure_card_data_' . $departure_post_id . '_' . $currency;

					// Set cache data.
					wp_cache_set( $cache_key, 'test data_' . $departure_post_id . $currency, POST_TYPE );

					// Verify cache data is set.
					$cache_data = wp_cache_get( $cache_key, POST_TYPE );
					$this->assertEquals( 'test data_' . $departure_post_id . $currency, $cache_data );
				}
			}
		}

		// Bust cache using expedition post.
		bust_card_data_cache_on_expedition_update( $expedition_post->ID );

		// Verify cache data is set.
		foreach ( $itinerary_posts as $itinerary_post_id ) {
			$departure_post_ids = get_departures_by_itinerary( absint( $itinerary_post_id ) );

			// Loop through departure posts.
			foreach ( $departure_post_ids as $departure_post_id ) {
				// Get currency list.
				$currencies = get_currencies();

				// Loop through currencies.
				foreach ( $currencies as $currency ) {
					// Set cache key.
					$cache_key = 'departure_card_data_' . $departure_post_id . '_' . $currency;

					// Get cache data.
					$cache_data = wp_cache_get( $cache_key, POST_TYPE );

					// Assert cache data is empty.
					$this->assertEmpty( $cache_data );
				}
			}
		}
	}

	/**
	 * Test sort promotions by type and value.
	 *
	 * @covers \Quark\Departures\sort_promotions_by_type_and_value()
	 *
	 * @return void
	 */
	public function test_sort_promotions_by_type_and_value(): void {
		// Promotions data.
		$promotions_data = [];

		// Empty promotions data.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertEmpty( $sorted_promotions );

		// Invalid promotions data.
		$promotions_data = [
			1,
			2,
			3,
		];

		// Invalid promotions data.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame( $promotions_data, $sorted_promotions );

		// Array promotions data but invalid keys.
		$promotions_data = [
			[
				'type'  => 'discount',
				'value' => 10,
			],
			[
				'type'  => 'discount',
				'value' => 20,
			],
			[
				'type'  => 'discount',
				'value' => 5,
			],
			[
				'type'  => 'discount',
				'value' => 15,
			],
		];

		// Test.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame( $promotions_data, $sorted_promotions );

		// Add discount_type but not discount_value.
		$promotions_data = [
			'ABC' => [
				'discount_type' => 'percentage_off',
			],
			'DEF' => [
				'discount_type' => 'percentage_off',
			],
		];

		// Test.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame( $promotions_data, $sorted_promotions );

		// Add discount_value but not discount_type.
		$promotions_data = [
			'ABC' => [
				'discount_value' => 10,
			],
			'DEF' => [
				'discount_value' => 20,
			],
		];

		// Test.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame( $promotions_data, $sorted_promotions );

		// Add discount_type and discount_value but no is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 10,
			],
			'DEF' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 20,
			],
		];

		// Test.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame( $promotions_data, $sorted_promotions );

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.1,
				'is_pif'         => 1,
			],
			'DEF' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
		];

		// Test with percentage_off types and one is pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'DEF' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'ABC' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.1,
					'is_pif'         => 1,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.1,
				'is_pif'         => 1,
			],
			'DEF' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.2,
				'is_pif'         => 1,
			],
		];

		// Test with percentage_off types and both are pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'ABC' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.1,
					'is_pif'         => 1,
				],
				'DEF' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.2,
					'is_pif'         => 1,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.1,
				'is_pif'         => 0,
			],
			'DEF' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
		];

		// Test with percentage_off types and both are not pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'DEF' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'ABC' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.1,
					'is_pif'         => 0,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.1,
				'is_pif'         => 1,
			],
			'DEF' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
		];

		// Test with fixed_off types and one is pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'DEF' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'ABC' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.1,
					'is_pif'         => 1,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.1,
				'is_pif'         => 1,
			],
			'DEF' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.2,
				'is_pif'         => 1,
			],
		];

		// Test with fixed_off types and both are pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'ABC' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.1,
					'is_pif'         => 1,
				],
				'DEF' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.2,
					'is_pif'         => 1,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.1,
				'is_pif'         => 0,
			],
			'DEF' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
		];

		// Test with fixed_off types and both are not pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'DEF' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'ABC' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.1,
					'is_pif'         => 0,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.1,
				'is_pif'         => 0,
			],
			'DEF' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
			'GHI' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.1,
				'is_pif'         => 0,
			],
			'JKL' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
		];

		// Test with mixed types and none are pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'DEF' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'ABC' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.1,
					'is_pif'         => 0,
				],
				'JKL' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'GHI' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.1,
					'is_pif'         => 0,
				],
			],
			$sorted_promotions
		);

		// Add discount_type, discount_value and is_pif.
		$promotions_data = [
			'ABC' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.1,
				'is_pif'         => 1,
			],
			'DEF' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
			'GHI' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.1,
				'is_pif'         => 1,
			],
			'JKL' => [
				'discount_type'  => 'percentage_off',
				'discount_value' => 0.2,
				'is_pif'         => 0,
			],
			'MNO' => [
				'discount_type'  => 'fixed_off',
				'discount_value' => 0.3,
				'is_pif'         => 1,
			],
		];

		// Test with mixed types and some are pif.
		$sorted_promotions = sort_promotions_by_type_and_value( $promotions_data );
		$this->assertSame(
			[
				'DEF' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'ABC' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.1,
					'is_pif'         => 1,
				],
				'MNO' => [
					'discount_type'  => 'fixed_off',
					'discount_value' => 0.3,
					'is_pif'         => 1,
				],
				'JKL' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.2,
					'is_pif'         => 0,
				],
				'GHI' => [
					'discount_type'  => 'percentage_off',
					'discount_value' => 0.1,
					'is_pif'         => 1,
				],
			],
			$sorted_promotions
		);
	}
}
