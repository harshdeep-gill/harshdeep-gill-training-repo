<?php
/**
 * Itineraries test suite.
 *
 * @package quark-itineraries
 */

namespace Quark\Itineraries;

use WP_Post;
use WP_Term;
use WP_UnitTestCase;

use Quark\Tests\Softrip\Softrip_TestCase;

use const Quark\InclusionSets\POST_TYPE as INCLUSION_SETS_POST_TYPE;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGES_POST_TYPE;

/**
 * Class Test_Itineraries.
 */
class Test_Itineraries extends Softrip_TestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Itineraries\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_action( 'init', 'Quark\Itineraries\register_itinerary_post_type' ) );
		$this->assertEquals( 10, has_action( 'init', 'Quark\Itineraries\register_departure_location_taxonomy' ) );
		$this->assertEquals( 10, has_action( 'init', 'Quark\Itineraries\register_tax_type_taxonomy' ) );
		$this->assertEquals( 10, has_filter( 'qe_departure_location_taxonomy_post_types', 'Quark\Itineraries\opt_in' ) );
		$this->assertEquals( 10, has_filter( 'qe_tax_types_taxonomy_post_types', 'Quark\Itineraries\opt_in' ) );
		$this->assertEquals( 10, has_filter( 'qe_season_taxonomy_post_types', 'Quark\Itineraries\opt_in' ) );
		$this->assertEquals( 10, has_action( 'save_post_' . POST_TYPE, 'Quark\Itineraries\bust_post_cache' ) );
		$this->assertEquals( 10, has_action( 'set_object_terms', 'Quark\Itineraries\bust_post_cache_on_term_assign' ) );
	}

	/**
	 * Test get().
	 *
	 * @covers \Quark\Itineraries\get()
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

		// Set DEPARTURE LOCATION taxonomy term.
		$departure_location_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
				'name'     => 'Test Term',
			]
		);

		// Assert term is created.
		$this->assertTrue( $departure_location_1 instanceof WP_Term );

		// Assign term to post.
		wp_set_object_terms( $post_1->ID, $departure_location_1->term_id, DEPARTURE_LOCATION_TAXONOMY );

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
					DEPARTURE_LOCATION_TAXONOMY => [
						[
							'term_id'     => strval( $departure_location_1->term_id ),
							'name'        => $departure_location_1->name,
							'slug'        => $departure_location_1->slug,
							'taxonomy'    => $departure_location_1->taxonomy,
							'description' => $departure_location_1->description,
							'parent'      => $departure_location_1->parent,
							'term_group'  => $departure_location_1->term_group,
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
	 * Test for get_starting_from_location().
	 *
	 * @covers \Quark\Itineraries\get_starting_from_location()
	 *
	 * @return void
	 */
	public function test_get_starting_from_location(): void {
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

		// Get starting from location.
		$starting_from_location = get_starting_from_location( $post_1->ID );

		// Assert starting from location is empty.
		$this->assertEmpty( $starting_from_location );

		// Create DEPARTURE LOCATION taxonomy term.
		$departure_location_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => DEPARTURE_LOCATION_TAXONOMY,
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $departure_location_1 instanceof WP_Term );

		// Set term ID to post meta.
		update_post_meta( $post_1->ID, 'start_location', $departure_location_1->term_id );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get starting from location.
		$starting_from_location = get_starting_from_location( $post_1->ID );

		// Assert starting from location is equal to term.
		$this->assertEquals( $departure_location_1->name, $starting_from_location );
	}

	/**
	 * Test for get_mandatory_transfer_price.
	 *
	 * @covers \Quark\Itineraries\get_mandatory_transfer_price()
	 * @covers \Quark\Itineraries\get_supplemental_price()
	 *
	 * @return void
	 */
	public function test_get_mandatory_transfer_price(): void {
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

		// Get mandatory transfer price.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID );

		// Assert mandatory transfer price is empty.
		$this->assertEmpty( $mandatory_transfer_price );

		// Get supplemental price.
		$supplemental_price = get_supplemental_price( $post_1->ID );

		// Assert supplemental price is empty.
		$this->assertEmpty( $supplemental_price );

		// Set mandatory transfer price to post meta.
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_usd', 100 );
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_cad', 200 );
		update_post_meta( $post_1->ID, 'supplemental_price_usd', 300 );
		update_post_meta( $post_1->ID, 'supplemental_price_eur', 400 );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get mandatory transfer price.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID );

		// Assert mandatory transfer price is equal to 100.
		$this->assertEquals( 100, $mandatory_transfer_price );

		// Get mandatory transfer price for CAD.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID, 'cad' );

		// Assert mandatory transfer price is equal to 200.
		$this->assertEquals( 200, $mandatory_transfer_price );

		// Get mandatory transfer price for EUR.
		$mandatory_transfer_price = get_mandatory_transfer_price( $post_1->ID, 'eur' );

		// Assert mandatory transfer price is empty.
		$this->assertEmpty( $mandatory_transfer_price );

		// Get supplemental price.
		$supplemental_price = get_supplemental_price( $post_1->ID );

		// Assert supplemental price is equal to 300.
		$this->assertEquals( 300, $supplemental_price );

		// Get supplemental price for EUR.
		$supplemental_price = get_supplemental_price( $post_1->ID, 'eur' );

		// Assert supplemental price is equal to 400.
		$this->assertEquals( 400, $supplemental_price );

		// Assert supplemental price for CAD is empty.
		$supplemental_price = get_supplemental_price( $post_1->ID, 'cad' );

		// Assert supplemental price is empty.
		$this->assertEmpty( $supplemental_price );
	}

	/**
	 * Test for get_included_transfer_package_details().
	 *
	 * @covers \Quark\Itineraries\get_included_transfer_package_details()
	 *
	 * @return void
	 */
	public function test_get_included_transfer_package_details(): void {
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

		// Get included transfer package details.
		$included_transfer_package_details = get_included_transfer_package_details( $post_1->ID );

		// Assert included transfer package details are not set.
		$this->assertEmpty( $included_transfer_package_details['sets'] );
		$this->assertEmpty( $included_transfer_package_details['price'] );

		// Create a post.
		$post_2 = $this->factory()->post->create_and_get(
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

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Set included transfer package details.
		update_post_meta( $post_1->ID, 'mandatory_transfer_package_inclusion', $post_2->ID );
		update_post_meta( $post_1->ID, 'included_transfer_package_details', 'Test Details' );
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_usd', 100 );
		update_post_meta( $post_1->ID, 'mandatory_transfer_price_cad', 200 );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get included transfer package details.
		$included_transfer_package_details = get_included_transfer_package_details( $post_1->ID );

		// Assert included transfer package details are set.
		$this->assertEquals( 'Includes', $included_transfer_package_details['title'] );
		$this->assertEquals( '100', $included_transfer_package_details['price'] );
		$this->assertEquals( '$100 USD', $included_transfer_package_details['formatted_price'] );
		$this->assertCount( 3, $included_transfer_package_details['sets'] );

		// Get included transfer package details for CAD.
		$included_transfer_package_details = get_included_transfer_package_details( $post_1->ID, 'cad' );

		// Assert included transfer package details are set.
		$this->assertEquals( 'Includes', $included_transfer_package_details['title'] );
		$this->assertEquals( '200', $included_transfer_package_details['price'] );
		$this->assertEquals( '$200 CAD', $included_transfer_package_details['formatted_price'] );
	}

	/**
	 * Test for get_policy_banner_details().
	 *
	 * @covers \Quark\Itineraries\get_policy_banner_details()
	 *
	 * @return void
	 */
	public function test_get_policy_banner_details(): void {
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

		// Get policy banner details.
		$policy_banner_details = get_policy_banner_details( $post_1->ID );

		// Assert policy banner details are not set.
		$this->assertEmpty( $policy_banner_details['title'] );

		// Create a post of POLICY_PAGES.
		$post_2 = $this->factory()->post->create_and_get(
			[
				'post_type'  => POLICY_PAGES_POST_TYPE,
				'meta_input' => [
					'alternate_title'          => 'value 1',
					'marketing_option_summary' => 'Lorem Ipsum Dolor Sit',
				],
			]
		);

		// Assert created post is instance of WP_Post.
		$this->assertTrue( $post_2 instanceof WP_Post );

		// Set policy banner details.
		update_post_meta( $post_1->ID, 'tnc_cancellation_policy', $post_2->ID );

		// Bust cache.
		bust_post_cache( $post_1->ID );

		// Get policy banner details.
		$policy_banner_details = get_policy_banner_details( $post_1->ID );

		// Assert policy banner details are set.
		$this->assertEquals( 'value 1', $policy_banner_details['title'] );
		$this->assertEquals( 'Lorem Ipsum Dolor Sit', $policy_banner_details['description'] );
		$this->assertEquals( 0, $policy_banner_details['icon_id'] );
		$this->assertEquals( get_permalink( $post_2->ID ), $policy_banner_details['permalink'] );
	}
}
