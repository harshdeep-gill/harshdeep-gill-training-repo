<?php
/**
 * Cabin category test suite.
 *
 * @package quark-cabin-categories
 */

namespace Quark\CabinCategories\Tests;

use WP_UnitTestCase;
use WP_Post;
use WP_Term;

use function Quark\CabinCategories\get_cabin_categories_data;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;

/**
 * Class Test_Cabin_Categories.
 */
class Test_Cabin_Categories extends WP_UnitTestCase {

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
}
