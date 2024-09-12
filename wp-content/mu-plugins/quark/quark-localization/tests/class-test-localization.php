<?php
/**
 * Test suite for localization namespace functions.
 *
 * @package quark-localization
 */

namespace Quark\Localization\Tests;

use WP_UnitTestCase;

use function Quark\Localization\front_end_data;
use function Quark\Localization\get_currencies;
use function Quark\Localization\get_current_currency;
use function Quark\Localization\set_current_currency;

use const Quark\Localization\CURRENCY_COOKIE;
use const Quark\Localization\USD_CURRENCY;
use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\DEFAULT_CURRENCY;

/**
 * Class Test_Localization
 */
class Test_Localization extends WP_UnitTestCase {
	/**
	 * Test get_current_currency function.
	 *
	 * @covers Quark\Localization\get_current_currency
	 *
	 * @return void
	 */
	public function test_get_current_currency(): void {
		// Test default currency.
		$this->assertEquals( 'USD', get_current_currency() );

		// Test currency cookie.
		set_current_currency( 'CAD' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Test invalid currency cookie.
		set_current_currency( 'XYZ' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Test with lower case currency.
		set_current_currency( 'cad' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Invalid currency cookie.
		set_current_currency();
		$this->assertEquals( 'CAD', get_current_currency() );

		// Cookie with special characters.
		$_COOKIE[ CURRENCY_COOKIE ] = 'CAD$';
		$this->assertEquals( 'USD', get_current_currency() );

		// Cleanup.
		unset( $_COOKIE[ CURRENCY_COOKIE ] );
	}

	/**
	 * Test get currency.
	 *
	 * @covers Quark\Localization\get_currencies
	 *
	 * @return void
	 */
	public function test_get_currencies(): void {
		// Test.
		$currencies = get_currencies();
		$this->assertSame(
			[
				USD_CURRENCY,
				AUD_CURRENCY,
				CAD_CURRENCY,
				EUR_CURRENCY,
				GBP_CURRENCY,
			],
			$currencies
		);
	}

	/**
	 * Test frontend data.
	 *
	 * @covers Quark\Localization\front_end_data
	 *
	 * @return void
	 */
	public function test_front_end_data(): void {
		// Test.
		$expected = [
			'currencies'       => [
				USD_CURRENCY => [
					'symbol'  => '$',
					'display' => 'USD',
				],
				AUD_CURRENCY => [
					'symbol'  => '$',
					'display' => 'AUD',
				],
				CAD_CURRENCY => [
					'symbol'  => '$',
					'display' => 'CAD',
				],
				EUR_CURRENCY => [
					'symbol'  => '€',
					'display' => 'EUR',
				],
				GBP_CURRENCY => [
					'symbol'  => '£',
					'display' => 'GBP',
				],
			],
			'default_currency' => DEFAULT_CURRENCY,
		];
		$actual   = front_end_data();
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test set current currency.
	 *
	 * @covers Quark\Localization\set_current_currency
	 *
	 * @return void
	 */
	public function test_set_current_currency(): void {
		// Test.
		set_current_currency( 'CAD' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Test invalid currency.
		set_current_currency( 'XYZ' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Test with lower case currency.
		set_current_currency( 'cad' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Invalid currency cookie.
		set_current_currency();
		$this->assertEquals( 'CAD', get_current_currency() );

		// Cookie with special characters.
		set_current_currency( 'CAD$' );
		$this->assertEquals( 'CAD', get_current_currency() );

		// Test with GBP.
		set_current_currency( 'GBP' );
		$this->assertEquals( 'GBP', get_current_currency() );

		// Test with EUR.
		set_current_currency( 'EUR' );
		$this->assertEquals( 'EUR', get_current_currency() );

		// Cleanup.
		unset( $_COOKIE[ CURRENCY_COOKIE ] );
	}
}
