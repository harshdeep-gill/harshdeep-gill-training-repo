<?php
/**
 * Test suite for filters namespace functions.
 *
 * @package quark-search
 */

namespace Quark\Search\Tests\Filters;

use WP_UnitTestCase;

use function Quark\Search\Filters\get_adventure_options_filter;
use function Quark\Search\Filters\get_duration_filter;
use function Quark\Search\Filters\get_expedition_filter;
use function Quark\Search\Filters\get_month_filter;
use function Quark\Search\Filters\get_region_and_season_filter;
use function Quark\Search\Filters\get_ship_filter;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;

/**
 * Class Test_Filters
 */
class Test_Filters extends WP_UnitTestCase {

	/**
	 * Test get region season filter.
	 *
	 * @covers \Quark\Search\Filters\get_region_season_filter
	 *
	 * @return void
	 */
	public function test_get_region_season_filter(): void {
		// Expected default.
		$expected_default = [];

		// Test with empty args.
		$actual = get_region_and_season_filter();
		$this->assertEquals( $expected_default, $actual );

		// Test with empty array.
		$actual = get_region_and_season_filter( [] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_region_and_season_filter( [ 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_region_and_season_filter( [ 'region' => 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Create a destination term.
		$term_id1 = $this->factory()->term->create(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'name'     => 'Antarctica',
			]
		);
		$this->assertIsInt( $term_id1 );
		$term_id2 = $this->factory()->term->create(
			[
				'taxonomy' => DESTINATION_TAXONOMY,
				'name'     => 'Arctic',
			]
		);
		$this->assertIsInt( $term_id2 );

		// Get term1.
		$term1 = get_term( $term_id1, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term1 );
		$term2 = get_term( $term_id2, DESTINATION_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term2 );

		// Add term meta.
		update_term_meta( $term_id1, 'softrip_id', 'ANT' );
		update_term_meta( $term_id2, 'softrip_id', 'ARC' );

		// Test with region only.
		$actual = get_region_and_season_filter(
			[
				'ANT' => 1,
				'ARC' => 2,
			]
		);
		$this->assertEquals( $expected_default, $actual );

		// Add season term.
		$term_id3 = $this->factory()->term->create(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => '2025',
			]
		);
		$this->assertIsInt( $term_id3 );
		$term_id4 = $this->factory()->term->create(
			[
				'taxonomy' => SEASON_TAXONOMY,
				'name'     => '2026',
			]
		);
		$this->assertIsInt( $term_id4 );

		// Get term3.
		$term3 = get_term( $term_id3, SEASON_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term3 );
		$term4 = get_term( $term_id4, SEASON_TAXONOMY, ARRAY_A );
		$this->assertIsArray( $term4 );

		// Test with region and season.
		$actual = get_region_and_season_filter(
			[
				'ANT-2025' => 1,
				'ARC-2026' => 2,
				'ARC-2025' => 3,
				'ANT-2026' => 4,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => $term1['name'] . ' ' . $term3['name'],
					'value' => 'ANT-2025',
					'count' => 1,
				],
				[
					'label' => $term2['name'] . ' ' . $term4['name'],
					'value' => 'ARC-2026',
					'count' => 2,
				],
				[
					'label' => $term2['name'] . ' ' . $term3['name'],
					'value' => 'ARC-2025',
					'count' => 3,
				],
				[
					'label' => $term1['name'] . ' ' . $term4['name'],
					'value' => 'ANT-2026',
					'count' => 4,
				],
			],
			$actual
		);
	}

	/**
	 * Test get expedition filter.
	 *
	 * @covers \Quark\Search\Filters\get_expedition_filter
	 *
	 * @return void
	 */
	public function test_get_expedition_filter(): void {
		// Expected default.
		$expected_default = [];

		// Test with empty args.
		$actual = get_expedition_filter();
		$this->assertEquals( $expected_default, $actual );

		// Test with empty array.
		$actual = get_expedition_filter( [] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_expedition_filter( [ 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_expedition_filter( [ 'expedition' => 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Create expedition post.
		$post_id1 = $this->factory()->post->create(
			[
				'post_type'  => EXPEDITION_POST_TYPE,
				'post_title' => 'Explorer Antarctica 2025',
			]
		);
		$this->assertIsInt( $post_id1 );
		$post_id2 = $this->factory()->post->create(
			[
				'post_type'  => EXPEDITION_POST_TYPE,
				'post_title' => 'Explorer Arctic 2026',
			]
		);
		$this->assertIsInt( $post_id2 );

		// Test with valid.
		$actual = get_expedition_filter(
			[
				$post_id1 => 1,
				$post_id2 => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => 'Explorer Antarctica 2025',
					'value' => $post_id1,
					'count' => 1,
				],
				[
					'label' => 'Explorer Arctic 2026',
					'value' => $post_id2,
					'count' => 2,
				],
			],
			$actual
		);
	}

	/**
	 * Test get ship filter.
	 *
	 * @covers \Quark\Search\Filters\get_ship_filter
	 *
	 * @return void
	 */
	public function test_get_ship_filter(): void {
		// Expected default.
		$expected_default = [];

		// Test with empty args.
		$actual = get_ship_filter();
		$this->assertEquals( $expected_default, $actual );

		// Test with empty array.
		$actual = get_ship_filter( [] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_ship_filter( [ 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_ship_filter( [ 'ship' => 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Create ship post.
		$post_id1 = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'post_title' => 'Ultramarine',
			]
		);
		$this->assertIsInt( $post_id1 );
		$post_id2 = $this->factory()->post->create(
			[
				'post_type'  => SHIP_POST_TYPE,
				'post_title' => 'World Explorer',
			]
		);
		$this->assertIsInt( $post_id2 );

		// Test with valid.
		$actual = get_ship_filter(
			[
				$post_id1 => 1,
				$post_id2 => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => 'Ultramarine',
					'value' => $post_id1,
					'count' => 1,
				],
				[
					'label' => 'World Explorer',
					'value' => $post_id2,
					'count' => 2,
				],
			],
			$actual
		);
	}

	/**
	 * Test get adventure options filter.
	 *
	 * @covers \Quark\Search\Filters\get_adventure_options_filter
	 *
	 * @return void
	 */
	public function test_get_adventure_options_filter(): void {
		// Expected default.
		$expected_default = [];

		// Test with empty args.
		$actual = get_adventure_options_filter();
		$this->assertEquals( $expected_default, $actual );

		// Test with empty array.
		$actual = get_adventure_options_filter( [] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_adventure_options_filter( [ 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_adventure_options_filter( [ 'adventure' => 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Create adventure option terms.
		$term_id1 = $this->factory()->term->create(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'Kayaking',
			]
		);
		$this->assertIsInt( $term_id1 );
		$term_id2 = $this->factory()->term->create(
			[
				'taxonomy' => ADVENTURE_OPTION_CATEGORY,
				'name'     => 'Hiking',
			]
		);
		$this->assertIsInt( $term_id2 );

		// Test with valid.
		$actual = get_adventure_options_filter(
			[
				$term_id1 => 1,
				$term_id2 => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => 'Kayaking',
					'value' => $term_id1,
					'count' => 1,
				],
				[
					'label' => 'Hiking',
					'value' => $term_id2,
					'count' => 2,
				],
			],
			$actual
		);
	}

	/**
	 * Test get month filter.
	 *
	 * @covers \Quark\Search\Filters\get_month_filter
	 *
	 * @return void
	 */
	public function test_get_month_filter(): void {
		// Expected default.
		$expected_default = [];

		// Test with empty args.
		$actual = get_month_filter();
		$this->assertEquals( $expected_default, $actual );

		// Test with empty array.
		$actual = get_month_filter( [] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_month_filter( [ 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_month_filter( [ 'month' => 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid time.
		$actual = get_month_filter(
			[
				'2025' => 1,
				'2026' => 2,
			]
		);
		$this->assertEquals( $expected_default, $actual );

		// Test with valid time.
		$actual = get_month_filter(
			[
				'2025-01' => 1,
				'2025-02' => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => 'January 2025',
					'value' => '01-2025',
					'count' => 1,
				],
				[
					'label' => 'February 2025',
					'value' => '02-2025',
					'count' => 2,
				],
			],
			$actual
		);

		// Test with different time format.
		$actual = get_month_filter(
			[
				'2025-01-01' => 1,
				'2025-02-01' => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => 'January 2025',
					'value' => '01-2025',
					'count' => 1,
				],
				[
					'label' => 'February 2025',
					'value' => '02-2025',
					'count' => 2,
				],
			],
			$actual
		);

		// Test with ISO time format.
		$actual = get_month_filter(
			[
				'2025-01-01T00:00:00' => 1,
				'2025-02-01T00:00:00' => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => 'January 2025',
					'value' => '01-2025',
					'count' => 1,
				],
				[
					'label' => 'February 2025',
					'value' => '02-2025',
					'count' => 2,
				],
			],
			$actual
		);
	}

	/**
	 * Test get duration filter.
	 *
	 * @covers \Quark\Search\Filters\get_duration_filter
	 *
	 * @return void
	 */
	public function test_get_duration_filter(): void {
		// Expected default.
		$expected_default = [];

		// Test with empty args.
		$actual = get_duration_filter();
		$this->assertEquals( $expected_default, $actual );

		// Test with empty array.
		$actual = get_duration_filter( [] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_duration_filter( [ 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with invalid args.
		$actual = get_duration_filter( [ 'duration' => 2 ] );
		$this->assertEquals( $expected_default, $actual );

		// Test with valid.
		$actual = get_duration_filter(
			[
				'1' => 1,
				'2' => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => '1-7 Days',
					'value' => '1-7',
					'count' => 1,
				],
				[
					'label' => '2-8 Days',
					'value' => '2-8',
					'count' => 2,
				],
			],
			$actual
		);

		// Test with negative.
		$actual = get_duration_filter(
			[
				'-1' => 1,
				'-2' => 2,
			]
		);
		$this->assertEquals(
			[
				[
					'label' => '1-7 Days',
					'value' => '1-7',
					'count' => 1,
				],
				[
					'label' => '2-8 Days',
					'value' => '2-8',
					'count' => 2,
				],
			],
			$actual
		);
	}
}
