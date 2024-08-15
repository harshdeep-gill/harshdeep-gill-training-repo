<?php
/**
 * Test suite for departures update.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Departures;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\Departures\update_departures;
use function Quark\Softrip\do_sync;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Test_Update_Departures
 */
class Test_Update_Departures extends Softrip_TestCase {
	/**
	 * Test update departures.
	 *
	 * @covers \Quark\Softrip\Departures\update_departures
	 *
	 * @return void
	 */
	public function test_update_departures(): void {
		// Empty arguments.
		$actual = update_departures();
		$this->assertFalse( $actual );

		// Default arguments.
		$actual = update_departures( [], '' );
		$this->assertFalse( $actual );

		// Setup variables.
		$pqo_softrip_package_code    = 'PQO-892';
		$original_pqo_raw_departures = [
			[ // Valid departure.
				'id'          => 'PQO-892:2027-08-26',
				'code'        => 'OMI20250826',
				'packageCode' => 'PQO-892',
				'startDate'   => '2027-08-26',
				'endDate'     => '2027-09-05',
				'duration'    => 11,
				'shipCode'    => 'OMI',
				'marketCode'  => 'PRC',
			],
			[ // Valid departure.
				'id'          => 'PQO-892:2027-09-05',
				'code'        => 'OMI20250905',
				'packageCode' => 'PQO-892',
				'startDate'   => '2027-09-05',
				'endDate'     => '2027-09-15',
				'duration'    => 10,
				'shipCode'    => 'OMX',
				'marketCode'  => 'PRC',
			],
		];
		$pqo_raw_departures          = $original_pqo_raw_departures;
		$yesterday                   = date_format( date_sub( $this->get_current_date(), $this->get_date_interval( '1 days' ) ), 'Y-m-d' );

		// Create english term.
		$english_term = wp_insert_term( 'English', SPOKEN_LANGUAGE_TAXONOMY );
		$this->assertIsArray( $english_term );
		$this->assertArrayHasKey( 'term_id', $english_term );

		// Create arabic term.
		$arabic_term = wp_insert_term( 'Arabic', SPOKEN_LANGUAGE_TAXONOMY );
		$this->assertIsArray( $arabic_term );
		$this->assertArrayHasKey( 'term_id', $arabic_term );

		// Get slug for english term.
		$english_term = get_term( $english_term['term_id'], SPOKEN_LANGUAGE_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $english_term );
		$this->assertArrayHasKey( 'slug', $english_term );
		$english_term_slug = $english_term['slug'];

		// Get slug for arabic term.
		$arabic_term = get_term( $arabic_term['term_id'], SPOKEN_LANGUAGE_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $arabic_term );
		$this->assertArrayHasKey( 'slug', $arabic_term );
		$arabic_term_slug = $arabic_term['slug'];

		// Try to update departures of an invalid softrip package code - no itinerary have it.
		$actual = update_departures( $pqo_raw_departures, 'invalid' );
		$this->assertFalse( $actual );

		// Create an itinerary post.
		$itinerary_id = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $pqo_softrip_package_code,
				],
			]
		);
		$this->assertIsInt( $itinerary_id );

		// Create one more itinerary post with same softrip package code.
		$itinerary_id_2 = $this->factory()->post->create(
			[
				'post_type'  => ITINERARY_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => $pqo_softrip_package_code,
				],
			]
		);
		$this->assertIsInt( $itinerary_id_2 );

		// Updating should fail as there are multiple itineraries with same softrip package code.
		$actual = update_departures( $pqo_raw_departures, $pqo_softrip_package_code );
		$this->assertFalse( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Delete the second itinerary.
		wp_delete_post( $itinerary_id_2, true );

		// Test with an itinerary that doesn't have any expedition.
		$actual = update_departures( $pqo_raw_departures, $pqo_softrip_package_code );
		$this->assertFalse( $actual );

		// Create an expedition post.
		$expedition_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_id );

		// Set the expedition post id in the itinerary post.
		update_post_meta( $itinerary_id, 'related_expedition', $expedition_id );

		// Create a ship post for OMX.
		$ship_id_omx = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'OMX',
				],
			]
		);
		$this->assertIsInt( $ship_id_omx );

		// Test with itinerary having expedition - proper update.
		$actual = update_departures( $pqo_raw_departures, $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Two departure posts should have been created. There are various ways to fetch departures. One via meta query. Second via parent.
		$departure_posts = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 2, $departure_posts );

		// Get the first departure post.
		$departure_post1 = $departure_posts[0];
		$this->assertIsInt( $departure_post1 );

		// Title of the first departure post should be the same as the id of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['id'], get_the_title( $departure_post1 ) );

		// Code of the first departure post should be the same as the code of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['code'], get_post_meta( $departure_post1, 'softrip_code', true ) );

		// Package code of the first departure post should be the same as the package code of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['packageCode'], get_post_meta( $departure_post1, 'softrip_package_code', true ) );

		// Start date of the first departure post should be the same as the start date of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['startDate'], get_post_meta( $departure_post1, 'start_date', true ) );

		// End date of the first departure post should be the same as the end date of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['endDate'], get_post_meta( $departure_post1, 'end_date', true ) );

		// Duration of the first departure post should be the same as the duration of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['duration'], get_post_meta( $departure_post1, 'duration', true ) );

		// Ship code of the first departure post should be the same as the ship code of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['shipCode'], get_post_meta( $departure_post1, 'ship_code', true ) );

		// Market code of the first departure post should be the same as the market code of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['marketCode'], get_post_meta( $departure_post1, 'softrip_market_code', true ) );

		// There should be no related_ship as ship post doesn't exists for the first departure.
		$this->assertEquals( 0, get_post_meta( $departure_post1, 'related_ship', true ) );

		// English should be set.
		$spoken_language_terms = wp_get_post_terms( $departure_post1, SPOKEN_LANGUAGE_TAXONOMY, [ 'fields' => 'slugs' ] );
		$this->assertIsArray( $spoken_language_terms );
		$this->assertEquals( [ $english_term_slug ], $spoken_language_terms );

		// Get the second departure post.
		$departure_post2 = $departure_posts[1];
		$this->assertIsInt( $departure_post2 );

		// Title of the second departure post should be the same as the id of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['id'], get_the_title( $departure_post2 ) );

		// Code of the second departure post should be the same as the code of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['code'], get_post_meta( $departure_post2, 'softrip_code', true ) );

		// Package code of the second departure post should be the same as the package code of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['packageCode'], get_post_meta( $departure_post2, 'softrip_package_code', true ) );

		// Start date of the second departure post should be the same as the start date of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['startDate'], get_post_meta( $departure_post2, 'start_date', true ) );

		// End date of the second departure post should be the same as the end date of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['endDate'], get_post_meta( $departure_post2, 'end_date', true ) );

		// Duration of the second departure post should be the same as the duration of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['duration'], get_post_meta( $departure_post2, 'duration', true ) );

		// Ship code of the second departure post should be the same as the ship code of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['shipCode'], get_post_meta( $departure_post2, 'ship_code', true ) );

		// Market code of the second departure post should be the same as the market code of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['marketCode'], get_post_meta( $departure_post2, 'softrip_market_code', true ) );

		// There should be a related_ship as ship post exists for the second departure.
		$this->assertEquals( $ship_id_omx, get_post_meta( $departure_post2, 'related_ship', true ) );

		// English should be set.
		$spoken_language_terms = wp_get_post_terms( $departure_post2, SPOKEN_LANGUAGE_TAXONOMY, [ 'fields' => 'slugs' ] );
		$this->assertIsArray( $spoken_language_terms );
		$this->assertEquals( [ $english_term_slug ], $spoken_language_terms );

		// Fetch departures via parent - itinerary is the parent of such departures.
		$departure_posts2 = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'post_parent'            => $itinerary_id,
			]
		);
		$this->assertCount( 2, $departure_posts2 );

		// Assert the same departure posts.
		$this->assertEquals( $departure_posts, $departure_posts2 );

		// Create OMI ship post.
		$ship_id_omi = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'meta_input' => [
					'ship_code' => 'OMI',
				],
			]
		);
		$this->assertIsInt( $ship_id_omi );

		// Let's try updating some fields of the first departure.
		$pqo_raw_departures[0]['startDate'] = '2027-08-27';
		$pqo_raw_departures[0]['endDate']   = '2027-09-06';
		$pqo_raw_departures[0]['duration']  = 10;
		$pqo_raw_departures[0]['shipCode']  = 'OMX';

		// Let's try updating some fields of the second departure.
		$pqo_raw_departures[1]['startDate'] = '2027-09-06';
		$pqo_raw_departures[1]['endDate']   = '2027-09-16';
		$pqo_raw_departures[1]['duration']  = 10;
		$pqo_raw_departures[1]['shipCode']  = 'OMI';

		// Also, update the spoken language of second departure to arabic. This is to set that it's not set again to english during update.
		wp_set_object_terms( $departure_post2, $arabic_term_slug, SPOKEN_LANGUAGE_TAXONOMY );

		// Update the departures.
		$actual = update_departures( $pqo_raw_departures, $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Make sure the departures are updated and not inserted again.
		$departure_posts_updated = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 2, $departure_posts_updated );

		// Get ids of the updated departure posts.
		$departure_post1_updated = $departure_posts_updated[0];
		$this->assertIsInt( $departure_post1_updated );
		$departure_post2_updated = $departure_posts_updated[1];
		$this->assertIsInt( $departure_post2_updated );

		// These should be same as the previous departure posts.
		$this->assertEquals( $departure_post1, $departure_post1_updated );
		$this->assertEquals( $departure_post2, $departure_post2_updated );

		// Start date of the first departure post should be the same as the start date of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['startDate'], get_post_meta( $departure_post1, 'start_date', true ) );

		// End date of the first departure post should be the same as the end date of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['endDate'], get_post_meta( $departure_post1, 'end_date', true ) );

		// Duration of the first departure post should be the same as the duration of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['duration'], get_post_meta( $departure_post1, 'duration', true ) );

		// Ship code of the first departure post should be the same as the ship code of the first raw departure.
		$this->assertEquals( $pqo_raw_departures[0]['shipCode'], get_post_meta( $departure_post1, 'ship_code', true ) );

		// There should be the related_ship.
		$this->assertEquals( $ship_id_omx, get_post_meta( $departure_post1, 'related_ship', true ) );

		// English should be set.
		$spoken_language_terms = wp_get_post_terms( $departure_post1, SPOKEN_LANGUAGE_TAXONOMY, [ 'fields' => 'slugs' ] );
		$this->assertIsArray( $spoken_language_terms );
		$this->assertEquals( [ $english_term_slug ], $spoken_language_terms );

		// Start date of the second departure post should be the same as the start date of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['startDate'], get_post_meta( $departure_post2, 'start_date', true ) );

		// End date of the second departure post should be the same as the end date of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['endDate'], get_post_meta( $departure_post2, 'end_date', true ) );

		// Duration of the second departure post should be the same as the duration of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['duration'], get_post_meta( $departure_post2, 'duration', true ) );

		// Ship code of the second departure post should be the same as the ship code of the second raw departure.
		$this->assertEquals( $pqo_raw_departures[1]['shipCode'], get_post_meta( $departure_post2, 'ship_code', true ) );

		// There should be the related_ship.
		$this->assertEquals( $ship_id_omi, get_post_meta( $departure_post2, 'related_ship', true ) );

		// Arabic should be set.
		$spoken_language_terms = wp_get_post_terms( $departure_post2, SPOKEN_LANGUAGE_TAXONOMY, [ 'fields' => 'slugs' ] );
		$this->assertIsArray( $spoken_language_terms );
		$this->assertEquals( [ $arabic_term_slug ], $spoken_language_terms );

		// Empty raw departure array provided for update.
		$actual = update_departures( [], $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Since, all departures are valid and haven't expired, they should still be there.
		$departure_posts_empty = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 2, $departure_posts_empty );

		// Make departure 1 expired.
		update_post_meta( $departure_post1, 'start_date', $yesterday );

		// Empty raw departure array provided for update.
		$actual = update_departures( [], $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Since, one departure is expired, only one should be there.
		$departure_posts_expired = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 1, $departure_posts_expired );

		// Departure 2 should be there.
		$this->assertContains( $departure_post2, $departure_posts_expired );

		// Departure 1 should not be there.
		$this->assertNotContains( $departure_post1, $departure_posts_expired );

		// Status of departure 1 should be draft.
		$this->assertEquals( 'draft', get_post_status( $departure_post1 ) );

		// Reset the start date of departure 1.
		update_post_meta( $departure_post1, 'start_date', $pqo_raw_departures[0]['startDate'] );

		// Update the departure.
		$actual = update_departures( [ $pqo_raw_departures[0] ], $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Get departures.
		$departure_posts_updated = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 1, $departure_posts_updated );

		// Drafted departure 1 should not be there.
		$this->assertNotContains( $departure_post1, $departure_posts_updated );

		// Get draft departures by the softrip package code.
		$drafted_departure_posts = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'post_status'            => 'draft',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 1, $drafted_departure_posts );

		// Drafted departure 1 should be there - meaning draft posts are not published again if start date has been updated.
		$this->assertContains( $departure_post1, $drafted_departure_posts );

		// Get original start date of departure 1 to affirm that date was updated for the draft post as well.
		$original_start_date = $pqo_raw_departures[0]['startDate'];
		$this->assertEquals( $original_start_date, get_post_meta( $departure_post1, 'start_date', true ) );

		// Publish the draft departure to test further.
		wp_update_post(
			[
				'ID'          => $departure_post1,
				'post_status' => 'publish',
			]
		);

		// Remove the first departure raw data.
		array_shift( $pqo_raw_departures );

		// Provide the updated raw departures.
		$actual = update_departures( $pqo_raw_departures, $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Still both departures should be there as both are valid and haven't expired.
		$departure_posts_updated = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 2, $departure_posts_updated );

		// Get the first departure post.
		$departure_post1_updated = $departure_posts_updated[0];
		$this->assertIsInt( $departure_post1_updated );

		// Get the second departure post.
		$departure_post2_updated = $departure_posts_updated[1];

		// These should be same as the previous departure posts.
		$this->assertEquals( $departure_post1, $departure_post1_updated );
		$this->assertEquals( $departure_post2, $departure_post2_updated );

		// Reset pqo_raw_departures.
		$pqo_raw_departures = $original_pqo_raw_departures;

		// Now, while updating, don't provide the first raw departure data.
		$actual = update_departures( [ $pqo_raw_departures[1] ], $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Both departures should still be present.
		$departure_posts_updated = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 2, $departure_posts_updated );

		// Get the first departure post.
		$departure_post1_updated = $departure_posts_updated[0];
		$this->assertIsInt( $departure_post1_updated );

		// Get the second departure post.
		$departure_post2_updated = $departure_posts_updated[1];
		$this->assertIsInt( $departure_post2_updated );

		// These should be same as the previous departure posts.
		$this->assertEquals( $departure_post1, $departure_post1_updated );
		$this->assertEquals( $departure_post2, $departure_post2_updated );

		// Now, make the first departure post expired. And while updating, don't provide the first raw departure data again.
		update_post_meta( $departure_post1, 'start_date', $yesterday );

		// Update the departures.
		$actual = update_departures( [ $pqo_raw_departures[1] ], $pqo_softrip_package_code );
		$this->assertTrue( $actual );

		// Flush the cache.
		wp_cache_flush();

		// Only the second departure should be there.
		$departure_posts_updated = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $pqo_softrip_package_code,
					],
				],
			]
		);
		$this->assertCount( 1, $departure_posts_updated );

		// First departure should not be there.
		$this->assertNotContains( $departure_post1, $departure_posts_updated );

		// Get the second departure post.
		$departure_post2_updated = $departure_posts_updated[0];
		$this->assertIsInt( $departure_post2_updated );

		// Only the second departure should be there.
		$this->assertEquals( $departure_post2, $departure_post2_updated );

		// First departure should be again drafted.
		$this->assertEquals( 'draft', get_post_status( $departure_post1 ) );

		// Clean up.
		wp_delete_post( $itinerary_id, true );
		wp_delete_post( $expedition_id, true );
		wp_delete_post( $ship_id_omx, true );

		// Test with real mock data.
		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Sync softrip with existing posts.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Initialize softrip codes.
		$softrip_package_code1 = 'ABC-123';
		$softrip_package_code2 = 'PQR-345';
		$softrip_package_code3 = 'JKL-012';
		$softrip_package_code4 = 'HIJ-456';

		/**
		 * Test for softrip package code 1 - ABC-123.
		 */

		// Get departure posts.
		$departure_posts = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $softrip_package_code1,
					],
				],
			]
		);
		$this->assertCount( 1, $departure_posts );

		/**
		 * Test for softrip package code 2 - PQR-345.
		 */

		// Get departure posts.
		$departure_posts = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $softrip_package_code2,
					],
				],
			]
		);
		$this->assertCount( 0, $departure_posts );

		/**
		 * Test for softrip package code 3 - JKL-012.
		 */

		// Get departure posts.
		$departure_posts = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $softrip_package_code3,
					],
				],
			]
		);
		$this->assertCount( 2, $departure_posts );

		/**
		 * Test for softrip package code 4 - HIJ-456.
		 */

		// Get departure posts.
		$departure_posts = get_posts(
			[
				'post_type'              => DEPARTURE_POST_TYPE,
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
				'suppress_filters'       => false,
				'fields'                 => 'ids',
				'order'                  => 'ASC',
				'meta_query'             => [
					[
						'key'   => 'softrip_package_code',
						'value' => $softrip_package_code4,
					],
				],
			]
		);
		$this->assertCount( 4, $departure_posts );
	}
}
