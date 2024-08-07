<?php
/**
 * Test suite for Occupancy class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Cabin;
use Quark\Softrip\Departure;
use Quark\Softrip\Occupancy;
use Quark\Softrip\Occupancy_Pricing;
use WP_UnitTestCase;

use const Quark\CabinCategories\POST_TYPE as CABIN_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Test Occupancy class.
 */
class Test_Occupancy extends WP_UnitTestCase {

	/**
	 * Test set.
	 *
	 * @covers \Quark\Softrip\Occupancy::set()
	 *
	 * @return void
	 */
	public function test_set(): void {
		// Test 1: Default arguments.
		$occupancy = new Occupancy();
		$occupancy->set();
		$data       = $occupancy->get_data();
		$entry_data = $occupancy->get_entry_data();
		$this->assertEmpty( $data );
		$this->assertEmpty( $entry_data );

		// Test 2: Providing empty array.
		$raw_occupancy_data = [];
		$occupancy          = new Occupancy();
		$occupancy->set( $raw_occupancy_data );
		$data       = $occupancy->get_data();
		$entry_data = $occupancy->get_entry_data();
		$this->assertEmpty( $data );
		$this->assertEmpty( $entry_data );

		// Test 3: Providing partial valid data, but without setting cabin parent.
		$raw_occupancy_data = [
			'id'                      => 'UNQ-123:2025-05-19:ULT-TPL:AAA',
			'name'                    => 'UNQ-123:2025-05-19:ULT-TPL:AAA',
			'mask'                    => 'AAA',
			'availabilityStatus'      => 'C',
			'availabilityDescription' => 'Unavailable',
			'spacesAvailable'         => 0,
			'seq'                     => '100',
		];
		$occupancy          = new Occupancy();
		$occupancy->set( $raw_occupancy_data );
		$data       = $occupancy->get_data();
		$entry_data = $occupancy->get_entry_data();
		$this->assertEmpty( $data );
		$this->assertEmpty( $entry_data );

		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create(
			[
				'post_type'   => CABIN_POST_TYPE,
				'post_title'  => 'Test Cabin Category',
				'post_status' => 'publish',
			]
		);
		$this->assertIsInt( $cabin_category_post );

		// Create a departure post.
		$departure_post = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_title'  => 'Test Departure',
				'post_status' => 'publish',
				'meta_input'  => [
					'test_meta'  => true,
					'softrip_id' => 'UNQ-123:2024-08-25',
				],
			]
		);
		$this->assertIsInt( $departure_post );

		// Create a departure object.
		$departure = new Departure( $departure_post );

		// Create a cabin object.
		$cabin = new Cabin( $cabin_category_post );
		$cabin->set_departure( $departure );

		// Set cabin raw data and save.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2024-08-25:OAD-TPL',
			'code'        => 'OAD-TPL',
			'name'        => 'Triple',
			'departureId' => 'UNQ-123:2024-08-25',
		];
		$cabin->set( $raw_cabin_data, true );

		// Create occupancy object.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $cabin );
		$occupancy->set( $raw_occupancy_data );
		$entry_data = $occupancy->get_entry_data();
		$this->assertNotEmpty( $entry_data );

		// Save occupancy.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $cabin );
		$occupancy->set( $raw_occupancy_data, true );
		$entry_data = $occupancy->get_entry_data();
		$this->assertIsArray( $entry_data );
		$this->assertNotEmpty( $entry_data );
		$this->assertArrayHasKey( 'id', $entry_data );
		$this->assertEquals( 'UNQ-123:2025-05-19:ULT-TPL:AAA', $entry_data['title'] );
		$this->assertArrayHasKey( 'occupancy_mask', $entry_data );
		$this->assertSame( 'AAA', $entry_data['occupancy_mask'] );
		$this->assertArrayHasKey( 'cabin_category', $entry_data );
		$this->assertEquals( $cabin->get_entry_data( 'id' ), $entry_data['cabin_category'] );

		// Clean up.
		wp_delete_post( $cabin_category_post, true );
		wp_delete_post( $departure_post, true );
		unset( $cabin, $departure, $occupancy );
	}

	/**
	 * Test get occupancy prices.
	 *
	 * @covers \Quark\Softrip\Occupancy::get_occupancy_prices()
	 *
	 * @return void
	 */
	public function test_get_occupancy_prices(): void {
		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create(
			[
				'post_type'   => CABIN_POST_TYPE,
				'post_title'  => 'Test Cabin Category',
				'post_status' => 'publish',
			]
		);
		$this->assertIsInt( $cabin_category_post );

		// Create a departure post.
		$departure_post = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_title'  => 'Test Departure',
				'post_status' => 'publish',
				'meta_input'  => [
					'test_meta'  => true,
					'softrip_id' => 'UNQ-123:2024-08-25',
				],
			]
		);
		$this->assertIsInt( $departure_post );

		// Create a departure object.
		$departure = new Departure( $departure_post );

		// Create a cabin object.
		$cabin = new Cabin( $cabin_category_post );
		$cabin->set_departure( $departure );

		// Set cabin raw data and save.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2024-08-25:OAD-TPL',
			'code'        => 'OAD-TPL',
			'name'        => 'Triple',
			'departureId' => 'UNQ-123:2024-08-25',
		];
		$cabin->set( $raw_cabin_data, true );

		// Create occupancy object.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $cabin );
		$prices = $occupancy->get_occupancy_prices();
		$this->assertIsArray( $prices );
		$this->assertEmpty( $prices );

		// Add prices - all currencies except CAD.
		$raw_occupancy_data['prices'] = [
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
		];

		// Get prices.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $cabin );
		$occupancy->set( $raw_occupancy_data, true );
		$prices = $occupancy->get_occupancy_prices();
		$this->assertIsArray( $prices );
		$this->assertNotEmpty( $prices );
		$this->assertArrayHasKey( 'USD', $prices );
		$this->assertArrayHasKey( 'AUD', $prices );
		$this->assertArrayNotHasKey( 'CAD', $prices );
		$this->assertArrayHasKey( 'EUR', $prices );
		$this->assertArrayHasKey( 'GBP', $prices );
		$this->assertInstanceOf( Occupancy_Pricing::class, $prices['USD'] );
		$this->assertInstanceOf( Occupancy_Pricing::class, $prices['AUD'] );
		$this->assertInstanceOf( Occupancy_Pricing::class, $prices['EUR'] );
		$this->assertInstanceOf( Occupancy_Pricing::class, $prices['GBP'] );

		// Clean up.
		wp_delete_post( $cabin_category_post, true );
		wp_delete_post( $departure_post, true );
		unset( $cabin, $departure, $occupancy );
	}

	/**
	 * Test get occupancy price by a code.
	 *
	 * @covers \Quark\Softrip\Occupancy::get_occupancy_price()
	 *
	 * @return void
	 */
	public function test_get_occupancy_price(): void {
		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create(
			[
				'post_type'   => CABIN_POST_TYPE,
				'post_title'  => 'Test Cabin Category',
				'post_status' => 'publish',
			]
		);
		$this->assertIsInt( $cabin_category_post );

		// Create a departure post.
		$departure_post = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_title'  => 'Test Departure',
				'post_status' => 'publish',
				'meta_input'  => [
					'test_meta'  => true,
					'softrip_id' => 'UNQ-123:2024-08-25',
				],
			]
		);
		$this->assertIsInt( $departure_post );

		// Create a departure object.
		$departure = new Departure( $departure_post );

		// Create a cabin object.
		$cabin = new Cabin( $cabin_category_post );
		$cabin->set_departure( $departure );

		// Set cabin raw data and save.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2024-08-25:OAD-TPL',
			'code'        => 'OAD-TPL',
			'name'        => 'Triple',
			'departureId' => 'UNQ-123:2024-08-25',
		];
		$cabin->set( $raw_cabin_data, true );

		// Add prices - all currencies except CAD.
		$raw_occupancy_data['prices'] = [
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
		];

		// Get occupancy price.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $cabin );
		$occupancy->set( $raw_occupancy_data, true );
		$price = $occupancy->get_occupancy_price( 'USD' );
		$this->assertInstanceOf( Occupancy_Pricing::class, $price );
		$entry_data = $price->get_entry_data();
		$this->assertIsArray( $entry_data );
		$this->assertNotEmpty( $entry_data );
		$this->assertArrayHasKey( 'currency_code', $entry_data );
		$this->assertEquals( 'USD', $entry_data['currency_code'] );
		$this->assertArrayHasKey( 'price_per_person', $entry_data );
		$this->assertEquals( 6595, $entry_data['price_per_person'] );
		$this->assertArrayHasKey( 'total_price_per_person', $entry_data );
		$this->assertEquals( 6595, $entry_data['total_price_per_person'] );

		// @todo Include tests for promos fields - promotion_code and promo_price_per_person.
		// Cleanup.
		wp_delete_post( $cabin_category_post, true );
		wp_delete_post( $departure_post, true );
		unset( $cabin, $departure, $occupancy );
	}

	/**
	 * Test get price per person.
	 *
	 * @covers \Quark\Softrip\Occupancy::get_price_per_person()
	 *
	 * @return void
	 */
	public function test_get_price_per_person(): void {
		// Without any cabin post.
		$occupancy = new Occupancy();
		$price     = $occupancy->get_price_per_person();
		$this->assertEquals( 0, $price );

		// Create cabin category post.
		$cabin_category_post = $this->factory()->post->create(
			[
				'post_type'   => CABIN_POST_TYPE,
				'post_title'  => 'Test Cabin Category',
				'post_status' => 'publish',
			]
		);
		$this->assertIsInt( $cabin_category_post );

		// Create a departure post.
		$departure_post = $this->factory()->post->create(
			[
				'post_type'   => DEPARTURE_POST_TYPE,
				'post_title'  => 'Test Departure',
				'post_status' => 'publish',
				'meta_input'  => [
					'test_meta'  => true,
					'softrip_id' => 'UNQ-123:2024-08-25',
				],
			]
		);
		$this->assertIsInt( $departure_post );

		// Create a departure object.
		$departure = new Departure( $departure_post );

		// Create a cabin object.
		$cabin = new Cabin( $cabin_category_post );
		$cabin->set_departure( $departure );

		// Set cabin raw data and save.
		$raw_cabin_data = [
			'id'          => 'UNQ-123:2024-08-25:OAD-TPL',
			'code'        => 'OAD-TPL',
			'name'        => 'Triple',
			'departureId' => 'UNQ-123:2024-08-25',
		];
		$cabin->set( $raw_cabin_data, true );

		// Add prices - all currencies except CAD.
		$raw_occupancy_data['prices'] = [
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
		];

		// Get occupancy price.
		$occupancy = new Occupancy();
		$occupancy->set_cabin( $cabin );
		$occupancy->set( $raw_occupancy_data, true );

		// Expected lowest price - inferred from above raw data.
		$expected_lowest_price = [
			'USD' => 6595,
			'AUD' => 10400,
			'CAD' => 9000,
			'EUR' => 6200,
			'GBP' => 5300,
		];

		// No argument provided - USD price.
		$price = $occupancy->get_price_per_person();
		$this->assertEquals( $expected_lowest_price['USD'], $price );

		// Invalid currency code.
		$price = $occupancy->get_price_per_person( 'XYZ' );
		$this->assertEquals( 0, $price );

		// For each currency.
		foreach ( $expected_lowest_price as $currency => $expected_price ) {
			$price = $occupancy->get_price_per_person( $currency );
			$this->assertEquals( $expected_price, $price );
		}
	}
}
