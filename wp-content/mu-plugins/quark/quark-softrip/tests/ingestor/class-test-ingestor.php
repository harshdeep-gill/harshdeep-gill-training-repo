<?php
/**
 * Test suite for ingestor.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Ingestor;

use Quark\Tests\Softrip\Softrip_TestCase;

use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as DECK_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

use function Quark\Core\get_pure_text_from_html;
use function Quark\Softrip\Ingestor\get_cabins_data;
use function Quark\Softrip\Ingestor\get_departures_data;
use function Quark\Softrip\Ingestor\get_destination_terms;
use function Quark\Softrip\Ingestor\get_itineraries;
use function Quark\Softrip\Ingestor\get_occupancies_data;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\update_occupancies;
use function Quark\Softrip\Promotions\get_promotions_by_code;
use function Quark\Softrip\Promotions\update_promotions;

/**
 * Class Test_Ingestor
 */
class Test_Ingestor extends Softrip_TestCase {
    /**
     * Test get destination terms.
     *
     * @covers \Quark\Softrip\Ingestor\get_destination_terms
     *
     * @return void
     */
    public function test_get_destination_terms() {
        // Test with no arguments.
        $expected = [];
        $actual = get_destination_terms();
        $this->assertEquals( $expected, $actual );

        // Test with default arg.
        $expected = [];
        $actual = get_destination_terms( 0 );
        $this->assertEquals( $expected, $actual );

        // Test with invalid post id.
        $expected = [];
        $actual = get_destination_terms( 999999 );
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
        $actual = get_destination_terms( $expedition_post_id );
        $this->assertEquals( $expected, $actual );

        // Create a child term but without softrip id.
        $destination_term_id2 = $this->factory()->term->create( [ 'taxonomy' => DESTINATION_TAXONOMY, 'parent' => $destination_term_id1 ] );
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
        $actual = get_destination_terms( $expedition_post_id );
        $this->assertEquals( $expected, $actual );

        // Add softrip id to parent term meta.
        update_term_meta( $destination_term_id1, 'softrip_id', '123' );

        // Test with assigned child term and parent term with softrip id.
        $actual = get_destination_terms( $expedition_post_id );
        $expected = [
            [
                'id' => $destination_term_id2,
                'name' => $destination_term2_name,
                'region' => [
                    'name' => $destination_term1_name,
                    'code' => '123',
                ],
            ],
        ];
        $this->assertEquals( $expected, $actual );

        // Add one more child term with softrip id.
        $destination_term_id3 = $this->factory()->term->create( [ 'taxonomy' => DESTINATION_TAXONOMY, 'parent' => $destination_term_id1 ] );
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
        $actual = get_destination_terms( $expedition_post_id );
        $expected = [
            [
                'id' => $destination_term_id2,
                'name' => $destination_term2_name,
                'region' => [
                    'name' => $destination_term1_name,
                    'code' => '123',
                ],
            ],
            [
                'id' => $destination_term_id3,
                'name' => $destination_term3_name,
                'region' => [
                    'name' => $destination_term1_name,
                    'code' => '123',
                ],
            ],
        ];
        $this->assertEquals( $expected, $actual );

        // Clean up.
        wp_delete_post( $expedition_post_id, true );
        wp_delete_term( $destination_term_id1, DESTINATION_TAXONOMY );
        wp_delete_term( $destination_term_id2, DESTINATION_TAXONOMY );
        wp_delete_term( $destination_term_id3, DESTINATION_TAXONOMY );
    }

    /**
     * Test get itineraries.
     *
     * @covers \Quark\Softrip\Ingestor\get_itineraries
     *
     * @return void
     */
    public function test_get_itineraries() {
        // Test with no arguments.
        $expected = [];
        $actual = get_itineraries();
        $this->assertEquals( $expected, $actual );

        // Test with default arg.
        $expected = [];
        $actual = get_itineraries( 0 );
        $this->assertEquals( $expected, $actual );

        // Test with invalid post id.
        $expected = [];
        $actual = get_itineraries( 999999 );
        $this->assertEquals( $expected, $actual );

        // Create a expedition post.
        $expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
        $this->assertIsInt( $expedition_post_id );

        // Test without assigning any itinerary.
        $expected = [];
        $actual = get_itineraries( $expedition_post_id );
        $this->assertEquals( $expected, $actual );

        // Create a itinerary post without softrip code.
        $itinerary_post_id1 = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
        $this->assertIsInt( $itinerary_post_id1 );

        // Assign itinerary to the expedition post.
        update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id1 ] );

        // Test with assigned itinerary that has no softrip code.
        $actual = get_itineraries( $expedition_post_id );
        $expected = [];
        $this->assertEquals( $expected, $actual );

        // Add softrip code to the itinerary post.
        update_post_meta( $itinerary_post_id1, 'softrip_package_code', 'UNQ-123' );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned itinerary that has softrip code.
        $actual = get_itineraries( $expedition_post_id );
        $expected = [
            [
                'id' => $itinerary_post_id1,
                'packageId' => 'UNQ-123',
                'name' => get_pure_text_from_html( get_the_title( $itinerary_post_id1 ) ),
                'startLocation' => '',
                'endLocation' => '',
                'departures' => [],
            ]
        ];
        $this->assertEquals( $expected, $actual );

        // Create location term.
        $start_location_term_id = $this->factory()->term->create( [ 'taxonomy' => DEPARTURE_LOCATION_TAXONOMY ] );
        $this->assertIsInt( $start_location_term_id );
        $start_location_term = get_term( $start_location_term_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );
        $this->assertIsArray( $start_location_term );
        $this->assertArrayHasKey( 'name', $start_location_term );
        $start_location_term_name = $start_location_term['name'];

        // Create end location term.
        $end_location_term_id = $this->factory()->term->create( [ 'taxonomy' => DEPARTURE_LOCATION_TAXONOMY ] );
        $this->assertIsInt( $end_location_term_id );
        $end_location_term = get_term( $end_location_term_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );
        $this->assertIsArray( $end_location_term );
        $this->assertArrayHasKey( 'name', $end_location_term );
        $end_location_term_name = $end_location_term['name'];

        // Add start and end to itinerary meta.
        update_post_meta( $itinerary_post_id1, 'start_location', $start_location_term_id );
        update_post_meta( $itinerary_post_id1, 'end_location', $end_location_term_id );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned itinerary that has softrip code and start/end location.
        $actual = get_itineraries( $expedition_post_id );
        $expected = [
            [
                'id' => $itinerary_post_id1,
                'packageId' => 'UNQ-123',
                'name' => get_pure_text_from_html( get_the_title( $itinerary_post_id1 ) ),
                'startLocation' => $start_location_term_name,
                'endLocation' => $end_location_term_name,
                'departures' => [],
            ]
        ];
        $this->assertEquals( $expected, $actual );

        // Create one more itinerary post with softrip code.
        $itinerary_post_id2 = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE, 'meta_input' => [
            'softrip_package_code' => 'UNQ-456',
        ] ] );
        $this->assertIsInt( $itinerary_post_id2 );

        // Assign both itineraries to the expedition post.
        update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id1, $itinerary_post_id2 ] );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned itineraries that have softrip code.
        $actual = get_itineraries( $expedition_post_id );
        $expected = [
            [
                'id' => $itinerary_post_id1,
                'packageId' => 'UNQ-123',
                'name' => get_pure_text_from_html( get_the_title( $itinerary_post_id1 ) ),
                'startLocation' => $start_location_term_name,
                'endLocation' => $end_location_term_name,
                'departures' => [],
            ],
            [
                'id' => $itinerary_post_id2,
                'packageId' => 'UNQ-456',
                'name' => get_pure_text_from_html( get_the_title( $itinerary_post_id2 ) ),
                'startLocation' => '',
                'endLocation' => '',
                'departures' => [],
            ]
        ];
        $this->assertEquals( $expected, $actual );

        // Clean up.
        wp_delete_post( $expedition_post_id, true );
        wp_delete_post( $itinerary_post_id1, true );
        wp_delete_post( $itinerary_post_id2, true );
        wp_delete_term( $start_location_term_id, DEPARTURE_LOCATION_TAXONOMY );
        wp_delete_term( $end_location_term_id, DEPARTURE_LOCATION_TAXONOMY );
    }

    /**
     * Test get departures data.
     *
     * @covers \Quark\Softrip\Ingestor\get_departures_data
     *
     * @return void
     */
    public function test_get_departures_data() {
        // Test with no arguments.
        $expected = [];
        $actual = get_departures_data();
        $this->assertEquals( $expected, $actual );

        // Test with default arg.
        $expected = [];
        $actual = get_departures_data( 0 );
        $this->assertEquals( $expected, $actual );

        // Test with invalid post id.
        $expected = [];
        $actual = get_departures_data( 999999 );
        $this->assertEquals( $expected, $actual );

        // Create expedition post.
        $expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
        $this->assertIsInt( $expedition_post_id );

        // Create a itinerary post.
        $itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
        $this->assertIsInt( $itinerary_post_id );

        // Test without assigning any departure.
        $expected = [];
        $actual = get_departures_data( $itinerary_post_id );
        $this->assertEquals( $expected, $actual );

        // Create a departure post without softrip id.
        $departure_post_id1 = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE, 'post_parent' => $itinerary_post_id, 'meta_input' => [
            'softrip_id' => 'UNQ-123:2025-01-01',
            'itinerary' => $itinerary_post_id,
            'related_expedition' => $expedition_post_id,
        ] ] );
        $this->assertIsInt( $departure_post_id1 );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned departure that has no softrip id.
        $actual = get_departures_data( $expedition_post_id, $itinerary_post_id );
        $expected = [
            [
                'id' => 'UNQ-123:2025-01-01',
                'name' => get_pure_text_from_html( get_the_title( $departure_post_id1 ) ),
                'startDate' => '',
                'endDate' => '',
                'durationInDays' => 0,
                'ship' => [],
                'languages' => '',
                'cabins' => [],
            ]
        ];
        $this->assertEquals( $expected, $actual );

        // Add start date to departure meta.
        update_post_meta( $departure_post_id1, 'start_date', '2025-01-01' );

        // Add end date to departure meta.
        update_post_meta( $departure_post_id1, 'end_date', '2025-01-02' );

        // Add duration to departure meta.
        update_post_meta( $departure_post_id1, 'duration', 2 );

        // Create language term.
        $language_term_id = $this->factory()->term->create( [ 'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY ] );
        $this->assertIsInt( $language_term_id );

        // Add language code to term meta.
        update_term_meta( $language_term_id, 'language_code', 'EN' );

        // Assign language to the departure post.
        wp_set_post_terms( $departure_post_id1, [ $language_term_id ], SPOKEN_LANGUAGE_TAXONOMY );

        // Create ship post.
        $ship_post_id = $this->factory()->post->create( [ 'post_type' => SHIP_POST_TYPE, 'meta_input' => [
            'ship_code' => 'OQP'
        ] ] );
        $this->assertIsInt( $ship_post_id );

        // Add ship to departure meta.
        update_post_meta( $departure_post_id1, 'related_ship', $ship_post_id );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned departure that has softrip id, start/end date, duration, language and ship.
        $actual = get_departures_data( $expedition_post_id, $itinerary_post_id );
        $expected = [
            [
                'id' => 'UNQ-123:2025-01-01',
                'name' => get_pure_text_from_html( get_the_title( $departure_post_id1 ) ),
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-02',
                'durationInDays' => 2,
                'ship' => [
                    'code' => 'OQP',
                    'id' => $ship_post_id,
                    'name' => get_pure_text_from_html( get_the_title( $ship_post_id ) ),
                ],
                'languages' => 'EN',
                'cabins' => [],
            ]
        ];
        $this->assertEquals( $expected, $actual );

        // Add one more departure post with softrip id.
        $departure_post_id2 = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE, 'post_parent' => $itinerary_post_id, 'meta_input' => [
            'softrip_id' => 'UNQ-456:2025-01-01',
            'itinerary' => $itinerary_post_id,
            'related_expedition' => $expedition_post_id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-02',
            'duration' => 2,
        ] ] );
        $this->assertIsInt( $departure_post_id2 );

        // Create language term.
        $language_term_id2 = $this->factory()->term->create( [ 'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY ] );
        $this->assertIsInt( $language_term_id2 );

        // Add language code to term meta.
        update_term_meta( $language_term_id2, 'language_code', 'FR' );

        // Assign language to the departure post.
        wp_set_post_terms( $departure_post_id2, [ $language_term_id2 ], SPOKEN_LANGUAGE_TAXONOMY );

        // Create ship post.
        $ship_post_id2 = $this->factory()->post->create( [ 'post_type' => SHIP_POST_TYPE, 'meta_input' => [
            'ship_code' => 'LOP'
        ] ] );
        $this->assertIsInt( $ship_post_id2 );

        // Add ship to departure meta.
        update_post_meta( $departure_post_id2, 'related_ship', $ship_post_id2 );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned departures that have softrip id, start/end date, duration, language and ship.
        $actual = get_departures_data( $expedition_post_id, $itinerary_post_id );
        $expected = [
            [
                'id' => 'UNQ-456:2025-01-01',
                'name' => get_pure_text_from_html( get_the_title( $departure_post_id2 ) ),
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-02',
                'durationInDays' => 2,
                'ship' => [
                    'code' => 'LOP',
                    'id' => $ship_post_id2,
                    'name' => get_pure_text_from_html( get_the_title( $ship_post_id2 ) ),
                ],
                'languages' => 'FR',
                'cabins' => [],
            ],
            [
                'id' => 'UNQ-123:2025-01-01',
                'name' => get_pure_text_from_html(get_the_title($departure_post_id1)),
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-02',
                'durationInDays' => 2,
                'ship' => [
                    'code' => 'OQP',
                    'id' => $ship_post_id,
                    'name' => get_pure_text_from_html(get_the_title($ship_post_id)),
                ],
                'languages' => 'EN',
                'cabins' => [],
            ],
        ];
        $this->assertEquals( $expected, $actual );

        // Clean up.
        wp_delete_post( $expedition_post_id, true );
        wp_delete_post( $itinerary_post_id, true );
        wp_delete_post( $departure_post_id1, true );
        wp_delete_post( $departure_post_id2, true );
        wp_delete_post( $ship_post_id, true );
        wp_delete_post( $ship_post_id2, true );
        wp_delete_term( $language_term_id, SPOKEN_LANGUAGE_TAXONOMY );
        wp_delete_term( $language_term_id2, SPOKEN_LANGUAGE_TAXONOMY );
    }

    /**
     * Test get cabins data.
     *
     * @covers \Quark\Softrip\Ingestor\get_cabins_data
     *
     * @return void
     */
    public function test_get_cabins_data() {
        // Test with no arguments.
        $expected = [];
        $actual = get_cabins_data();
        $this->assertEquals( $expected, $actual );

        // Test with default arg.
        $expected = [];
        $actual = get_cabins_data( 0 );
        $this->assertEquals( $expected, $actual );

        // Test with invalid post id.
        $expected = [];
        $actual = get_cabins_data( 999999 );
        $this->assertEquals( $expected, $actual );

        // Create expedition post.
        $expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
        $this->assertIsInt( $expedition_post_id );

        // Create itinerary post.
        $itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE, ] );
        $this->assertIsInt( $itinerary_post_id );

        // Update related itinerary.
        update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

        // Create departure post.
        $departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE, 'post_parent' => $itinerary_post_id, 'meta_input' => [
            'softrip_id' => 'UNQ-123:2025-01-01',
            'itinerary' => $itinerary_post_id,
            'related_expedition' => $expedition_post_id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-02',
            'duration' => 2,
        ] ] );
        $this->assertIsInt( $departure_post_id );

        // Create ship post.
        $ship_post_id = $this->factory()->post->create( [ 'post_type' => SHIP_POST_TYPE, 'meta_input' => [
            'ship_code' => 'POQ',
        ] ] );
        $this->assertIsInt( $ship_post_id );

        // Test without assigning any cabin.
        $expected = [];
        $actual = get_cabins_data( $ship_post_id );
        $this->assertEquals( $expected, $actual );

        // Create a cabin post without softrip id.
        $cabin_post_id1 = $this->factory()->post->create( [ 'post_type' => CABIN_CATEGORY_POST_TYPE, 'meta_input' => [
            'cabin_category_id' => 'POQ-SGL',
        ] ] );
        $this->assertIsInt( $cabin_post_id1 );

        // Insert occupancies for this cabin.
        $raw_cabins_data = [
            [
                'id' => 'UNQ-123:2025-01-01:POQ-SGL',
                'code' => 'POQ-SGL',
                'name' => 'Explorer Single',
                'departureId' => 'UNQ-123:2025-01-01',
                'occupancies' => [
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:A',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:A',
                        'mask' => 'A',
                        'saleStatusCode' => 'O',
                        'saleStatus' => 'Open',
                        'prices' => [],
                    ],
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:AA',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:AA',
                        'mask' => 'AA',
                        'saleStatusCode' => 'S',
                        'saleStatus' => 'Sold Out',
                        'prices' => [],
                    ],
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:SA',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:SA',
                        'mask' => 'SA',
                        'saleStatusCode' => 'N',
                        'saleStatus' => 'No display',
                        'prices' => [],
                    ],
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                        'mask' => 'SAA',
                        'saleStatusCode' => 'O',
                        'saleStatus' => 'Open',
                        'prices' => [
                            'AUD' => [
                                'currencyCode' => 'AUD',
                                'pricePerPerson' => 1000,
                            ],
                        ],
                    ]
                ]
            ],
        ];
        $is_updated = update_occupancies( $raw_cabins_data, $departure_post_id );
        $this->assertTrue( $is_updated );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned cabin that has no softrip id.
        $actual = get_cabins_data($expedition_post_id, $itinerary_post_id, $departure_post_id);
        $expected = [
            [
                'id' => 'UNQ-123:2025-01-01:POQ-SGL',
                'name' => get_pure_text_from_html( get_the_title( $cabin_post_id1 ) ),
                'code' => 'POQ-SGL',
                'description' => get_pure_text_from_html( get_the_content( null, false, $cabin_post_id1 ) ),
                'bedDescription' => '',
                'type' => '',
                'location' => '',
                'size' => '',
                'media' => [],
                'occupancies' => [
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                        'mask' => 'SAA',
                        'description' => get_description_and_pax_count_by_mask( 'SAA' )['description'],
                        'availabilityStatus' => 'O',
                        'availabilityDescription' => 'Open',
                        'spacesAvailable' => 0,
                        'prices' => [
                            'AUD' => [
                                'currencyCode' => 'AUD',
                                'pricePerPerson' => 1000,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'USD' => [
                                'currencyCode' => 'USD',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'EUR' => [
                                'currencyCode' => 'EUR',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'GBP' => [
                                'currencyCode' => 'GBP',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'CAD' => [
                                'currencyCode' => 'CAD',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],

                        ]
                    ]
                ],
            ]
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
        $deck_post_id1 = $this->factory()->post->create( [ 'post_type' => DECK_POST_TYPE, 'meta_input' => [
            'deck_name' => 'Deck 1'
        ] ] );
        $this->assertIsInt( $deck_post_id1 );

        // Create one more deck post.
        $deck_post_id2 = $this->factory()->post->create( [ 'post_type' => DECK_POST_TYPE, 'meta_input' => [
            'deck_name' => 'Deck 2'
        ] ] );
        $this->assertIsInt( $deck_post_id2 );

        // Add these two decks to the cabin post in related_decks meta.
        update_post_meta( $cabin_post_id1, 'related_decks', [ $deck_post_id1, $deck_post_id2 ] );

        // Add from and to size on cabin meta.
        update_post_meta( $cabin_post_id1, 'cabin_category_size_range_from', '100' );
        update_post_meta( $cabin_post_id1, 'cabin_category_size_range_to', '200' );

        // Create some media attachment posts.
        $media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/cabin1.jpg', $cabin_post_id1 );
        $this->assertIsInt( $media_post_id1 );
        $media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/cabin2.jpg', $cabin_post_id1 );
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
        $actual = get_cabins_data($expedition_post_id, $itinerary_post_id, $departure_post_id);
        $expected = [
            [
                'id' => 'UNQ-123:2025-01-01:POQ-SGL',
                'name' => get_pure_text_from_html( get_the_title( $cabin_post_id1 ) ),
                'code' => 'POQ-SGL',
                'description' => get_pure_text_from_html( get_the_content( null, false, $cabin_post_id1 ) ),
                'bedDescription' => 'Twin',
                'type' => implode( ', ', [ $cabin_class_term_name, $cabin_class_term_name2 ] ),
                'location' => implode( ', ', [ 'Deck 1', 'Deck 2' ] ),
                'size' => '100 - 200',
                'media' => [
                    [
                        'id' => $media_post_id1,
                        'fullSizeUrl' => wp_get_attachment_url( $media_post_id1 ),
                        'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
                        'alt' => $alt_text1,
                    ],
                    [
                        'id' => $media_post_id2,
                        'fullSizeUrl' => wp_get_attachment_url( $media_post_id2 ),
                        'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
                        'alt' => 'Cabin 2',
                    ]
                ],
                'occupancies' => [
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                        'mask' => 'SAA',
                        'description' => get_description_and_pax_count_by_mask( 'SAA' )['description'],
                        'availabilityStatus' => 'O',
                        'availabilityDescription' => 'Open',
                        'spacesAvailable' => 0,
                        'prices' => [
                            'AUD' => [
                                'currencyCode' => 'AUD',
                                'pricePerPerson' => 1000,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'USD' => [
                                'currencyCode' => 'USD',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'EUR' => [
                                'currencyCode' => 'EUR',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'GBP' => [
                                'currencyCode' => 'GBP',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                            'CAD' => [
                                'currencyCode' => 'CAD',
                                'pricePerPerson' => 0,
                                'mandatoryTransferPricePerPerson' => 0,
                                'supplementalPricePerPerson' => 0,
                                'promotionsApplied' => [],
                            ],
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEquals( $expected, $actual );

        // Clean up.
        wp_delete_post( $media_post_id1, true );
        wp_delete_post( $media_post_id2, true );
        wp_delete_post( $deck_post_id1, true );
        wp_delete_post( $deck_post_id2, true );
        wp_delete_post( $cabin_post_id1, true );
        wp_delete_post( $ship_post_id, true );
        wp_delete_post( $departure_post_id, true );
        wp_delete_post( $itinerary_post_id, true );
        wp_delete_post( $expedition_post_id, true );
    }

    /**
     * Test get occupancies data.
     *
     * @covers \Quark\Softrip\Ingestor\get_occupancies_data
     *
     * @return void
     */
    public function test_get_occupancies_data(): void {
         // Test with no arguments.
        $expected = [];
        $actual = get_occupancies_data();
        $this->assertEquals( $expected, $actual );

        // Test with default arg.
        $expected = [];
        $actual = get_occupancies_data( 0 );
        $this->assertEquals( $expected, $actual );

        // Test with invalid post id.
        $expected = [];
        $actual = get_occupancies_data( 999999 );
        $this->assertEquals( $expected, $actual );

        // Create expedition post.
        $expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
        $this->assertIsInt( $expedition_post_id );

        // Create itinerary post.
        $itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE, ] );
        $this->assertIsInt( $itinerary_post_id );

        // Update related itinerary.
        update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id ] );

        // Create departure post.
        $departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE, 'post_parent' => $itinerary_post_id, 'meta_input' => [
            'softrip_id' => 'UNQ-123:2025-01-01',
            'itinerary' => $itinerary_post_id,
            'related_expedition' => $expedition_post_id,
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-02',
            'duration' => 2,
        ] ] );
        $this->assertIsInt( $departure_post_id );

        // Create ship post.
        $ship_post_id = $this->factory()->post->create( [ 'post_type' => SHIP_POST_TYPE, 'meta_input' => [
            'ship_code' => 'POQ',
        ] ] );
        $this->assertIsInt( $ship_post_id );

        // Create a cabin post without softrip id.
        $cabin_post_id1 = $this->factory()->post->create( [ 'post_type' => CABIN_CATEGORY_POST_TYPE, 'meta_input' => [
            'cabin_category_id' => 'POQ-SGL',
        ] ] );
        $this->assertIsInt( $cabin_post_id1 );

        // Insert some promotions.
        $raw_promotions = [
            [
                'endDate' => '2025-03-01',
                'startDate' => '2025-01-01',
                'description' => 'Promotion 1',
                'discountType' => 'percentage',
                'discountValue' => '0.1',
                'promotionCode' => '10PROMO',
                'isPIF' => false,
            ],
            [
                'endDate' => '2025-04-01',
                'startDate' => '2025-02-01',
                'description' => 'Promotion 2',
                'discountType' => 'fixed',
                'discountValue' => '0.1',
                'promotionCode' => '10PIF',
                'isPIF' => true,
            ],
            [
                'endDate' => '2025-03-22',
                'startDate' => '2025-01-12',
                'description' => 'Promotion 3',
                'discountType' => 'percentage',
                'discountValue' => '0.2',
                'promotionCode' => '20PROMO',
                'isPIF' => false,
            ]
        ];
        $is_success = update_promotions( $raw_promotions, $departure_post_id );
        $this->assertTrue( $is_success );

        // Get promotion by code - 10PROMO.
        $promotions = get_promotions_by_code( '10PROMO' );
        $this->assertIsArray( $promotions );
        $this->assertNotEmpty( $promotions );
        $this->assertCount( 1, $promotions );
        $promotion1 = $promotions[0];
        $this->assertIsArray( $promotion1 );
        $this->assertArrayHasKey( 'id', $promotion1 );
        $promotion_id1 = $promotion1['id'];

        // Get promotion by code - 20PROMO.
        $promotions = get_promotions_by_code( '20PROMO' );
        $this->assertIsArray( $promotions );
        $this->assertNotEmpty( $promotions );
        $this->assertCount( 1, $promotions );
        $promotion2 = $promotions[0];
        $this->assertIsArray( $promotion2 );
        $this->assertArrayHasKey( 'id', $promotion2 );
        $promotion_id2 = $promotion2['id'];

        // Get promotion by code - 10PIF.
        $promotions = get_promotions_by_code( '10PIF' );
        $this->assertIsArray( $promotions );
        $this->assertNotEmpty( $promotions );
        $this->assertCount( 1, $promotions );
        $promotion3 = $promotions[0];
        $this->assertIsArray( $promotion3 );
        $this->assertArrayHasKey( 'id', $promotion3 );
        $promotion_id3 = $promotion3['id'];

        // Insert occupancies for this cabin.
        $raw_cabins_data = [
            [
                'id' => 'UNQ-123:2025-01-01:POQ-SGL',
                'code' => 'POQ-SGL',
                'name' => 'Explorer Single',
                'departureId' => 'UNQ-123:2025-01-01',
                'occupancies' => [
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:A',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:A',
                        'mask' => 'A',
                        'saleStatusCode' => 'O',
                        'saleStatus' => 'Open',
                        'prices' => [
                            'AUD' => [
                                'currencyCode' => 'AUD',
                                'pricePerPerson' => 1000,
                                'promos' => [
                                    '10PIF' => [
                                        'promoPricePerPerson' => 900,
                                    ],
                                    '20PROMO' => [
                                        'promoPricePerPerson' => 800,
                                    ],
                                ],
                            ],
                            'USD' => [
                                'currencyCode' => 'USD',
                                'pricePerPerson' => 8176,
                                'promos' => [
                                    '10PIF' => [
                                        'promoPricePerPerson' => 7360,
                                    ],
                                    '20PROMO' => [
                                        'promoPricePerPerson' => 6544,
                                    ],
                                    '10PROMO' => [
                                        'promoPricePerPerson' => 5360,
                                    ]
                                ],
                            ],
                            'CAD' => [
                                'currencyCode' => 'CAD',
                                'pricePerPerson' => 1000,
                                'promos' => [
                                    '10PIF' => [
                                        'promoPricePerPerson' => 900,
                                    ],
                                    '20PROMO' => [
                                        'promoPricePerPerson' => 800,
                                    ],
                                ],
                            ],
                            'EUR' => [
                                'currencyCode' => 'EUR',
                                'pricePerPerson' => 780,
                                'promos' => [
                                    '10PIF' => [
                                        'promoPricePerPerson' => 900,
                                    ],
                                    '20PROMO' => [
                                        'promoPricePerPerson' => 800,
                                    ],
                                    '10PROMO' => [
                                        'promoPricePerPerson' => 800,
                                    ],
                                ],
                            ],
                            'GBP' => [
                                'currencyCode' => 'GBP',
                                'pricePerPerson' => 18722,
                                'promos' => [
                                    '10PIF' => [
                                        'promoPricePerPerson' => 16850,
                                    ],
                                    '20PROMO' => [
                                        'promoPricePerPerson' => 14978,
                                    ],
                                    '10PROMO' => [
                                        'promoPricePerPerson' => 12300,
                                    ],
                                ],
                            ]
                        ],
                    ],
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:AA',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:AA',
                        'mask' => 'AA',
                        'saleStatusCode' => 'S',
                        'saleStatus' => 'Sold Out',
                        'prices' => [],
                    ],
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:SA',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:SA',
                        'mask' => 'SA',
                        'saleStatusCode' => 'N',
                        'saleStatus' => 'No display',
                        'prices' => [],
                    ],
                    [
                        'id' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                        'name' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                        'mask' => 'SAA',
                        'saleStatusCode' => 'O',
                        'saleStatus' => 'Open',
                        'prices' => [
                            'AUD' => [
                                'currencyCode' => 'AUD',
                                'pricePerPerson' => 1000,
                            ],
                        ],
                    ]
                ]
            ],
        ];

        $is_updated = update_occupancies( $raw_cabins_data, $departure_post_id );
        $this->assertTrue( $is_updated );

        // Flush the cache.
        wp_cache_flush();

        // Test with assigned occupancy that has no softrip id.
        $actual = get_occupancies_data($itinerary_post_id, $departure_post_id, $cabin_post_id1);
        $expected = [
            [
                'id' => 'UNQ-123:2025-01-01:POQ-SGL:A',
                'mask' => 'A',
                'description' => get_description_and_pax_count_by_mask( 'A' )['description'],
                'availabilityStatus' => 'O',
                'availabilityDescription' => 'Open',
                'spacesAvailable' => 0,
                'prices' => [
                    'AUD' => [
                        'currencyCode' => 'AUD',
                        'pricePerPerson' => 1000,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [
                            [
                                'id' => $promotion_id3,
                                'promotion_code' => '10PIF',
                                'promo_price_per_person' => 900,
                            ],
                            [
                                'id' => $promotion_id2,
                                'promotion_code' => '20PROMO',
                                'promo_price_per_person' => 800,
                            ],
                        ],
                    ],
                    'USD' => [
                        'currencyCode' => 'USD',
                        'pricePerPerson' => 8176,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [
                            [
                                'id' => $promotion_id3,
                                'promotion_code' => '10PIF',
                                'promo_price_per_person' => 7360,
                            ],
                            [
                                'id' => $promotion_id2,
                                'promotion_code' => '20PROMO',
                                'promo_price_per_person' => 6544,
                            ],
                            [
                                'id' => $promotion_id1,
                                'promotion_code' => '10PROMO',
                                'promo_price_per_person' => 5360,
                            ],
                        ],
                    ],
                    'CAD' => [
                        'currencyCode' => 'CAD',
                        'pricePerPerson' => 1000,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [
                            [
                                'id' => $promotion_id3,
                                'promotion_code' => '10PIF',
                                'promo_price_per_person' => 900,
                            ],
                            [
                                'id' => $promotion_id2,
                                'promotion_code' => '20PROMO',
                                'promo_price_per_person' => 800,
                            ],
                        ],
                    ],
                    'EUR' => [
                        'currencyCode' => 'EUR',
                        'pricePerPerson' => 780,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [
                            [
                                'id' => $promotion_id3,
                                'promotion_code' => '10PIF',
                                'promo_price_per_person' => 900,
                            ],
                            [
                                'id' => $promotion_id2,
                                'promotion_code' => '20PROMO',
                                'promo_price_per_person' => 800,
                            ],
                            [
                                'id' => $promotion_id1,
                                'promotion_code' => '10PROMO',
                                'promo_price_per_person' => 800,
                            ],
                        ]
                    ],
                    'GBP' => [
                        'currencyCode' => 'GBP',
                        'pricePerPerson' => 18722,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [
                            [
                                'id' => $promotion_id3,
                                'promotion_code' => '10PIF',
                                'promo_price_per_person' => 16850,
                            ],
                            [
                                'id' => $promotion_id2,
                                'promotion_code' => '20PROMO',
                                'promo_price_per_person' => 14978,
                            ],
                            [
                                'id' => $promotion_id1,
                                'promotion_code' => '10PROMO',
                                'promo_price_per_person' => 12300,
                            ],
                        ]
                    ],
                ]
            ],
            [
                'id' => 'UNQ-123:2025-01-01:POQ-SGL:SAA',
                'mask' => 'SAA',
                'description' => get_description_and_pax_count_by_mask( 'SAA' )['description'],
                'availabilityStatus' => 'O',
                'availabilityDescription' => 'Open',
                'spacesAvailable' => 0,
                'prices' => [
                    'AUD' => [
                        'currencyCode' => 'AUD',
                        'pricePerPerson' => 1000,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [],
                    ],
                    'USD' => [
                        'currencyCode' => 'USD',
                        'pricePerPerson' => 0,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [],
                    ],
                    'EUR' => [
                        'currencyCode' => 'EUR',
                        'pricePerPerson' => 0,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [],
                    ],
                    'GBP' => [
                        'currencyCode' => 'GBP',
                        'pricePerPerson' => 0,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [],
                    ],
                    'CAD' => [
                        'currencyCode' => 'CAD',
                        'pricePerPerson' => 0,
                        'mandatoryTransferPricePerPerson' => 0,
                        'supplementalPricePerPerson' => 0,
                        'promotionsApplied' => [],
                    ],
                ]
            ]
        ];
        $this->assertEquals( $expected, $actual );
    }
}