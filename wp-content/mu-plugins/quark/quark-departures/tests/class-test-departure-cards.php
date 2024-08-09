<?php
/**
 * Departure Card test suite.
 *
 * @package quark-departures
 */

namespace Quark\Departures\Tests;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Post;
use WP_Term;

use function Quark\Departures\get_card_data;
use function Quark\Departures\get_cards_data;
use function Quark\Softrip\do_sync;

use const Quark\Departures\POST_TYPE;
use const Quark\Departures\PROMOTION_TAG;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_SETS_POST_TYPE;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGES_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Departure_Cards.
 */
class Test_Departure_Cards extends Softrip_TestCase {

	/**
	 * Expedition post.
	 *
	 * @var WP_Post
	 */
	protected static WP_Post $post_expedition;

	/**
	 * Departure location terms.
	 *
	 * @var array<WP_Term>
	 */
	protected static array $departure_location_terms;

	/**
	 * Spoken language terms.
	 *
	 * @var array<WP_Term>
	 */
	protected static array $spoken_language_terms;

	/**
	 * Promotion tag terms.
	 *
	 * @var array<WP_Term>
	 */
	protected static array $promotion_tag_terms;

	/**
	 * Adventure option terms.
	 *
	 * @var array<WP_Term>
	 */
	protected static array $adventure_option_terms;

	/**
	 * Policy pages.
	 *
	 * @var array<WP_Post>
	 */
	protected static array $policy_pages;

	/**
	 * Inclusion set ids.
	 *
	 * @var array<int>
	 */
	protected static array $inclusion_set_ids;

	/**
	 * Setup before class.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass(): void {
		// Call parent setup.
		parent::setUpBeforeClass();

		// Create Expedition post.
		$post_expedition = self::factory()->post->create_and_get(
			[
				'post_type'   => EXPEDITION_POST_TYPE,
				'post_title'  => 'Test Expedition Post',
				'post_status' => 'publish',
			]
		);

		// Assert created post is instance of WP_Post.
		self::assertTrue( $post_expedition instanceof WP_Post );

		// Set post.
		self::$post_expedition = $post_expedition;

		// Create term DEPARTURE_LOCATION_TAXONOMY.
		$departure_location_term_1 = self::factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
			]
		);

		// Create term DEPARTURE_LOCATION_TAXONOMY.
		$departure_location_term_2 = self::factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
			]
		);

		// Assert created terms are instance of WP_Term.
		self::assertTrue( $departure_location_term_1 instanceof WP_Term );
		self::assertTrue( $departure_location_term_2 instanceof WP_Term );

		// Set terms.
		self::$departure_location_terms = [
			$departure_location_term_1,
			$departure_location_term_2,
		];

		// Create Spoken language term.
		$spoken_language_term_1 = self::factory()->term->create_and_get(
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
				'name'     => 'spoken_language_1',
			]
		);

		// Create Spoken language term.
		$spoken_language_term_2 = self::factory()->term->create_and_get(
			[
				'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY,
				'name'     => 'spoken_language_2',
			]
		);

		// Assert created terms are instance of WP_Term.
		self::assertTrue( $spoken_language_term_1 instanceof WP_Term );
		self::assertTrue( $spoken_language_term_2 instanceof WP_Term );

		// Set terms.
		self::$spoken_language_terms = [
			$spoken_language_term_1,
			$spoken_language_term_2,
		];

		// Create term of Promotion Tags.
		$promotion_tag_term_1 = self::factory()->term->create_and_get(
			[
				'taxonomy' => PROMOTION_TAG,
				'name'     => 'promotion_tag_1',
			]
		);

		// Create term of Promotion Tags.
		$promotion_tag_term_2 = self::factory()->term->create_and_get(
			[
				'taxonomy' => PROMOTION_TAG,
				'name'     => 'promotion_tag_2',
			]
		);

		// Create term of Promotion Tags.
		$promotion_tag_term_3 = self::factory()->term->create_and_get(
			[
				'taxonomy' => PROMOTION_TAG,
				'name'     => 'promotion_tag_3',
			]
		);

		// Assert term is created.
		self::assertTrue( $promotion_tag_term_1 instanceof WP_Term );
		self::assertTrue( $promotion_tag_term_2 instanceof WP_Term );
		self::assertTrue( $promotion_tag_term_3 instanceof WP_Term );

		// Set terms.
		self::$promotion_tag_terms = [
			$promotion_tag_term_1,
			$promotion_tag_term_2,
			$promotion_tag_term_3,
		];

		// Create term of Adventure Options.
		$adventure_option_term_1 = self::factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option_1',
			]
		);

		// Create term of Adventure Options.
		$adventure_option_term_2 = self::factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option_2',
			]
		);

		// Create term of Adventure Options.
		$adventure_option_term_3 = self::factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option_3',
			]
		);

		// Assert term is created.
		self::assertTrue( $adventure_option_term_1 instanceof WP_Term );
		self::assertTrue( $adventure_option_term_2 instanceof WP_Term );
		self::assertTrue( $adventure_option_term_3 instanceof WP_Term );

		// Set terms.
		self::$adventure_option_terms = [
			$adventure_option_term_1,
			$adventure_option_term_2,
			$adventure_option_term_3,
		];

		// Create a post of POLICY_PAGES.
		$policy_page_1 = self::factory()->post->create_and_get(
			[
				'post_type'  => POLICY_PAGES_POST_TYPE,
				'meta_input' => [
					'alternate_title'          => 'value 1',
					'marketing_option_summary' => 'Lorem Ipsum Dolor Sit',
				],
			]
		);

		// Create a post of POLICY_PAGES.
		$policy_page_2 = self::factory()->post->create_and_get(
			[
				'post_type'  => POLICY_PAGES_POST_TYPE,
				'meta_input' => [
					'alternate_title'          => 'value 2',
					'icon_id'                  => '132',
					'marketing_option_summary' => 'Lorem Ipsum Dolor Sit Amet Consectetur',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		self::assertTrue( $policy_page_1 instanceof WP_Post );
		self::assertTrue( $policy_page_2 instanceof WP_Post );

		// Set posts.
		self::$policy_pages = [
			$policy_page_1,
			$policy_page_2,
		];

		// Create an Inclusion Set post.
		$inclusion_set_1 = self::factory()->post->create_and_get(
			[
				'post_type'   => INCLUSION_SETS_POST_TYPE,
				'post_title'  => 'Test Post 2',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1'     => 'value_1',
					'set'        => '3',
					'set_0_item' => 'Test Item 1',
					'set_1_item' => 'Test Item 2',
					'set_2_item' => 'Test Item 3',
				],
			]
		);

		// Create an Inclusion Set post.
		$inclusion_set_2 = self::factory()->post->create_and_get(
			[
				'post_type'   => INCLUSION_SETS_POST_TYPE,
				'post_title'  => 'Test Post 3',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1'     => 'value_2',
					'set'        => '3',
					'set_0_item' => 'Test Item 4',
					'set_1_item' => 'Test Item 5',
					'set_2_item' => 'Test Item 6',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		self::assertTrue( $inclusion_set_1 instanceof WP_Post );
		self::assertTrue( $inclusion_set_2 instanceof WP_Post );

		// Set post.
		self::$inclusion_set_ids = [
			$inclusion_set_1->ID,
			$inclusion_set_2->ID,
		];

		// Fetch itinerary posts for expedition.
		$itinerary_query_args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'posts_per_page'         => 1,
			'update_post_meta_cache' => false,
			'meta_query'             => [
				[
					'key'     => 'softrip_package_id',
					'value'   => [
						'JKL-012',
					],
					'compare' => 'IN',
				],
			],
		];

		// Get Itinerary posts.
		$itinerary_ids = get_posts( $itinerary_query_args );

		// Assert fetched posts.
		self::assertCount( 1, $itinerary_ids );

		// Set itinerary id - 1.
		$itinerary_id_1 = $itinerary_ids[0];

		// Itinerary post - set meta data.
		$meta_data = [
			'duration_in_days'                     => 16,
			'start_location'                       => self::$departure_location_terms[0]->term_id,
			'related_expedition'                   => self::$post_expedition->ID,
			'tnc_cancellation_policy'              => self::$policy_pages[1]->ID,
			'mandatory_transfer_package_inclusion' => self::$inclusion_set_ids[1],
			'mandatory_transfer_price_usd'         => 200,
			'mandatory_transfer_price_cad'         => 250,
			'mandatory_transfer_price_gbp'         => 190,
		];

		// Update Itinerary post.
		wp_update_post(
			[
				'ID'         => $itinerary_id_1,
				'meta_input' => $meta_data,
			]
		);

		// Update Meta_query value as - ABC-123.
		$itinerary_query_args['meta_query'][0]['value'] = 'ABC-123';

		// Get Itinerary posts.
		$itinerary_ids = get_posts( $itinerary_query_args );

		// Assert fetched posts.
		self::assertCount( 1, $itinerary_ids );

		// Set itinerary id - 2.
		$itinerary_id_2 = $itinerary_ids[0];

		// Itinerary post - set meta data.
		$meta_data = [
			'duration_in_days'                     => 11,
			'start_location'                       => self::$departure_location_terms[0]->term_id,
			'related_expedition'                   => self::$post_expedition->ID,
			'mandatory_transfer_package_inclusion' => self::$inclusion_set_ids[1],
			'mandatory_transfer_price_usd'         => 200,
			'mandatory_transfer_price_cad'         => 250,
			'mandatory_transfer_price_gbp'         => 190,
		];

		// Update Itinerary post.
		wp_update_post(
			[
				'ID'         => $itinerary_id_2,
				'meta_input' => $meta_data,
			]
		);
	}

	/**
	 * Test 1: Without any itinerary OR Expedition.
	 *
	 * @return void
	 */
	public function test_get_card_data_without_itinerary_or_expedition(): void {
		// Create Departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Get card data.
		$card_data = get_card_data( $departure_post->ID );

		// Assert expected get data is empty.
		$this->assertEmpty( $card_data );
	}

	/**
	 * Test get_cards_data().
	 *
	 * @covers \Quark\Departures\get_card_data()
	 * @covers \Quark\Departures\get_cards_data()
	 *
	 * @return void
	 */
	public function test_get_cards_data(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with exising posts.
		do_sync();

		// Fetch Departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'meta_query'             => [
				[
					'key'     => 'softrip_departure_id',
					'value'   => 'ULT20250109',
					'compare' => '=',
				],
			],
		];

		// Get Departure posts.
		$departure_posts = get_posts( $departure_query_args );

		// Assert fetched posts are 2.
		$this->assertCount( 1, $departure_posts );

		// Set departure post - 1.
		$departure_post_1 = $departure_posts[0];

		// Assert created post is int.
		$this->assertIsInt( $departure_post_1 );

		// Set terms.
		wp_set_object_terms(
			absint( $departure_posts[0] ),
			[
				self::$spoken_language_terms[0]->term_id,
				self::$spoken_language_terms[1]->term_id,
			],
			SPOKEN_LANGUAGE_TAXONOMY
		);

		// Set terms.
		wp_set_object_terms(
			absint( $departure_posts[0] ),
			[
				self::$promotion_tag_terms[0]->term_id,
				self::$promotion_tag_terms[2]->term_id,
			],
			PROMOTION_TAG
		);

		// Update post meta.
		wp_update_post(
			[
				'ID'         => $departure_post_1,
				'meta_input' => [
					'adventure_options' => [
						self::$adventure_option_terms[0]->term_id,
						self::$adventure_option_terms[2]->term_id,
					],
				],
			]
		);

		// Update departure meta query value - OEX20260228.
		$departure_query_args['meta_query'][0]['value'] = 'OEX20260228';

		// Get Departure posts.
		$departure_posts = get_posts( $departure_query_args );

		// Assert fetched posts are 1.
		$this->assertCount( 1, $departure_posts );

		// Set departure post - 2.
		$departure_post_2 = $departure_posts[0];

		// Assert created post is int.
		$this->assertIsInt( $departure_post_2 );

		// Set terms.
		wp_set_object_terms(
			$departure_post_2,
			[
				self::$promotion_tag_terms[0]->term_id,
				self::$promotion_tag_terms[2]->term_id,
			],
			PROMOTION_TAG
		);

		// departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
		];

		// Get Departure posts.
		$all_departures = get_posts( $departure_query_args );

		// assert fetched posts are 3.
		$this->assertCount( 3, $all_departures );

		// Get cards data.
		$cards_data = get_cards_data( array_map( 'absint', $all_departures ) );

		// Assert cards data.
		$this->assertCount( 3, $cards_data );

		// Get card data for departure post - 1.
		$card_data = $cards_data[ $departure_post_1 ] ?? [];

		// Assert cabin key exists.
		$card_data['cabins'] = $card_data['cabins'] ?? [];

		// Assert cabin count.
		$this->assertCount( 2, $card_data['cabins'] );
		$this->assertCount( 1, $card_data['cabins']['ULT-SGL']['occupancies'] );
		$this->assertCount( 2, $card_data['cabins']['ULT-DBL']['occupancies'] );

		// Expected data for ULT-SGL occupancy.
		$ult_sgl_occupancy = [
			[
				'name'         => 'A',
				'description'  => 'Single Room',
				'no_of_guests' => '1',
				'prices'       => [
					'USD' => [
						'original_price'   => 45105,
						'discounted_price' => 38369,
					],
					'AUD' => [
						'original_price'   => 70605,
						'discounted_price' => 60014,
					],
					'CAD' => [
						'original_price'   => 61455,
						'discounted_price' => 52274,
					],
					'EUR' => [
						'original_price'   => 41905,
						'discounted_price' => 35619,
					],
					'GBP' => [
						'original_price'   => 36095,
						'discounted_price' => 30709,
					],
				],
				'promotions'   => [],
			],
		];

		// Assert occupancy data.
		$this->assertEqualSetsWithIndex( $ult_sgl_occupancy, $card_data['cabins']['ULT-SGL']['occupancies'] );

		// Expected data for ULT-DBL occupancy.
		$ult_dbl_occupancy = [
			[
				'name'         => 'A',
				'description'  => 'Single Room',
				'no_of_guests' => '1',
				'prices'       => [
					'USD' => [
						'original_price'   => 75100,
						'discounted_price' => 63865,
					],
					'AUD' => [
						'original_price'   => 117500,
						'discounted_price' => 99875,
					],
					'CAD' => [
						'original_price'   => 102250,
						'discounted_price' => 86950,
					],
					'EUR' => [
						'original_price'   => 69900,
						'discounted_price' => 59415,
					],
					'GBP' => [
						'original_price'   => 60090,
						'discounted_price' => 51105,
					],
				],
				'promotions'   => [],
			],
			[
				'name'         => 'AA',
				'description'  => 'Double Room',
				'no_of_guests' => '2',
				'prices'       => [
					'USD' => [
						'original_price'   => 34800,
						'discounted_price' => 29610,
					],
					'AUD' => [
						'original_price'   => 54200,
						'discounted_price' => 46070,
					],
					'CAD' => [
						'original_price'   => 47250,
						'discounted_price' => 40200,
					],
					'EUR' => [
						'original_price'   => 32200,
						'discounted_price' => 27370,
					],
					'GBP' => [
						'original_price'   => 27790,
						'discounted_price' => 23650,
					],
				],
				'promotions'   => [],
			],
		];

		// Assert occupancy data.
		$this->assertEqualSetsWithIndex( $ult_dbl_occupancy, $card_data['cabins']['ULT-DBL']['occupancies'] );

		// remove cabins from card_data.
		unset( $card_data['cabins'] );

		// Expected data.
		$expected_data_1 = $this->get_expected_data_1( $departure_post_1 );

		// Assert data.
		$this->assertEqualSetsWithIndex( $expected_data_1, $card_data );

		// Get card data for departure post - 2.
		$card_data = $cards_data[ $departure_post_2 ] ?? [];

		// Assert cabin key exists.
		$card_data['cabins'] = $card_data['cabins'] ?? [];

		// Assert cabin count.
		$this->assertCount( 1, $card_data['cabins'] );

		// Assert occupancy count.
		$this->assertCount( 1, $card_data['cabins']['OEX-SGL']['occupancies'] );

		// Expected data for OEX-SGL occupancy.
		$oex_sgl_occupancy = [
			[
				'name'         => 'A',
				'description'  => 'Single Room',
				'no_of_guests' => '1',
				'prices'       => [
					'USD' => [
						'original_price'   => 35095,
						'discounted_price' => 26371,
					],
					'AUD' => [
						'original_price'   => 54795,
						'discounted_price' => 41096,
					],
					'CAD' => [
						'original_price'   => 47745,
						'discounted_price' => 35871,
					],
					'EUR' => [
						'original_price'   => 32495,
						'discounted_price' => 24371,
					],
					'GBP' => [
						'original_price'   => 28185,
						'discounted_price' => 21186,
					],
				],
				'promotions'   => [],
			],
		];

		// Assert occupancy data.
		$this->assertEqualSetsWithIndex( $oex_sgl_occupancy, $card_data['cabins']['OEX-SGL']['occupancies'] );

		// remove cabins from card_data.
		unset( $card_data['cabins'] );

		// Assert data.
		$expected_data_2 = $this->get_expected_data_2( $departure_post_2 );
		$this->assertEqualSetsWithIndex( $expected_data_2, $card_data );

		// remove above departure.
		unset( $cards_data[ $departure_post_1 ] );
		unset( $cards_data[ $departure_post_2 ] );

		// Assert data.
		$departure_post_3 = key( $cards_data );

		// verify departure post - 3.
		$this->assertIsInt( $departure_post_3 );

		// Get expected data for departure post - 3.
		$expected_data_3 = $this->get_expected_data_3( $departure_post_3 );
		$card_data       = $cards_data[ $departure_post_3 ] ?? [];

		// remove cabins from card_data.
		unset( $card_data['cabins'] );

		// Assert data.
		$this->assertEqualSetsWithIndex( $expected_data_3, $card_data );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
	}

	/**
	 * Get expected data for departure post - 1.
	 *
	 * @param int $departure_post_1 Departure post id.
	 *
	 * @return array{
	 *     departure_id: int,
	 *     expedition_name: string,
	 *     expedition_link: string|false,
	 *     duration_days: int,
	 *     package_id: string,
	 *     languages: string,
	 *     duration_dates: string,
	 *     starting_from_location: string,
	 *     promotion_tags: string[],
	 *     lowest_price: array{
	 *         discounted_price: string,
	 *         original_price: string,
	 *     },
	 *     transfer_package_details: array{
	 *         title: string,
	 *         sets: array<string>,
	 *         price: float,
	 *         formatted_price: string,
	 *     },
	 *     paid_adventure_options: array<int, string>,
	 *     banner_details: array{
	 *         title: string,
	 *         icon_id: int,
	 *         description: string,
	 *         permalink: string|false,
	 *     },
	 *     ship_name ?: string,
	 * }
	 */
	private function get_expected_data_1( int $departure_post_1 = 0 ): array {
		// Expected data.
		$expected_data_1 = [
			'departure_id'             => $departure_post_1,
			'expedition_name'          => 'Test Expedition Post',
			'expedition_link'          => get_permalink( self::$post_expedition->ID ),
			'duration_days'            => 16,
			'package_id'               => 'JKL-012',
			'languages'                => 'spoken_language_1, spoken_language_2',
			'duration_dates'           => 'January 9-25, 2025',
			'starting_from_location'   => self::$departure_location_terms[0]->name,
			'promotion_tags'           => [
				'promotion_tag_1',
				'promotion_tag_3',
			],
			'lowest_price'             => [
				'discounted_price' => '$29,610 USD',
				'original_price'   => '$34,800 USD',
			],
			'transfer_package_details' => [
				'title'           => 'Includes',
				'sets'            => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'           => 200,
				'formatted_price' => '$200 USD',
			],
			'paid_adventure_options'   => [
				self::$adventure_option_terms[0]->term_id => self::$adventure_option_terms[0]->name,
				self::$adventure_option_terms[2]->term_id => self::$adventure_option_terms[2]->name,
			],
			'banner_details'           => [
				'title'       => 'value 2',
				'icon_id'     => 0,
				'description' => 'Lorem Ipsum Dolor Sit Amet Consectetur',
				'permalink'   => get_permalink( self::$policy_pages[1] ),
			],
		];

		// Search ship post with code ULT.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_id',
						'value' => 'ULT',
					],
				],
			]
		);

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_data_1['ship_name'] = $ship_post->post_title;
		}

		// Return expected data.
		return $expected_data_1;
	}

	/**
	 * Get expected data for departure post - 2.
	 *
	 * @param int $departure_post_2 Departure post id.
	 *
	 * @return array{
	 *     departure_id: int,
	 *     expedition_name: string,
	 *     expedition_link: string|false,
	 *     duration_days: int,
	 *     package_id: string,
	 *     languages: string,
	 *     duration_dates: string,
	 *     starting_from_location: string,
	 *     promotion_tags: string[],
	 *     lowest_price: array{
	 *         discounted_price: string,
	 *         original_price: string,
	 *     },
	 *     transfer_package_details: array{
	 *         title: string,
	 *         sets: array<string>,
	 *         price: float,
	 *         formatted_price: string,
	 *     },
	 *     paid_adventure_options: string[],
	 *     banner_details: array{
	 *         title: string,
	 *         icon_id: int,
	 *         description: string,
	 *         permalink: string|false,
	 *     },
	 *     ship_name ?: string,
	 * }
	 */
	private function get_expected_data_2( int $departure_post_2 = 0 ): array {
		// Expected data.
		$expected_data_2 = [
			'departure_id'             => $departure_post_2,
			'expedition_name'          => 'Test Expedition Post',
			'expedition_link'          => get_permalink( self::$post_expedition->ID ),
			'duration_days'            => 11,
			'package_id'               => 'ABC-123',
			'languages'                => '',
			'duration_dates'           => 'February 28 - March 11, 2026',
			'starting_from_location'   => self::$departure_location_terms[0]->name,
			'promotion_tags'           => [
				'promotion_tag_1',
				'promotion_tag_3',
			],
			'lowest_price'             => [
				'discounted_price' => '$26,371 USD',
				'original_price'   => '$35,095 USD',
			],
			'transfer_package_details' => [
				'title'           => 'Includes',
				'sets'            => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'           => 200,
				'formatted_price' => '$200 USD',
			],
			'paid_adventure_options'   => [],
			'banner_details'           => [
				'title'       => '',
				'icon_id'     => 0,
				'description' => '',
				'permalink'   => '',
			],
		];

		// Search ship post with code OEX.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_id',
						'value' => 'OEX',
					],
				],
			]
		);

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_data_2['ship_name'] = $ship_post->post_title;
		}

		// Return expected data.
		return $expected_data_2;
	}

	/**
	 * Get expected data for departure post - 3.
	 *
	 * @param int $departure_post_3 Departure post id.
	 *
	 * @return array{
	 *     departure_id: int,
	 *     expedition_name: string,
	 *     expedition_link: string|false,
	 *     duration_days: int,
	 *     package_id: string,
	 *     languages: string,
	 *     duration_dates: string,
	 *     starting_from_location: string,
	 *     promotion_tags: string[],
	 *     lowest_price: array{
	 *         discounted_price: string,
	 *         original_price: string,
	 *     },
	 *     transfer_package_details: array{
	 *         title: string,
	 *         sets: array<string>,
	 *         price: float,
	 *         formatted_price: string,
	 *     },
	 *     paid_adventure_options: string[],
	 *     banner_details: array{
	 *         title: string,
	 *         icon_id: int,
	 *         description: string,
	 *         permalink: string|false,
	 *     },
	 *     ship_name ?: string,
	 * }
	 */
	private function get_expected_data_3( int $departure_post_3 = 0 ): array {
		// Expected data.
		$expected_data_3 = [
			'departure_id'             => $departure_post_3,
			'expedition_name'          => 'Test Expedition Post',
			'expedition_link'          => get_permalink( self::$post_expedition->ID ),
			'duration_days'            => 16,
			'package_id'               => 'JKL-012',
			'languages'                => '',
			'duration_dates'           => 'January 16 - February 1, 2026',
			'starting_from_location'   => self::$departure_location_terms[0]->name,
			'promotion_tags'           => [],
			'lowest_price'             => [
				'discounted_price' => '$40,069 USD',
				'original_price'   => '$47,105 USD',
			],
			'transfer_package_details' => [
				'title'           => 'Includes',
				'sets'            => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'           => 200,
				'formatted_price' => '$200 USD',
			],
			'paid_adventure_options'   => [],
			'banner_details'           => [
				'title'       => 'value 2',
				'icon_id'     => 0,
				'description' => 'Lorem Ipsum Dolor Sit Amet Consectetur',
				'permalink'   => get_permalink( self::$policy_pages[1] ),
			],
		];

		// Search ship post with code OEX.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_id',
						'value' => 'ULT',
					],
				],
			]
		);

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_data_3['ship_name'] = $ship_post->post_title;
		}

		// Return expected data.
		return $expected_data_3;
	}
}
