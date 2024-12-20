<?php
/**
 * Test suite for Cabins.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Cabins;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\Cabins\get_cabins_data;
use function Quark\Ingestor\get_image_details;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\update_occupancies;

use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as DECK_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\Tests\Ingestor\TEST_IMAGE_PATH;

/**
 * Class Test_Cabins
 */
class Test_Cabins extends Softrip_TestCase {
		/**
		 * Test get cabins data.
		 *
		 * @covers \Quark\Ingestor\get_cabins_data
		 *
		 * @return void
		 */
	public function test_get_cabins_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_cabins_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_cabins_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_cabins_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Update related itinerary.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-123:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'start_date'         => '2025-01-01',
					'end_date'           => '2025-01-02',
					'duration'           => 2,
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Create ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'POQ',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Test without assigning any cabin.
		$expected = [];
		$actual   = get_cabins_data( $ship_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a cabin post without softrip id.
		$cabin_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORY_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'POQ-SGL',
					'drupal_id'         => 81,
					'cabin_name'        => 'Explorer Single',
				],
			]
		);
		$this->assertIsInt( $cabin_post_id1 );

		// Insert occupancies for this cabin.
		$raw_cabins_data = [
			[
				'id'          => 'UNQ-123:2025-01-01:POQ-SGL',
				'code'        => 'POQ-SGL',
				'name'        => 'Explorer Single',
				'departureId' => 'UNQ-123:2025-01-01',
				'occupancies' => [
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:A',
						'mask'           => 'A',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:AA',
						'mask'           => 'AA',
						'saleStatusCode' => 'S',
						'saleStatus'     => 'Sold Out',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SA',
						'mask'           => 'SA',
						'saleStatusCode' => 'N',
						'saleStatus'     => 'No display',
						'prices'         => [],
					],
					[
						'id'             => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'name'           => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'           => 'SAA',
						'saleStatusCode' => 'O',
						'saleStatus'     => 'Open',
						'prices'         => [
							'AUD' => [
								'currencyCode'   => 'AUD',
								'pricePerPerson' => 1000,
							],
						],
					],
				],
			],
		];
		$is_updated      = update_occupancies( $raw_cabins_data, $departure_post_id );
		$this->assertTrue( $is_updated );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned cabin that has no softrip id.
		$actual   = get_cabins_data( $expedition_post_id, $itinerary_post_id, $departure_post_id );
		$expected = [
			[
				'id'             => $cabin_post_id1,
				'name'           => 'Explorer Single',
				'drupalId'       => 81,
				'modified'       => get_post_modified_time( $cabin_post_id1 ),
				'title'          => get_raw_text_from_html( get_the_title( $cabin_post_id1 ) ),
				'softripId'      => 'UNQ-123:2025-01-01:POQ-SGL',
				'code'           => 'POQ-SGL',
				'description'    => get_raw_text_from_html( get_the_content( null, false, $cabin_post_id1 ) ),
				'bedDescription' => '',
				'type'           => '',
				'location'       => '',
				'size'           => '',
				'occupancySize'  => '',
				'media'          => [],
				'occupancies'    => [
					[
						'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'                    => 'SAA',
						'description'             => get_description_and_pax_count_by_mask( 'SAA' )['description'],
						'availabilityStatus'      => 'O',
						'availabilityDescription' => 'Open',
						'spacesAvailable'         => 0,
						'prices'                  => [
							'AUD' => [
								'currencyCode'                    => 'AUD',
								'pricePerPerson'                  => 1000,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'USD' => [
								'currencyCode'                    => 'USD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'EUR' => [
								'currencyCode'                    => 'EUR',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'GBP' => [
								'currencyCode'                    => 'GBP',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'CAD' => [
								'currencyCode'                    => 'CAD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
						],
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add cabin_bed configuration to cabin meta.
		update_post_meta( $cabin_post_id1, 'cabin_bed_configuration', 'Twin' );

		// Create cabin class term.
		$cabin_class_term_id = $this->factory()->term->create( [ 'taxonomy' => CABIN_CLASS_TAXONOMY ] );
		$this->assertIsInt( $cabin_class_term_id );
		$cabin_class_term = get_term( $cabin_class_term_id, CABIN_CLASS_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $cabin_class_term );
		$this->assertArrayHasKey( 'name', $cabin_class_term );
		$cabin_class_term_name = $cabin_class_term['name'];

		// Create one more cabin class term.
		$cabin_class_term_id2 = $this->factory()->term->create( [ 'taxonomy' => CABIN_CLASS_TAXONOMY ] );
		$this->assertIsInt( $cabin_class_term_id2 );
		$cabin_class_term2 = get_term( $cabin_class_term_id2, CABIN_CLASS_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $cabin_class_term2 );
		$this->assertArrayHasKey( 'name', $cabin_class_term2 );
		$cabin_class_term_name2 = $cabin_class_term2['name'];

		// Assign these cabin class terms to the cabin post.
		wp_set_post_terms( $cabin_post_id1, [ $cabin_class_term_id, $cabin_class_term_id2 ], CABIN_CLASS_TAXONOMY );

		// Create a deck post.
		$deck_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => DECK_POST_TYPE,
				'meta_input' => [
					'deck_name' => 'Deck 1',
				],
			]
		);
		$this->assertIsInt( $deck_post_id1 );

		// Create one more deck post.
		$deck_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => DECK_POST_TYPE,
				'meta_input' => [
					'deck_name' => 'Deck 2',
				],
			]
		);
		$this->assertIsInt( $deck_post_id2 );

		// Add these two decks to the cabin post in related_decks meta.
		update_post_meta( $cabin_post_id1, 'related_decks', [ $deck_post_id1, $deck_post_id2 ] );

		// Add from and to size on cabin meta.
		update_post_meta( $cabin_post_id1, 'cabin_category_size_range_from', '100' );
		update_post_meta( $cabin_post_id1, 'cabin_category_size_range_to', '200' );

		// Add from and to occupancy size on cabin meta.
		update_post_meta( $cabin_post_id1, 'cabin_occupancy_pax_range_from', '1' );
		update_post_meta( $cabin_post_id1, 'cabin_occupancy_pax_range_to', '2' );

		// Create two media posts.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id2 );

		// Get alt text for media post.
		$alt_text1 = get_post_meta( $media_post_id1, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text1 ) ) {
			$alt_text1 = get_post_field( 'post_title', $media_post_id1 );
		}

		// Set alt text on second media.
		update_post_meta( $media_post_id2, '_wp_attachment_image_alt', 'Cabin 2' );

		// Add these media posts to the cabin post in cabin_images meta.
		update_post_meta( $cabin_post_id1, 'cabin_images', [ $media_post_id1, $media_post_id2 ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned cabin that has softrip id, bed configuration, cabin class, decks and occupancies.
		$actual   = get_cabins_data( $expedition_post_id, $itinerary_post_id, $departure_post_id );
		$expected = [
			[
				'id'             => $cabin_post_id1,
				'name'           => 'Explorer Single',
				'drupalId'       => 81,
				'modified'       => get_post_modified_time( $cabin_post_id1 ),
				'title'          => get_raw_text_from_html( get_the_title( $cabin_post_id1 ) ),
				'softripId'      => 'UNQ-123:2025-01-01:POQ-SGL',
				'code'           => 'POQ-SGL',
				'description'    => get_raw_text_from_html( get_the_content( null, false, $cabin_post_id1 ) ),
				'bedDescription' => 'Twin',
				'type'           => implode( ', ', [ $cabin_class_term_name, $cabin_class_term_name2 ] ),
				'location'       => implode( ', ', [ 'Deck 1', 'Deck 2' ] ),
				'size'           => '100 - 200',
				'occupancySize'  => '1 - 2',
				'media'          => [
					get_image_details( $media_post_id1 ),
					get_image_details( $media_post_id2 ),
				],
				'occupancies'    => [
					[
						'id'                      => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
						'mask'                    => 'SAA',
						'description'             => get_description_and_pax_count_by_mask( 'SAA' )['description'],
						'availabilityStatus'      => 'O',
						'availabilityDescription' => 'Open',
						'spacesAvailable'         => 0,
						'prices'                  => [
							'AUD' => [
								'currencyCode'                    => 'AUD',
								'pricePerPerson'                  => 1000,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'USD' => [
								'currencyCode'                    => 'USD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'EUR' => [
								'currencyCode'                    => 'EUR',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'GBP' => [
								'currencyCode'                    => 'GBP',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
							'CAD' => [
								'currencyCode'                    => 'CAD',
								'pricePerPerson'                  => 0,
								'mandatoryTransferPricePerPerson' => 0,
								'supplementalPricePerPerson'      => 0,
								'promotionsApplied'               => [],
							],
						],
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );

		// Delete media files.
		wp_delete_attachment( $media_post_id1, true );
		wp_delete_attachment( $media_post_id2, true );
	}
}
