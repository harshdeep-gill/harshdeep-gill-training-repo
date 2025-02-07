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
use function Quark\Ships\get_cabins_and_decks;
use function Quark\Ships\translate_meta_keys;
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
				'name'            => $ship_post->post_name,
				'title'           => $ship_post->post_title,
				'permalink'       => get_permalink( $ship_post->ID ),
				'description'     => $ship_post->post_excerpt,
				'related_decks'   => [ $deck_post_1->ID, $deck_post_2->ID ],
				'specifications'  => [
					'cruising_speed'   => '10',
					'guests'           => '100',
					'ice_class'        => '1A',
					'length'           => '100',
					'life_boats'       => '10',
					'staff_and_crew'   => '50',
					'year_refurbished' => '2020',
				],
				'collage_images'  => [],
				'vessel_features' => [],
				'ship_amenities'  => [],
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
				'data'            => [
					'collage'         => [],
					'vessel_features' => [],
					'ship_amenities'  => [],
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
				'data'            => [
					'collage'         => [
						[
							'media_type' => 'image',
							'size'       => 'small',
							'caption'    => '',
							'title'      => 'ABC',
							'image_id'   => 1324,
							'video_url'  => '',
							'image'      => [
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
							'media_type' => 'image',
							'size'       => 'large',
							'caption'    => '',
							'title'      => '',
							'image_id'   => 5421,
							'video_url'  => '',
							'image'      => [
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
							'media_type' => 'video',
							'video_url'  => 'https://abc.com/video.mp4',
							'size'       => 'small',
							'caption'    => '',
						],
					],
					'vessel_features' => [
						'Twin Engine Helicopters',
						'Four Embarkation Points',
						'MAGS',
					],
					'ship_amenities'  => [
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

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\Ships\translate_meta_keys()
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
				'meta_key'         => 'string',
				'icon'             => 'attachment',
				'deck_plan_image'  => 'attachment',
				'staff_and_crew'   => 'string',
				'draft'            => 'string',
				'registration'     => 'string',
				'guests'           => 'string',
				'guest_ratio'      => 'string',
				'stabilizers'      => 'string',
				'propulsion'       => 'string',
				'lifeboats'        => 'string',
				'zodiacs'          => 'string',
				'voltage'          => 'string',
				'length'           => 'string',
				'ice_class'        => 'string',
				'breadth'          => 'string',
				'gross_tonnage'    => 'string',
				'cruising_speed'   => 'string',
				'year_built'       => 'string',
				'year_refurbished' => 'string',
				'related_decks'    => 'Quark\Ships\translate_meta_key',
			],
			translate_meta_keys( $input )
		);
	}
}
