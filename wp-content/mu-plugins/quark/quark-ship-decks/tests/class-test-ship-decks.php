<?php
/**
 * Quark Ship Decks test suite.
 *
 * @package quark-ship-decks
 */

namespace Quark\ShipDecks\Tests;

use WP_UnitTestCase;
use WP_Post;
use WP_Term;

use function Quark\ShipDecks\get_cabin_options;
use function Quark\ShipDecks\prepare_public_spaces;
use function Quark\ShipDecks\get_deck_data;
use function Quark\ShipDecks\translate_meta_keys;

use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;

/**
 * Class Test_Ship_Decks.
 */
class Test_Ship_Decks extends WP_UnitTestCase {

	/**
	 * Test prepare_public_spaces function.
	 *
	 * @covers \Quark\ShipDecks\prepare_public_spaces()
	 *
	 * @return void
	 */
	public function test_prepare_public_spaces(): void {
		// Create test meta data.
		$test_meta_data = [
			'name'                        => 'Test Ship Deck Title',
			'public_spaces_0_title'       => 'Test Public Space 0 Title',
			'public_spaces_0_description' => 'Test Public Space 0 Content',
			'public_spaces_0_image'       => 0,
			'public_spaces_1_title'       => 'Test Public Space 1 Title',
			'public_spaces_1_description' => 'Test Public Space 1 Content',
			'public_spaces_1_image'       => 1,
			'random_key'                  => 'Random Value',
		];

		// Assert data.
		$this->assertEquals(
			[
				[
					'title'       => 'Test Public Space 0 Title',
					'description' => apply_filters( 'the_content', 'Test Public Space 0 Content' ),
					'image'       => 0,
				],
				[
					'title'       => 'Test Public Space 1 Title',
					'description' => apply_filters( 'the_content', 'Test Public Space 1 Content' ),
					'image'       => 1,
				],
			],
			prepare_public_spaces( $test_meta_data )
		);
	}

	/**
	 * Test get_deck_data function.
	 *
	 * @covers \Quark\ShipDecks\get_deck_data()
	 *
	 * @return void
	 */
	public function test_get_deck_data(): void {
		// Create post.
		$deck_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Ship Deck Post',
				'post_status'  => 'publish',
				'post_content' => 'Test ship deck content',
			]
		);

		// Check if post is a WP_Post object.
		$this->assertTrue( $deck_post instanceof WP_Post );

		// Create cabin category post.
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

		// Update post meta.
		update_post_meta( $deck_post->ID, 'deck_name', 'Test Ship Deck Title' );
		update_post_meta( $deck_post->ID, 'deck_plan_image', 777 );
		update_post_meta( $deck_post->ID, 'vertical_deck_plan_image', 888 );
		update_post_meta( $deck_post->ID, 'public_spaces_0_title', 'Test Public Space 0 Title' );
		update_post_meta( $deck_post->ID, 'public_spaces_0_description', 'Test Public Space 0 Content' );
		update_post_meta( $deck_post->ID, 'public_spaces_0_image', 0 );
		update_post_meta( $deck_post->ID, 'public_spaces_1_title', 'Test Public Space 1 Title' );
		update_post_meta( $deck_post->ID, 'public_spaces_1_description', 'Test Public Space 1 Content' );
		update_post_meta( $deck_post->ID, 'public_spaces_1_image', 1 );
		update_post_meta( $deck_post->ID, 'cabin_categories', [ $cabin_category_post->ID ] );

		// Assert data.
		$this->assertEquals(
			[
				'id'                => $deck_post->post_name,
				'title'             => 'Test Ship Deck Title',
				'image_id'          => 777,
				'vertical_image_id' => 888,
				'description'       => apply_filters( 'the_content', 'Test ship deck content' ),
				'cabin_options'     => [
					[
						'id'          => $cabin_category_post->post_name,
						'title'       => 'Test Title',
						'image_id'    => 0,
						'description' => apply_filters( 'the_content', 'Test cabin category content' ),
						'details'     => [
							'size_from'         => '100',
							'size_to'           => '200',
							'occupancy_from'    => '10',
							'occupancy_to'      => '20',
							'bed_configuration' => apply_filters( 'the_content', 'Test Bed Configuration' ),
							'location'          => $deck_post_1->post_title . ', ' . $deck_post_2->post_title,
							'class'             => 'Test Cabin Class',
						],
					],
				],
				'public_spaces'     => [
					[
						'title'       => 'Test Public Space 0 Title',
						'description' => apply_filters( 'the_content', 'Test Public Space 0 Content' ),
						'image'       => 0,
					],
					[
						'title'       => 'Test Public Space 1 Title',
						'description' => apply_filters( 'the_content', 'Test Public Space 1 Content' ),
						'image'       => 1,
					],
				],
			],
			get_deck_data( $deck_post->ID )
		);
	}

	/**
	 * Test get_cabin_options function.
	 *
	 * @covers \Quark\ShipDecks\get_cabin_options()
	 *
	 * @return void
	 */
	public function test_get_cabin_options(): void {
		// Create post.
		$cabin_category_post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
				'post_title'   => 'Test Cabin Category Post',
				'post_status'  => 'publish',
				'post_content' => 'Test cabin category content',
			]
		);
		$cabin_category_post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
				'post_title'   => 'Test Cabin Category Post 2',
				'post_status'  => 'publish',
				'post_content' => 'Test cabin category content 2',
			]
		);

		// Make sure post is created.
		$this->assertTrue( $cabin_category_post_1 instanceof WP_Post );
		$this->assertTrue( $cabin_category_post_2 instanceof WP_Post );

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
		$deck_post_3 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Deck Post 3',
				'post_status'  => 'publish',
				'post_content' => 'Test deck content 3',
			]
		);
		$deck_post_4 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Deck Post 4',
				'post_status'  => 'publish',
				'post_content' => 'Test deck content 4',
			]
		);

		// Make sure post is created.
		$this->assertTrue( $deck_post_1 instanceof WP_Post );
		$this->assertTrue( $deck_post_2 instanceof WP_Post );
		$this->assertTrue( $deck_post_3 instanceof WP_Post );
		$this->assertTrue( $deck_post_4 instanceof WP_Post );

		// Set Meta data for cabin category post 1.
		update_post_meta( $cabin_category_post_1->ID, 'cabin_name', 'Test Title' );
		update_post_meta( $cabin_category_post_1->ID, 'cabin_category_size_range_from', '100' );
		update_post_meta( $cabin_category_post_1->ID, 'cabin_category_size_range_to', '200' );
		update_post_meta( $cabin_category_post_1->ID, 'cabin_occupancy_pax_range_from', '10' );
		update_post_meta( $cabin_category_post_1->ID, 'cabin_occupancy_pax_range_to', '20' );
		update_post_meta( $cabin_category_post_1->ID, 'cabin_bed_configuration', 'Test Bed Configuration' );
		update_post_meta( $cabin_category_post_1->ID, 'related_decks', [ $deck_post_1->ID, $deck_post_2->ID ] );
		update_post_meta( $deck_post_1->ID, 'deck_name', 'Test Deck Post' );
		update_post_meta( $deck_post_2->ID, 'deck_name', 'Test Deck Post 2' );

		// Set Meta data for cabin category post 2.
		update_post_meta( $cabin_category_post_2->ID, 'cabin_name', 'Test Title 2' );
		update_post_meta( $cabin_category_post_2->ID, 'cabin_category_size_range_from', '200' );
		update_post_meta( $cabin_category_post_2->ID, 'cabin_category_size_range_to', '300' );
		update_post_meta( $cabin_category_post_2->ID, 'cabin_occupancy_pax_range_from', '20' );
		update_post_meta( $cabin_category_post_2->ID, 'cabin_occupancy_pax_range_to', '30' );
		update_post_meta( $cabin_category_post_2->ID, 'cabin_bed_configuration', 'Test Bed Configuration 2' );
		update_post_meta( $cabin_category_post_2->ID, 'related_decks', [ $deck_post_3->ID, $deck_post_4->ID ] );
		update_post_meta( $deck_post_3->ID, 'deck_name', 'Test Deck Post 3' );
		update_post_meta( $deck_post_4->ID, 'deck_name', 'Test Deck Post 4' );

		// Create Taxonomy.
		$cabin_class_tax   = $this->factory()->term->create_and_get(
			[
				'taxonomy' => CABIN_CLASS_TAXONOMY,
				'name'     => 'Test Cabin Class',
			]
		);
		$cabin_class_tax_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => CABIN_CLASS_TAXONOMY,
				'name'     => 'Test Cabin Class 2',
			]
		);

		// Make sure taxonomy is created.
		$this->assertTrue( $cabin_class_tax instanceof WP_Term );
		$this->assertTrue( $cabin_class_tax_2 instanceof WP_Term );

		// Assign taxonomy to post.
		wp_set_post_terms( $cabin_category_post_1->ID, [ $cabin_class_tax->term_id ], CABIN_CLASS_TAXONOMY );
		wp_set_post_terms( $cabin_category_post_2->ID, [ $cabin_class_tax_2->term_id ], CABIN_CLASS_TAXONOMY );

		// Assert data.
		$this->assertEquals(
			[
				[
					'id'          => $cabin_category_post_1->post_name,
					'title'       => 'Test Title',
					'image_id'    => 0,
					'description' => apply_filters( 'the_content', $cabin_category_post_1->post_content ),
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
				[
					'id'          => $cabin_category_post_2->post_name,
					'title'       => 'Test Title 2',
					'image_id'    => 0,
					'description' => apply_filters( 'the_content', $cabin_category_post_2->post_content ),
					'details'     => [
						'size_from'         => '200',
						'size_to'           => '300',
						'occupancy_from'    => '20',
						'occupancy_to'      => '30',
						'bed_configuration' => apply_filters( 'the_content', 'Test Bed Configuration 2' ),
						'location'          => $deck_post_3->post_title . ', ' . $deck_post_4->post_title,
						'class'             => $cabin_class_tax_2->name,
					],
				],
			],
			get_cabin_options( [ $cabin_category_post_1->ID, $cabin_category_post_2->ID ] )
		);
	}

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\ShipDecks\translate_meta_keys()
	 *
	 * @return void
	 */
	public function test_translate_meta_keys(): void {
		// Input data.
		$input = [
			'meta_key' => 'string',
			'icon'     => 'attachment',
		];

		// Assert data.
		$this->assertEquals(
			[
				'meta_key'                      => 'string',
				'icon'                          => 'attachment',
				'deck_name'                     => 'string',
				'deck_plan_image'               => 'attachment',
				'vertical_deck_plan_image'      => 'attachment',
				'cabin_categories'              => 'post',
				'public_spaces_\d+_title'       => 'string',
				'public_spaces_\d+_image'       => 'attachment',
				'public_spaces_\d+_description' => 'string',
			],
			translate_meta_keys( $input )
		);
	}
}
