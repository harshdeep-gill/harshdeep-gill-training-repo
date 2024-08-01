<?php
/**
 * Softrip test functions.
 *
 * @package quark
 */

namespace Quark\Tests\Softrip;

use Quark\Softrip\Softrip_DB;

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

	// Include DB functions.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	// Init DB object.
	$db = new Softrip_DB();

	// Get SQL array.
	$tables = $db->get_db_tables_sql();

	// Start table creation.
	foreach ( $tables as $name => $sql ) {
		$table_name = $db->prefix_table_name( $name );
		maybe_create_table( $table_name, $sql );
	}

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
	// Check if the URL is the one we want to mock.
	if ( 'https://softrip-adapter.dev/departures' !== $url ) {
		return $response;
	}

	// Setup variables.
	$data          = [];
	$product_codes = [];

	// Check if the body is set.
	if ( isset( $parsed_args['body'] ) && is_array( $parsed_args['body'] ) ) {
		$product_codes = explode( ',', $parsed_args['body']['productCodes'] );
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
							'id'          => 'ABC-123:2026-02-28:OEX-SGL',
							'code'        => 'OEX-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'ABC-123:2026-02-28',
							'occupancies' => [
								[
									'id'                      => 'ABC-123:2026-02-28:OEX-SGL:A',
									'name'                    => 'ABC-123:2026-02-28:OEX-SGL:A',
									'mask'                    => 'A',
									'availabilityStatus'      => 'C',
									'availabilityDescription' => 'Unavailable',
									'spacesAvailable'         => 0,
									'seq'                     => '100',
									'prices'                  => [
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
							'promotions'  => [
								'available' => [
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
							'id'          => 'JKL-012:2025-01-09:ULT-SGL',
							'code'        => 'ULT-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'JKL-012:2025-01-09',
							'occupancies' => [
								[
									'id'                      => 'JKL-012:2025-01-09:ULT-SGL:A',
									'name'                    => 'JKL-012:2025-01-09:ULT-SGL:A',
									'mask'                    => 'A',
									'availabilityStatus'      => 'O',
									'availabilityDescription' => 'Available',
									'spacesAvailable'         => 10,
									'seq'                     => '100',
									'prices'                  => [
										'USD' => [
											'pricePerPerson' => 44905,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 38169,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 70605,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 60014,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 61205,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 52024,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 41905,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 35619,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 35905,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 30519,
												],
											],
										],
									],
								],
							],
							'promotions'  => [
								'available' => [
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
						[
							'id'          => 'JKL-012:2025-01-09:ULT-DBL',
							'code'        => 'ULT-DBL',
							'name'        => 'Studio Double',
							'departureId' => 'JKL-012:2025-01-09',
							'occupancies' => [
								[
									'id'                      => 'JKL-012:2025-01-09:ULT-DBL:A',
									'name'                    => 'JKL-012:2025-01-09:ULT-DBL:A',
									'mask'                    => 'A',
									'availabilityStatus'      => 'O',
									'availabilityDescription' => 'Available',
									'spacesAvailable'         => 10,
									'seq'                     => '100',
									'prices'                  => [
										'USD' => [
											'pricePerPerson' => 74900,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 63665,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 117500,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 99875,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 102000,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 86700,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 69900,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 59415,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 59900,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 50915,
												],
											],
										],
									],
								],
								[
									'id'                      => 'JKL-012:2025-01-09:ULT-DBL:AA',
									'name'                    => 'JKL-012:2025-01-09:ULT-DBL:AA',
									'mask'                    => 'AA',
									'availabilityStatus'      => 'O',
									'availabilityDescription' => 'Available',
									'spacesAvailable'         => 10,
									'seq'                     => '100',
									'prices'                  => [
										'USD' => [
											'pricePerPerson' => 34600,
											'currencyCode'   => 'USD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 29410,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 54200,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 46070,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 47000,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 39950,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 32200,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 27370,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 27600,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'15PROMO' => [
													'promoPricePerPerson' => 23460,
												],
											],
										],
									],
								],
							],
							'promotions'  => [
								'available' => [
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
							'id'          => 'JKL-012:2026-01-16:ULT-SGL',
							'code'        => 'ULT-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'JKL-012:2026-01-16',
							'occupancies' => [
								[
									'id'                      => 'JKL-012:2026-01-16:ULT-SGL:A',
									'name'                    => 'JKL-012:2026-01-16:ULT-SGL:A',
									'mask'                    => 'A',
									'availabilityStatus'      => 'O',
									'availabilityDescription' => 'Available',
									'spacesAvailable'         => 10,
									'seq'                     => '100',
									'prices'                  => [
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
							'promotions'  => [
								'available' => [
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
					],
				],
			],
		];
	}

	// Check if data is empty.
	if ( [] === $data ) {
		return [
			'body'     => wp_json_encode( $data ),
			'response' => [
				'code'    => 201,
				'message' => 'No data',
			],
			'headers'  => [],
		];
	}

	// Return the response.
	return [
		'body'     => wp_json_encode( $data ),
		'response' => [
			'code'    => 200,
			'message' => 'Created',
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

	// Init DB object.
	$db = new Softrip_DB();

	// Get SQL array.
	$tables = $db->get_db_tables_sql();

	// Truncate tables.
	foreach ( $tables as $name => $sql ) {
		$table_name = $db->prefix_table_name( $name );
		$wpdb->query( "TRUNCATE TABLE $table_name" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
