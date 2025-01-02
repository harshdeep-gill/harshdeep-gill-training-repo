<?php
/**
 * Test suite for Itineraries.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Itineraries;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Core\get_raw_text_from_html;
use function Quark\ExclusionSets\bust_post_cache as bust_exclusion_post_cache;
use function Quark\InclusionSets\bust_post_cache as bust_inclusion_post_cache;
use function Quark\Ingestor\get_id;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ingestor\Itineraries\get_exclusions_data;
use function Quark\Ingestor\Itineraries\get_inclusions_data;
use function Quark\Ingestor\Itineraries\get_itineraries;
use function Quark\Ingestor\Itineraries\get_itinerary_days_data;
use function Quark\Itineraries\bust_post_cache;
use function Quark\ItineraryDays\bust_post_cache as bust_day_post_cache;
use function Quark\Ports\bust_post_cache as bust_port_post_cache;

use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_POST_TYPE;
use const Quark\ExclusionSets\POST_TYPE as EXCLUSION_POST_TYPE;
use const Quark\InclusionSets\INCLUSION_EXCLUSION_CATEGORY;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAY_POST_TYPE;
use const Quark\Ports\POST_TYPE as PORT_POST_TYPE;

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
		$itinerary_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'drupal_id' => 123,
				],
			]
		);
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
				'id'                     => get_id( $itinerary_post_id1 ),
				'packageId'              => 'UNQ-123',
				'name'                   => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'              => true,
				'startLocation'          => '',
				'endLocation'            => '',
				'departures'             => [],
				'durationInDays'         => 7,
				'modified'               => get_post_modified_time( $itinerary_post_id1 ),
				'season'                 => '',
				'embarkation'            => '',
				'embarkationPortCode'    => '',
				'disembarkation'         => '',
				'disembarkationPortCode' => '',
				'itineraryMap'           => [],
				'days'                   => get_itinerary_days_data( $itinerary_post_id1 ),
				'inclusions'             => get_inclusions_data( $itinerary_post_id1 ),
				'exclusions'             => get_exclusions_data( $itinerary_post_id1 ),
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
				'id'                     => get_id( $itinerary_post_id1 ),
				'packageId'              => 'UNQ-123',
				'name'                   => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'              => true,
				'startLocation'          => $start_location_term_name,
				'endLocation'            => $end_location_term_name,
				'departures'             => [],
				'durationInDays'         => 7,
				'modified'               => get_post_modified_time( $itinerary_post_id1 ),
				'season'                 => '',
				'embarkation'            => '',
				'embarkationPortCode'    => '',
				'disembarkation'         => '',
				'disembarkationPortCode' => '',
				'itineraryMap'           => [],
				'days'                   => get_itinerary_days_data( $itinerary_post_id1 ),
				'inclusions'             => get_inclusions_data( $itinerary_post_id1 ),
				'exclusions'             => get_exclusions_data( $itinerary_post_id1 ),
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

		// Create day post with all meta.
		$day_post_id = $this->factory()->post->create(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Day 1',
				'post_content' => 'Day 1 content',
				'meta_input'   => [
					'day_number_from' => 1,
					'day_number_to'   => 1,
					'location'        => 'Location',
				],
			]
		);
		$this->assertIsInt( $day_post_id );

		// Assign day to the itinerary post.
		update_post_meta( $itinerary_post_id2, 'itinerary_days', [ $day_post_id ] );

		// Create inclusion post with all meta.
		$inclusion_post_id = $this->factory()->post->create(
			[
				'post_type'  => INCLUSION_POST_TYPE,
				'post_title' => 'Inclusion 1',
				'meta_input' => [
					'display_title' => 'Inclusion 1',
					'set_1'         => 'Set 1',
					'set_2'         => 'Set 2',
					'set_3'         => 'Set 3',
				],
			]
		);
		$this->assertIsInt( $inclusion_post_id );

		// Assign inclusion to the itinerary post.
		update_post_meta( $itinerary_post_id2, 'inclusions', [ $inclusion_post_id ] );

		// Create exclusion post with all meta.
		$exclusion_post_id = $this->factory()->post->create(
			[
				'post_type'  => EXCLUSION_POST_TYPE,
				'post_title' => 'Exclusion 1',
				'meta_input' => [
					'display_title' => 'Exclusion 1',
					'set_1'         => 'Set 1',
					'set_2'         => 'Set 2',
					'set_3'         => 'Set 3',
				],
			]
		);
		$this->assertIsInt( $exclusion_post_id );

		// Assign exclusion to the itinerary post.
		update_post_meta( $itinerary_post_id2, 'exclusions', [ $exclusion_post_id ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with assigned itineraries that have softrip code.
		$actual   = get_itineraries( $expedition_post_id );
		$expected = [
			[
				'id'                     => get_id( $itinerary_post_id1 ),
				'packageId'              => 'UNQ-123',
				'name'                   => get_raw_text_from_html( get_the_title( $itinerary_post_id1 ) ),
				'published'              => true,
				'startLocation'          => $start_location_term_name,
				'endLocation'            => $end_location_term_name,
				'departures'             => [],
				'durationInDays'         => 7,
				'modified'               => get_post_modified_time( $itinerary_post_id1 ),
				'season'                 => '',
				'embarkation'            => '',
				'embarkationPortCode'    => '',
				'disembarkation'         => '',
				'disembarkationPortCode' => '',
				'itineraryMap'           => [],
				'days'                   => get_itinerary_days_data( $itinerary_post_id1 ),
				'inclusions'             => get_inclusions_data( $itinerary_post_id1 ),
				'exclusions'             => get_exclusions_data( $itinerary_post_id1 ),
			],
			[
				'id'                     => get_id( $itinerary_post_id2 ),
				'packageId'              => 'UNQ-456',
				'name'                   => get_raw_text_from_html( get_the_title( $itinerary_post_id2 ) ),
				'published'              => true,
				'startLocation'          => '',
				'endLocation'            => '',
				'departures'             => [],
				'durationInDays'         => 0,
				'modified'               => get_post_modified_time( $itinerary_post_id2 ),
				'season'                 => '',
				'embarkation'            => '',
				'embarkationPortCode'    => '',
				'disembarkation'         => '',
				'disembarkationPortCode' => '',
				'itineraryMap'           => [],
				'days'                   => get_itinerary_days_data( $itinerary_post_id2 ),
				'inclusions'             => get_inclusions_data( $itinerary_post_id2 ),
				'exclusions'             => get_exclusions_data( $itinerary_post_id2 ),
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get itinerary days data.
	 *
	 * @covers \Quark\Ingestor\Itineraries\get_itinerary_days_data
	 *
	 * @return void
	 */
	public function test_get_itinerary_days_data(): void {
		// Default expected data.
		$default_expected = [];

		// Test without post id.
		$actual = get_itinerary_days_data();
		$this->assertEquals( $default_expected, $actual );

		// Test with invalid post id.
		$actual = get_itinerary_days_data( 999999 );
		$this->assertEquals( $default_expected, $actual );

		// Create an itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Test with itinerary post that has no days.
		$actual = get_itinerary_days_data( $itinerary_post_id );
		$this->assertEquals( $default_expected, $actual );

		// Create a day post.
		$day_post_id = $this->factory()->post->create(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Day 1',
				'post_content' => 'Day 1 content',
			]
		);
		$this->assertIsInt( $day_post_id );

		// Assign day to the itinerary post.
		update_post_meta( $itinerary_post_id, 'itinerary_days', [ $day_post_id ] );

		// Bust the cache.
		bust_post_cache( $itinerary_post_id );

		// Expected data.
		$expected_data = [
			'id'             => $day_post_id,
			'title'          => '',
			'dayStartNumber' => 0,
			'dayEndNumber'   => 0,
			'location'       => '',
			'portCode'       => '',
			'portLocation'   => '',
			'description'    => 'Day 1 content',
		];

		// Test with itinerary post that has one day.
		$actual   = get_itinerary_days_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Add meta to the day post.
		update_post_meta( $day_post_id, 'day_number_from', 1 );
		update_post_meta( $day_post_id, 'day_number_to', 1 );
		update_post_meta( $day_post_id, 'location', 'Location' );

		// Bust the cache of day.
		bust_day_post_cache( $day_post_id );

		// Update expected data.
		$expected_data['dayStartNumber'] = 1;
		$expected_data['dayEndNumber']   = 1;
		$expected_data['location']       = 'Location';

		// Test with itinerary post that has one day with meta.
		$actual   = get_itinerary_days_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Create a port post.
		$port_post_id = $this->factory()->post->create(
			[
				'post_type'  => PORT_POST_TYPE,
				'post_title' => 'Port 1',
			]
		);
		$this->assertIsInt( $port_post_id );

		// Assign port to the day post.
		update_post_meta( $day_post_id, 'port', $port_post_id );

		// Bust the cache of day.
		bust_day_post_cache( $day_post_id );

		// Update expected data.
		$expected_data['portLocation'] = 'Port 1';

		// Test with itinerary post that has one day with port (without port code).
		$actual   = get_itinerary_days_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Add port code to the port post.
		update_post_meta( $port_post_id, 'port_code', 'PORT1' );

		// Bust the cache of port.
		bust_port_post_cache( $port_post_id );

		// Update expected data.
		$expected_data['portCode'] = 'PORT1';

		// Test with itinerary post that has one day with port (with port code).
		$actual   = get_itinerary_days_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Create one more port with all meta.
		$port_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => PORT_POST_TYPE,
				'post_title' => 'Port 2',
				'meta_input' => [
					'port_code' => 'PORT2',
				],
			]
		);
		$this->assertIsInt( $port_post_id2 );

		// Create one more day post.
		$day_post_id2 = $this->factory()->post->create(
			[
				'post_type'    => ITINERARY_DAY_POST_TYPE,
				'post_title'   => 'Day 2',
				'post_content' => 'Day 2 content',
				'meta_input'   => [
					'day_number_from' => 2,
					'day_number_to'   => 2,
					'location'        => 'Location 2',
					'port'            => $port_post_id2,
				],
			]
		);
		$this->assertIsInt( $day_post_id2 );

		// Assign day to the itinerary post.
		update_post_meta( $itinerary_post_id, 'itinerary_days', [ $day_post_id, $day_post_id2 ] );

		// Bust the itinerary cache.
		bust_post_cache( $itinerary_post_id );

		// Update expected data.
		$expected_data2 = [
			'id'             => $day_post_id2,
			'title'          => '',
			'dayStartNumber' => 2,
			'dayEndNumber'   => 2,
			'location'       => 'Location 2',
			'portCode'       => 'PORT2',
			'portLocation'   => 'Port 2',
			'description'    => 'Day 2 content',
		];

		// Test with itinerary post that has two days.
		$actual   = get_itinerary_days_data( $itinerary_post_id );
		$expected = [
			$expected_data,
			$expected_data2,
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get inclusions data.
	 *
	 * @covers \Quark\Ingestor\Itineraries\get_inclusions_data
	 *
	 * @return void
	 */
	public function test_get_inclusions_data(): void {
		// Expected data.
		$default_expected = [];

		// Test without post id.
		$actual = get_inclusions_data();
		$this->assertEquals( $default_expected, $actual );

		// Test with invalid post id.
		$actual = get_inclusions_data( 999999 );
		$this->assertEquals( $default_expected, $actual );

		// Create an itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Test with itinerary post that has no inclusions.
		$actual = get_inclusions_data( $itinerary_post_id );
		$this->assertEquals( $default_expected, $actual );

		// Create an inclusion post.
		$inclusion_post_id = $this->factory()->post->create(
			[
				'post_type'  => INCLUSION_POST_TYPE,
				'post_title' => 'Inclusion 1',
			]
		);
		$this->assertIsInt( $inclusion_post_id );

		// Assign inclusion to the itinerary post.
		update_post_meta( $itinerary_post_id, 'inclusions', [ $inclusion_post_id ] );

		// Bust the cache.
		bust_post_cache( $itinerary_post_id );

		// Expected data.
		$expected_data = [
			'id'           => $inclusion_post_id,
			'title'        => '',
			'items'        => [],
			'categoryId'   => 0,
			'categoryName' => '',
		];

		// Test with itinerary post that has one inclusion.
		$actual   = get_inclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Add title.
		update_post_meta( $inclusion_post_id, 'display_title', 'Inclusion 1' );

		// Bust the cache.
		bust_inclusion_post_cache( $inclusion_post_id );

		// Update expected data.
		$expected_data['title'] = 'Inclusion 1';

		// Test with itinerary post that has one inclusion with title.
		$actual   = get_inclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Add inclusion items.
		update_post_meta( $inclusion_post_id, 'set_1', 'Set 1' );
		update_post_meta( $inclusion_post_id, 'set_2', 'Set 2' );
		update_post_meta( $inclusion_post_id, 'set_3', '<span>Set 3</span>' );

		// Bust the cache.
		bust_inclusion_post_cache( $inclusion_post_id );

		// Update expected data.
		$expected_data['items'] = [
			'Set 1',
			'Set 2',
			'Set 3',
		];

		// Test with itinerary post that has one inclusion with items.
		$actual   = get_inclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Create a inclusion category term.
		$inclusion_category_term_id = $this->factory()->term->create(
			[
				'taxonomy' => INCLUSION_EXCLUSION_CATEGORY,
				'name'     => 'Inclusion Category 1',
			]
		);
		$this->assertIsInt( $inclusion_category_term_id );

		// Assign category to the inclusion post.
		wp_set_object_terms( $inclusion_post_id, $inclusion_category_term_id, INCLUSION_EXCLUSION_CATEGORY );

		// Bust the cache.
		bust_inclusion_post_cache( $inclusion_post_id );

		// Update expected data.
		$expected_data['categoryId']   = $inclusion_category_term_id;
		$expected_data['categoryName'] = 'Inclusion Category 1';

		// Test with itinerary post that has one inclusion with category.
		$actual   = get_inclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Create one more inclusion post with all meta.
		$inclusion_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => INCLUSION_POST_TYPE,
				'post_title' => 'Inclusion 2',
				'meta_input' => [
					'display_title' => 'Inclusion 2',
					'set_1'         => 'Set 1',
					'set_2'         => 'Set 2',
					'set_3'         => 'Set 3',
				],
			]
		);
		$this->assertIsInt( $inclusion_post_id2 );

		// Create one more inclusion category term.
		$inclusion_category_term_id2 = $this->factory()->term->create(
			[
				'taxonomy' => INCLUSION_EXCLUSION_CATEGORY,
				'name'     => 'Inclusion Category 2',
			]
		);
		$this->assertIsInt( $inclusion_category_term_id2 );

		// Assign category to the inclusion post.
		wp_set_object_terms( $inclusion_post_id2, $inclusion_category_term_id2, INCLUSION_EXCLUSION_CATEGORY );

		// Assign both inclusions to the itinerary post.
		update_post_meta( $itinerary_post_id, 'inclusions', [ $inclusion_post_id, $inclusion_post_id2 ] );

		// Bust the cache.
		bust_post_cache( $itinerary_post_id );

		// Update expected data.
		$expected_data2 = [
			'id'           => $inclusion_post_id2,
			'title'        => 'Inclusion 2',
			'items'        => [
				'Set 1',
				'Set 2',
				'Set 3',
			],
			'categoryId'   => $inclusion_category_term_id2,
			'categoryName' => 'Inclusion Category 2',
		];

		// Test with itinerary post that has two inclusions.
		$actual   = get_inclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
			$expected_data2,
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get exclusions data.
	 *
	 * @covers \Quark\Ingestor\Itineraries\get_exclusions_data
	 *
	 * @return void
	 */
	public function test_get_exclusions_data(): void {
		// Expected data.
		$default_expected = [];

		// Test without post id.
		$actual = get_exclusions_data();
		$this->assertEquals( $default_expected, $actual );

		// Test with invalid post id.
		$actual = get_exclusions_data( 999999 );

		// Create an itinerary post.
		$itinerary_post_id = $this->factory()->post->create( [ 'post_type' => ITINERARY_POST_TYPE ] );
		$this->assertIsInt( $itinerary_post_id );

		// Test with itinerary post that has no exclusions.
		$actual = get_exclusions_data( $itinerary_post_id );
		$this->assertEquals( $default_expected, $actual );

		// Create an exclusion post.
		$exclusion_post_id = $this->factory()->post->create(
			[
				'post_type'  => EXCLUSION_POST_TYPE,
				'post_title' => 'Exclusion 1',
			]
		);
		$this->assertIsInt( $exclusion_post_id );

		// Assign exclusion to the itinerary post.
		update_post_meta( $itinerary_post_id, 'exclusions', [ $exclusion_post_id ] );

		// Bust the cache.
		bust_post_cache( $itinerary_post_id );

		// Expected data.
		$expected_data = [
			'id'           => $exclusion_post_id,
			'title'        => '',
			'items'        => [],
			'categoryId'   => 0,
			'categoryName' => '',
		];

		// Test with itinerary post that has one exclusion.
		$actual   = get_exclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Add title.
		update_post_meta( $exclusion_post_id, 'display_title', 'Exclusion 1' );

		// Bust the cache.
		bust_exclusion_post_cache( $exclusion_post_id );

		// Update expected data.
		$expected_data['title'] = 'Exclusion 1';

		// Test with itinerary post that has one exclusion with title.
		$actual   = get_exclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Add exclusion items.
		update_post_meta( $exclusion_post_id, 'set_1', 'Set 1' );
		update_post_meta( $exclusion_post_id, 'set_2', 'Set 2' );
		update_post_meta( $exclusion_post_id, 'set_3', '<span>Set 3</span>' );

		// Bust the cache.
		bust_exclusion_post_cache( $exclusion_post_id );

		// Update expected data.
		$expected_data['items'] = [
			'Set 1',
			'Set 2',
			'Set 3',
		];

		// Test with itinerary post that has one exclusion with items.
		$actual   = get_exclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Create a exclusion category term.
		$exclusion_category_term_id = $this->factory()->term->create(
			[
				'taxonomy' => INCLUSION_EXCLUSION_CATEGORY,
				'name'     => 'Exclusion Category 1',
			]
		);
		$this->assertIsInt( $exclusion_category_term_id );

		// Assign category to the exclusion post.
		wp_set_object_terms( $exclusion_post_id, $exclusion_category_term_id, INCLUSION_EXCLUSION_CATEGORY );

		// Bust the cache.
		bust_exclusion_post_cache( $exclusion_post_id );

		// Update expected data.
		$expected_data['categoryId']   = $exclusion_category_term_id;
		$expected_data['categoryName'] = 'Exclusion Category 1';

		// Test with itinerary post that has one exclusion with category.
		$actual   = get_exclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
		];
		$this->assertEquals( $expected, $actual );

		// Create one more exclusion post with all meta.
		$exclusion_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => EXCLUSION_POST_TYPE,
				'post_title' => 'Exclusion 2',
				'meta_input' => [
					'display_title' => 'Exclusion 2',
					'set_1'         => 'Set 1',
					'set_2'         => 'Set 2',
					'set_3'         => 'Set 3',
				],
			]
		);
		$this->assertIsInt( $exclusion_post_id2 );

		// Create one more exclusion category term.
		$exclusion_category_term_id2 = $this->factory()->term->create(
			[
				'taxonomy' => INCLUSION_EXCLUSION_CATEGORY,
				'name'     => 'Exclusion Category 2',
			]
		);
		$this->assertIsInt( $exclusion_category_term_id2 );

		// Assign category to the exclusion post.
		wp_set_object_terms( $exclusion_post_id2, $exclusion_category_term_id2, INCLUSION_EXCLUSION_CATEGORY );

		// Assign both exclusions to the itinerary post.
		update_post_meta( $itinerary_post_id, 'exclusions', [ $exclusion_post_id, $exclusion_post_id2 ] );

		// Bust the cache.
		bust_post_cache( $itinerary_post_id );

		// Update expected data.
		$expected_data2 = [
			'id'           => $exclusion_post_id2,
			'title'        => 'Exclusion 2',
			'items'        => [
				'Set 1',
				'Set 2',
				'Set 3',
			],
			'categoryId'   => $exclusion_category_term_id2,
			'categoryName' => 'Exclusion Category 2',
		];

		// Test with itinerary post that has two exclusions.
		$actual   = get_exclusions_data( $itinerary_post_id );
		$expected = [
			$expected_data,
			$expected_data2,
		];
		$this->assertEquals( $expected, $actual );
	}
}
