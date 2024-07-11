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

use function Quark\Departures\get;
use function Quark\Departures\get_departure_region_and_season;

use const Quark\Departures\POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;

/**
 * Class Test_Departure.
 */
class Test_Departure extends WP_UnitTestCase {

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
	 * Test get_departure_region_and_season().
	 *
	 * @covers \Quark\Departures\get_departure_region_and_season()
	 * @covers \Quark\Departures\get_departure_season()
	 *
	 * @return void
	 */
	public function test_get_departure_region_and_season(): void {
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
		$region_and_season = get_departure_region_and_season( $post_1->ID );

		// Test if region is correct.
		$this->assertEquals( 'value_1-season_1', $region_and_season );

		// clean up.
		wp_delete_post( $post_1->ID, true );
		wp_delete_post( $post_itinerary->ID, true );
		wp_delete_term( $season_term->term_id, SEASON_TAXONOMY );
	}
}
