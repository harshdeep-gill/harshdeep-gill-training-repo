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

	// Custom fields. Only enable main site.
	if ( is_admin() && 1 === get_current_blog_id() ) {
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
