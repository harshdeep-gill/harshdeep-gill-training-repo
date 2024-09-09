<?php
/**
 * Test suite for localization namespace functions.
 *
 * @package quark-localization
 */

namespace Quark\Localization\Tests;

use WP_UnitTestCase;

use function Quark\Localization\get_current_currency;

use const Quark\Localization\CURRENCY_COOKIE;

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
		$_COOKIE[ CURRENCY_COOKIE ] = 'CAD';
		$this->assertEquals( 'CAD', get_current_currency() );

		// Test invalid currency cookie.
		$_COOKIE[ CURRENCY_COOKIE ] = 'INVALID';
		$this->assertEquals( 'USD', get_current_currency() );

		// Test with lower case currency.
		$_COOKIE[ CURRENCY_COOKIE ] = 'cad';
		$this->assertEquals( 'CAD', get_current_currency() );

		// Invalid currency cookie.
		$_COOKIE[ CURRENCY_COOKIE ] = 34;
		$this->assertEquals( 'USD', get_current_currency() );

		// Invalid currency cookie.
		$_COOKIE[ CURRENCY_COOKIE ] = [ 'CAD' ];
		$this->assertEquals( 'USD', get_current_currency() );

		// Cookie with special characters.
		$_COOKIE[ CURRENCY_COOKIE ] = 'CAD$';
		$this->assertEquals( 'USD', get_current_currency() );

		// Cookie with html.
		$_COOKIE[ CURRENCY_COOKIE ] = 'CAD<script>alert("XSS")</script>';
		$this->assertEquals( 'CAD', get_current_currency() );

		// Cleanup.
		unset( $_COOKIE[ CURRENCY_COOKIE ] );
	}
}
