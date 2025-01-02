<?php
/**
 * Test suite for expedition functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Expeditions;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Error;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\Expeditions\get_destination_terms;
use function Quark\Ingestor\Expeditions\get_expedition_data;
use function Quark\Ingestor\get_id;
use function Quark\Ingestor\get_image_details;
use function Quark\Ingestor\get_post_modified_time;

use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Tests\Ingestor\TEST_IMAGE_PATH;

/**
 * Class Test_Expeditions
 */
class Test_Expeditions extends Softrip_TestCase {
	/**
	 * Test get expedition data.
	 *
	 * @covers \Quark\Ingestor\get_expedition_data
	 *
	 * @return void
	 */
	public function test_get_expedition_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_expedition_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_expedition_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_expedition_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Test without assigning any data.
		$expected =
			[
				'id'           => get_id( $expedition_post_id ),
				'name'         => get_raw_text_from_html( get_the_title( $expedition_post_id ) ),
				'published'    => true,
				'description'  => '',
				'images'       => [],
				'destinations' => [],
				'itineraries'  => [],
				'heroImage'    => [],
				'modified'     => get_post_modified_time( $expedition_post_id ),
				'highlights'   => [],
				'url'          => get_permalink( $expedition_post_id ),
			];
		$actual   = get_expedition_data( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Add itinerary to the expedition post.
		$itinerary_post_id = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => 'UNQ-123',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id );

		// Assign itinerary to the expedition post.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

		// Create some media post.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id2 );

		// Get alt text for media post.
		$alt_text1 = get_post_meta( $media_post_id1, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text1 ) ) {
			$alt_text1 = get_the_title( $media_post_id1 );
		}

		// Get alt text for media post.
		$alt_text2 = get_post_meta( $media_post_id2, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text2 ) ) {
			$alt_text2 = get_the_title( $media_post_id2 );
		}

		// Post content.
		$post_content = sprintf(
			'<!-- wp:paragraph -->
			<p>On this extraordinary journey, you will pack all the excitement of an epic Arctic cruise into just seven days, experience incredible Arctic wilderness you never dreamt possible. Though this rocky island is covered in mountains and glaciers, the towering cliffs and fjords play host to a thriving and diverse ecosystem. Exploring as much of the area as possible, you will enjoy maximum opportunities to spot, among other wildlife, the walrus with its long tusks and distinctive whiskers, the resilient and Arctic birds in all their varied majesty, and that most iconic of Arctic creatures, the polar bear.</p>
			<!-- /wp:paragraph -->

			<!-- wp:quark/expedition-hero -->
			<!-- wp:quark/expedition-hero-content -->
			<!-- wp:quark/expedition-hero-content-left -->
			<!-- wp:quark/expedition-details /-->
			<!-- /wp:quark/expedition-hero-content-left -->

			<!-- wp:quark/expedition-hero-content-right -->
			<!-- wp:quark/hero-card-slider {"items":[{"id":%1$s,"src":"%3$s","width":300,"height":200,"alt":"","caption":"","size":"medium"},{"id":%2$s,"src":"%4$s","width":300,"height":200,"alt":"","caption":"","size":"medium"},{"id":6592,"src":"https://local.quarkexpeditions.com/wp-content/uploads/2024/08/strote-jared-201809-214x300.jpg","width":214,"height":300,"alt":"","caption":"","size":"medium"},{"id":6594,"src":"https://local.quarkexpeditions.com/wp-content/uploads/2024/08/white-andrew-202102-300x200.jpg","width":300,"height":200,"alt":"","caption":"","size":"medium"}]} /-->
			<!-- /wp:quark/expedition-hero-content-right -->
			<!-- /wp:quark/expedition-hero-content -->
			<!-- /wp:quark/expedition-hero -->

			<!-- wp:quark/book-departures-expeditions /-->
			<!-- wp:quark/column -->
			<!-- wp:quark/highlights {"title":"Highlights"} -->
			<!-- wp:quark/highlight-item {"icon":"explore"} -->
			<!-- wp:quark/highlight-item-text {"text":"Explore highlights of the Antarctic Peninsula"} /-->
			<!-- /wp:quark/highlight-item -->

			<!-- wp:quark/highlight-item {"icon":"ship"} -->
			<!-- wp:quark/highlight-item-text {"text":"Travel safely and comfortably aboard any Quark Expeditions ice-strengthened vessel"} /-->
			<!-- /wp:quark/highlight-item -->

			<!-- wp:quark/highlight-item {"icon":"presentations"} -->
			<!-- wp:quark/highlight-item-text {"text":"Enjoy presentations on wildlife, history, glaciology \u0026amp; geology by our onboard polar experts"} /-->
			<!-- /wp:quark/highlight-item -->

			<!-- wp:quark/highlight-item {"icon":"wildlife-penguin"} -->
			<!-- wp:quark/highlight-item-text {"text":"Experience abundant wildlife, such as penguins, seals and whales"} /-->
			<!-- /wp:quark/highlight-item -->

			<!-- wp:quark/highlight-item {"icon":"paddling-excursions"} -->
			<!-- wp:quark/highlight-item-text {"text":"Immerse yourself in the icy realm of the Antarctic, with an optional paddling excursion"} /-->

			<!-- wp:quark/highlight-item-text {"text":"ABCD"} /-->
			<!-- /wp:quark/highlight-item -->

			<!-- wp:quark/highlight-item -->
			<!-- wp:quark/highlight-item-title {"title":"Wow"} /-->

			<!-- wp:quark/highlight-item-overline {"overline":"This is the best expedition."} /-->
			<!-- /wp:quark/highlight-item -->
			<!-- /wp:quark/highlights -->
			<!-- /wp:quark/column -->',
			$media_post_id1,
			$media_post_id2,
			wp_get_attachment_image_url( $media_post_id1, 'medium' ),
			wp_get_attachment_image_url( $media_post_id2, 'full' )
		);

		// Update post content.
		wp_update_post(
			[
				'ID'           => $expedition_post_id,
				'post_content' => $post_content,
				'meta_input'   => [
					'overview' => 'Here is the overview. <h1>Surfing</h1> You never know the world until you explore it.',
				],
			]
		);

		// Set featured image.
		set_post_thumbnail( $expedition_post_id, $media_post_id1 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itinerary.
		$expected =
			[
				'id'           => get_id( $expedition_post_id ),
				'name'         => get_raw_text_from_html( get_the_title( $expedition_post_id ) ),
				'published'    => true,
				'description'  => 'Here is the overview. Surfing You never know the world until you explore it.',
				'images'       => [
					get_image_details( $media_post_id1 ),
					get_image_details( $media_post_id2 ),
				],
				'destinations' => [],
				'itineraries'  => [
					[
						'id'                     => get_id( $itinerary_post_id ),
						'packageId'              => 'UNQ-123',
						'name'                   => get_raw_text_from_html( get_the_title( $itinerary_post_id ) ),
						'published'              => true,
						'startLocation'          => '',
						'endLocation'            => '',
						'departures'             => [],
						'durationInDays'         => 0,
						'modified'               => get_post_modified_time( $itinerary_post_id ),
						'season'                 => '',
						'embarkation'            => '',
						'embarkationPortCode'    => '',
						'disembarkation'         => '',
						'disembarkationPortCode' => '',
						'itineraryMap'           => [],
						'days'                   => [],
						'inclusions'             => [],
						'exclusions'             => [],
					],
				],
				'modified'     => get_post_modified_time( $expedition_post_id ),
				'highlights'   => [
					'Explore highlights of the Antarctic Peninsula',
					'Travel safely and comfortably aboard any Quark Expeditions ice-strengthened vessel',
					'Enjoy presentations on wildlife, history, glaciology u0026amp; geology by our onboard polar experts',
					'Experience abundant wildlife, such as penguins, seals and whales',
					'Immerse yourself in the icy realm of the Antarctic, with an optional paddling excursion',
					'ABCD',
				],
				'heroImage'    => get_image_details( $media_post_id1 ),
				'url'          => get_permalink( $expedition_post_id ),
			];
		$actual   = get_expedition_data( $expedition_post_id );
		$this->assertEquals( $expected, $actual );
	}

		/**
		 * Test get destination terms.
		 *
		 * @covers \Quark\Ingestor\get_destination_terms
		 *
		 * @return void
		 */
	public function test_get_destination_terms(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_destination_terms();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_destination_terms( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_destination_terms( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create a expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create a destination term.
		$destination_term_id1 = $this->factory()->term->create( [ 'taxonomy' => DESTINATION_TAXONOMY ] );
		$this->assertIsInt( $destination_term_id1 );
		$destination_term1 = get_term( $destination_term_id1, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $destination_term1 );
		$this->assertArrayHasKey( 'name', $destination_term1 );
		$destination_term1_name = $destination_term1['name'];

		// Test without assigning any destination term.
		$expected = [];
		$actual   = get_destination_terms( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a child term but without softrip id.
		$destination_term_id2 = $this->factory()->term->create(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $destination_term_id1,
			]
		);
		$this->assertIsInt( $destination_term_id2 );
		$destination_term2 = get_term( $destination_term_id2, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $destination_term2 );
		$this->assertArrayHasKey( 'name', $destination_term2 );
		$destination_term2_name = $destination_term2['name'];

		// Assign child term to the expedition post.
		wp_set_post_terms( $expedition_post_id, [ $destination_term_id2 ], DESTINATION_TAXONOMY );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned child term but without softrip id.
		$expected = [];
		$actual   = get_destination_terms( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Add softrip id to parent term meta.
		update_term_meta( $destination_term_id1, 'softrip_id', '123' );

		// Test with assigned child term and parent term with softrip id.
		$actual   = get_destination_terms( $expedition_post_id );
		$expected = [
			[
				'id'     => $destination_term_id2,
				'name'   => $destination_term2_name,
				'region' => [
					'name' => $destination_term1_name,
					'code' => '123',
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add one more child term with softrip id.
		$destination_term_id3 = $this->factory()->term->create(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'parent'   => $destination_term_id1,
			]
		);
		$this->assertIsInt( $destination_term_id3 );
		$destination_term3 = get_term( $destination_term_id3, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $destination_term3 );
		$this->assertArrayHasKey( 'name', $destination_term3 );
		$destination_term3_name = $destination_term3['name'];
		update_term_meta( $destination_term_id3, 'softrip_id', '456' );

		// Assign child term to the expedition post.
		wp_set_post_terms( $expedition_post_id, [ $destination_term_id2, $destination_term_id3 ], DESTINATION_TAXONOMY );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned child term and parent term with softrip id.
		$actual   = get_destination_terms( $expedition_post_id );
		$expected = [
			[
				'id'     => $destination_term_id2,
				'name'   => $destination_term2_name,
				'region' => [
					'name' => $destination_term1_name,
					'code' => '123',
				],
			],
			[
				'id'     => $destination_term_id3,
				'name'   => $destination_term3_name,
				'region' => [
					'name' => $destination_term1_name,
					'code' => '123',
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}
}
