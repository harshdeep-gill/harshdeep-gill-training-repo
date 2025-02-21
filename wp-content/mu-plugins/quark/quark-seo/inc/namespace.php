<?php
/**
 * Namespace functions.
 *
 * @package quark-seo
 */

namespace Quark\SEO;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Hooks.
	add_filter( 'robots_txt', __NAMESPACE__ . '\\custom_robots_txt', 999999 ); // Override Yoast SEO hooked at 99999.
	add_filter( 'travelopia_seo_structured_data_schema', __NAMESPACE__ . '\\seo_structured_data' );

	// Set https scheme for canonical.
	add_filter( 'wpseo_canonical', __NAMESPACE__ . '\\set_canonical_scheme' );

	// Custom fields.
	if ( is_admin() ) {
		// ACF options page.
		add_action( 'admin_menu', __NAMESPACE__ . '\\setup_settings' );

		// Custom fields.
		require_once __DIR__ . '/../custom-fields/options-seo.php';
	}
}

/**
 * Site settings.
 *
 * @return void
 */
function setup_settings(): void {
	// Check if ACF is active.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	// Add options page.
	acf_add_options_page(
		[
			'page_title'  => 'SEO',
			'menu_title'  => 'SEO',
			'parent_slug' => 'site-settings',
			'capability'  => 'manage_options',
		]
	);
}

/**
 * Add a custom robots.txt value.
 *
 * @param string $robots_txt Original robots.txt value.
 *
 * @return string
 */
function custom_robots_txt( string $robots_txt = '' ): string {
	// Get custom robots.txt value.
	$custom_robots_txt = get_option( 'options_seo_robots_txt' );

	// Check if custom robots.txt value has been added.
	if ( ! empty( $custom_robots_txt ) ) {
		return strval( $custom_robots_txt );
	}

	// Return custom robots.txt value.
	return $robots_txt;
}

/**
 * Build structured data for schema.
 *
 * @param mixed[] $schema All schema data.
 *
 * @return mixed[]
 */
function seo_structured_data( array $schema = [] ): array {
	// Check if this is homepage.
	if ( ! is_front_page() ) {
		return $schema;
	}

	// Get and insert the schema.
	$schema[] = get_structured_data();

	// Return the schema.
	return $schema;
}

/**
 * Get structured data for schema.
 *
 * @return array{
 *    "@context": string,
 *    "@graph": array{
 *        "@type": string,
 *        additionalType: string,
 *        "@id": string,
 *        description: string,
 *        name: string,
 *        sameAs: string[],
 *        url: string,
 *        telephone: string,
 *        contactPoint: array{
 *            "@type": string,
 *            telephone: string[],
 *            email: string,
 *            contactType: string,
 *            availableLanguage: string[],
 *            contactOption: string,
 *            areaServed: array{
 *                "@type": string,
 *                address: array{
 *                    "@type": string,
 *                    addressCountry: string[],
 *                },
 *            },
 *        }[],
 *        address: array{
 *            "@type": string,
 *            streetAddress: string[],
 *            addressLocality: string,
 *            addressRegion: string,
 *            postalCode: string,
 *            addressCountry: string,
 *        },
 *    }[],
 * }
 */
function get_structured_data(): array {
	// Get social links.
	$social_links = [
		strval( get_option( 'options_facebook_url' ) ),
		strval( get_option( 'options_twitter_url' ) ),
		strval( get_option( 'options_instagram_url' ) ),
		strval( get_option( 'options_pinterest_url' ) ),
		strval( get_option( 'options_youtube_url' ) ),
	];
	$social_links = array_values( array_filter( $social_links ) );

	// TODO:: Verify the hardcoded details for the organization.
	// TODO:: - For multisite site we need to get the details from the site options.
	// Return schema.
	return [
		'@context' => 'https://schema.org',
		'@graph'   => [
			[
				'@type'          => 'Organization',
				'additionalType' => 'Corporation',
				'@id'            => get_home_url(),
				'description'    => 'Quark Expeditions is uncompromisingly polar, specializing in expeditions to the Antarctic and the Arctic. We have been the leading provider of polar adventure travel for over 25 years.',
				'name'           => 'Quark Expeditions',
				'sameAs'         => $social_links,
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
}

/**
 * Set https scheme for canonical.
 *
 * @param string $canonical Original canonical URL.
 *
 * @return string
 */
function set_canonical_scheme( string $canonical = '' ): string {
	// Check if canonical URL is not empty and starts with "http://".
	if ( ! empty( $canonical ) && str_starts_with( $canonical, 'http://' ) ) {
		// Replace "http" with "https".
		$canonical = str_replace( 'http://', 'https://', $canonical );
	}

	// Return canonical URL.
	return $canonical;
}
