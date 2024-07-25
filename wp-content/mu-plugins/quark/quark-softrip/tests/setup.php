<?php
/**
 * Softrip test functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

/**
 * Setup Softrip DB.
 *
 * @return void
 */
function setup_softrip_db(): void {
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
	$tables = $db->get_db_tables();

	// Start table creation.
	foreach ( $tables as $name => $sql ) {
		$table_name = $db->prefix_table_name( $name );
		maybe_create_table( $table_name, $sql );
	}

	// Flag as run.
	$run = true;
}

/**
 * Setup Softrip DB.
 *
 * @return void
 */
function tear_down_softrip_db(): void {
	// Get global WPDB object.
	global $wpdb;

	// Init DB object.
	$db = new Softrip_DB();

	// Get SQL array.
	$tables = $db->get_db_tables();

	// Truncate tables.
	foreach ( $tables as $name => $sql ) {
		$table_name = $db->prefix_table_name( $name );
		$wpdb->query( "TRUNCATE TABLE $table_name" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
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
function mock_http_request( array|false $response = [], array $parsed_args = [], string $url = null ): false|array {
	// Check if the URL is the one we want to mock.
	if ( 'http://localhost.com/departures' === $url ) {
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
						'id'          => 'ABC-123:2026-01-09',
						'code'        => 'OEX20260109',
						'packageCode' => 'ABC-123',
						'startDate'   => '2026-01-09',
						'endDate'     => '2026-01-19',
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
										'price'                   => [
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

		// Check if data is empty.
		if ( empty( $data ) ) {
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

	// Return the response.
	return $response;
}
