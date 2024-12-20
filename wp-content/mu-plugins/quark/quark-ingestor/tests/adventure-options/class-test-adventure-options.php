<?php
/**
 * Test suite for Adventure Options.
 *
 * @package quark-ingestor
 */

namespace Quark\Tests\Ingestor\Adventure_Options;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Softrip\AdventureOptions\update_adventure_options;
use function Quark\Ingestor\AdventureOptions\get_adventure_option_category_data_from_meta;
use function Quark\Ingestor\AdventureOptions\get_included_adventure_options_data;
use function Quark\Ingestor\AdventureOptions\get_paid_adventure_options_data;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTION_POST_TYPE;
use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\USD_CURRENCY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;

/**
 * Class Test_Adventure_Options
 */
class Test_Adventure_Options extends Softrip_TestCase {
	/**
	 * Test get included adventure options data.
	 *
	 * @covers \Quark\Ingestor\get_included_adventure_options_data
	 *
	 * @return void
	 */
	public function test_get_included_adventure_options_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_included_adventure_options_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_included_adventure_options_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_included_adventure_options_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Create adventure option post.
		$adventure_option_post_id1 = $this->factory()->post->create(
			[
				'post_type' => ADVENTURE_OPTION_POST_TYPE,
			]
		);
		$this->assertIsInt( $adventure_option_post_id1 );

		// Test with expedition that has no related adventure options.
		$actual   = get_included_adventure_options_data( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Update related adventure options.
		update_post_meta( $expedition_post_id, 'included_activities', [ $adventure_option_post_id1 ] );

		// Flush the cache.
		wp_cache_flush();

		// Test with expedition that has related adventure options but no assigned category.
		$actual   = get_included_adventure_options_data( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Create adventure option category term.
		$adventure_option_category_term_id = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id );
		$adventure_option_category = get_term( $adventure_option_category_term_id, ADVENTURE_OPTION_CATEGORY, ARRAY_A );
		$this->assertIsArray( $adventure_option_category );
		$this->assertArrayHasKey( 'name', $adventure_option_category );
		$adventure_option_category_name = $adventure_option_category['name'];

		// Assign this category to the adventure option post.
		wp_set_post_terms( $adventure_option_post_id1, [ $adventure_option_category_term_id ], ADVENTURE_OPTION_CATEGORY );

		// Test without departure id.
		$actual   = get_included_adventure_options_data( $expedition_post_id );
		$expected = [];
		$this->assertEquals( $expected, $actual );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_id' => 'UNQ-123:2025-01-01',
					'ship_code'  => 'POQ',
				],
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Flush the cache.
		wp_cache_flush();

		// Test with expedition that has related adventure options and assigned category.
		$actual   = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$expected = [
			[
				'id'        => $adventure_option_category_term_id,
				'name'      => $adventure_option_category_name,
				'icon'      => '',
				'optionIds' => '',
			],
		];
		$this->assertEquals( $expected, $actual );

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );

		// Update icon on adventure option category.
		update_term_meta( $adventure_option_category_term_id, 'icon', $media_post_id1 );

		// Test with expedition that has related adventure options and assigned category with icon.
		$actual   = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$expected = [
			[
				'id'        => $adventure_option_category_term_id,
				'name'      => $adventure_option_category_name,
				'icon'      => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
				'optionIds' => '',
			],
		];
		$this->assertEquals( $expected, $actual );

		// Add option ids to the category.
		update_term_meta( $adventure_option_category_term_id, 'softrip_0_id', 'ABC' );
		update_term_meta( $adventure_option_category_term_id, 'softrip_1_id', 'DEF' );

		// Test with expedition that has related adventure options and assigned category with icon and option ids.
		$actual   = get_included_adventure_options_data( $expedition_post_id, $departure_post_id );
		$expected = [
			[
				'id'        => $adventure_option_category_term_id,
				'name'      => $adventure_option_category_name,
				'icon'      => wp_get_attachment_image_url( $media_post_id1, 'thumbnail' ),
				'optionIds' => 'ABC, DEF',
			],
		];
		$this->assertEquals( $expected, $actual );

		// Delete media files.
		wp_delete_attachment( $media_post_id1, true );
	}

	/**
	 * Test get adventure option category data from meta.
	 *
	 * @covers \Quark\Ingestor\get_adventure_option_category_data_from_meta
	 *
	 * @return void
	 */
	public function test_get_adventure_option_category_data_from_meta(): void {
		// Default expected.
		$default_expected = [
			'icon'      => '',
			'optionIds' => [],
			'images'    => [],
		];

		// Test with no arguments.
		$actual = get_adventure_option_category_data_from_meta();
		$this->assertEquals( $default_expected, $actual );

		// Test with default arg.
		$actual = get_adventure_option_category_data_from_meta( 0 );
		$this->assertEquals( $default_expected, $actual );

		// Test with invalid term id.
		$actual = get_adventure_option_category_data_from_meta( 999999 );
		$this->assertEquals( $default_expected, $actual );

		// Create adventure option category term.
		$adventure_option_category_term_id = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id );

		// Test with term id that has no meta.
		$actual = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$this->assertEquals( $default_expected, $actual );

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );

		// Update icon on adventure option category.
		update_term_meta( $adventure_option_category_term_id, 'icon', $media_post_id1 );

		// Test with term id that has icon meta.
		$actual   = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$expected = [
			'icon'      => wp_get_attachment_image_url( $media_post_id1, 'full' ),
			'optionIds' => [],
			'images'    => [],
		];
		$this->assertEquals( $expected, $actual );

		// Add option ids to the category.
		update_term_meta( $adventure_option_category_term_id, 'softrip_0_id', 'ABC' );
		update_term_meta( $adventure_option_category_term_id, 'softrip_1_id', 'DEF' );

		// Test with term id that has icon and option ids meta.
		$actual   = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$expected = [
			'icon'      => wp_get_attachment_image_url( $media_post_id1, 'full' ),
			'optionIds' => [ 'ABC', 'DEF' ],
			'images'    => [],
		];
		$this->assertEquals( $expected, $actual );

		// Create attachment.
		$media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id2 );
		$alt_text = get_post_meta( $media_post_id2, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text ) ) {
			$alt_text = get_post_field( 'post_title', $media_post_id2 );
		}

		// Update images on adventure option category.
		update_term_meta( $adventure_option_category_term_id, 'image', $media_post_id2 );

		// Test with term id that has icon, option ids and image meta.
		$actual   = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );
		$expected = [
			'icon'      => wp_get_attachment_image_url( $media_post_id1, 'full' ),
			'optionIds' => [ 'ABC', 'DEF' ],
			'images'    => [
				[
					'id'           => $media_post_id2,
					'fullSizeUrl'  => wp_get_attachment_url( $media_post_id2 ),
					'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
					'alt'          => $alt_text,
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test get paid adventure options data.
	 *
	 * @covers \Quark\Ingestor\get_paid_adventure_options_data
	 *
	 * @return void
	 */
	public function test_get_paid_adventure_options_data(): void {
		// Test with no arguments.
		$expected = [];
		$actual   = get_paid_adventure_options_data();
		$this->assertEquals( $expected, $actual );

		// Test with default arg.
		$expected = [];
		$actual   = get_paid_adventure_options_data( 0 );
		$this->assertEquals( $expected, $actual );

		// Test with invalid post id.
		$expected = [];
		$actual   = get_paid_adventure_options_data( 999999 );
		$this->assertEquals( $expected, $actual );

		// Create adventure option category terms.
		$adventure_option_category_term_id1 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id1 );
		$adventure_option_category_term1 = get_term( $adventure_option_category_term_id1, ADVENTURE_OPTION_CATEGORY, ARRAY_A );
		$this->assertIsArray( $adventure_option_category_term1 );
		$this->assertArrayHasKey( 'name', $adventure_option_category_term1 );
		$adventure_option_category_name1 = $adventure_option_category_term1['name'];

		// Create second adventure option category term.
		$adventure_option_category_term_id2 = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_category_term_id2 );
		$adventure_option_category_term2 = get_term( $adventure_option_category_term_id2, ADVENTURE_OPTION_CATEGORY, ARRAY_A );
		$this->assertIsArray( $adventure_option_category_term2 );
		$this->assertArrayHasKey( 'name', $adventure_option_category_term2 );
		$adventure_option_category_name2 = $adventure_option_category_term2['name'];

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id2 );
		$media_post_id3 = $this->factory()->attachment->create_upload_object( __DIR__ . '/data/test.jpg' );
		$this->assertIsInt( $media_post_id3 );

		// Update alt text on media 2.
		update_post_meta( $media_post_id2, '_wp_attachment_image_alt', 'Alt text 2' );

		// Alt text.
		$alt_text1 = get_post_meta( $media_post_id1, '_wp_attachment_image_alt', true );
		$alt_text2 = get_post_meta( $media_post_id2, '_wp_attachment_image_alt', true );
		$alt_text3 = get_post_meta( $media_post_id3, '_wp_attachment_image_alt', true );

		// If empty alt, see title.
		if ( empty( $alt_text1 ) ) {
			$alt_text1 = get_post_field( 'post_title', $media_post_id1 );
		}

		// If empty alt, see title.
		if ( empty( $alt_text2 ) ) {
			$alt_text2 = get_post_field( 'post_title', $media_post_id2 );
		}

		// If empty alt, see title.
		if ( empty( $alt_text3 ) ) {
			$alt_text3 = get_post_field( 'post_title', $media_post_id3 );
		}

		// Update icon on adventure option category.
		update_term_meta( $adventure_option_category_term_id1, 'icon', $media_post_id1 );
		update_term_meta( $adventure_option_category_term_id2, 'icon', $media_post_id3 );

		// Update images on adventure option category.
		update_term_meta( $adventure_option_category_term_id1, 'image', $media_post_id2 );
		update_term_meta( $adventure_option_category_term_id2, 'image', $media_post_id3 );

		// Add softrip ids to the categories.
		update_term_meta( $adventure_option_category_term_id1, 'softrip_0_id', 'ABC' );
		update_term_meta( $adventure_option_category_term_id1, 'softrip_1_id', 'DEF' );
		update_term_meta( $adventure_option_category_term_id2, 'softrip_0_id', 'GHI' );
		update_term_meta( $adventure_option_category_term_id2, 'softrip_1_id', 'JKL' );

		// Create departure post.
		$departure_post_id = $this->factory()->post->create( [ 'post_type' => DEPARTURE_POST_TYPE ] );
		$this->assertIsInt( $departure_post_id );

		// Add softrip_package_code to the departure.
		update_post_meta( $departure_post_id, 'softrip_package_code', 'UNQ-123-11D2025' );

		// Create adventure option rows.
		$raw_adventure_options_data = [
			[
				'id'              => 'UNQ-123-11D2025:2025-08-26:KAYAK',
				'spacesAvailable' => 10,
				'serviceIds'      => [ 'ABC', 'DEF' ],
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 1000,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 8176,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 1000,
					],
				],
			],
			[
				'id'              => 'UNQ-123-11D2025:2025-08-26:HIKE',
				'spacesAvailable' => 0,
				'serviceIds'      => [ 'GHI', 'JKL' ],
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 234,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 235456,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 29332,
					],
				],
			],
		];

		// Insert adventure options.
		$is_success = update_adventure_options( $raw_adventure_options_data, $departure_post_id );
		$this->assertTrue( $is_success );

		// Flush the cache.
		wp_cache_flush();

		// Test with departure that has softrip_package_code.
		$actual   = get_paid_adventure_options_data( $departure_post_id );
		$expected = [
			[
				'id'              => $adventure_option_category_term_id1,
				'name'            => get_raw_text_from_html( $adventure_option_category_name1 ),
				'icon'            => wp_get_attachment_image_url( $media_post_id1, 'full' ),
				'optionIds'       => 'ABC, DEF',
				'images'          => [
					[
						'id'           => $media_post_id2,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id2 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id2, 'thumbnail' ),
						'alt'          => $alt_text2,
					],
				],
				'spacesAvailable' => 10,
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 1000,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 8176,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 1000,
					],
					GBP_CURRENCY => [
						'currencyCode'   => GBP_CURRENCY,
						'pricePerPerson' => 0,
					],
					EUR_CURRENCY => [
						'currencyCode'   => EUR_CURRENCY,
						'pricePerPerson' => 0,
					],
				],
			],
			[
				'id'              => $adventure_option_category_term_id2,
				'name'            => get_raw_text_from_html( $adventure_option_category_name2 ),
				'icon'            => wp_get_attachment_image_url( $media_post_id3, 'full' ),
				'optionIds'       => 'GHI, JKL',
				'images'          => [
					[
						'id'           => $media_post_id3,
						'fullSizeUrl'  => wp_get_attachment_url( $media_post_id3 ),
						'thumbnailUrl' => wp_get_attachment_image_url( $media_post_id3, 'thumbnail' ),
						'alt'          => $alt_text3,
					],
				],
				'spacesAvailable' => 0,
				'price'           => [
					AUD_CURRENCY => [
						'currencyCode'   => AUD_CURRENCY,
						'pricePerPerson' => 234,
					],
					USD_CURRENCY => [
						'currencyCode'   => USD_CURRENCY,
						'pricePerPerson' => 235456,
					],
					CAD_CURRENCY => [
						'currencyCode'   => CAD_CURRENCY,
						'pricePerPerson' => 29332,
					],
					GBP_CURRENCY => [
						'currencyCode'   => GBP_CURRENCY,
						'pricePerPerson' => 0,
					],
					EUR_CURRENCY => [
						'currencyCode'   => EUR_CURRENCY,
						'pricePerPerson' => 0,
					],
				],
			],
		];
		$this->assertEquals( $expected, $actual );
	}
}
