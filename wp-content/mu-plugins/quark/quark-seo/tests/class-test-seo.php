<?php
/**
 * SEO test suite.
 *
 * @package quark-seo
 */

namespace Quark\SEO\Tests;

use WP_UnitTestCase;

use function Quark\SEO\custom_robots_txt;
use function Quark\SEO\get_structured_data;
use function Quark\SEO\set_canonical_scheme;

/**
 * Class Test_SEO.
 */
class Test_SEO extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\SEO\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test hook.
		$this->assertSame( 999999, has_filter( 'robots_txt', 'Quark\SEO\custom_robots_txt' ) );
	}

	/**
	 * Test breadcrumbs.
	 *
	 * @covers \Quark\SEO\custom_robots_txt()
	 *
	 * @return void
	 */
	public function test_custom_robots_txt(): void {
		// Update option.
		update_option( 'options_seo_robots_txt', 'User-agent: *' );

		// Test hook.
		$this->assertEquals( 'User-agent: *', custom_robots_txt() );
		$this->assertEquals( 'User-agent: *', apply_filters( 'robots_txt', '' ) );
	}

	/**
	 * Test Get Structured Data.
	 *
	 * @covers \Quark\SEO\seo_structured_data()
	 *
	 * @return void
	 */
	public function test_seo_structured_datas(): void {
		// Add social media links.
		update_option( 'options_facebook_url', 'https://www.facebook.com/QuarkExpeditions' );
		update_option( 'options_twitter_url', 'https://twitter.com/quarkexpedition' );
		update_option( 'options_instagram_url', 'https://www.instagram.com/quarkexpeditions' );
		update_option( 'options_pinterest_url', '' );
		update_option( 'options_youtube_url', 'https://www.youtube.com/user/QuarkExpeditions' );

		// Get structured data.
		$actual = get_structured_data();

		// Prepare expected.
		$expected = [
			'@context' => 'https://schema.org',
			'@graph'   => [
				[
					'@type'          => 'Organization',
					'additionalType' => 'Corporation',
					'@id'            => get_home_url(),
					'description'    => 'Quark Expeditions is uncompromisingly polar, specializing in expeditions to the Antarctic and the Arctic. We have been the leading provider of polar adventure travel for over 25 years.',
					'name'           => 'Quark Expeditions',
					'sameAs'         => [
						'https://www.facebook.com/QuarkExpeditions',
						'https://twitter.com/quarkexpedition',
						'https://www.instagram.com/quarkexpeditions',
						'https://www.youtube.com/user/QuarkExpeditions',
					],
					'url'            => get_home_url(),
					'telephone'      => '+1-416-504-5900',
					'contactPoint'   => [
						[
							'@type'             => 'ContactPoint',
							'telephone'         => [
								'+1-888-979-4073',
								'+1-802-490-1843',
							],
							'email'             => 'explore@quarkexpeditions.com',
							'contactType'       => 'Sales',
							'availableLanguage' => [
								'English',
								'Spanish',
								'French',
								'Chinese/Mandarin',
							],
							'contactOption'     => 'TollFree',
							'areaServed'        => [
								'@type'   => 'AdministrativeArea',
								'address' => [
									'@type'          => 'PostalAddress',
									'addressCountry' => [
										'US',
										'CA',
										'AU',
										'GB',
									],
								],
							],
						],
					],
					'address'        => [
						'@type'           => 'PostalAddress',
						'streetAddress'   => [
							'3131 Elliot Avenue',
							'Suite 250',
						],
						'addressLocality' => 'Seattle',
						'addressRegion'   => 'WA',
						'postalCode'      => '98121',
						'addressCountry'  => 'US',
					],
				],
			],
		];

		// Test schema.
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test set_canonical_scheme.
	 *
	 * @covers \Quark\SEO\set_canonical_scheme()
	 *
	 * @return void
	 */
	public function test_set_canonical_scheme(): void {
		// Test empty.
		$this->assertEquals( '', set_canonical_scheme() );

		// Test http.
		$this->assertEquals( 'https://example.com', set_canonical_scheme( 'http://example.com' ) );

		// Test https.
		$this->assertEquals( 'https://example.com', set_canonical_scheme( 'https://example.com' ) );
	}
}
