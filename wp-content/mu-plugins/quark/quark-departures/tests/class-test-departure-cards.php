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

use function Quark\Core\format_price;
use function Quark\Departures\bust_post_cache;
use function Quark\Departures\get_card_data;
use function Quark\Departures\get_cards_data;
use function Quark\Departures\get_dates_rates_card_data;
use function Quark\Departures\get_dates_rates_cards_data;
use function Quark\Departures\get_promotions_description;
use function Quark\Leads\get_request_a_quote_url;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\update_promotions;

use const Quark\Departures\POST_TYPE;
use const Quark\Departures\PROMOTION_TAG;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_SETS_POST_TYPE;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTION_POST_TYPE;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
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
	 * Destination terms.
	 *
	 * @var array<WP_Term>
	 */
	protected static array $destination_terms;

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

		// Create terms of DESTINATION_TAXONOMY.
		$destination_term_1 = self::factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'name'     => 'Antarctica',
			]
		);

		// Assert terms are created.
		self::assertTrue( $destination_term_1 instanceof WP_Term );

		// Create terms of DESTINATION_TAXONOMY.
		$destination_term_2 = self::factory()->term->create_and_get(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'name'     => 'Arctic',
				'parent'   => $destination_term_1->term_id,
			]
		);

		// Assert terms are created.
		self::assertTrue( $destination_term_2 instanceof WP_Term );

		// Set terms.
		self::$destination_terms = [
			$destination_term_1,
			$destination_term_2,
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
					'key'     => 'softrip_package_code',
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
			'end_location'                         => self::$departure_location_terms[1]->term_id,
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
			'end_location'                         => self::$departure_location_terms[1]->term_id,
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

		// Update Meta_query value as - ABC-123.
		$itinerary_query_args['meta_query'][0]['value'] = 'HIJ-456';

		// Get Itinerary posts.
		$itinerary_ids = get_posts( $itinerary_query_args );

		// Assert fetched posts.
		self::assertCount( 1, $itinerary_ids );

		// Set itinerary id - 3.
		$itinerary_id_3 = $itinerary_ids[0];

		// Itinerary post - set meta data.
		$meta_data = [
			'duration_in_days'                     => 11,
			'start_location'                       => self::$departure_location_terms[0]->term_id,
			'end_location'                         => self::$departure_location_terms[0]->term_id,
			'related_expedition'                   => self::$post_expedition->ID,
			'mandatory_transfer_package_inclusion' => self::$inclusion_set_ids[0],
			'mandatory_transfer_price_usd'         => 150,
			'mandatory_transfer_price_cad'         => 250,
			'mandatory_transfer_price_gbp'         => 300,
		];

		// Update Itinerary post.
		wp_update_post(
			[
				'ID'         => $itinerary_id_3,
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

		// Sync softrip with existing posts.
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
					'key'     => 'softrip_code',
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

		// Update post meta.
		update_post_meta(
			$departure_post_1,
			'related_promotion_tags',
			[
				self::$promotion_tag_terms[0]->term_id,
				self::$promotion_tag_terms[2]->term_id,
			]
		);

		// Bust cache.
		bust_post_cache( $departure_post_1 );

		// Update post meta.
		update_post_meta(
			$departure_post_2,
			'related_promotion_tags',
			[
				self::$promotion_tag_terms[0]->term_id,
				self::$promotion_tag_terms[2]->term_id,
			]
		);

		// Bust cache.
		bust_post_cache( $departure_post_2 );

		// departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'posts_per_page'         => -1,
		];

		// Get Departure posts.
		$all_departures = get_posts( $departure_query_args );

		// assert fetched posts are 3.
		$this->assertCount( 7, $all_departures );

		// Get cards data.
		$cards_data = get_cards_data( array_map( 'absint', $all_departures ) );

		// Assert cards data.
		$this->assertCount( 7, $cards_data );

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
				'price'        => [
					'original_price'   => format_price( 45105 ),
					'discounted_price' => format_price( 38369 ),
				],
				'promotions'   => [],
				'checkout_url' => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-SGL&currency=USD&mask=A',
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
				'price'        => [
					'original_price'   => format_price( 75100 ),
					'discounted_price' => format_price( 63865 ),
				],
				'promotions'   => [],
				'checkout_url' => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-DBL&currency=USD&mask=A',
			],
			[
				'name'         => 'AA',
				'description'  => 'Double Room',
				'no_of_guests' => '2',
				'price'        => [
					'original_price'   => format_price( 34800 ),
					'discounted_price' => format_price( 29610 ),
				],
				'promotions'   => [],
				'checkout_url' => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-DBL&currency=USD&mask=AA',
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
		$this->assertCount( 0, $card_data['cabins'] );

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
	 *         offer_inclusion_text: string
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
			'expedition_slider_images' => [],
			'expedition_categories'    => [],
			'duration_days'            => 16,
			'package_id'               => 'JKL-012',
			'languages'                => 'spoken_language_1, spoken_language_2',
			'duration_dates'           => 'January 9-25, 2025',
			'request_a_quote_url'      => get_request_a_quote_url( $departure_post_1 ),
			'starting_from_location'   => self::$departure_location_terms[0]->name,
			'departure_status'         => 'A',
			'promotion_tags'           => [
				'promotion_tag_1',
				'promotion_tag_3',
			],
			'promotion_banner'         => 'Save up to 15%',
			'lowest_price'             => [
				'discounted_price' => '$29,610 USD',
				'original_price'   => '$34,800 USD',
			],
			'transfer_package_details' => [
				'title'                => 'Includes',
				'sets'                 => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'                => 200,
				'formatted_price'      => '$200 USD',
				'offer_inclusion_text' => '',
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
			'promotions'               => [
				'Save 15% - Offer Code 15PROMO',
			],
		];

		// Search ship post with code ULT.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_code',
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
	 *         offer_inclusion_text: string,
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
			'expedition_slider_images' => [],
			'expedition_categories'    => [],
			'duration_days'            => 11,
			'package_id'               => 'ABC-123',
			'languages'                => 'english',
			'duration_dates'           => 'February 28 - March 11, 2026',
			'request_a_quote_url'      => get_request_a_quote_url( $departure_post_2 ),
			'starting_from_location'   => self::$departure_location_terms[0]->name,
			'departure_status'         => 'S',
			'promotion_tags'           => [
				'promotion_tag_1',
				'promotion_tag_3',
			],
			'promotion_banner'         => 'Save up to 25%',
			'lowest_price'             => [
				'discounted_price' => '$26,371 USD',
				'original_price'   => '$35,095 USD',
			],
			'transfer_package_details' => [
				'title'                => 'Includes',
				'sets'                 => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'                => 200,
				'formatted_price'      => '$200 USD',
				'offer_inclusion_text' => '',
			],
			'paid_adventure_options'   => [],
			'banner_details'           => [
				'title'       => '',
				'icon_id'     => 0,
				'description' => '',
				'permalink'   => '',
			],
			'promotions'               => [
				'Save 25% - Offer Code 25PROMO',
			],
		];

		// Search ship post with code OEX.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_code',
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
	 *         offer_inclusion_text: string,
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
			'expedition_slider_images' => [],
			'expedition_categories'    => [],
			'duration_days'            => 16,
			'package_id'               => 'JKL-012',
			'languages'                => 'english',
			'duration_dates'           => 'January 16 - February 1, 2026',
			'starting_from_location'   => self::$departure_location_terms[0]->name,
			'promotion_tags'           => [],
			'promotion_banner'         => 'Save up to 15%',
			'request_a_quote_url'      => get_request_a_quote_url( $departure_post_3 ),
			'departure_status'         => 'A',
			'lowest_price'             => [
				'discounted_price' => '$40,069 USD',
				'original_price'   => '$47,105 USD',
			],
			'transfer_package_details' => [
				'title'                => 'Includes',
				'sets'                 => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'                => 200,
				'formatted_price'      => '$200 USD',
				'offer_inclusion_text' => '',
			],
			'paid_adventure_options'   => [],
			'banner_details'           => [
				'title'       => 'value 2',
				'icon_id'     => 0,
				'description' => 'Lorem Ipsum Dolor Sit Amet Consectetur',
				'permalink'   => get_permalink( self::$policy_pages[1] ),
			],
			'promotions'               => [
				'Save 15% - Offer Code 15PROMO',
			],
		];

		// Search ship post with code OEX.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_code',
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

	/**
	 * Test 1: Without any itinerary OR Expedition.
	 *
	 * @covers \Quark\Departures\get_dates_rates_card_data()
	 *
	 * @return void
	 */
	public function test_get_dates_rates_cards_data_without_itinerary_or_expedition(): void {
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
		$card_data = get_dates_rates_card_data( $departure_post->ID );

		// Assert expected get data is empty.
		$this->assertEmpty( $card_data );
	}

	/**
	 * Test get_dates_rates_card_data().
	 *
	 * @covers \Quark\Departures\get_dates_rates_card_data()
	 *
	 * @return void
	 */
	public function test_get_dates_rates_card_data(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
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
					'key'     => 'softrip_code',
					'value'   => 'ULT20250109',
					'compare' => '=',
				],
			],
		];

		// Get Departure posts.
		$departure_posts = get_posts( $departure_query_args );

		// Assert fetched posts count is 1.
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
			absint( self::$post_expedition->ID ),
			[
				self::$destination_terms[0]->term_id,
				self::$destination_terms[1]->term_id,
			],
			DESTINATION_TAXONOMY
		);

		// Bust cache after setting terms.
		bust_post_cache( $departure_post_1 );

		// departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'posts_per_page'         => -1,
		];

		// Get Departure posts.
		$all_departures = get_posts( $departure_query_args );

		// assert fetched posts are 3.
		$this->assertCount( 7, $all_departures );

		// Get cards data.
		$card_data = get_dates_rates_card_data( $departure_post_1 );

		// Prepare expected data.
		$expected_data = [
			'departure_id'               => $departure_post_1,
			'region'                     => 'Antarctica',
			'expedition_title'           => 'Test Expedition Post',
			'expedition_link'            => get_permalink( self::$post_expedition->ID ),
			'duration_days'              => 16,
			'duration_dates'             => 'January 9-25, 2025',
			'start_location'             => self::$departure_location_terms[0]->name,
			'end_location'               => self::$departure_location_terms[1]->name,
			'languages'                  => 'spoken_language_1, spoken_language_2',
			'included_adventure_options' => [],
			'paid_adventure_options'     => [],
			'request_a_quote_url'        => get_request_a_quote_url( $departure_post_1 ),
			'transfer_package_details'   => [
				'title'                => 'Includes',
				'sets'                 => [
					'Test Item 4',
					'Test Item 5',
					'Test Item 6',
				],
				'price'                => 200,
				'formatted_price'      => '$200 USD',
				'offer_inclusion_text' => '',
			],
			'tax_types'                  => [],
			'cabin_data'                 => [
				'ULT-SGL' => [
					'name'                     => 'cabin_name - ULT-SGL',
					'availability_status'      => 'A',
					'availability_description' => 'Available',
					'spaces_available'         => 10,
					'brochure_price'           => '$45,105 USD',
					'type'                     => '',
					'sort_priority'            => 0,
					'promos'                   => [
						'15PROMO' => '$38,369 USD',
					],
					'checkout_url'             => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-SGL&currency=USD',
				],
				'ULT-DBL' => [
					'name'                     => 'cabin_name - ULT-DBL',
					'availability_status'      => 'A',
					'availability_description' => 'Available',
					'checkout_url'             => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-DBL&currency=USD',
					'spaces_available'         => 20,
					'brochure_price'           => '$34,800 USD',
					'promos'                   => [
						'15PROMO' => '$29,610 USD',
					],
					'type'                     => '',
					'sort_priority'            => 0,
				],
			],
		];

		// Search ship post with code ULT.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => -1,
				'meta_query'     => [
					[
						'key'   => 'ship_code',
						'value' => 'ULT',
					],
				],
			]
		);

		// Update expected data with ship posts.
		foreach ( $ship_posts as $ship_post ) {
			$this->assertTrue( $ship_post instanceof WP_Post );
			$expected_data['ship_title'] = $ship_post->post_title;
			$expected_data['ship_link']  = get_permalink( $ship_post->ID );
		}

		// Get promotions data.
		$expected_data['available_promos']['15PROMO'] = get_promotions_by_code( '15PROMO' )[0];

		// Assert data.
		$this->assertEqualSetsWithIndex( $expected_data, $card_data );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
	}

	/**
	 * Test get_dates_rates_card_data().
	 *
	 * @covers \Quark\Departures\get_dates_rates_card_data()
	 *
	 * @return void
	 */
	public function test_get_dates_rates_cards_data(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Flush cache.
		wp_cache_flush();

		// Fetch Departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'order'                  => 'ASC',
			'orderby'                => 'ID',
			'meta_query'             => [
				[
					'key'     => 'softrip_code',
					'value'   => 'OEX20260228',
					'compare' => '=',
				],
			],
		];
		$departure_posts      = get_posts( $departure_query_args );

		// Assert fetched posts count is 1.
		$this->assertCount( 1, $departure_posts );

		// Set departure post - 1.
		$departure_post_1 = $departure_posts[0];

		// Assert created post is int.
		$this->assertIsInt( $departure_post_1 );

		// Fetch Departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'order'                  => 'ASC',
			'orderby'                => 'ID',
			'meta_query'             => [
				[
					'key'     => 'softrip_code',
					'value'   => 'ULT20250109',
					'compare' => '=',
				],
			],
		];
		$departure_posts      = get_posts( $departure_query_args );

		// Assert fetched posts count is 1.
		$this->assertCount( 1, $departure_posts );

		// Second departure post.
		$departure_post_2 = $departure_posts[0];

		// Assert created post is int.
		$this->assertIsInt( $departure_post_2 );

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
			absint( self::$post_expedition->ID ),
			[
				self::$destination_terms[0]->term_id,
				self::$destination_terms[1]->term_id,
			],
			DESTINATION_TAXONOMY
		);

		// departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'posts_per_page'         => -1,
			'order'                  => 'ASC',
			'orderby'                => 'ID',
		];

		// Get Departure posts.
		$all_departures = get_posts( $departure_query_args );

		// assert fetched posts are 3.
		$this->assertCount( 7, $all_departures );

		// Get cards data.
		$card_data = get_dates_rates_cards_data( [ $departure_post_1, $departure_post_2 ] );

		// Prepare expected data.
		$expected_data = [
			$departure_post_1 => [
				'departure_id'               => $departure_post_1,
				'region'                     => 'Antarctica',
				'expedition_title'           => 'Test Expedition Post',
				'expedition_link'            => get_permalink( self::$post_expedition->ID ),
				'duration_days'              => 11,
				'duration_dates'             => 'February 28 - March 11, 2026',
				'start_location'             => self::$departure_location_terms[0]->name,
				'end_location'               => self::$departure_location_terms[1]->name,
				'languages'                  => 'english',
				'included_adventure_options' => [],
				'paid_adventure_options'     => [],
				'request_a_quote_url'        => get_request_a_quote_url( $departure_post_1 ),
				'transfer_package_details'   => [
					'title'                => 'Includes',
					'sets'                 => [
						'Test Item 4',
						'Test Item 5',
						'Test Item 6',
					],
					'price'                => 200,
					'formatted_price'      => '$200 USD',
					'offer_inclusion_text' => '',
				],
				'tax_types'                  => [],
				'cabin_data'                 => [
					'OEX-SGL' => [
						'name'                     => 'cabin_name - OEX-SGL',
						'availability_status'      => 'S',
						'availability_description' => 'Sold Out',
						'spaces_available'         => 0,
						'promos'                   => [
							'25PROMO' => '$26,371 USD',
						],
						'brochure_price'           => '$35,095 USD',
						'checkout_url'             => 'https://local-checkout.quarkexpeditions.com?package_id=ABC-123&departure_date=2026-02-28&cabin_code=OEX-SGL&currency=USD',
						'type'                     => '',
						'sort_priority'            => 0,
					],
				],
				'available_promos'           => [
					'25PROMO' => get_promotions_by_code( '25PROMO' )[0],
				],
			],
			$departure_post_2 => [
				'departure_id'               => $departure_post_2,
				'region'                     => 'Antarctica',
				'expedition_title'           => 'Test Expedition Post',
				'expedition_link'            => get_permalink( self::$post_expedition->ID ),
				'duration_days'              => 16,
				'duration_dates'             => 'January 9-25, 2025',
				'start_location'             => self::$departure_location_terms[0]->name,
				'end_location'               => self::$departure_location_terms[1]->name,
				'languages'                  => 'spoken_language_1, spoken_language_2',
				'included_adventure_options' => [],
				'paid_adventure_options'     => [],
				'request_a_quote_url'        => get_request_a_quote_url( $departure_post_2 ),
				'transfer_package_details'   => [
					'title'                => 'Includes',
					'sets'                 => [
						'Test Item 4',
						'Test Item 5',
						'Test Item 6',
					],
					'price'                => 200,
					'formatted_price'      => '$200 USD',
					'offer_inclusion_text' => '',
				],
				'tax_types'                  => [],
				'cabin_data'                 => [
					'ULT-SGL' => [
						'name'                     => 'cabin_name - ULT-SGL',
						'availability_status'      => 'A',
						'availability_description' => 'Available',
						'spaces_available'         => 10,
						'brochure_price'           => '$45,105 USD',
						'promos'                   => [
							'15PROMO' => '$38,369 USD',
						],
						'checkout_url'             => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-SGL&currency=USD',
						'type'                     => '',
						'sort_priority'            => 0,
					],
					'ULT-DBL' => [
						'name'                     => 'cabin_name - ULT-DBL',
						'availability_status'      => 'A',
						'availability_description' => 'Available',
						'spaces_available'         => 20,
						'brochure_price'           => '$34,800 USD',
						'promos'                   => [
							'15PROMO' => '$29,610 USD',
						],
						'checkout_url'             => 'https://local-checkout.quarkexpeditions.com?package_id=JKL-012&departure_date=2025-01-09&cabin_code=ULT-DBL&currency=USD',
						'type'                     => '',
						'sort_priority'            => 0,
					],
				],
				'available_promos'           => [
					'15PROMO' => get_promotions_by_code( '15PROMO' )[0],
				],
			],
		];

		// Search ship post with code OEX.
		$ship_posts = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => 1,
				'meta_query'     => [
					[
						'key'   => 'ship_code',
						'value' => 'OEX',
					],
				],
			]
		);
		$this->assertTrue( $ship_posts[0] instanceof WP_Post );
		$expected_data[ $departure_post_1 ]['ship_title'] = $ship_posts[0]->post_title;
		$expected_data[ $departure_post_1 ]['ship_link']  = get_permalink( $ship_posts[0]->ID );

		// Search ship post with code ULT.
		$ship_post2 = get_posts(
			[
				'post_type'      => SHIP_POST_TYPE,
				'posts_per_page' => 1,
				'meta_query'     => [
					[
						'key'   => 'ship_code',
						'value' => 'ULT',
					],
				],
			]
		);
		$this->assertTrue( $ship_post2[0] instanceof WP_Post );
		$expected_data[ $departure_post_2 ]['ship_title'] = $ship_post2[0]->post_title;
		$expected_data[ $departure_post_2 ]['ship_link']  = get_permalink( $ship_post2[0]->ID );

		// Assert data.
		$this->assertArrayHasKey( $departure_post_1, $card_data );
		$this->assertArrayHasKey( $departure_post_2, $card_data );

		// Assert data for each card.
		$this->assertEqualSetsWithIndex( $expected_data[ $departure_post_1 ], $card_data[ $departure_post_1 ] );
		$this->assertEqualSetsWithIndex( $expected_data[ $departure_post_2 ], $card_data[ $departure_post_2 ] );

		// Cleanup.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
	}

	/**
	 * Test get_dates_rates_card_data() with adventure options.
	 *
	 * @covers \Quark\Departures\get_dates_rates_card_data()
	 *
	 * @return void
	 */
	public function test_get_dates_rates_card_data_with_adventure_options(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Create Adventure option post.
		$adventure_option_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => ADVENTURE_OPTION_POST_TYPE,
				'post_title'  => 'Adventure Option post 1',
				'post_status' => 'publish',
			]
		);

		// Create Adventure option post.
		$adventure_option_2 = $this->factory()->post->create_and_get(
			[
				'post_type'   => ADVENTURE_OPTION_POST_TYPE,
				'post_title'  => 'Adventure Option post 2',
				'post_status' => 'publish',
			]
		);

		// Assert created posts are instance of WP_Post.
		$this->assertTrue( $adventure_option_1 instanceof WP_Post );
		$this->assertTrue( $adventure_option_2 instanceof WP_Post );

		// Set terms.
		wp_set_object_terms(
			$adventure_option_1->ID,
			[
				self::$adventure_option_terms[0]->term_id,
			],
			ADVENTURE_OPTION_CATEGORY
		);

		// Set terms.
		wp_set_object_terms(
			$adventure_option_2->ID,
			[
				self::$adventure_option_terms[1]->term_id,
				self::$adventure_option_terms[2]->term_id,
			],
			ADVENTURE_OPTION_CATEGORY
		);

		// Set expedition post meta.
		wp_update_post(
			[
				'ID'         => self::$post_expedition->ID,
				'meta_input' => [
					'included_activities' => [
						$adventure_option_1->ID,
						$adventure_option_2->ID,
					],
				],
			]
		);

		// Sync softrip with existing posts.
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
					'key'     => 'softrip_code',
					'value'   => 'OEX20250904',
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

		// departure posts.
		$departure_query_args = [
			'post_type'              => POST_TYPE,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'posts_per_page'         => -1,
		];

		// Get Departure posts.
		$all_departures = get_posts( $departure_query_args );

		// assert fetched posts are 3.
		$this->assertCount( 7, $all_departures );

		// Get cards data.
		$card_data = get_dates_rates_card_data( $departure_post_1, 'CAD' );

		// Assert keys.
		$this->assertArrayHasKey( 'included_adventure_options', $card_data );
		$this->assertArrayHasKey( 'paid_adventure_options', $card_data );
		$this->assertArrayHasKey( 'cabin_data', $card_data );

		// Assert included adventure options.
		$this->assertEqualSetsWithIndex(
			[
				[
					'title'         => 'adventure_option_1',
					'icon_image_id' => '',
				],
				[
					'title'         => 'adventure_option_2',
					'icon_image_id' => '',
				],
				[
					'title'         => 'adventure_option_3',
					'icon_image_id' => '',
				],
			],
			$card_data['included_adventure_options'] ?? []
		);

		// Assert paid adventure options.
		$this->assertEqualSetsWithIndex(
			[
				[
					'title'            => 'adventure_option-1',
					'icon_image_id'    => '234',
					'spaces_available' => 0,
					'price_per_person' => '$1,090 CAD',
					'currency'         => 'CAD',
				],
				[
					'title'            => 'adventure_option-2',
					'icon_image_id'    => '987',
					'price_per_person' => '$270 CAD',
					'spaces_available' => 13,
					'currency'         => 'CAD',
				],
			],
			$card_data['paid_adventure_options'] ?? []
		);

		// Assert cabin count.
		$this->assertCount( 8, $card_data['cabin_data'] ?? [] );
		$this->assertEqualSetsWithIndex(
			[
				'name'                     => 'cabin_name - OEX-JST',
				'availability_status'      => 'R',
				'availability_description' => 'Please Call',
				'spaces_available'         => 0,
				'checkout_url'             => 'https://local-checkout.quarkexpeditions.com?package_id=HIJ-456&departure_date=2025-09-04&cabin_code=OEX-JST&currency=CAD',
				'brochure_price'           => '$46,050 CAD',
				'promos'                   => [
					'10PIF'   => '$41,470 CAD',
					'20PROMO' => '$36,890 CAD',
				],
				'type'                     => '',
				'sort_priority'            => 0,
			],
			$card_data['cabin_data']['OEX-JST'] ?? []
		);
	}

	/**
	 * Test getting promotion descriptions.
	 *
	 * @covers \Quark\Departures\get_promotions_description()
	 *
	 * @return void
	 */
	public function test_get_promotions_description(): void {
		// Default expected data.
		$default_expected = [];

		// Empty args.
		$actual = get_promotions_description();
		$this->assertEquals( $default_expected, $actual );

		// Default args.
		$actual = get_promotions_description( 0 );
		$this->assertEquals( $default_expected, $actual );

		// Invalid departure id.
		$actual = get_promotions_description( 999 );
		$this->assertEquals( $default_expected, $actual );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => POST_TYPE,
				'post_status' => 'publish',
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Test without setting any meta.
		$actual = get_promotions_description( $departure_post_id );
		$this->assertEquals( $default_expected, $actual );

		// Set non-array promotion codes on meta.
		update_post_meta( $departure_post_id, 'promotion_codes', '15PROMO' );

		// Test with non-array promotion codes.
		$actual = get_promotions_description( $departure_post_id );
		$this->assertEquals( $default_expected, $actual );

		// Bust cache.
		bust_post_cache( $departure_post_id );

		// Set valid promotion codes on meta.
		update_post_meta( $departure_post_id, 'promotion_codes', [ '15PROMO', '25PROMO' ] );

		// Test with valid promotion codes, but non-existing codes.
		$actual = get_promotions_description( $departure_post_id );
		$this->assertEquals( $default_expected, $actual );

		// Insert promotions in custom table.
		$raw_promotions_data = [
			[
				'endDate'       => '2025-01-01',
				'startDate'     => '2025-01-01',
				'description'   => 'Save 15%',
				'discountType'  => 'percentage',
				'discountValue' => '0.15',
				'promotionCode' => '15PROMO',
				'isPIF'         => 0,
			],
			[
				'endDate'       => '2025-01-01',
				'startDate'     => '2025-01-01',
				'description'   => 'Save 25%',
				'discountType'  => 'percentage',
				'discountValue' => '0.25',
				'promotionCode' => '25PROMO',
				'isPIF'         => 0,
			],
			[
				'endDate'       => '2025-01-01',
				'startDate'     => '2025-01-01',
				'description'   => 'Pay in Full and Save 30%',
				'discountType'  => 'percentage',
				'discountValue' => '0.30',
				'promotionCode' => '30PIF',
				'isPIF'         => 1,
			],
		];
		update_promotions( $raw_promotions_data, $departure_post_id );

		// Test if promotions are inserted.
		$promo_15 = get_promotions_by_code( '15PROMO' );
		$promo_25 = get_promotions_by_code( '25PROMO' );
		$promo_30 = get_promotions_by_code( '30PIF' );
		$this->assertCount( 1, $promo_15 );
		$this->assertCount( 1, $promo_25 );
		$this->assertCount( 1, $promo_30 );

		// Get first item.
		$promo_15 = $promo_15[0];
		$promo_25 = $promo_25[0];
		$promo_30 = $promo_30[0];
		$this->assertNotEmpty( $promo_15 );
		$this->assertNotEmpty( $promo_25 );
		$this->assertNotEmpty( $promo_30 );

		// Update post meta with valid and invalid promotion codes.
		update_post_meta( $departure_post_id, 'promotion_codes', [ '15PROMO', '25PROMO', '30PIF', 'INVALID' ] );

		// Bust cache.
		bust_post_cache( $departure_post_id );

		// Test with valid promotion codes.
		$actual = get_promotions_description( $departure_post_id );
		$this->assertEquals(
			[
				'Save 15% - Offer Code 15PROMO',
				'Save 25% - Offer Code 25PROMO',
				'Pay in Full and Save 30% - Offer Code 30PIF',
			],
			$actual
		);
	}
}
