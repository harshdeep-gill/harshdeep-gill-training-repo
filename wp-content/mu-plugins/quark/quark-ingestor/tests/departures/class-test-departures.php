<?php
/**
 * Test suite for Departures.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Departures;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\Departures\get_departures_data;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ingestor\Promotions\get_promotions_data;
use function Quark\Ingestor\Ships\get_ship_data;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Departures
 */
class Test_Departures extends Softrip_TestCase {
		/**
		 * Test get departures data.
		 *
		 * @covers \Quark\Ingestor\get_departures_data
		 *
		 * @return void
		 */
	public function test_get_departures_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_departures_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_departures_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_departures_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create a itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Test without assigning any departure.
		$expected = [];
		$actual   = get_departures_data( $itinerary_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a departure post without softrip id.
		$departure_post_id1 = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-123:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'softrip_code'       => 'OEX20250101',
				],
			]
		);
		$this->assertIsInt( $departure_post_id1 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned departure that has no softrip id.
		$actual   = get_departures_data( $expedition_post_id, $itinerary_post_id );
		$expected = [
			[
				'id'               => $departure_post_id1,
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id1 ) ),
				'softripId'        => 'UNQ-123:2025-01-01',
				'url'              => '',
				'code'             => 'OEX20250101',
				'modified'         => get_post_modified_time( $departure_post_id1 ),
				'published'        => true,
				'startDate'        => '',
				'endDate'          => '',
				'durationInDays'   => 0,
				'ship'             => [],
				'languages'        => '',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
				'promotions'       => get_promotions_data( $departure_post_id1 ),
			],
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
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'OQP',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Add ship to departure meta.
		update_post_meta( $departure_post_id1, 'related_ship', $ship_post_id );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned departure that has softrip id, start/end date, duration, language and ship.
		$actual   = get_departures_data( $expedition_post_id, $itinerary_post_id );
		$expected = [
			[
				'id'               => $departure_post_id1,
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id1 ) ),
				'softripId'        => 'UNQ-123:2025-01-01',
				'url'              => '',
				'code'             => 'OEX20250101',
				'modified'         => get_post_modified_time( $departure_post_id1 ),
				'published'        => true,
				'startDate'        => '2025-01-01',
				'endDate'          => '2025-01-02',
				'durationInDays'   => 2,
				'ship'             => get_ship_data( $ship_post_id ),
				'languages'        => 'EN',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
				'promotions'       => get_promotions_data( $departure_post_id1 ),
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add one more departure post with softrip id.
		$departure_post_id2 = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_parent' => $itinerary_post_id,
				'meta_input'  => [
					'softrip_id'         => 'UNQ-456:2025-01-01',
					'itinerary'          => $itinerary_post_id,
					'related_expedition' => $expedition_post_id,
					'start_date'         => '2025-01-01',
					'end_date'           => '2025-01-02',
					'duration'           => 2,
					'softrip_code'       => 'OEX20250101',
				],
			]
		);
		$this->assertIsInt( $departure_post_id2 );

		// Create language term.
		$language_term_id2 = $this->factory()->term->create( [ 'taxonomy' => SPOKEN_LANGUAGE_TAXONOMY ] );
		$this->assertIsInt( $language_term_id2 );

		// Add language code to term meta.
		update_term_meta( $language_term_id2, 'language_code', 'FR' );

		// Assign language to the departure post.
		wp_set_post_terms( $departure_post_id2, [ $language_term_id2 ], SPOKEN_LANGUAGE_TAXONOMY );

		// Create ship post.
		$ship_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'LOP',
				],
			]
		);
		$this->assertIsInt( $ship_post_id2 );

		// Add ship to departure meta.
		update_post_meta( $departure_post_id2, 'related_ship', $ship_post_id2 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned departures that have softrip id, start/end date, duration, language and ship.
		$actual   = get_departures_data( $expedition_post_id, $itinerary_post_id );
		$expected = [
			[
				'id'               => $departure_post_id2,
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id2 ) ),
				'softripId'        => 'UNQ-456:2025-01-01',
				'url'              => '',
				'code'             => 'OEX20250101',
				'modified'         => get_post_modified_time( $departure_post_id2 ),
				'published'        => true,
				'startDate'        => '2025-01-01',
				'endDate'          => '2025-01-02',
				'durationInDays'   => 2,
				'ship'             => get_ship_data( $ship_post_id2 ),
				'languages'        => 'FR',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
				'promotions'       => get_promotions_data( $departure_post_id2 ),
			],
			[
				'id'               => $departure_post_id1,
				'name'             => get_raw_text_from_html( get_the_title( $departure_post_id1 ) ),
				'softripId'        => 'UNQ-123:2025-01-01',
				'url'              => '',
				'code'             => 'OEX20250101',
				'modified'         => get_post_modified_time( $departure_post_id1 ),
				'published'        => true,
				'startDate'        => '2025-01-01',
				'endDate'          => '2025-01-02',
				'durationInDays'   => 2,
				'ship'             => get_ship_data( $ship_post_id ),
				'languages'        => 'EN',
				'cabins'           => [],
				'adventureOptions' => [
					'includedOptions' => [],
					'paidOptions'     => [],
				],
				'promotions'       => get_promotions_data( $departure_post_id1 ),
			],
		];
		$this->assertEquals( $expected, $actual );
	}
}
