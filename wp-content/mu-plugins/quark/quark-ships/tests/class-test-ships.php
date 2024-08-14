<?php
/**
 * Quark Ships test suite.
 *
 * @package quark-ships
 */

namespace Quark\Ships\Tests;

use WP_UnitTestCase;
use WP_Post;

use function Quark\Ships\get_ship_data;
use function Quark\Ships\get_cabins_and_decks;

use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;

/**
 * Class Test_Ships.
 */
class Test_Ships extends WP_UnitTestCase {

	/**
	 * Test get_ship_data function.
	 *
	 * @covers \Quark\Ships\get_ship_data()
	 *
	 * @return void
	 */
	public function test_get_ship_data(): void {
		// Create test ship.
		$ship_post = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_POST_TYPE,
				'post_title'   => 'Test Ship Title',
				'post_status'  => 'publish',
				'post_content' => 'Test ship content',
			]
		);

		// Check if post is created.
		$this->assertTrue( $ship_post instanceof WP_Post );

		// Create Ship deck post.
		$deck_post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Ship Deck Post',
				'post_status'  => 'publish',
				'post_content' => 'Test ship deck content',
			]
		);
		$deck_post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_DECK_POST_TYPE,
				'post_title'   => 'Test Ship Deck Post 2',
				'post_status'  => 'publish',
				'post_content' => 'Test ship deck content 2',
			]
		);

		// Check if post is created.
		$this->assertTrue( $deck_post_1 instanceof WP_Post );
		$this->assertTrue( $deck_post_2 instanceof WP_Post );

		// Set Meta data.
		update_post_meta( $ship_post->ID, 'related_decks', [ $deck_post_1->ID, $deck_post_2->ID ] );

		// Assert ship data.
		$this->assertEquals(
			[
				'name'          => $ship_post->post_name,
				'title'         => $ship_post->post_title,
				'permalink'     => get_permalink( $ship_post->ID ),
				'description'   => apply_filters( 'the_content', $ship_post->post_content ),
				'related_decks' => [ $deck_post_1->ID, $deck_post_2->ID ],
			],
			get_ship_data( $ship_post->ID )
		);
	}

	/**
	 * Test get_cabins_and_decks.
	 *
	 * @covers \Quark\Ships\get_cabins_and_decks()
	 *
	 * @return void
	 */
	public function test_get_cabins_and_decks(): void {
		// Create Cabin Category posts.
		$cabin_category_posts = [];

		// Create 8 Cabin Category posts.
		for ( $i = 1; $i <= 8; $i++ ) {
			$cabin_category_post = $this->factory()->post->create_and_get(
				[
					'post_type'   => 'qrk_cabin_category',
					'post_title'  => 'Test Cabin Category ' . $i,
					'post_status' => 'publish',
					'meta_input'  => [
						'cabin_name' => 'Cabin category name - ' . $i,
					],
				]
			);

			// Check if post is created.
			$this->assertTrue( $cabin_category_post instanceof WP_Post );
			$cabin_category_posts[] = $cabin_category_post;
		}

		// Create 5 Ship deck posts.
		$deck_posts = [];

		/**
		 * Here - We are assigning 2 cabin categories to each deck post.
		 *  - i.e. Deck 1 - Category 1 and Category 2.
		 *  - i.e. Deck 5 - Category 5 and Category 6.
		 *  - and Category 7 and Category 8 don't have any deck assigned.
		 */

		// Create 5 Ship deck posts.
		for ( $i = 0; $i < 5; $i++ ) {
			$deck_post = $this->factory()->post->create_and_get(
				[
					'post_type'   => SHIP_DECK_POST_TYPE,
					'post_title'  => 'Test Ship Deck Post ' . $i,
					'post_status' => 'publish',
					'meta_input'  => [
						'deck_name'        => sprintf( 'Deck Name - %s', $i + 1 ),
						'cabin_categories' => array_map(
							function ( $cabin_category_post ) {
								return $cabin_category_post->ID;
							},
							array_slice( $cabin_category_posts, $i, 2 )
						),
					],
				]
			);

			// Check if post is created.
			$this->assertTrue( $deck_post instanceof WP_Post );
			$deck_posts[] = $deck_post;
		}

		// Create test ship.
		$ship_post = $this->factory()->post->create_and_get(
			[
				'post_type'   => SHIP_POST_TYPE,
				'post_title'  => 'Test Ship Title',
				'post_status' => 'publish',
				'meta_input'  => [
					'related_decks' => array_map(
						function ( $deck_post ) {
							return $deck_post->ID;
						},
						$deck_posts
					),
				],
			]
		);

		// Check if post is created.
		$this->assertTrue( $ship_post instanceof WP_Post );

		// Get get_cabins_and_decks.
		$comparison_data = get_cabins_and_decks( $ship_post->ID );

		// Assert comparison data.
		$this->assertEqualsCanonicalizing(
			[
				[
					'cabin_name' => 'Cabin category name - 1',
					'ship_deck'  => [
						'Deck Name - 1',
					],
				],
				[
					'cabin_name' => 'Cabin category name - 2',
					'ship_deck'  => [
						'Deck Name - 1',
						'Deck Name - 2',
					],
				],
				[
					'cabin_name' => 'Cabin category name - 3',
					'ship_deck'  => [
						'Deck Name - 2',
						'Deck Name - 3',
					],
				],
				[
					'cabin_name' => 'Cabin category name - 4',
					'ship_deck'  => [
						'Deck Name - 3',
						'Deck Name - 4',
					],
				],
				[
					'cabin_name' => 'Cabin category name - 5',
					'ship_deck'  => [
						'Deck Name - 4',
						'Deck Name - 5',
					],
				],
				[
					'cabin_name' => 'Cabin category name - 6',
					'ship_deck'  => [
						'Deck Name - 5',
					],
				],
			],
			$comparison_data
		);
	}
}
