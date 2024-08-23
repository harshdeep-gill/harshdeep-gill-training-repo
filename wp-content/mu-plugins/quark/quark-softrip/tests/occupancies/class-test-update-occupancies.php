<?php
/**
 * Test suite for update of occupancies.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\Occupancies;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Softrip\Occupancies\get_occupancies_by_cabin_category_and_departure;
use function Quark\Softrip\Occupancies\get_occupancies_by_departure;
use function Quark\Softrip\Occupancies\get_occupancy_data_by_softrip_id;
use function Quark\Softrip\Occupancies\update_occupancies;

use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORIES_POST_TYPE;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Update_Occupancies
 */
class Test_Update_Occupancies extends Softrip_TestCase {
	/**
	 * Test update occupancies.
	 *
	 * @covers \Quark\Softrip\Occupancies\update_occupancies
	 *
	 * @return void
	 */
	public function test_update_occupancies(): void {
		// No arguments.
		$actual = update_occupancies();
		$this->assertFalse( $actual );

		// Default raw data.
		$actual = update_occupancies( [] );
		$this->assertFalse( $actual );

		// Default raw data and departure post id.
		$actual = update_occupancies( [], 0 );
		$this->assertFalse( $actual );

		// There should be no occupancy for non-existing departure.
		$occupancies = get_occupancies_by_departure( 1, true );
		$this->assertEmpty( $occupancies );

		// Create first departure post.
		$departure_id1 = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => 'PKG1',
				],
			]
		);
		$this->assertIsInt( $departure_id1 );

		// Create second departure post.
		$departure_id2 = $this->factory()->post->create(
			[
				'post_type'  => DEPARTURE_POST_TYPE,
				'meta_input' => [
					'softrip_package_code' => 'PKG2',
				],
			]
		);
		$this->assertIsInt( $departure_id2 );

		// First cabin raw data with empty array of occupancies.
		$raw_cabins_data = [
			[],
		];
		$actual          = update_occupancies( $raw_cabins_data, $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Set partial raw data without required field empty.
		$raw_cabins_data = [
			[
				'code' => 'CAB1',
			],
		];
		$actual          = update_occupancies( $raw_cabins_data, $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Set partial raw data without occupancies array.
		$raw_cabins_data = [
			[
				'code'        => 'CAB1',
				'occupancies' => [],
			],
		];
		$actual          = update_occupancies( $raw_cabins_data, $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Occupancy is non-array.
		$raw_cabins_data = [
			[
				'code'        => 'CAB1',
				'occupancies' => 'OCC1',
			],
		];
		$actual          = update_occupancies( $raw_cabins_data, $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// First cabin data - with invalid CAB1 cabin category code. There doesn't exist any cabin with this code.
		$raw_cabin_data1 = [
			'code'        => 'CAB1',
			'name'        => 'Explorer Suite',
			'occupancies' => [
				[],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Create first cabin post.
		$cabin_category_post_id1 = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORIES_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'CAB1',
				],
			]
		);
		$this->assertIsInt( $cabin_category_post_id1 );

		// Try again with raw data. This time, cabin post exists but raw data has empty occupancies.
		$raw_cabin_data1 = [
			'code'        => 'CAB1',
			'name'        => 'Explorer Suite',
			'occupancies' => [
				[],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Set partially valid occupancy data.
		$raw_cabin_data1 = [
			'code'        => 'CAB1',
			'name'        => 'Explorer Suite',
			'occupancies' => [
				[
					'id'   => 'CAB1:OCC1',
					'name' => 'Single',
					'mask' => 'A',
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Set valid occupancy data but without price.
		$raw_cabin_data1 = [
			'code'        => 'CAB1',
			'name'        => 'Explorer Suite',
			'occupancies' => [
				[
					'id'              => 'CAB1:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 10,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Set valid occupancy data. A new occupancy should be created.
		$raw_cabin_data1 = [
			'code'        => 'CAB1',
			'name'        => 'Explorer Suite',
			'occupancies' => [
				[
					'id'              => 'CAB1:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 10,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 1000,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertArrayHasKey( 'softrip_id', $occupancy );
		$this->assertArrayHasKey( 'softrip_name', $occupancy );
		$this->assertArrayHasKey( 'mask', $occupancy );
		$this->assertArrayHasKey( 'spaces_available', $occupancy );
		$this->assertArrayHasKey( 'availability_description', $occupancy );
		$this->assertArrayHasKey( 'availability_status', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_usd', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_cad', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_eur', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_gbp', $occupancy );
		$this->assertArrayHasKey( 'price_per_person_aud', $occupancy );
		$this->assertSame( 'CAB1:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );
		$this->assertSame( 10, $occupancy['spaces_available'] );
		$this->assertSame( 'Open', $occupancy['availability_description'] );
		$this->assertSame( 'S', $occupancy['availability_status'] );
		$this->assertSame( 1000, $occupancy['price_per_person_usd'] );
		$this->assertSame( 0, $occupancy['price_per_person_cad'] );
		$this->assertSame( 0, $occupancy['price_per_person_eur'] );
		$this->assertSame( 0, $occupancy['price_per_person_gbp'] );
		$this->assertSame( 0, $occupancy['price_per_person_aud'] );

		// Cabin spaces available should be set on departure post meta.
		$spaces_available = absint( get_post_meta( $departure_id1, 'cabin_spaces_available_' . $cabin_category_post_id1, true ) );
		$this->assertSame( 0, $spaces_available );

		// Let's update the price.
		$raw_cabin_data1 = [
			'code'            => 'CAB1',
			'name'            => 'Explorer Suite',
			'spacesAvailable' => 10,
			'occupancies'     => [
				[
					'id'              => 'CAB1:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 10,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 2000,
						],
						'CAD' => [
							'pricePerPerson' => 2500,
						],
						'EUR' => [
							'pricePerPerson' => 3000,
						],
						'GBP' => [
							'pricePerPerson' => 3500,
						],
						'AUD' => [
							'pricePerPerson' => 4000,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 2000, $occupancy['price_per_person_usd'] );
		$this->assertSame( 2500, $occupancy['price_per_person_cad'] );
		$this->assertSame( 3000, $occupancy['price_per_person_eur'] );
		$this->assertSame( 3500, $occupancy['price_per_person_gbp'] );
		$this->assertSame( 4000, $occupancy['price_per_person_aud'] );

		// Cabin spaces available should be set on departure post meta.
		$spaces_available = absint( get_post_meta( $departure_id1, 'cabin_spaces_available_' . $cabin_category_post_id1, true ) );
		$this->assertSame( 10, $spaces_available );

		// Update availability status.
		$raw_cabin_data1 = [
			'code'            => 'CAB1',
			'name'            => 'Explorer Suite',
			'spacesAvailable' => 10,
			'occupancies'     => [
				[
					'id'              => 'CAB1:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 10,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'R',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 2000,
						],
						'CAD' => [
							'pricePerPerson' => 2500,
						],
						'EUR' => [
							'pricePerPerson' => 3000,
						],
						'GBP' => [
							'pricePerPerson' => 3500,
						],
						'AUD' => [
							'pricePerPerson' => 4000,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'R', $occupancy['availability_status'] );

		// Let's add one more occupancy to same cabin.
		$raw_cabin_data1 = [
			'code'        => 'CAB1',
			'name'        => 'Explorer Suite',
			'occupancies' => [
				[
					'id'              => 'CAB1:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 10,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 2000,
						],
						'CAD' => [
							'pricePerPerson' => 2500,
						],
						'EUR' => [
							'pricePerPerson' => 3000,
						],
						'GBP' => [
							'pricePerPerson' => 3500,
						],
						'AUD' => [
							'pricePerPerson' => 4000,
						],
					],
				],
				[
					'id'              => 'CAB1:OCC2',
					'name'            => 'Double',
					'mask'            => 'B',
					'spacesAvailable' => 20,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'O',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 1500,
						],
						'CAD' => [
							'pricePerPerson' => 2000,
						],
						'EUR' => [
							'pricePerPerson' => 2500,
						],
						'GBP' => [
							'pricePerPerson' => 3000,
						],
						'AUD' => [
							'pricePerPerson' => 3500,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 2, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB1:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );
		$this->assertSame( 10, $occupancy['spaces_available'] );
		$this->assertSame( 'Open', $occupancy['availability_description'] );
		$this->assertSame( 'S', $occupancy['availability_status'] );
		$this->assertSame( 2000, $occupancy['price_per_person_usd'] );
		$this->assertSame( 2500, $occupancy['price_per_person_cad'] );
		$this->assertSame( 3000, $occupancy['price_per_person_eur'] );
		$this->assertSame( 3500, $occupancy['price_per_person_gbp'] );
		$this->assertSame( 4000, $occupancy['price_per_person_aud'] );

		// Get second occupancy.
		$occupancy = $occupancies[1];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB1:OCC2', $occupancy['softrip_id'] );
		$this->assertSame( 'Double', $occupancy['softrip_name'] );
		$this->assertSame( 'B', $occupancy['mask'] );
		$this->assertSame( 20, $occupancy['spaces_available'] );
		$this->assertSame( 'Open', $occupancy['availability_description'] );
		$this->assertSame( 'O', $occupancy['availability_status'] );
		$this->assertSame( 1500, $occupancy['price_per_person_usd'] );
		$this->assertSame( 2000, $occupancy['price_per_person_cad'] );
		$this->assertSame( 2500, $occupancy['price_per_person_eur'] );
		$this->assertSame( 3000, $occupancy['price_per_person_gbp'] );
		$this->assertSame( 3500, $occupancy['price_per_person_aud'] );

		// Add one more cabin with one occupancy.
		$raw_cabin_data2 = [
			'code'        => 'CAB2',
			'name'        => 'Deluxe Suite',
			'occupancies' => [
				[
					'id'              => 'CAB2:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 5,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 3000,
						],
						'CAD' => [
							'pricePerPerson' => 3500,
						],
						'EUR' => [
							'pricePerPerson' => 4000,
						],
						'GBP' => [
							'pricePerPerson' => 4500,
						],
						'AUD' => [
							'pricePerPerson' => 5000,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1, $raw_cabin_data2 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure. The second cabin doesn't exist. So, occupancy shouldn't be added.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 2, $occupancies );

		// Create another cabin.
		$cabin_category_post_id2 = $this->factory()->post->create(
			[
				'post_type'  => CABIN_CATEGORIES_POST_TYPE,
				'meta_input' => [
					'cabin_category_id' => 'CAB2',
				],
			]
		);
		$this->assertIsInt( $cabin_category_post_id2 );

		// Try again.
		$actual = update_occupancies( [ $raw_cabin_data1, $raw_cabin_data2 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 3, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB1:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );

		// Get second occupancy.
		$occupancy = $occupancies[1];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB1:OCC2', $occupancy['softrip_id'] );
		$this->assertSame( 'Double', $occupancy['softrip_name'] );
		$this->assertSame( 'B', $occupancy['mask'] );

		// Get third occupancy.
		$occupancy = $occupancies[2];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB2:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );

		// Update the occupancy for the second cabin.
		$raw_cabin_data2 = [
			'code'        => 'CAB2',
			'name'        => 'Deluxe Suite',
			'occupancies' => [
				[
					'id'              => 'CAB2:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 5,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 3500,
						],
						'CAD' => [
							'pricePerPerson' => 4000,
						],
						'EUR' => [
							'pricePerPerson' => 4500,
						],
						'GBP' => [
							'pricePerPerson' => 5000,
						],
						'AUD' => [
							'pricePerPerson' => 5500,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1, $raw_cabin_data2 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure and CAB2 cabin.
		$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id2, $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB2:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );
		$this->assertSame( 5, $occupancy['spaces_available'] );
		$this->assertSame( 'Open', $occupancy['availability_description'] );
		$this->assertSame( 'S', $occupancy['availability_status'] );
		$this->assertSame( 3500, $occupancy['price_per_person_usd'] );
		$this->assertSame( 4000, $occupancy['price_per_person_cad'] );
		$this->assertSame( 4500, $occupancy['price_per_person_eur'] );
		$this->assertSame( 5000, $occupancy['price_per_person_gbp'] );
		$this->assertSame( 5500, $occupancy['price_per_person_aud'] );

		/**
		 * Till here, we have tested the creation and updation of occupancies.
		 * Let's now test the deletion of occupancies in different scenarios.
		 *
		 * Deletion happens in two cases:
		 * 1. When the occupancy is not present in the raw data.
		 * 2. When the cabin is not present in the raw data - in this case, all occupancies given the cabin and departure would be removed.
		 */

		// Remove the second occupancy of CAB1 cabin.
		unset( $raw_cabin_data1['occupancies'][1] );

		// Update the occupancies.
		$actual = update_occupancies( [ $raw_cabin_data1, $raw_cabin_data2 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id1, $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB1:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );

		// Get second occupancy - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB1:OCC2', true );
		$this->assertEmpty( $occupancies );

		// The cabin spaces available meta should also be deleted and hence, empty now.
		$spaces_available = get_post_meta( $departure_id1, 'cabin_spaces_available_' . $cabin_category_post_id1, true );
		$this->assertSame( '', $spaces_available );

		// Add one more occupancy to cabin 2 - CAB2.
		$raw_cabin_data2 = [
			'code'        => 'CAB2',
			'name'        => 'Deluxe Suite',
			'occupancies' => [
				[
					'id'              => 'CAB2:OCC1',
					'name'            => 'Single',
					'mask'            => 'A',
					'spacesAvailable' => 5,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 3500,
						],
						'CAD' => [
							'pricePerPerson' => 4000,
						],
						'EUR' => [
							'pricePerPerson' => 4500,
						],
						'GBP' => [
							'pricePerPerson' => 5000,
						],
						'AUD' => [
							'pricePerPerson' => 5500,
						],
					],
				],
				[
					'id'              => 'CAB2:OCC2',
					'name'            => 'Double',
					'mask'            => 'B',
					'spacesAvailable' => 10,
					'saleStatus'      => 'Open',
					'saleStatusCode'  => 'S',
					'prices'          => [
						'USD' => [
							'pricePerPerson' => 4000,
						],
						'CAD' => [
							'pricePerPerson' => 4500,
						],
						'EUR' => [
							'pricePerPerson' => 5000,
						],
						'GBP' => [
							'pricePerPerson' => 5500,
						],
						'AUD' => [
							'pricePerPerson' => 6000,
						],
					],
				],
			],
		];
		$actual          = update_occupancies( [ $raw_cabin_data1, $raw_cabin_data2 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id2, $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 2, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB2:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );

		// Get second occupancy.
		$occupancy = $occupancies[1];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB2:OCC2', $occupancy['softrip_id'] );
		$this->assertSame( 'Double', $occupancy['softrip_name'] );
		$this->assertSame( 'B', $occupancy['mask'] );

		// Remove the second cabin - no more passing second cabin data.
		$actual = update_occupancies( [ $raw_cabin_data1 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 1, $occupancies );

		// Get first occupancy.
		$occupancy = $occupancies[0];
		$this->assertIsArray( $occupancy );
		$this->assertSame( 'CAB1:OCC1', $occupancy['softrip_id'] );
		$this->assertSame( 'Single', $occupancy['softrip_name'] );
		$this->assertSame( 'A', $occupancy['mask'] );

		// Get first occupancy of second cabin - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB2:OCC1', true );
		$this->assertEmpty( $occupancies );

		// Get second occupancy of second cabin - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB2:OCC2', true );
		$this->assertEmpty( $occupancies );

		// Again add the second cabin.
		$actual = update_occupancies( [ $raw_cabin_data1, $raw_cabin_data2 ], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertNotEmpty( $occupancies );
		$this->assertCount( 3, $occupancies );

		// Now, passing empty array - should remove all occupancies.
		$actual = update_occupancies( [], $departure_id1 );
		$this->assertTrue( $actual );

		// Get occupancies for first departure.
		$occupancies = get_occupancies_by_departure( $departure_id1, true );
		$this->assertEmpty( $occupancies );

		// Get occupancy data for CAB1:OCC1 - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB1:OCC1', true );
		$this->assertEmpty( $occupancies );

		// Get occupancy data for CAB2:OCC1 - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB2:OCC1', true );
		$this->assertEmpty( $occupancies );

		// Get occupancy data for CAB2:OCC2 - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB2:OCC2', true );
		$this->assertEmpty( $occupancies );

		// Get occupancy data for CAB1:OCC2 - shouldn't be present.
		$occupancies = get_occupancy_data_by_softrip_id( 'CAB1:OCC2', true );
		$this->assertEmpty( $occupancies );
	}
}
