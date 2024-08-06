<?php
/**
 * Test suite for the Cabin class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Cabin;
use Quark\Softrip\Departure;
use WP_Post;
use WP_UnitTestCase;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Cabin
 */
class Test_Cabin extends WP_UnitTestCase {

	/**
	 * Test load.
	 *
	 * @covers \Quark\Softrip\Cabin::load()
	 *
	 * @return void
	 */
	public function test_load(): void {
		// Empty post ID - default values should be returned.
		$cabin = new Cabin();
		$cabin->load();
		$data = $cabin->get_data();
		$this->assertArrayHasKey( 'post', $data );
		$this->assertArrayHasKey( 'post_meta', $data );
		$this->assertArrayHasKey( 'post_taxonomies', $data );
		$this->assertArrayHasKey( 'post_thumbnail', $data );

		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Cabin Category',
				'post_content' => 'Cabin Category content',
				'post_status'  => 'publish',
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
		$this->assertTrue( $cabin_category_post instanceof WP_Post );

		// Load the cabin category.
		$cabin = new Cabin();
		$cabin->load( $cabin_category_post->ID );
		$data = $cabin->get_data();
		$this->assertNotEmpty( $data );
		$this->assertArrayHasKey( 'post', $data );
		$this->assertArrayHasKey( 'post_meta', $data );
		$this->assertArrayHasKey( 'post_taxonomies', $data );
		$this->assertArrayHasKey( 'post_thumbnail', $data );

		// Verify each field.
		$this->assertInstanceOf( WP_Post::class, $data['post'] );
		$this->assertEquals( $cabin_category_post->ID, $data['post']->ID );
		$this->assertIsArray( $data['post_meta'] );
		$this->assertArrayHasKey( 'test_meta', $data['post_meta'] );
		$this->assertEquals( 1, $data['post_meta']['test_meta'] );
	}

	/**
	 * Test set departure.
	 *
	 * @covers \Quark\Softrip\Cabin::set_departure()
	 *
	 * @return void
	 */
	public function test_set_departure(): void {
		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Cabin Category',
				'post_content' => 'Cabin Category content',
				'post_status'  => 'publish',
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
		$this->assertTrue( $cabin_category_post instanceof WP_Post );

		// Create departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Departure content',
				'post_status'  => 'publish',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Load the cabin category.
		$cabin = new Cabin();
		$cabin->load( $cabin_category_post->ID );
		$departure = $cabin->get_entry_data( 'departure' );
		$this->assertEmpty( $departure );
	}

	/**
	 * Test set.
	 *
	 * @covers \Quark\Softrip\Cabin::set()
	 *
	 * @return void
	 */
	public function test_set(): void {
		// Test 1: Default arguments.
		$cabin = new Cabin();
		$cabin->set();
		$data = $cabin->get_entry_data();
		$this->assertEmpty( $data );

		// Test 2: Providing empty array.
		$raw_cabin_data = [];
		$cabin          = new Cabin();
		$cabin->set( $raw_cabin_data );
		$data = $cabin->get_entry_data();
		$this->assertEmpty( $data );

		// Test 3: Providing valid raw data but without setting departure.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2025-05-19:CLT-OPL',
			'code'        => 'CLT-OPL',
			'name'        => 'Explorer Triple',
			'departureId' => 'UNQ-123:2025-05-19',
		];
		$cabin          = new Cabin();
		$cabin->set( $raw_cabin_data );
		$data = $cabin->get_entry_data();
		$this->assertEmpty( $data );

		// Test 4: Providing valid raw data and setting departure.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2025-05-19:CLT-OPL',
			'code'        => 'CLT-OPL',
			'name'        => 'Explorer Triple',
			'departureId' => 'UNQ-123:2025-05-19',
		];

		// Create departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Departure content',
				'post_status'  => 'publish',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta'           => true,
					'softrip_id' => 'UNQ-123:2025-05-19',
				],
			]
		);
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Create departure object.
		$departure = new Departure();
		$departure->load( $departure_post->ID );

		// Create cabin object, set departure, and set cabin data, but do not save.
		$cabin = new Cabin();
		$cabin->set_departure( $departure );
		$cabin->set( $raw_cabin_data );
		$data = $cabin->get_entry_data();
		$this->assertNotEmpty( $data );
		$this->assertIsArray( $data );
		$this->assertArrayHasKey( 'id', $data );
		$this->assertEmpty( $data['id'] );
		$this->assertArrayHasKey( 'title', $data );
		$this->assertEquals( $raw_cabin_data['id'], $data['title'] );
		$this->assertArrayHasKey( 'departure', $data );
		$this->assertEquals( $departure->get_id(), $data['departure'] );
		$this->assertArrayHasKey( 'cabin_category', $data );
		$this->assertEquals( $cabin->get_id(), $data['cabin_category'] );
		$this->assertArrayHasKey( 'availability_status', $data );
		$this->assertSame( 'C', $data['availability_status'] );
		$this->assertArrayHasKey( 'spaces_available', $data );
		$this->assertSame( '0', $data['spaces_available'] );
		$this->assertArrayNotHasKey( 'occupancies', $data );
		$this->assertArrayHasKey( 'cabin_category_id', $data );
		$this->assertEquals( $raw_cabin_data['code'], $data['cabin_category_id'] );

		// Now, save the cabin by creating cabin post.
		$cabin = new Cabin();
		$cabin->set_departure( $departure );
		$cabin->set( $raw_cabin_data, true );
		$data = $cabin->get_entry_data();
		$this->assertNotEmpty( $data );
		$this->assertIsArray( $data );
		$this->assertArrayHasKey( 'id', $data );
		$this->assertNotEmpty( $data['id'] ); // New WP ID should be generated.

		// Cleanup.
		wp_delete_post( $departure_post->ID, true );
		wp_delete_post( $data['id'], true );
		unset( $departure );
		unset( $cabin );
	}

	/**
	 * Test get lowest price.
	 *
	 * @covers \Quark\Softrip\Cabin::get_lowest_price()
	 *
	 * @return void
	 */
	public function test_get_lowest_price(): void {
		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Cabin Category',
				'post_content' => 'Cabin Category content',
				'post_status'  => 'publish',
				'post_type'    => CABIN_CATEGORY_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
		$this->assertTrue( $cabin_category_post instanceof WP_Post );

		// Create departure post.
		$departure_post = $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Departure',
				'post_content' => 'Departure content',
				'post_status'  => 'publish',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
		$this->assertTrue( $departure_post instanceof WP_Post );

		// Create departure object.
		$departure = new Departure( $departure_post->ID );

		// Load the cabin category.
		$cabin = new Cabin();
		$cabin->load( $cabin_category_post->ID );

		// Test 1: No prices set.
		$price = $cabin->get_lowest_price();
		$this->assertEquals( 0, $price );

		// Test 2: Set prices.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2025-05-19:CLT-OPL',
			'code'        => 'CLT-OPL',
			'name'        => 'Explorer Triple',
			'departureId' => 'UNQ-123:2025-05-19',
			'occupancies' => [
				[
					'id'                      => 'UNQ-123:2025-05-19:ULT-TPL:AAA',
					'name'                    => 'UNQ-123:2025-05-19:ULT-TPL:AAA',
					'mask'                    => 'AAA',
					'availabilityStatus'      => 'C',
					'availabilityDescription' => 'Unavailable',
					'spacesAvailable'         => 0,
					'seq'                     => '100',
					'prices'                  => [
						'USD' => [
							'currencyCode'   => 'USD',
							'pricePerPerson' => 6595,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 5936,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 5276,
								],
							],
						],
						'AUD' => [
							'currencyCode'   => 'AUD',
							'pricePerPerson' => 10400,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 9360,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 8320,
								],
							],
						],
						'CAD' => [
							'currencyCode'   => 'CAD',
							'pricePerPerson' => 9000,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 8100,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 7200,
								],
							],
						],
						'EUR' => [
							'currencyCode'   => 'EUR',
							'pricePerPerson' => 6200,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 5580,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 4960,
								],
							],
						],
						'GBP' => [
							'currencyCode'   => 'GBP',
							'pricePerPerson' => 5300,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 4770,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 4240,
								],
							],
						],
					],
				],
				[
					'id'                      => 'UNQ-123:2025-05-19:ULT-TPL:SAAA',
					'name'                    => 'UNQ-123:2025-05-19:ULT-TPL:SAAA',
					'mask'                    => 'SAAA',
					'availabilityStatus'      => 'O',
					'availabilityDescription' => 'Available',
					'spacesAvailable'         => 0,
					'seq'                     => '100',
					'prices'                  => [
						'USD' => [
							'currencyCode'   => 'USD',
							'pricePerPerson' => 4595,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 5936,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 5276,
								],
							],
						],
						'AUD' => [
							'currencyCode'   => 'AUD',
							'pricePerPerson' => 11492,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 9360,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 8320,
								],
							],
						],
						'CAD' => [
							'currencyCode'   => 'CAD',
							'pricePerPerson' => 9081,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 8100,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 7200,
								],
							],
						],
						'EUR' => [
							'currencyCode'   => 'EUR',
							'pricePerPerson' => 2938,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 5580,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 4960,
								],
							],
						],
						'GBP' => [
							'currencyCode'   => 'GBP',
							'pricePerPerson' => 5300,
							'promos'         => [
								'10PIF'   => [
									'promoPricePerPerson' => 4770,
								],
								'20PROMO' => [
									'promoPricePerPerson' => 4240,
								],
							],
						],
					],
				],
			],
		];

		// Expected lowest price - inferred from above departure data.
		$expected_lowest_price = [
			'USD' => 4595,
			'AUD' => 10400,
			'CAD' => 9000,
			'EUR' => 2938,
			'GBP' => 5300,
		];

		// Create cabin object, set departure, and set cabin data.
		$cabin = new Cabin();
		$cabin->set_departure( $departure );
		$cabin->set( $raw_cabin_data );

		// Test lowest price without currency.
		$price = $cabin->get_lowest_price();
		$this->assertEquals( $expected_lowest_price['USD'], $price );

		// Test lowest price with currency.
		foreach ( $expected_lowest_price as $currency => $expected_price ) {
			$price = $cabin->get_lowest_price( $currency );
			$this->assertEquals( $expected_price, $price );
		}
	}
}
