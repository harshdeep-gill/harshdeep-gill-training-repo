<?php
/**
 * Core test suite.
 *
 * @package quark-core
 */

namespace Quark\Core;

use WP_UnitTestCase;
use WP_Term;
use WP_Post;

use const Quark\Localization\USD_CURRENCY;
use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\DEFAULT_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;

use const Quark\Brochures\POST_TYPE as BROCHURE_POST_TYPE;

/**
 * Class Test_Core.
 */
class Test_Core extends WP_UnitTestCase {

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\Core\get_front_end_data()
	 *
	 * @return void
	 */
	public function test_get_front_end_data(): void {
		// No data.
		$original_data = [
			'header'               => [
				'logo_url' => 'http://test.quarkexpeditions.com',
				'nav_menu' => "<div></div>\n",
			],
			'social_links'         => [
				'facebook'  => '',
				'twitter'   => '',
				'instagram' => '',
				'pinterest' => '',
				'youtube'   => '',
			],
			'leads_api_endpoint'   => 'http://test.quarkexpeditions.com/wp-json/quark-leads/v1/leads/create',
			'current_url'          => false,
			'dynamic_phone_number' => [
				'api_endpoint'         => 'http://test.quarkexpeditions.com/wp-json/qrk-phone-numbers/v1/phone-number/get',
				'default_phone_number' => '+1234567890',
			],
			'currencies'           => [
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
			'default_currency'     => DEFAULT_CURRENCY,
			'filters_api_url'      => home_url( 'wp-json/quark-search/v1/filter-options/by-destination-and-month' ),
			'search_page_url'      => '',
			'site_url'             => 'http://test.quarkexpeditions.com',
			'site_name'            => 'Quark',
		];

		// Update default phone number.
		update_option( 'options_default_phone_number', '+1234567890' );

		// Test front-end data.
		$this->assertEquals(
			$original_data,
			\Quark\Core\get_front_end_data()
		);

		// Test layout and data.
		$test_data = [
			'key' => 'value',
		];
		add_filter( 'quark_front_end_data', fn () => $test_data );

		// Original data without force.
		$this->assertEquals(
			$original_data,
			\Quark\Core\get_front_end_data()
		);

		// Test data with force.
		$this->assertEquals(
			$test_data,
			\Quark\Core\get_front_end_data( true )
		);
	}

	/**
	 * Test getting front-end data.
	 *
	 * @covers \Quark\Core\get_front_end_data()
	 * @covers \Quark\Core\core_front_end_data()
	 *
	 * @return void
	 */
	public function test_core_front_end_data(): void {
		// Prepare data.
		update_option( 'options_facebook_url', 'https://facebook.com' );
		update_option( 'options_twitter_url', 'https://twitter.com' );
		update_option( 'options_instagram_url', 'https://instagram.com' );
		update_option( 'options_pinterest_url', 'https://pinterest.com' );
		update_option( 'options_youtube_url', 'https://youtube.com' );

		// Get data.
		$data = get_front_end_data( true );

		// Test data.
		$this->assertEquals(
			[
				'logo_url' => 'http://test.quarkexpeditions.com',
				'nav_menu' => "<div></div>\n",
			],
			$data['header']
		);

		// Assert expected social links and actual social links are equal.
		$this->assertEquals(
			[
				'facebook'  => 'https://facebook.com',
				'twitter'   => 'https://twitter.com',
				'instagram' => 'https://instagram.com',
				'pinterest' => 'https://pinterest.com',
				'youtube'   => 'https://youtube.com',
			],
			$data['social_links'] ?? []
		);
	}

	/**
	 * Test nav menus.
	 *
	 * @covers \Quark\Core\nav_menus()
	 *
	 * @return void
	 */
	public function test_nav_menus(): void {
		// Test navigation menus.
		$this->assertEquals(
			[
				'main' => 'Main Menu',
			],
			get_registered_nav_menus()
		);
	}

	/**
	 * Test doing automated tests.
	 *
	 * @covers \Quark\Core\doing_automated_test()
	 *
	 * @return void
	 */
	public function test_doing_automated_test(): void {
		// Test default state.
		$this->assertFalse( doing_automated_test() );

		// Define config.
		$_SERVER['HTTP_USER_AGENT'] = 'TEST_USER_AGENT';
		define( 'QUARK_AUTOMATED_TEST_USER_AGENT', 'TEST_USER_AGENT' );

		// Test user agent.
		$this->assertTrue( doing_automated_test() );

		// Clean up.
		$_SERVER['HTTP_USER_AGENT'] = '';
	}

	/**
	 * Test format_price.
	 *
	 * @covers \Quark\Core\format_price()
	 *
	 * @return void
	 */
	public function test_format_price(): void {
		// Test price formatting.
		$this->assertEquals(
			'$1,000 USD',
			format_price( 1000 )
		);

		// Test price formatting.
		$this->assertEquals(
			'$1,000.10 USD',
			format_price( 1000.10 )
		);

		// Test price formatting with custom currency.
		$this->assertEquals(
			'€10,000 EUR',
			format_price( 10000, 'eur' )
		);

		// Test price formatting with custom currency.
		$this->assertEquals(
			'£100,000 GBP',
			format_price( 100000, 'GBP' )
		);
	}

	/**
	 * Test order_terms_by_hierarchy.
	 *
	 * @covers \Quark\Core\order_terms_by_hierarchy()
	 *
	 * @return void
	 */
	public function test_order_terms_by_hierarchy(): void {
		// Create a taxonomy.
		register_taxonomy(
			'test_taxonomy',
			'post',
			[
				'labels' => [
					'name' => 'Test Taxonomy',
				],
			]
		);

		// Create parent term 1.
		$parent_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'test_taxonomy',
			]
		);
		$this->assertTrue( $parent_term_1 instanceof WP_Term );

		// Create parent term 2.
		$parent_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'test_taxonomy',
			]
		);
		$this->assertTrue( $parent_term_2 instanceof WP_Term );

		// Create child term 1.
		$child_term_1 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'test_taxonomy',
				'parent'   => $parent_term_1->term_id,
			]
		);
		$this->assertTrue( $child_term_1 instanceof WP_Term );

		// Create child term 2.
		$child_term_2 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'test_taxonomy',
				'parent'   => $parent_term_1->term_id,
			]
		);
		$this->assertTrue( $child_term_2 instanceof WP_Term );

		// Create child term 3.
		$child_term_3 = $this->factory()->term->create_and_get(
			[
				'taxonomy' => 'test_taxonomy',
				'parent'   => $parent_term_2->term_id,
			]
		);
		$this->assertTrue( $child_term_3 instanceof WP_Term );

		// Assign the child terms to the parent terms.
		wp_set_object_terms(
			$parent_term_1->term_id,
			[
				$child_term_1->term_id,
				$child_term_2->term_id,
			],
			'test_taxonomy'
		);
		wp_set_object_terms(
			$parent_term_2->term_id,
			[
				$child_term_3->term_id,
			],
			'test_taxonomy'
		);

		// Assert the function returns the correct organised terms.
		$this->assertEquals(
			[
				$parent_term_1->term_id => [
					'parent_term' => $parent_term_1,
					'child_terms' => [
						$child_term_1,
						$child_term_2,
					],
				],
				$parent_term_2->term_id => [
					'parent_term' => $parent_term_2,
					'child_terms' => [
						$child_term_3,
					],
				],
			],
			order_terms_by_hierarchy(
				[
					$parent_term_1->term_id,
					$parent_term_2->term_id,
					$child_term_1->term_id,
					$child_term_2->term_id,
					$child_term_3->term_id,
				],
				'test_taxonomy'
			)
		);

		// Clean up.
		wp_delete_term( $parent_term_1->term_id, 'test_taxonomy' );
		wp_delete_term( $parent_term_2->term_id, 'test_taxonomy' );
		wp_delete_term( $child_term_1->term_id, 'test_taxonomy' );
		wp_delete_term( $child_term_2->term_id, 'test_taxonomy' );
		wp_delete_term( $child_term_3->term_id, 'test_taxonomy' );
		unregister_taxonomy( 'test_taxonomy' );
	}

	/**
	 * Test limit_revisions_for_posts.
	 *
	 * @covers \Quark\Core\limit_revisions_for_posts()
	 *
	 * @return void
	 */
	public function test_limit_revisions_for_posts(): void {
		// Create a post.
		$post = $this->factory()->post->create_and_get();

		// Assert the $post.
		$this->assertTrue( $post instanceof WP_Post );

		// Assert the post has the default number of revisions.
		$this->assertEquals(
			5,
			wp_revisions_to_keep( $post )
		);

		// Assert the post has the correct number of revisions.
		$this->assertEquals(
			5,
			wp_revisions_to_keep( $post )
		);

		// Create A post of Brochure type.
		$brochure_post = $this->factory()->post->create_and_get(
			[
				'post_type' => BROCHURE_POST_TYPE,
			]
		);

		// Assert the $brochure_post.
		$this->assertTrue( $brochure_post instanceof WP_Post );

		// Assert the post has the default number of revisions.
		$this->assertEquals(
			0,
			wp_revisions_to_keep( $brochure_post )
		);
	}

	/**
	 * Test get raw text from HTML.
	 *
	 * @covers \Quark\Core\get_raw_text_from_html()
	 *
	 * @return void
	 */
	public function test_get_raw_text_from_html(): void {
		// Default expected.
		$default_expected = '';

		// Test without arguments.
		$this->assertEquals(
			$default_expected,
			get_raw_text_from_html()
		);

		// Test with empty string.
		$this->assertEquals(
			$default_expected,
			get_raw_text_from_html( '' )
		);

		// Test with plain text.
		$this->assertEquals(
			'Hello World',
			get_raw_text_from_html( 'Hello World' )
		);

		// Prepare a HTML.
		$html     = '<div>Hello <strong>World</strong></div>';
		$expected = 'Hello World';
		$actual   = get_raw_text_from_html( $html );
		$this->assertSame( $expected, $actual );

		// Prepare HTML with line breaks.
		$html     = "<div>Hello\nWorld</div>";
		$expected = 'Hello World';
		$actual   = get_raw_text_from_html( $html );
		$this->assertSame( $expected, $actual );

		// Prepare HTML with entities.
		$html     = 'Hello&nbsp;World &amp; Universe';
		$expected = 'Hello World & Universe';
		$this->assertEquals(
			$expected,
			get_raw_text_from_html( $html )
		);

		// Prepare HTML with special characters.
		$html     = '<p>© 2024 Company</p>';
		$expected = '© 2024 Company';
		$this->assertEquals(
			$expected,
			get_raw_text_from_html( $html )
		);

		// Prepare HTML with mixed encoding.
		$html     = "<div>Â<div>¡<div>\xC2\xA1Hola! &#9731;</div>";
		$expected = 'Â¡¡Hola! ☃';
		$this->assertEquals(
			$expected,
			get_raw_text_from_html( $html )
		);

		// Prepare HTML with broken HTML.
		$html     = '<div><span>Broken HTML';
		$expected = 'Broken HTML';
		$this->assertEquals(
			$expected,
			get_raw_text_from_html( $html )
		);

		// Prepare HTML with nested tags.
		$html     = '<div><span>Nested <b>Tags</b></span></div>';
		$expected = 'Nested Tags';
		$this->assertEquals(
			$expected,
			get_raw_text_from_html( $html )
		);
	}
}
