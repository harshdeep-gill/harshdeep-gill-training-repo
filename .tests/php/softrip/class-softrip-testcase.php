<?php
/**
 * Softrip Test case class.
 *
 * @package quark
 */

namespace Quark\Tests\Softrip;

use DateInterval;
use DateTime;
use WP_UnitTestCase;
use WP_Term;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
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
	 * Expedition posts.
	 *
	 * @var array<int|WP_Error>
	 */
	protected static array $expedition_ids = [];

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public static function set_up_before_class(): void {
		// Run parent and include setup.
		parent::set_up_before_class();

		// Create test expedition posts.
		self::$expedition_ids = self::factory()->post->create_many(
			5,
			[
				'post_type' => EXPEDITION_POST_TYPE,
			]
		);

		// Create a test itinerary post.
		self::$itinerary_ids = self::factory()->post->create_many(
			20,
			[
				'post_type' => ITINERARY_POST_TYPE,
			]
		);

		// Write above code in loop.
		$softrip_package_codes = [
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

		// Loop through the itineraries and set softrip package code meta.
		foreach ( self::$itinerary_ids as $index => $itinerary_id ) {
			update_post_meta( absint( $itinerary_id ), 'softrip_package_code', $softrip_package_codes[ $index ] );
			update_post_meta( absint( $itinerary_id ), 'related_expedition', self::$expedition_ids[ $index % 5 ] );
			wp_cache_delete( ITINERARY_POST_TYPE . '_' . absint( $itinerary_id ), ITINERARY_POST_TYPE );
		}

		// List the Cabin softrip codes.
		$softrip_cabin_ids = [
			'OEX-SGL',
			'OEX-DBL',
			'ULT-SGL',
			'ULT-DBL',
			'OEX-FWD',
			'OEX-JST',
			'OEX-SVS',
		];

		// Create Cabin Category posts.
		$cabin_ids = self::factory()->post->create_many(
			count( $softrip_cabin_ids ),
			[
				'post_type' => CABIN_CATEGORY_POST_TYPE,
			]
		);

		// Loop through the cabins and set meta.
		foreach ( $cabin_ids as $index => $cabin_id ) {
			update_post_meta( absint( $cabin_id ), 'cabin_name', 'cabin_name - ' . $softrip_cabin_ids[ $index ] );
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
			update_post_meta( absint( $ship_id ), 'ship_code', $softrip_ship_ids[ $index ] );
			wp_cache_delete( SHIP_POST_TYPE . '_' . absint( $ship_id ), SHIP_POST_TYPE );
		}

		// Create term of Adventure Options.
		$adventure_option_term_1 = self::factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option-1',
			]
		);

		// Create term of Adventure Options.
		$adventure_option_term_2 = self::factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option-2',
			]
		);

		// Create term of Adventure Options.
		$adventure_option_term_3 = self::factory()->term->create_and_get(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'adventure_option-3',
			]
		);

		// Assert term is created.
		self::assertTrue( $adventure_option_term_1 instanceof WP_Term );
		self::assertTrue( $adventure_option_term_2 instanceof WP_Term );
		self::assertTrue( $adventure_option_term_3 instanceof WP_Term );

		// Set term meta.
		update_term_meta( $adventure_option_term_1->term_id, 'softrip_0_id', 'KAYAK' );
		update_term_meta( $adventure_option_term_1->term_id, 'icon', '234' );
		update_term_meta( $adventure_option_term_2->term_id, 'softrip_0_id', 'KAYEXP' );
		update_term_meta( $adventure_option_term_2->term_id, 'softrip_1_id', 'KAYEXP2' );
		update_term_meta( $adventure_option_term_2->term_id, 'icon', '987' );
	}

	/**
	 * Tear down after each test.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// Run parent and include tear down.
		parent::tear_down();

		// Truncate the Softrip custom tables.
		truncate_softrip_db_tables();

		// Flush the cache.
		wp_cache_flush();
	}

	/**
	 * Get Date interval from a date string.
	 *
	 * @param string $datetime Date string.
	 *
	 * @return DateInterval
	 */
	protected function get_date_interval( string $datetime = '' ): DateInterval {
		// Create a date interval from the string.
		$interval_date = date_interval_create_from_date_string( $datetime );
		$this->assertTrue( $interval_date instanceof DateInterval );

		// Return date interval.
		return $interval_date;
	}

	/**
	 * Get current date.
	 *
	 * @return DateTime
	 */
	protected function get_current_date(): DateTime {
		// Create a date object.
		$current_date = date_create();
		$this->assertTrue( $current_date instanceof DateTime );

		// Return the date object.
		return $current_date;
	}
}
