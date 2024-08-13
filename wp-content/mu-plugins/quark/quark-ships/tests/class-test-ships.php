<?php
/**
 * Quark Ships test suite.
 *
 * @package quark-ships
 */

namespace Quark\Ships\Tests;

use WP_UnitTestCase;
use WP_Post;
use WP_Term;

use function Quark\Ships\get_ship_data;
use function Quark\Ships\parse_block_attributes;
use function Quark\Ships\get;

use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\Ships\SHIP_CATEGORY_TAXONOMY;
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
	 * Test get().
	 *
	 * @covers \Quark\Ships\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_POST_TYPE,
				'post_title'   => 'Test Post',
				'post_status'  => 'publish',
				'post_content' => '<!-- wp:paragraph --><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed consequat maximus fringilla.</p><!-- /wp:paragraph -->',
				'meta_input'   => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Set SHIP_CATEGORY_TAXONOMY taxonomy term.
		$ship_category_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => SHIP_CATEGORY_TAXONOMY,
				'name'     => 'Test Term',
			]
		);

		// Assert term is created.
		$this->assertTrue( $ship_category_1 instanceof WP_Term );

		// Assign term to post.
		wp_set_object_terms( $post_1->ID, $ship_category_1->term_id, SHIP_CATEGORY_TAXONOMY );

		// Get post.
		$the_post = get( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				'post'            => $post_1,
				'permalink'       => get_permalink( $post_1->ID ),
				'post_meta'       => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
				'post_taxonomies' => [
					SHIP_CATEGORY_TAXONOMY => [
						[
							'term_id'     => strval( $ship_category_1->term_id ),
							'name'        => $ship_category_1->name,
							'slug'        => $ship_category_1->slug,
							'taxonomy'    => $ship_category_1->taxonomy,
							'description' => $ship_category_1->description,
							'parent'      => $ship_category_1->parent,
							'term_group'  => $ship_category_1->term_group,
						],
					],
				],
				'block_attrs'     => [
					'ship_collage'               => [],
					'ship_vessel_features_title' => [],
					'ship_amenities_title'       => [],
				],
			],
			$the_post
		);
	}

	/**
	 * Test get() with Blocks.
	 *
	 * @covers \Quark\Ships\get()
	 * @covers \Quark\Ships\parse_block_attributes()
	 *
	 * @return void
	 */
	public function test_get_with_blocks(): void {
		// Generate post content.
		$post_content = '
		<!-- wp:quark/collage -->
			<!-- wp:quark/collage-media-item {"image":{"id":1324,"src":"","width":1234,"height":2233,"alt":"","caption":"","title":"ABC","size":"full"}} /-->
			<!-- wp:quark/collage-media-item {"size":"large","image":{"id":5421,"src":"","width":200,"height":112,"alt":"","caption":"","title":"","size":"full"}} /-->
			<!-- wp:quark/collage-media-item {"mediaType":"video","videoUrl":"https://abc.com/video.mp4"} /-->
		<!-- /wp:quark/collage -->
		<!-- wp:paragraph --><p>The newest ship in our fleet.</p><!-- /wp:paragraph -->
		<!-- wp:quark/ship-vessel-features -->
			<!-- wp:quark/ship-vessel-features-card {"title":"Twin Engine Helicopters","image":{"id":4261,"src":"","width":"","height":"","alt":"","caption":"","title":"","size":""},"description":"Two twin-engine helicopters operated."} /-->
			<!-- wp:quark/ship-vessel-features-card {"title":"Four Embarkation Points","image":{"id":15190,"src":"","width":5616,"height":3744,"alt":"","caption":"","title":"","size":"full"},"description":"Embarkation points on the starboard."} /-->
			<!-- wp:quark/ship-vessel-features-card {"title":"MAGS","image":{"id":5217,"src":"https://test.quarkexpeditions.com/plc.jpg","width":300,"height":240,"alt":"","caption":"","title":"plc_antarctic_images.jpg","size":"medium"},"description":"Micro Auto Gasification System."} /-->
		<!-- /wp:quark/ship-vessel-features -->
		<!-- wp:paragraph --><p>With all this and more,</p><!-- /wp:paragraph -->
		<!-- wp:quark/ship-features-amenities -->
			<!-- wp:quark/ship-features-amenities-card {"title":"Ready Rooms","image":{"id":6270,"src":"https://test.quarkexpeditions.com/ready_room.jpg","width":300,"height":200,"alt":"","caption":"","title":"","size":"medium"},"description":"Designed for efficiency—with wider hallways."} /-->
			<!-- wp:quark/ship-features-amenities-card {"title":"Polar Boutique","image":{"id":6310,"src":"https://test.quarkexpeditions.com/polar_boutique.jpg","width":300,"height":150,"alt":"","caption":"","title":"","size":"medium"},"description":"Forgot something."} /-->
			<!-- wp:quark/ship-features-amenities-card {"title":"Ambassador Theater","image":{"id":6317,"src":"https://test.quarkexpeditions.com/Theater.jpg","width":300,"height":168,"alt":"","caption":"","title":"Ultramarine Auditorium","size":"medium"},"description":"A state-of-the-art angle."} /-->
			<!-- wp:quark/ship-features-amenities-card {"title":"Balena Restaurant","image":{"id":3636,"src":"https://test.quarkexpeditions.com/Arctic-Watch.jpg","width":300,"height":199,"alt":"","caption":"","title":"Food-at-Arctic-Watch.jpg","size":"medium"},"description":"Featuring table tops."} /-->
		<!-- /wp:quark/ship-features-amenities -->
		';

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'    => SHIP_POST_TYPE,
				'post_title'   => 'Test Post',
				'post_status'  => 'publish',
				'meta_input'   => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
				'post_content' => $post_content,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get post.
		$the_post = get( $post_1->ID );

		// Assert expected get data is equal to actual data.
		$this->assertEquals(
			[
				'post'            => $post_1,
				'permalink'       => get_permalink( $post_1->ID ),
				'post_meta'       => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
				'post_taxonomies' => [],
				'block_attrs'     => [
					'ship_collage'               => [
						[
							'image' => [
								'id'      => 1324,
								'src'     => '',
								'width'   => 1234,
								'height'  => 2233,
								'alt'     => '',
								'caption' => '',
								'title'   => 'ABC',
								'size'    => 'full',
							],
						],
						[
							'size'  => 'large',
							'image' => [
								'id'      => 5421,
								'src'     => '',
								'width'   => 200,
								'height'  => 112,
								'alt'     => '',
								'caption' => '',
								'title'   => '',
								'size'    => 'full',
							],
						],
						[
							'mediaType' => 'video',
							'videoUrl'  => 'https://abc.com/video.mp4',
						],
					],
					'ship_vessel_features_title' => [
						'Twin Engine Helicopters',
						'Four Embarkation Points',
						'MAGS',
					],
					'ship_amenities_title'       => [
						'Ready Rooms',
						'Polar Boutique',
						'Ambassador Theater',
						'Balena Restaurant',
					],
				],
			],
			$the_post
		);
	}
}
