<?php
/**
 * Softrip test functions.
 *
 * @package quark
 */

namespace Quark\Tests\Softrip;

use function Quark\Softrip\create_custom_db_tables;
use function Quark\Softrip\get_custom_db_table_mapping;

/**
 * Setup Softrip integration.
 *
 * @return void
 */
function setup_softrip_integration(): void {
	// Define constants.
	if ( function_exists( 'getenv' ) ) {
		define( 'QUARK_SOFTRIP_ADAPTER_BASE_URL', getenv( 'QUARK_SOFTRIP_ADAPTER_BASE_URL' ) );
		define( 'QUARK_SOFTRIP_ADAPTER_API_KEY', getenv( 'QUARK_SOFTRIP_ADAPTER_API_KEY' ) );
	}

	// Setup DB tables.
	setup_softrip_db_tables();
}

/**
 * Setup Softrip DB.
 *
 * @return void
 */
function setup_softrip_db_tables(): void {
	// Set run status.
	static $run;

	// End if run.
	if ( ! empty( $run ) ) {
		return;
	}

	// Create DB tables.
	create_custom_db_tables();

	// Flag as run.
	$run = true;
}

/**
 * Mock the HTTP request.
 *
 * @param mixed[]|false $response    The response.
 * @param mixed[]       $parsed_args The parsed args.
 * @param string|null   $url         The URL.
 *
 * @return false|array{}|array{
 *     body: string|false,
 *     response: array{
 *          code: int,
 *          message: string,
 *     },
 *     headers: array{},
 * }
 */
function mock_softrip_http_request( array|false $response = [], array $parsed_args = [], string $url = null ): false|array {
	// Check if the softrip constants are set.
	if ( ! defined( 'QUARK_SOFTRIP_ADAPTER_BASE_URL' ) || empty( QUARK_SOFTRIP_ADAPTER_BASE_URL ) ||
		! defined( 'QUARK_SOFTRIP_ADAPTER_API_KEY' ) || empty( QUARK_SOFTRIP_ADAPTER_API_KEY )
	) {
		return $response;
	}

	// Check if the URL is the one we want to mock.
	if ( str_contains( $url, 'https://softrip-adapter.dev/' ) ) {
		if ( 'https://softrip-adapter.dev/' === $url ) {
			// Return 400 if no productCodes.
			return [
				'response' => [
					'code'    => 400,
					'message' => 'Missing productCodes in query parameter',
				],
				'headers'  => [],
			];
		} elseif ( 'https://softrip-adapter.dev/departures' !== $url ) {
			// Return 404 if the URL is not the one we want to mock.
			return [
				'response' => [
					'code'    => 404,
					'message' => 'Not Found',
				],
				'headers'  => [],
			];
		}
	} else {
		// Return response if the URL is not the one we want to mock.
		return $response;
	}

	// Setup variables.
	$data          = [];
	$product_codes = [];

	// Check if the body is set.
	if ( isset( $parsed_args['body'] ) && is_array( $parsed_args['body'] ) ) {
		$product_codes = explode( ',', $parsed_args['body']['productCodes'] );
	} else {
		// Return 400 if no productCodes.
		return [
			'response' => [
				'code'    => 400,
				'message' => 'Missing productCodes in query parameter',
			],
			'headers'  => [],
		];
	}

	// Check if there are too many product codes.
	if ( 5 < count( $product_codes ) ) {
		return [
			'response' => [
				'code'    => 400,
				'message' => 'Too many productCodes in query parameter',
			],
			'headers'  => [],
		];
	}

	// Init data.
	foreach ( $product_codes as $product_code ) {
		$data[ $product_code ] = [
			'departures' => [],
		];
	}

	// Add data for ABC-123.
	if ( in_array( 'ABC-123', $product_codes, true ) ) {
		$data['ABC-123'] = [
			'departures' => [
				[
					'id'          => 'ABC-123:2026-02-28',
					'code'        => 'OEX20260228',
					'packageCode' => 'ABC-123',
					'startDate'   => '2026-02-28',
					'endDate'     => '2026-03-11',
					'duration'    => 11,
					'shipCode'    => 'OEX',
					'marketCode'  => 'ANT',
					'cabins'      => [
						[
							'id'              => 'ABC-123:2026-02-28:OEX-SGL',
							'code'            => 'OEX-SGL',
							'name'            => 'Studio Single',
							'departureId'     => 'ABC-123:2026-02-28',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'ABC-123:2026-02-28:OEX-SGL:A',
									'name'            => 'ABC-123:2026-02-28:OEX-SGL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'pricePerPerson' => 34895,
											'currencyCode'   => 'USD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 26171,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 54795,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 41096,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 47495,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 35621,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 32495,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 24371,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 27995,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 20996,
												],
											],
										],
									],
								],
							],
						],
					],
					'promotions'  => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 25%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.25',
							'promotionCode' => '25PROMO',
							'isPIF'         => false,
						],
					],
				],
			],
		];
	}

	// Add data for PQR-345.
	if ( in_array( 'PQR-345', $product_codes, true ) ) {
		$data['PQR-345'] = [
			'departures' => [],
		];
	}

	// Add data for JKL-012.
	if ( in_array( 'JKL-012', $product_codes, true ) ) {
		$data['JKL-012'] = [
			'departures' => [
				[
					'id'          => 'JKL-012:2025-01-09',
					'code'        => 'ULT20250109',
					'packageCode' => 'JKL-012',
					'startDate'   => '2025-01-09',
					'endDate'     => '2025-01-25',
					'duration'    => 16,
					'shipCode'    => 'ULT',
					'marketCode'  => 'ANT',
					'cabins'      => [
						[
							'id'              => 'JKL-012:2025-01-09:ULT-SGL',
							'code'            => 'ULT-SGL',
							'name'            => 'Studio Single',
							'departureId'     => 'JKL-012:2025-01-09',
							'spacesAvailable' => 10,
							'occupancies'     => [
								[
									'id'              => 'JKL-012:2025-01-09:ULT-SGL:A',
									'name'            => 'JKL-012:2025-01-09:ULT-SGL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 10,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'pricePerPerson' => 44905,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 38169,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 38169,
												],
												'SGLPROMO'  => [
													'promoPricePerPerson' => 38169,
												],
												'FLIGHTUSD' => [
													'promoPricePerPerson' => 38169,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 70605,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 60014,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 60014,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 61205,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 52024,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 52024,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 41905,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 35619,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 35619,
												],
												'SGLPROMOE' => [
													'promoPricePerPerson' => 35619,
												],
												'FLIGHTEUR' => [
													'promoPricePerPerson' => 35619,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 35905,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 30519,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 30519,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'JKL-012:2025-01-09:ULT-DBL',
							'code'            => 'ULT-DBL',
							'name'            => 'Studio Double',
							'departureId'     => 'JKL-012:2025-01-09',
							'spacesAvailable' => 20,
							'occupancies'     => [
								[
									'id'              => 'JKL-012:2025-01-09:ULT-DBL:A',
									'name'            => 'JKL-012:2025-01-09:ULT-DBL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 10,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'pricePerPerson' => 74900,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 63665,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 63665,
												],
												'SGLPROMO'  => [
													'promoPricePerPerson' => 63665,
												],
												'FLIGHTUSD' => [
													'promoPricePerPerson' => 63665,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 117500,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 99875,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 99875,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 102000,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 86700,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 86700,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 69900,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 59415,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 59415,
												],
												'SGLPROMOE' => [
													'promoPricePerPerson' => 38169,
												],
												'FLIGHTEUR' => [
													'promoPricePerPerson' => 38169,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 59900,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 50915,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 50915,
												],
											],
										],
									],
								],
								[
									'id'              => 'JKL-012:2025-01-09:ULT-DBL:AA',
									'name'            => 'JKL-012:2025-01-09:ULT-DBL:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 10,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'pricePerPerson' => 34600,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 29410,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 29410,
												],
												'SGLPROMO'  => [
													'promoPricePerPerson' => 29410,
												],
												'FLIGHTUSD' => [
													'promoPricePerPerson' => 29410,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 54200,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 46070,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 46070,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 47000,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 39950,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 39950,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 32200,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 27370,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 27370,
												],
												'SGLPROMOE' => [
													'promoPricePerPerson' => 27370,
												],
												'FLIGHTEUR' => [
													'promoPricePerPerson' => 27370,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 27600,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO'   => [
													'promoPricePerPerson' => 23460,
												],
												'PRIOPOASS' => [
													'promoPricePerPerson' => 23460,
												],
											],
										],
									],
								],
							],
						],
					],
					'promotions'  => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 15%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.15',
							'promotionCode' => '15PROMO',
							'isPIF'         => false,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'One Year Priority Pass™ Membership',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => 0,
							'promotionCode' => 'PRIOPASS',
							'pricingBasis'  => 'per_person',
							'isPIF'         => false,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Free Single Supplement on Standard Cabins',
							'currencyCode'  => 'EUR',
							'discountType'  => 'percentage_off',
							'discountValue' => 0,
							'promotionCode' => 'SGLPROMOE',
							'pricingBasis'  => 'per_person',
							'isPIF'         => false,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Free Single Supplement on Standard Cabins',
							'currencyCode'  => 'USD',
							'discountType'  => 'percentage_off',
							'discountValue' => 0,
							'promotionCode' => 'SGLPROMO',
							'pricingBasis'  => 'per_person',
							'isPIF'         => false,
						],
						[
							'endDate'       => '2025-04-01T00:00:00',
							'startDate'     => '2025-01-06T00:00:00',
							'description'   => '$1400 EUR Flight Credit',
							'currencyCode'  => 'EUR',
							'discountType'  => 'fixed_off',
							'discountValue' => 1400,
							'promotionCode' => 'FLIGHTEUR',
							'pricingBasis'  => 'per_person',
							'isPIF'         => false,
						],
						[
							'endDate'       => '2025-04-01T00:00:00',
							'startDate'     => '2025-01-06T00:00:00',
							'description'   => '$1000 USD Flight Credit',
							'currencyCode'  => 'USD',
							'discountType'  => 'fixed_off',
							'discountValue' => 1400,
							'promotionCode' => 'FLIGHTUSD',
							'pricingBasis'  => 'per_person',
							'isPIF'         => false,
						],
					],
				],
				[
					'id'          => 'JKL-012:2026-01-16',
					'code'        => 'ULT20260116',
					'packageCode' => 'JKL-012',
					'startDate'   => '2026-01-16',
					'endDate'     => '2026-02-01',
					'duration'    => 16,
					'shipCode'    => 'ULT',
					'marketCode'  => 'ANT',
					'cabins'      => [
						[
							'id'              => 'JKL-012:2026-01-16:ULT-SGL',
							'code'            => 'ULT-SGL',
							'name'            => 'Studio Single',
							'departureId'     => 'JKL-012:2026-01-16',
							'spacesAvailable' => 10,
							'occupancies'     => [
								[
									'id'              => 'JKL-012:2026-01-16:ULT-SGL:A',
									'name'            => 'JKL-012:2026-01-16:ULT-SGL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 10,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'pricePerPerson' => 46905,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 39869,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 73605,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 62564,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 64205,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 54574,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 43905,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 37319,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 37905,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 32219,
												],
											],
										],
									],
								],
							],
						],
					],
					'promotions'  => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 15%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.15',
							'promotionCode' => '15PROMO',
							'isPIF'         => false,
						],
					],
				],
			],
		];
	}

	// Add data for HIJ-456. This has multiple cabins, occupancies, promos and hence, extensive data.
	if ( in_array( 'HIJ-456', $product_codes, true ) ) {
		$data['HIJ-456'] = [
			'departures' => [
				[
					'id'               => 'HIJ-456:2025-08-26',
					'code'             => 'OEX20250826',
					'packageCode'      => 'HIJ-456',
					'startDate'        => '2025-08-26',
					'endDate'          => '2025-09-05',
					'duration'         => 11,
					'shipCode'         => 'OEX',
					'marketCode'       => 'ARC',
					'cabins'           => [
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-SGL',
							'code'            => 'OEX-SGL',
							'name'            => 'Studio Single',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 5,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-SGL:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-SGL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 5,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 16795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15116,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15116,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 26400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23760,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 23760,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 22900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20610,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 20610,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 15700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14130,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 14130,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 13500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12150,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 12150,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-FWD',
							'code'            => 'OEX-FWD',
							'name'            => 'Deluxe Veranda Forward',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-FWD:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-FWD:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 3,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 21752,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 19577,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 19577,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 34170,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30753,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 30753,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 29750,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 26775,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 26775,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 20230,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18207,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 18207,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 17510,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15759,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15759,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-FWD:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-FWD:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 3,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 11516,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 18090,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15750,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 10710,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 9270,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-08-26:OEX-FWD:SAA',
									'name'            => 'HIJ-456:2025-08-26:OEX-FWD:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 3,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 11516,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 18090,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15750,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 10710,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 9270,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-JST',
							'code'            => 'OEX-JST',
							'name'            => 'Junior Suite',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-JST:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-JST:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 6,
									'seq'             => '300',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 33590,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30231,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 30231,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 52800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 47520,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 47520,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 45800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 41220,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 41220,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 31400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 28260,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 28260,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 27000,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 24300,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 24300,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-JST:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-JST:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '300',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 16795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15116,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15116,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 26400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23760,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 23760,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 22900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20610,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 20610,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 15700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14130,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 14130,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 13500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12150,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 12150,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-SVS',
							'code'            => 'OEX-SVS',
							'name'            => 'Studio Veranda Single',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-SVS:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-SVS:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '400',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 18095,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16286,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 16286,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 28500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 25650,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 25650,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 24700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 22230,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 22230,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 16900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15210,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15210,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 14500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 13050,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 13050,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-SVS:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-SVS:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '400',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 10995,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9896,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 9896,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 17200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15480,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15480,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 14900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 13410,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 13410,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 10200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9180,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 9180,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-EXP',
							'code'            => 'OEX-EXP',
							'name'            => 'Penthouse Suite',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-EXP:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-EXP:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 38990,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 35091,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 35091,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 61400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 55260,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 55260,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 53200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 47880,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 47880,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 36400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 32760,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 32760,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 31200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 28080,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 28080,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-EXP:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-EXP:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 19495,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17546,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 17546,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 30700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 27630,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 27630,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 26600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23940,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 23940,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 18200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16380,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 16380,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 15600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14040,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 14040,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-OWN',
							'code'            => 'OEX-OWN',
							'name'            => "Owner\'s Suite",
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-OWN:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-OWN:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '600',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 41590,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 37431,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 37431,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 65400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 58860,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 58860,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 56600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 50940,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 50940,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 38800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 34920,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 34920,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 33400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30060,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 30060,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-OWN:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-OWN:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '600',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 20795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18716,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 18716,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 32700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 29430,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 29430,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 28300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 25470,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 25470,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 17460,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 16700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15030,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 15030,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-VER',
							'code'            => 'OEX-VER',
							'name'            => 'Veranda Stateroom',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 34,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-VER:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-VER:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 17,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 22772,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20495,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 20495,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 35870,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 32283,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 32283,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 31110,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 27999,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 27999,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 21250,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 19125,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 19125,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 18360,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16524,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 16524,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-VER:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-VER:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 17,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 13395,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12056,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 12056,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 21100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18990,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 18990,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 18300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16470,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 16470,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 12500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11250,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 11250,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9720,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 9720,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-08-26:OEX-VER:SAA',
									'name'            => 'HIJ-456:2025-08-26:OEX-VER:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 17,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 13395,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12056,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 12056,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 21100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18990,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 18990,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 18300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16470,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 16470,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 12500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11250,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 11250,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9720,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 9720,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:OEX-VST',
							'code'            => 'OEX-VST',
							'name'            => 'Veranda Suite',
							'departureId'     => 'HIJ-456:2025-08-26',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-VST:A',
									'name'            => 'HIJ-456:2025-08-26:OEX-VST:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 33,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 24132,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 21719,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 21719,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 37910,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 34119,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 34119,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 32980,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 29682,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 29682,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 22610,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20349,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 20349,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 19380,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17442,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 17442,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-08-26:OEX-VST:AA',
									'name'            => 'HIJ-456:2025-08-26:OEX-VST:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 33,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 14195,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12776,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 12776,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 22300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20070,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 20070,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 17460,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 13300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11970,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 11970,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 11400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10260,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 10260,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-08-26:OEX-VST:SAA',
									'name'            => 'HIJ-456:2025-08-26:OEX-VST:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 33,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 14195,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12776,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 12776,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 22300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20070,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 20070,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 17460,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 13300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11970,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 11970,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 11400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10260,
												],
												'10PROMO' => [
													'promoPricePerPerson' => 10260,
												],
											],
										],
									],
								],
							],
						],
					],
					'adventureOptions' => [
						[
							'id'              => 'HIJ-456:2025-08-26:KAYAK',
							'spacesAvailable' => 7,
							'serviceIds'      => [ 'KAYAK' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 640,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 740,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 795,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 1090,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 1250,
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-08-26:KAYEXP',
							'spacesAvailable' => 7,
							'serviceIds'      => [ 'KAYEXP', 'KAYEXP2' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 160,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 190,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 195,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 270,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 310,
								],
							],
						],
					],
					'promotions'       => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-19T00:00:00',
							'description'   =>
							'Pay in full at time of booking & Save 10%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.1',
							'promotionCode' => '10PIF',
							'isPIF'         => true,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 10%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.1',
							'promotionCode' => '10PROMO',
							'isPIF'         => false,
						],
					],
				],
				[
					'id'               => 'HIJ-456:2025-09-04',
					'code'             => 'OEX20250904',
					'packageCode'      => 'HIJ-456',
					'startDate'        => '2025-09-04',
					'endDate'          => '2025-09-14',
					'duration'         => 11,
					'shipCode'         => 'OEX',
					'marketCode'       => 'ARC',
					'cabins'           => [
						[
							'id'          => 'HIJ-456:2025-09-04:OEX-SGL',
							'code'        => 'OEX-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'HIJ-456:2025-09-04',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-SGL:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-SGL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 5,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 16795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15116,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 13436,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 26400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23760,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21120,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 22900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20610,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 18320,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 15700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14130,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 12560,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 13500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12150,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10800,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-04:OEX-FWD',
							'code'        => 'OEX-FWD',
							'name'        => 'Deluxe Veranda Forward',
							'departureId' => 'HIJ-456:2025-09-04',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-FWD:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-FWD:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 21752,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 19577,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17402,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 34170,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30753,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 27336,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 29750,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 26775,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 23800,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 20230,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18207,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16184,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 17510,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15759,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14008,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-FWD:AA',
									'name'            => 'HIJ-456:2025-09-04:OEX-FWD:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10236,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16080,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14000,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8240,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-04:OEX-FWD:SAA',
									'name'            => 'HIJ-456:2025-09-04:OEX-FWD:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10236,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16080,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14000,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8240,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-04:OEX-JST',
							'code'        => 'OEX-JST',
							'name'        => 'Junior Suite',
							'departureId' => 'HIJ-456:2025-09-04',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-JST:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-JST:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 6,
									'seq'             => '300',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 33590,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30231,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 26872,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 52800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 47520,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 42240,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 45800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 41220,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 36640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 31400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 28260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 25120,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 27000,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 24300,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21600,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-09-04:OEX-SVS',
							'code'            => 'OEX-SVS',
							'name'            => 'Studio Veranda Single',
							'departureId'     => 'HIJ-456:2025-09-04',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-SVS:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-SVS:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '400',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 18095,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16286,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14476,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 28500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 25650,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 22800,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 24700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 22230,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 19760,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 16900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15210,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 13520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 14500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 13050,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 11600,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-04:OEX-EXP',
							'code'        => 'OEX-EXP',
							'name'        => 'Penthouse Suite',
							'departureId' => 'HIJ-456:2025-09-04',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-EXP:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-EXP:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 38990,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 35091,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 31192,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 61400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 55260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 49120,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 53200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 47880,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 42560,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 36400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 32760,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 29120,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 31200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 28080,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 24960,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-EXP:AA',
									'name'            => 'HIJ-456:2025-09-04:OEX-EXP:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 19495,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17546,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15596,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 30700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 27630,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 24560,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 26600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23940,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21280,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 18200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16380,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14560,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 15600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14040,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 12480,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-09-04:OEX-OWN',
							'code'            => 'OEX-OWN',
							'name'            => "Owner\'s Suite",
							'departureId'     => 'HIJ-456:2025-09-04',
							'spacesAvailable' => 0,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-OWN:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-OWN:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '600',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 41590,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 37431,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 33272,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 65400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 58860,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 52320,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 56600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 50940,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 45280,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 38800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 34920,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 31040,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 33400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30060,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 26720,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-OWN:AA',
									'name'            => 'HIJ-456:2025-09-04:OEX-OWN:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'S',
									'saleStatus'      => 'Sold Out',
									'spacesAvailable' => 0,
									'seq'             => '600',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 20795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18716,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16636,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 32700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 29430,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 26160,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 28300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 25470,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 22640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 16700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15030,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 13360,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-09-04:OEX-VER',
							'code'            => 'OEX-VER',
							'name'            => 'Veranda Stateroom',
							'departureId'     => 'HIJ-456:2025-09-04',
							'spacesAvailable' => 18,
							'occupancies'     => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-VER:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-VER:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 18,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 22772,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20495,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 18218,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 35870,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 32283,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 28696,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 31110,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 27999,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 24888,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 21250,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 19125,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17000,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 18360,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16524,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14688,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-VER:AA',
									'name'            => 'HIJ-456:2025-09-04:OEX-VER:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 18,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 13395,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12056,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10716,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 21100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18990,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16880,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 18300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16470,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 12500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11250,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10000,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9720,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8640,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-04:OEX-VER:SAA',
									'name'            => 'HIJ-456:2025-09-04:OEX-VER:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 18,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 13395,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12056,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10716,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 21100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18990,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16880,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 18300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16470,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 12500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11250,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10000,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9720,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8640,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-04:OEX-VST',
							'code'        => 'OEX-VST',
							'name'        => 'Veranda Suite',
							'departureId' => 'HIJ-456:2025-09-04',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-VST:A',
									'name'            => 'HIJ-456:2025-09-04:OEX-VST:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 32,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 24132,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 21719,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 19306,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 37910,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 34119,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 30328,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 32980,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 29682,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 26384,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 22610,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20349,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 18088,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 19380,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17442,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15504,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-04:OEX-VST:AA',
									'name'            => 'HIJ-456:2025-09-04:OEX-VST:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 32,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 14195,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12776,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 11356,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 22300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20070,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17840,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15520,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 13300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11970,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10640,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 11400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9120,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-04:OEX-VST:SAA',
									'name'            => 'HIJ-456:2025-09-04:OEX-VST:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 32,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 14195,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12776,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 11356,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 22300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20070,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17840,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15520,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 13300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11970,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10640,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 11400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9120,
												],
											],
										],
									],
								],
							],
						],
					],
					'adventureOptions' => [
						[
							'id'              => 'HIJ-456:2025-09-04:KAYAK',
							'spacesAvailable' => 0,
							'serviceIds'      => [ 'KAYAK' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 640,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 740,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 795,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 1090,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 1250,
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-09-04:KAYEXP',
							'spacesAvailable' => 13,
							'serviceIds'      => [ 'KAYEXP', 'KAYEXP2' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 160,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 190,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 195,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 270,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 310,
								],
							],
						],
					],
					'promotions'       => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-19T00:00:00',
							'description'   =>
							'Pay in full at time of booking & Save 10%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.1',
							'promotionCode' => '10PIF',
							'isPIF'         => true,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 20%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.2',
							'promotionCode' => '20PROMO',
							'isPIF'         => false,
						],
					],
				],
				[
					'id'               => 'HIJ-456:2025-09-13',
					'code'             => 'OEX20250913',
					'packageCode'      => 'HIJ-456',
					'startDate'        => '2025-09-13',
					'endDate'          => '2025-09-23',
					'duration'         => 11,
					'shipCode'         => 'OEX',
					'marketCode'       => 'ARC',
					'cabins'           => [
						[
							'id'          => 'HIJ-456:2025-09-13:OEX-SGL',
							'code'        => 'OEX-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'HIJ-456:2025-09-13',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-13:OEX-SGL:A',
									'name'            => 'HIJ-456:2025-09-13:OEX-SGL:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 3,
									'seq'             => '100',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 16795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15116,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 13436,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 26400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23760,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21120,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 22900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20610,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 18320,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 15700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14130,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 12560,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 13500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12150,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10800,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-13:OEX-FWD',
							'code'        => 'OEX-FWD',
							'name'        => 'Deluxe Veranda Forward',
							'departureId' => 'HIJ-456:2025-09-13',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-13:OEX-FWD:A',
									'name'            => 'HIJ-456:2025-09-13:OEX-FWD:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 4,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 21752,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 19577,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17402,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 34170,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30753,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 27336,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 29750,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 26775,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 23800,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 20230,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18207,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16184,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 17510,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15759,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14008,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-13:OEX-FWD:AA',
									'name'            => 'HIJ-456:2025-09-13:OEX-FWD:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 4,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10236,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16080,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14000,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8240,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-13:OEX-FWD:SAA',
									'name'            => 'HIJ-456:2025-09-13:OEX-FWD:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 4,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10236,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16080,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14000,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8240,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-13:OEX-EXP',
							'code'        => 'OEX-EXP',
							'name'        => 'Penthouse Suite',
							'departureId' => 'HIJ-456:2025-09-13',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-13:OEX-EXP:AA',
									'name'            => 'HIJ-456:2025-09-13:OEX-EXP:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 19495,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17546,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15596,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 30700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 27630,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 24560,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 26600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23940,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21280,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 18200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16380,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14560,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 15600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14040,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 12480,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-13:OEX-VER',
							'code'        => 'OEX-VER',
							'name'        => 'Veranda Stateroom',
							'departureId' => 'HIJ-456:2025-09-13',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-13:OEX-VER:AA',
									'name'            => 'HIJ-456:2025-09-13:OEX-VER:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 9,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 13395,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12056,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10716,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 21100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18990,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16880,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 18300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16470,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 12500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11250,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10000,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9720,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8640,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-13:OEX-VER:SAA',
									'name'            => 'HIJ-456:2025-09-13:OEX-VER:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 9,
									'seq'             => '700',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 13395,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12056,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10716,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 21100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18990,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16880,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 18300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16470,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 12500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11250,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10000,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9720,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8640,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-13:OEX-VST',
							'code'        => 'OEX-VST',
							'name'        => 'Veranda Suite',
							'departureId' => 'HIJ-456:2025-09-13',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-13:OEX-VST:AA',
									'name'            => 'HIJ-456:2025-09-13:OEX-VST:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 21,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 14195,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12776,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 11356,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 22300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20070,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17840,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15520,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 13300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11970,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10640,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 11400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9120,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-13:OEX-VST:SAA',
									'name'            => 'HIJ-456:2025-09-13:OEX-VST:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 21,
									'seq'             => '800',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 14195,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12776,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 11356,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 22300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20070,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17840,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 19400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17460,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15520,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 13300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11970,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10640,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 11400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9120,
												],
											],
										],
									],
								],
							],
						],
					],
					'adventureOptions' => [
						[
							'id'              => 'HIJ-456:2025-09-13:KAYAK',
							'spacesAvailable' => 6,
							'serviceIds'      => [ 'KAYAK' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 640,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 740,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 795,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 1090,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 1250,
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-09-13:KAYEXP',
							'spacesAvailable' => 16,
							'serviceIds'      => [ 'KAYEXP', 'KAYEXP2' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 160,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 190,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 195,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 270,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 310,
								],
							],
						],
					],
					'promotions'       => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-19T00:00:00',
							'description'   =>
							'Pay in full at time of booking & Save 10%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.1',
							'promotionCode' => '10PIF',
							'isPIF'         => true,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 20%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.2',
							'promotionCode' => '20PROMO',
							'isPIF'         => false,
						],
					],
				],
				[
					'id'               => 'HIJ-456:2025-09-22',
					'code'             => 'OEX20250922',
					'packageCode'      => 'HIJ-456',
					'startDate'        => '2025-09-22',
					'endDate'          => '2025-10-02',
					'duration'         => 11,
					'shipCode'         => 'OEX',
					'marketCode'       => 'ARC',
					'cabins'           => [
						[
							'id'          => 'HIJ-456:2025-09-22:OEX-FWD',
							'code'        => 'OEX-FWD',
							'name'        => 'Deluxe Veranda Forward',
							'departureId' => 'HIJ-456:2025-09-22',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-22:OEX-FWD:A',
									'name'            => 'HIJ-456:2025-09-22:OEX-FWD:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 2,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 21752,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 19577,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 17402,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 34170,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30753,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 27336,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 29750,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 26775,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 23800,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 20230,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18207,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16184,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 17510,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15759,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14008,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-22:OEX-FWD:AA',
									'name'            => 'HIJ-456:2025-09-22:OEX-FWD:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 2,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10236,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16080,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14000,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8240,
												],
											],
										],
									],
								],
								[
									'id'              =>
									'HIJ-456:2025-09-22:OEX-FWD:SAA',
									'name'            => 'HIJ-456:2025-09-22:OEX-FWD:SAA',
									'mask'            => 'SAA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 2,
									'seq'             => '200',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 12795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 11516,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10236,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 20100,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 18090,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 16080,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 17500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15750,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14000,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 11900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 10710,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 9520,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 10300,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 9270,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 8240,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-22:OEX-JST',
							'code'        => 'OEX-JST',
							'name'        => 'Junior Suite',
							'departureId' => 'HIJ-456:2025-09-22',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-22:OEX-JST:A',
									'name'            => 'HIJ-456:2025-09-22:OEX-JST:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 6,
									'seq'             => '300',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 33590,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 30231,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 26872,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 52800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 47520,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 42240,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 45800,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 41220,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 36640,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 31400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 28260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 25120,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 27000,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 24300,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21600,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-22:OEX-JST:AA',
									'name'            => 'HIJ-456:2025-09-22:OEX-JST:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 6,
									'seq'             => '300',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 16795,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 15116,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 13436,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 26400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23760,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21120,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 22900,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 20610,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 18320,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 15700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14130,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 12560,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 13500,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 12150,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 10800,
												],
											],
										],
									],
								],
							],
						],
						[
							'id'          => 'HIJ-456:2025-09-22:OEX-EXP',
							'code'        => 'OEX-EXP',
							'name'        => 'Penthouse Suite',
							'departureId' => 'HIJ-456:2025-09-22',
							'occupancies' => [
								[
									'id'              => 'HIJ-456:2025-09-22:OEX-EXP:A',
									'name'            => 'HIJ-456:2025-09-22:OEX-EXP:A',
									'mask'            => 'A',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 38990,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 35091,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 31192,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 61400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 55260,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 49120,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 53200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 47880,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 42560,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 36400,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 32760,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 29120,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 31200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 28080,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 24960,
												],
											],
										],
									],
								],
								[
									'id'              => 'HIJ-456:2025-09-22:OEX-EXP:AA',
									'name'            => 'HIJ-456:2025-09-22:OEX-EXP:AA',
									'mask'            => 'AA',
									'saleStatusCode'  => 'O',
									'saleStatus'      => 'Open',
									'spacesAvailable' => 1,
									'seq'             => '500',
									'prices'          => [
										'USD' => [
											'currencyCode'   => 'USD',
											'pricePerPerson' => 19495,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 17546,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 15596,
												],
											],
										],
										'AUD' => [
											'currencyCode'   => 'AUD',
											'pricePerPerson' => 30700,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 27630,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 24560,
												],
											],
										],
										'CAD' => [
											'currencyCode'   => 'CAD',
											'pricePerPerson' => 26600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 23940,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 21280,
												],
											],
										],
										'EUR' => [
											'currencyCode'   => 'EUR',
											'pricePerPerson' => 18200,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 16380,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 14560,
												],
											],
										],
										'GBP' => [
											'currencyCode'   => 'GBP',
											'pricePerPerson' => 15600,
											'promos'         => [
												'10PIF'   => [
													'promoPricePerPerson' => 14040,
												],
												'20PROMO' => [
													'promoPricePerPerson' => 12480,
												],
											],
										],
									],
								],
							],
						],
					],
					'adventureOptions' => [
						[
							'id'              => 'HIJ-456:2025-09-22:KAYAK',
							'spacesAvailable' => 6,
							'serviceIds'      => [ 'KAYAK' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 640,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 740,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 795,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 1090,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 1250,
								],
							],
						],
						[
							'id'              => 'HIJ-456:2025-09-22:KAYEXP',
							'spacesAvailable' => 17,
							'serviceIds'      => [ 'KAYEXP', 'KAYEXP2' ],
							'price'           => [
								'GBP' => [
									'currencyCode'   => 'GBP',
									'pricePerPerson' => 160,
								],
								'EUR' => [
									'currencyCode'   => 'EUR',
									'pricePerPerson' => 190,
								],
								'USD' => [
									'currencyCode'   => 'USD',
									'pricePerPerson' => 195,
								],
								'CAD' => [
									'currencyCode'   => 'CAD',
									'pricePerPerson' => 270,
								],
								'AUD' => [
									'currencyCode'   => 'AUD',
									'pricePerPerson' => 310,
								],
							],
						],
					],
					'promotions'       => [
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-19T00:00:00',
							'description'   =>
							'Pay in full at time of booking & Save 10%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.1',
							'promotionCode' => '10PIF',
							'isPIF'         => true,
						],
						[
							'endDate'       => '2050-12-31T00:00:00',
							'startDate'     => '2023-09-28T00:00:00',
							'description'   => 'Save 20%',
							'currencyCode'  => null,
							'discountType'  => 'percentage_off',
							'discountValue' => '0.2',
							'promotionCode' => '20PROMO',
							'isPIF'         => false,
						],
					],
				],
			],
		];
	}

	// Return the response.
	return [
		'body'     => wp_json_encode( $data ),
		'response' => [
			'code'    => 200,
			'message' => 'OK',
		],
		'headers'  => [],
	];
}

/**
 * Setup Softrip DB.
 *
 * @return void
 */
function truncate_softrip_db_tables(): void {
	// Get global WPDB object.
	global $wpdb;

	// Get custom DB table mapping.
	$tables = get_custom_db_table_mapping();

	// Truncate tables.
	foreach ( $tables as $table_name => $sql ) {
		$wpdb->query( "TRUNCATE TABLE $table_name" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

/**
 * Drop Softrip DB tables.
 *
 * @return void
 */
function drop_softrip_db_tables(): void {
	// Get global WPDB object.
	global $wpdb;

	// Get custom DB table mapping.
	$tables = get_custom_db_table_mapping();

	// Drop tables.
	foreach ( $tables as $table_name => $sql ) {
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
