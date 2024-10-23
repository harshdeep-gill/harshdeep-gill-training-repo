<?php
/**
 * Test suite for checkout functionality.
 *
 * @package quark-checkout
 */

namespace Quark\Tests\Checkout;

use WP_UnitTestCase;

use function Quark\Checkout\get_checkout_url;

use const Quark\CabinCategories\POST_TYPE as CABIN_POST_TYPE;
use const Quark\Departures\POST_TYPE as POST_TYPE_DEPARTURE;
use const Quark\Localization\USD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;

/**
 * Class Test_Checkout
 */
class Test_Checkout extends WP_UnitTestCase {
	/**
	 * Test get_checkout_url function.
	 *
	 * @covers \Quark\Checkout\get_checkout_url
	 *
	 * @return void
	 */
	public function test_get_checkout_url(): void {
		// Check if constant is defined.
		$this->assertTrue( defined( 'QUARK_CHECKOUT_BASE_URL' ) );

		// Default checkout url.
		$default_checkout_url = QUARK_CHECKOUT_BASE_URL;

		// Check if not empty.
		$this->assertNotEmpty( $default_checkout_url );

		// Test with no arg.
		$actual = get_checkout_url();
		$this->assertEquals( $default_checkout_url, $actual );

		// Test with invalid departure post ID.
		$actual = get_checkout_url( 99 );
		$this->assertEquals( $default_checkout_url, $actual );

		// Create a departure post.
		$departure_post_id = $this->factory()->post->create(
			[
				'post_type' => POST_TYPE_DEPARTURE,
			]
		);
		$this->assertIsInt( $departure_post_id );

		// Test with valid departure post ID, but without cabin post ID.
		$actual = get_checkout_url( $departure_post_id );
		$this->assertEquals( $default_checkout_url, $actual );

		// Create a cabin post.
		$cabin_post_id = $this->factory()->post->create(
			[
				'post_type' => CABIN_POST_TYPE,
			]
		);
		$this->assertIsInt( $cabin_post_id );

		// Test with valid departure post ID and cabin post ID, but without any meta.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id );
		$this->assertEquals( $default_checkout_url, $actual );

		// Test with invalid currency.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, 'XYZ' );
		$this->assertEquals( $default_checkout_url, $actual );

		// Test with valid departure post ID, cabin post ID and currency, but without any valid meta.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, USD_CURRENCY );
		$this->assertEquals( $default_checkout_url, $actual );

		// Add softrip_package_code meta to departure post.
		update_post_meta( $departure_post_id, 'softrip_package_code', 'UNQ-123' );

		// Test with valid departure post ID, cabin post ID and currency but without start date.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, USD_CURRENCY );
		$this->assertEquals( $default_checkout_url, $actual );

		// Add start date to departure post.
		update_post_meta( $departure_post_id, 'start_date', '2021-01-01' );

		// Test with valid departure post ID, cabin post ID and currency.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, USD_CURRENCY );
		$this->assertEquals( $default_checkout_url, $actual );

		// Add cabin_category_id meta to cabin post.
		update_post_meta( $cabin_post_id, 'cabin_category_id', 'CAB-123' );

		// Flush cache.
		wp_cache_flush();

		// Test with valid departure post ID, cabin post ID and currency.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, USD_CURRENCY );
		$this->assertSame( 'https://local-checkout.quarkexpeditions.com?package_id=UNQ-123&departure_date=2021-01-01&cabin_code=CAB-123&currency=USD', $actual );

		// Test with smaller case currency.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, USD_CURRENCY );
		$this->assertSame( 'https://local-checkout.quarkexpeditions.com?package_id=UNQ-123&departure_date=2021-01-01&cabin_code=CAB-123&currency=USD', $actual );

		// Test with other currency.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, CAD_CURRENCY );
		$this->assertSame( 'https://local-checkout.quarkexpeditions.com?package_id=UNQ-123&departure_date=2021-01-01&cabin_code=CAB-123&currency=CAD', $actual );

		// Test with other currency.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, EUR_CURRENCY );
		$this->assertSame( 'https://local-checkout.quarkexpeditions.com?package_id=UNQ-123&departure_date=2021-01-01&cabin_code=CAB-123&currency=EUR', $actual );

		// Test with GBP currency - restricted.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, GBP_CURRENCY );
		$this->assertSame( '', $actual );

		// Update departure post for start date.
		wp_update_post(
			[
				'ID'         => $departure_post_id,
				'meta_input' => [
					'start_date' => '2021-01-02',
				],
			]
		);

		// Test if checkout url changes on departure post update.
		$actual = get_checkout_url( $departure_post_id, $cabin_post_id, USD_CURRENCY );
		$this->assertSame( 'https://local-checkout.quarkexpeditions.com?package_id=UNQ-123&departure_date=2021-01-02&cabin_code=CAB-123&currency=USD', $actual );
	}
}
