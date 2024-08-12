<?php
/**
 * Departure test suite.
 *
 * @package quark-departures
 */

namespace Quark\Departures\Tests;

use WP_Post;
use WP_Term;
use WP_UnitTestCase;

use function Quark\Departures\bust_post_cache;
use function Quark\Departures\get;
use function Quark\Departures\get_region_and_season;
use function Quark\Departures\get_paid_adventure_options;
use function Quark\Departures\get_languages;
use function Quark\Departures\get_promotion_tags;

use const Quark\Departures\POST_TYPE;
use const Quark\Departures\PROMOTION_TAG;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;

/**
 * Class Test_Departure.
 */
class Test_Departures extends WP_UnitTestCase {

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
	 * Test get_region_and_season.
	 *
	 * @covers \Quark\Departures\get_region_and_season()
	 * @covers \Quark\Departures\get_season()
	 *
	 * @return void
	 */
	public function test_get_region_and_season(): void {
		// Create itinerary post.
		$post_itinerary = $this->factory()->post->create_and_get(
			[
				'post_type'   => ITINERARY_POST_TYPE,
				'post_title'  => 'Test itinerary Post',
				'post_status' => 'publish',
			]
		);

		// Make sure post is created.
		$this->assertTrue( $post_itinerary instanceof WP_Post );

		// Create SEASON terms.
		$season_term = $this->factory()->term->create_and_get(
			[
				'name'     => 'season_1',
				'taxonomy' => SEASON_TAXONOMY,
			]
		);

		// Assert term is created.
		$this->assertTrue( $season_term instanceof WP_Term );

		// Set terms.
		wp_set_object_terms( $post_itinerary->ID, $season_term->term_id, SEASON_TAXONOMY );

		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'region'    => 'value_1',
					'itinerary' => $post_itinerary->ID,
				],
			]
		);

		// Make sure post is created.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Get departure region and season.
		$region_and_season = get_region_and_season( $post_1->ID );

		// Test if region is correct.
		$this->assertEquals( 'value_1-season_1', $region_and_season );

		// clean up.
		wp_delete_post( $post_1->ID, true );
		wp_delete_post( $post_itinerary->ID, true );
		wp_delete_term( $season_term->term_id, SEASON_TAXONOMY );
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
}
