<?php
/**
 * Softrip Test case class.
 *
 * @package quark
 */

namespace Quark\Tests\Softrip;

use WP_UnitTestCase;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

/**
 * Class Softrip_TestCase
 *
 * Base class for any test suit that needs Softrip related data.
 */
abstract class Softrip_TestCase extends WP_UnitTestCase {
	/**
	 * Itinerary posts.
	 *
	 * @var array<int|WP_Error>
	 */
	protected static array $itinerary_ids = [];

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		// Run parent and include setup.
		parent::set_up_before_class();

		// Create a test itinerary post.
		self::$itinerary_ids = self::factory()->post->create_many(
			20,
			[
				'post_type' => ITINERARY_POST_TYPE,
			]
		);

		// Write above code in loop.
		$softrip_package_ids = [
			'ABC-123',
			'DEF-456',
			'GHI-789',
			'JKL-012',
			'MNO-678',
			'PQR-345',
			'STU-901',
			'VWX-234',
			'YZA-567',
			'BCD-890',
			'EFG-123',
			'HIJ-456',
			'KLM-789',
			'NOP-012',
			'QRS-345',
			'TUV-678',
			'WXY-901',
			'ZAB-234',
			'CDE-567',
			'FGH-890',
		];

		// Loop through the itineraries and set softrip package id meta.
		foreach ( self::$itinerary_ids as $index => $itinerary_id ) {
			update_post_meta( absint( $itinerary_id ), 'softrip_package_id', $softrip_package_ids[ $index ] );
			wp_cache_delete( ITINERARY_POST_TYPE . '_' . absint( $itinerary_id ), ITINERARY_POST_TYPE );
		}

		// Create Cabin Category posts.
		$cabin_ids = self::factory()->post->create_many(
			5,
			[
				'post_type' => CABIN_CATEGORY_POST_TYPE,
			]
		);

		// List the Cabin softrip codes.
		$softrip_cabin_ids = [
			'OEX-SGL',
			'OEX-DBL',
			'ULT-SGL',
			'ULT-SGL',
			'ULT-DBL',
			'ULT-DBL',
		];

		// Loop through the cabins and set meta.
		foreach ( $cabin_ids as $index => $cabin_id ) {
			update_post_meta( absint( $cabin_id ), 'cabin_category_id', $softrip_cabin_ids[ $index ] );
			wp_cache_delete( CABIN_CATEGORY_POST_TYPE . '_' . absint( $cabin_id ), CABIN_CATEGORY_POST_TYPE );
		}

		// Create ship posts.
		$ship_ids = self::factory()->post->create_many(
			5,
			[
				'post_status' => 'publish',
				'post_type'   => SHIP_POST_TYPE,
			]
		);

		// List the Ship softrip codes.
		$softrip_ship_ids = [
			'OEX',
			'GHI',
			'JKL',
			'ULT',
			'MNO',
		];

		// Loop through the ships and set meta.
		foreach ( $ship_ids as $index => $ship_id ) {
			update_post_meta( absint( $ship_id ), 'ship_id', $softrip_ship_ids[ $index ] );
			wp_cache_delete( SHIP_POST_TYPE . '_' . absint( $ship_id ), SHIP_POST_TYPE );
		}
	}
}
