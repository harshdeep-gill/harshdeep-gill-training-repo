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
				'post_excerpt' => 'Test ship excerpt',
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
		update_post_meta( $ship_post->ID, 'cruising_speed', '10' );
		update_post_meta( $ship_post->ID, 'guests', '100' );
		update_post_meta( $ship_post->ID, 'ice_class', '1A' );
		update_post_meta( $ship_post->ID, 'length', '100' );
		update_post_meta( $ship_post->ID, 'lifeboats', '10' );
		update_post_meta( $ship_post->ID, 'staff_and_crew', '50' );
		update_post_meta( $ship_post->ID, 'year_refurbished', '2020' );

		// Assert ship data.
		$this->assertEquals(
			[
				'name'           => $ship_post->post_name,
				'title'          => $ship_post->post_title,
				'permalink'      => get_permalink( $ship_post->ID ),
				'description'    => $ship_post->post_excerpt,
				'related_decks'  => [ $deck_post_1->ID, $deck_post_2->ID ],
				'specifications' => [
					'cruising_speed'   => '10',
					'guests'           => '100',
					'ice_class'        => '1A',
					'length'           => '100',
					'life_boats'       => '10',
					'staff_and_crew'   => '50',
					'year_refurbished' => '2020',
				],
			],
			get_ship_data( $ship_post->ID )
		);
	}
}
