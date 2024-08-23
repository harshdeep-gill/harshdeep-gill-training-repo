<?php
/**
 * Cabin category test suite.
 *
 * @package quark-cabin-categories
 */

namespace Quark\CabinCategories\Tests;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Post;
use WP_Query;
use WP_Term;

use function Quark\CabinCategories\get_cabin_categories_data;
use function Quark\CabinCategories\get_cabin_details_by_departure;
use function Quark\Softrip\do_sync;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\Core\AUD_CURRENCY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;

/**
 * Class Test_Cabin_Categories.
 */
class Test_Cabin_Categories extends Softrip_TestCase {

	/**
	 * Test get_cabin_categories_data function.
	 *
	 * @covers \Quark\CabinCategories\get_cabin_categories_data()
	 *
	 * @return void
	 */
	public function test_get_cabin_categories_data(): void {
		// Create post.
		$cabin_category_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
				'post_title'   => 'Test Cabin Category Post',
				'post_status'  => 'publish',
				'post_content' => 'Test cabin category content',
			]
		);

		// Make sure post is created.
		$this->assertTrue( $cabin_category_post instanceof WP_Post );

		// Create post of Deck type.
		$deck_post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Deck Post',
				'post_status'  => 'publish',
				'post_content' => 'Test deck content',
			]
		);
		$deck_post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Deck Post 2',
				'post_status'  => 'publish',
				'post_content' => 'Test deck content 2',
			]
		);

		// Make sure post is created.
		$this->assertTrue( $deck_post_1 instanceof WP_Post );
		$this->assertTrue( $deck_post_2 instanceof WP_Post );

		// Set Meta data.
		update_post_meta( $cabin_category_post->ID, 'cabin_name', 'Test Title' );
		update_post_meta( $cabin_category_post->ID, 'cabin_category_size_range_from', '100' );
		update_post_meta( $cabin_category_post->ID, 'cabin_category_size_range_to', '200' );
		update_post_meta( $cabin_category_post->ID, 'cabin_occupancy_pax_range_from', '10' );
		update_post_meta( $cabin_category_post->ID, 'cabin_occupancy_pax_range_to', '20' );
		update_post_meta( $cabin_category_post->ID, 'cabin_bed_configuration', 'Test Bed Configuration' );
		update_post_meta( $cabin_category_post->ID, 'related_decks', [ $deck_post_1->ID, $deck_post_2->ID ] );
		update_post_meta( $deck_post_1->ID, 'deck_name', 'Test Deck Post' );
		update_post_meta( $deck_post_2->ID, 'deck_name', 'Test Deck Post 2' );

		// Create Taxonomy.
		$cabin_class_tax = $this->factory()->term->create_and_get(
			[
				'taxonomy' => CABIN_CLASS_TAXONOMY,
				'name'     => 'Test Cabin Class',
			]
		);

		// Make sure taxonomy is created.
		$this->assertTrue( $cabin_class_tax instanceof WP_Term );

		// Assign taxonomy to post.
		wp_set_post_terms( $cabin_category_post->ID, [ $cabin_class_tax->term_id ], CABIN_CLASS_TAXONOMY );

		// Assert data.
		$this->assertEquals(
			[
				'id'          => $cabin_category_post->post_name,
				'title'       => 'Test Title',
				'image_id'    => 0,
				'description' => apply_filters( 'the_content', $cabin_category_post->post_content ),
				'details'     => [
					'size_from'         => '100',
					'size_to'           => '200',
					'occupancy_from'    => '10',
					'occupancy_to'      => '20',
					'bed_configuration' => apply_filters( 'the_content', 'Test Bed Configuration' ),
					'location'          => $deck_post_1->post_title . ', ' . $deck_post_2->post_title,
					'class'             => $cabin_class_tax->name,
				],
			],
			get_cabin_categories_data( $cabin_category_post->ID )
		);
	}

	/**
	 * Test get cabin details by departure.
	 *
	 * @covers \Quark\CabinCategories\get_cabin_details_by_departure()
	 *
	 * @return void
	 */
	public function test_get_cabin_details_by_departure(): void {
		// Test with no departure.
		$expected = [];
		$actual = get_cabin_details_by_departure();
		$this->assertEquals( $expected, $actual );

		// Default args.
		$expected = [];
		$actual = get_cabin_details_by_departure( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid departure.
		$expected = [];
		$actual = get_cabin_details_by_departure( 9999 );
		$this->assertEquals( $expected, $actual );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Fetch departure posts.
		$departure_query_args = [
			'post_type'              => DEPARTURE_POST_TYPE,
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

		// Get departure post.
		$departure_post_ids = get_posts( $departure_query_args );

		// Assert fetched posts is 1.
		$this->assertCount( 1, $departure_post_ids );

		// Get first post.
		$departure_post_id1 = $departure_post_ids[0];
		$this->assertIsInt( $departure_post_id1 );

		// Get cabin category post for ULT-SGL.
		$cabin_category_posts = get_posts(
			[
				'post_type'      => CABIN_CATEGORY_POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'suppress_filters' => false,
				'ignore_sticky_posts' => true,
				'meta_query' => [
					[
					'key' => 'cabin_category_id',
					'value' => 'ULT-SGL',
					]
				],
			]
		);
		$this->assertCount( 1, $cabin_category_posts );

		// Get first post.
		$cabin_category_post_id1 = $cabin_category_posts[0];
		$this->assertIsInt( $cabin_category_post_id1 );

		// Get cabin category post for ULT-DBL.
		$cabin_category_posts = get_posts(
			[
				'post_type'      => CABIN_CATEGORY_POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'suppress_filters' => false,
				'ignore_sticky_posts' => true,
				'meta_query' => [
					[
					'key' => 'cabin_category_id',
					'value' => 'ULT-DBL',
					]
				],
			]
		);
		$this->assertCount( 1, $cabin_category_posts );

		// Get first post.
		$cabin_category_post_id2 = $cabin_category_posts[0];
		$this->assertIsInt( $cabin_category_post_id2 );

		// Get cabin details.
		$cabin_details = get_cabin_details_by_departure( $departure_post_id1 );

		// Prepare expected data.
		$expected = [
			'ULT-SGL' => [
				'cabin_code' => 'ULT-SGL',
				'description' => 'Post content 0000045',
				'name' => 'cabin_name - ULT-SGL',
				'gallery' => [],
				'type' => '',
				'specifications' => [
					'availability_status' => 'R',
					'availability_description' => 'Please Call',
					'spaces_available' => 0,
					'occupancy' => '',
					'location' => '',
					'size' => '',
					'bed_configuration' => '',
				],
				'from_price' => [
					'discounted_price' => '$38,169 USD',
					'original_price' => '$44,905 USD',
				],
				'occupancies' => [
					[
						'name' => 'A',
						'description' => 'Single Room',
						'no_of_guests' => '1',
						'price' => [
							'original_price' => '$44,905 USD',
							'discounted_price' => '$38,169 USD',
						],
						'promotions' => [],
					]
				],
			],
			'ULT-DBL' => [
				'cabin_code' => 'ULT-DBL',
				'description' => 'Post content 0000046',
				'name' => 'cabin_name - ULT-DBL',
				'gallery' => [],
				'type' => '',
				'specifications' => [
					'availability_status' => 'R',
					'availability_description' => 'Please Call',
					'spaces_available' => 0,
					'occupancy' => '',
					'location' => '',
					'size' => '',
					'bed_configuration' => '',
				],
				'from_price' => [
					'discounted_price' => '$29,410 USD',
					'original_price' => '$34,600 USD',
				],
				'occupancies' => [
					[
						'name' => 'A',
						'description' => 'Single Room',
						'no_of_guests' => '1',
						'price' => [
							'original_price' => '$74,900 USD',
							'discounted_price' => '$63,665 USD',
						],
						'promotions' => [],
					],
					[
						'name' => 'AA',
						'description' => 'Double Room',
						'no_of_guests' => '2',
						'price' => [
							'original_price' => '$34,600 USD',
							'discounted_price' => '$29,410 USD',
						],
						'promotions' => [],
					]
				],
			]
		];

		// Assert data.
		$this->assertEquals( $expected, $cabin_details );

		// Create cabin class taxonomy term.
		$standard_cabin_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => CABIN_CLASS_TAXONOMY,
				'name'     => 'Standard',
			]
		);
		$this->assertInstanceOf( WP_Term::class, $standard_cabin_term );

		// Create one more term.
		$premium_cabin_term = $this->factory()->term->create_and_get(
			[
				'taxonomy' => CABIN_CLASS_TAXONOMY,
				'name'     => 'Premium',
			]
		);
		$this->assertInstanceOf( WP_Term::class, $premium_cabin_term );

		// Set cabin class taxonomy term to cabin category post.
		wp_set_post_terms( $cabin_category_post_id1, [ $standard_cabin_term->term_id ], CABIN_CLASS_TAXONOMY );

		// Set cabin class taxonomy term to cabin category post.
		wp_set_post_terms( $cabin_category_post_id2, [ $premium_cabin_term->term_id ], CABIN_CLASS_TAXONOMY );

		// Add dummy gallery images id on meta.
		update_post_meta( $cabin_category_post_id1, 'cabin_images', [ 1, 2, 3 ] );

		// Add from and to size range.
		update_post_meta( $cabin_category_post_id1, 'cabin_category_size_range_from', '100' );
		update_post_meta( $cabin_category_post_id1, 'cabin_category_size_range_to', '200' );

		// Add bed configuration.
		update_post_meta( $cabin_category_post_id1, 'cabin_bed_configuration', 'Test Bed Configuration' );

		// Flush cache.
		wp_cache_delete( CABIN_CATEGORY_POST_TYPE . '_' . $cabin_category_post_id1, CABIN_CATEGORY_POST_TYPE );
		wp_cache_delete( CABIN_CATEGORY_POST_TYPE . '_' . $cabin_category_post_id2, CABIN_CATEGORY_POST_TYPE );

		// Get cabin details.
		$cabin_details = get_cabin_details_by_departure( $departure_post_id1 );

		// Prepare expected data.
		$expected = [
			'ULT-SGL' => [
				'cabin_code' => 'ULT-SGL',
				'description' => 'Post content 0000045',
				'name' => 'cabin_name - ULT-SGL',
				'gallery' => [1, 2, 3],
				'type' => 'Standard',
				'specifications' => [
					'availability_status' => 'R',
					'availability_description' => 'Please Call',
					'spaces_available' => 0,
					'occupancy' => '',
					'location' => '',
					'size' => '100 - 200',
					'bed_configuration' => 'Test Bed Configuration',
				],
				'from_price' => [
					'discounted_price' => '$38,169 USD',
					'original_price' => '$44,905 USD',
				],
				'occupancies' => [
					[
						'name' => 'A',
						'description' => 'Single Room',
						'no_of_guests' => '1',
						'price' => [
							'original_price' => '$44,905 USD',
							'discounted_price' => '$38,169 USD',
						],
						'promotions' => [],
					]
				],
			],
			'ULT-DBL' => [
				'cabin_code' => 'ULT-DBL',
				'description' => 'Post content 0000046',
				'name' => 'cabin_name - ULT-DBL',
				'gallery' => [],
				'type' => 'Premium',
				'specifications' => [
					'availability_status' => 'R',
					'availability_description' => 'Please Call',
					'spaces_available' => 0,
					'occupancy' => '',
					'location' => '',
					'size' => '',
					'bed_configuration' => '',
				],
				'from_price' => [
					'discounted_price' => '$29,410 USD',
					'original_price' => '$34,600 USD',
				],
				'occupancies' => [
					[
						'name' => 'A',
						'description' => 'Single Room',
						'no_of_guests' => '1',
						'price' => [
							'original_price' => '$74,900 USD',
							'discounted_price' => '$63,665 USD',
						],
						'promotions' => [],
					],
					[
						'name' => 'AA',
						'description' => 'Double Room',
						'no_of_guests' => '2',
						'price' => [
							'original_price' => '$34,600 USD',
							'discounted_price' => '$29,410 USD',
						],
						'promotions' => [],
					]
				]
			],
		];

		// Assert data.
		$this->assertEquals( $expected, $cabin_details );
	}
}
