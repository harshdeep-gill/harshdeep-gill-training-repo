<?php
/**
 * Test suite for Itineraries.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Itineraries;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\Itineraries\get_itineraries;

use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;

/**
 * Class Test_Itineraries
 */
class Test_Itineraries extends Softrip_TestCase {
		/**
		 * Test get itineraries.
		 *
		 * @covers \Quark\Ingestor\get_itineraries
		 *
		 * @return void
		 */
	public function test_get_itineraries(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_itineraries();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_itineraries( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_itineraries( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create a expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Test without assigning any itinerary.
		$expected = [];
		$actual   = get_itineraries( $expedition_post_id );
		$this->assertEquals( $expected, $actual );

		// Create a itinerary post without softrip code.
		$itinerary_post_id1 = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id1 );

		// Assign itinerary to the expedition post.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id1 ] );

		// Test with assigned itinerary that has no softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Add softrip code to the itinerary post.
		update_post_meta( $itinerary_post_id1, 'softrip_package_code', 'UNQ-123' );

		// Add duration in days.
		update_post_meta( $itinerary_post_id1, 'duration_in_days', 7 );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itinerary that has softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'             => $itinerary_post_id1,
				'packageId'      => 'UNQ-123',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'      => true,
				'startLocation'  => '',
				'endLocation'    => '',
				'departures'     => [],
				'durationInDays' => 7,
			],
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
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'             => $itinerary_post_id1,
				'packageId'      => 'UNQ-123',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'      => true,
				'startLocation'  => $start_location_term_name,
				'endLocation'    => $end_location_term_name,
				'departures'     => [],
				'durationInDays' => 7,
			],
		];
		$this->assertEquals( $expected, $actual );

		// Create one more itinerary post with softrip code.
		$itinerary_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => 'UNQ-456',
				],
			]
		);
		$this->assertIsInt( $itinerary_post_id2 );

		// Assign both itineraries to the expedition post.
		update_post_meta( $expedition_post_id, 'related_itineraries', [ $itinerary_post_id1, $itinerary_post_id2 ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itineraries that have softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'             => $itinerary_post_id1,
				'packageId'      => 'UNQ-123',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'      => true,
				'startLocation'  => $start_location_term_name,
				'endLocation'    => $end_location_term_name,
				'departures'     => [],
				'durationInDays' => 7,
			],
			[
				'id'             => $itinerary_post_id2,
				'packageId'      => 'UNQ-456',
				'name'           => get_raw_text_from_html( get_the_title( $itinerary_post_id2 ) ),
				'published'      => true,
				'startLocation'  => '',
				'endLocation'    => '',
				'departures'     => [],
				'durationInDays' => 0,
			],
		];
		$this->assertEquals( $expected, $actual );
	}
}
