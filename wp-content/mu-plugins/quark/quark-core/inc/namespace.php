<?php
/**
 * Namespace functions.
 *
 * @package quark-core
 */

namespace Quark\Core;

use JB\Cloudinary\Core as Cloudinary_Core;
use JB\Cloudinary\Frontend as Cloudinary_Frontend;
use WP_Post;

use function Travelopia\Core\cached_nav_menu;

const CURRENCIES = [ 'USD', 'CAD', 'AUD', 'GBP', 'EUR' ];

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Other hooks.
	add_action( 'admin_menu', __NAMESPACE__ . '\\setup_settings' );
	add_action( 'init', __NAMESPACE__ . '\\nav_menus' );
	add_filter( 'quark_front_end_data', __NAMESPACE__ . '\\core_front_end_data' );
	add_filter( 'wp_mail_from', __NAMESPACE__ . '\\default_email_from_address' );
	add_filter( 'wp_mail_from_name', __NAMESPACE__ . '\\default_email_from_name' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\reusable_blocks_menu_item' );

	// Allow Auto Cloudinary in REST API calls.
	add_action( 'rest_api_init', __NAMESPACE__ . '\\init_auto_cloudinary' );
	add_filter( 'cloudinary_allow_rest_api_call', '__return_true' );

	// Filter for Attachments.
	add_filter( 'add_attachment', __NAMESPACE__ . '\\update_svg_content', 10, 4 );
	add_filter( 'upload_mimes', __NAMESPACE__ . '\\allow_mime_types' );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/options-social.php';
		require_once __DIR__ . '/../custom-fields/attachments.php';
		require_once __DIR__ . '/../custom-fields/pages-setup.php';
	}
}

/**
 * Front-end layout.
 *
 * @return void
 */
function layout(): void {
	// Check if 404 page.
	if ( is_404() ) {
		add_filter( 'quark_front_end_data', __NAMESPACE__ . '\\layout_404' );
	}
}

/**
 * Layout: 404.
 *
 * @param mixed[] $data Front-end data.
 *
 * @return mixed[]
 */
function layout_404( array $data = [] ): array {
	// Return updated data.
	return $data;
}

/**
 * Site settings.
 *
 * @return void
 */
function setup_settings(): void {
	// Check if ACF plugin is enabled.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	// Social.
	acf_add_options_sub_page(
		[
			'page_title'  => 'Social',
			'menu_title'  => 'Social',
			'parent_slug' => 'site-settings',
			'capability'  => 'manage_options',
		]
	);

	// Pages Setup.
	acf_add_options_sub_page(
		[
			'page_title'  => 'Pages Setup',
			'menu_title'  => 'Pages Setup',
			'parent_slug' => 'site-settings',
		]
	);
}

/**
 * Register navigation menus.
 *
 * @return void
 */
function nav_menus(): void {
	// Register all nav menus.
	register_nav_menus(
		[
			'main' => 'Main Menu',
		]
	);
}

/**
 * Initialize the Auto Cloudinary plugin.
 *
 * @return void
 */
function init_auto_cloudinary(): void {
	// Check if Auto Cloudinary plugin is enabled.
	if ( class_exists( 'JB\Cloudinary\Core' ) && class_exists( 'JB\Cloudinary\Frontend' ) ) {
		// Get core instance.
		$core = Cloudinary_Core::get_instance();

		// Check if we got the instance.
		if ( $core instanceof Cloudinary_Core ) {
			$core->setup();
		}

		// Get front-end instance.
		$front_end = Cloudinary_Frontend::get_instance();

		// Check if we got the instance.
		if ( $front_end instanceof Cloudinary_Frontend ) {
			$front_end->setup();
		}
	}
}

/**
 * Get core front-end data.
 *
 * @param mixed[] $data Template data.
 *
 * @return mixed[]
 */
function core_front_end_data( array $data = [] ): array {
	// Build core data.
	$header_options = [
		'logo_url' => home_url(),
		'nav_menu' => cached_nav_menu(
			[
				'theme_location' => 'main',
				'container'      => '',
				'menu_class'     => '',
				'menu_id'        => '',
			]
		),
	];

	// Get socials links from options.
	$social_options = [
		'facebook'  => get_option( 'options_facebook_url', '' ),
		'twitter'   => get_option( 'options_twitter_url', '' ),
		'instagram' => get_option( 'options_instagram_url', '' ),
		'pinterest' => get_option( 'options_pinterest_url', '' ),
		'youtube'   => get_option( 'options_youtube_url', '' ),
	];

	// Return updated data.
	return array_merge(
		$data,
		[
			'current_url'  => get_permalink(),
			'header'       => $header_options,
			'social_links' => $social_options,
		]
	);
}

/**
 * Default "from" email address.
 *
 * @return string
 */
function default_email_from_address(): string {
	// Update default "from" email address.
	return 'noreply@quarkexpeditions.com';
}

/**
 * Default "from" name.
 *
 * @return string
 */
function default_email_from_name(): string {
	// Update default "from" name.
	return 'Quark Expeditions';
}

/**
 * Add an admin menu item for Reusable Blocks.
 *
 * @return void
 */
function reusable_blocks_menu_item(): void {
	// Add a menu page for Reusable Blocks.
	add_menu_page(
		'Reusable Blocks',
		'Reusable Blocks',
		'edit_posts',
		'edit.php?post_type=wp_block',
		'', // @phpstan-ignore-line
		'dashicons-controls-repeat',
		22
	);
}

/**
 * Get front-end data.
 *
 * All front-end data goes through this function.
 *
 * @param bool $force Force uncached version.
 *
 * @return mixed[]
 */
function get_front_end_data( bool $force = false ): array {
	// Set cached version.
	static $template_data = [];

	// Check if template data is already set and not forced.
	if ( false === $force && ! empty( $template_data ) ) {
		return $template_data;
	}

	// Get front-end data.
	$template_data = (array) apply_filters( 'quark_front_end_data', [] );

	// Return updated front-end data.
	return $template_data;
}

/**
 * Update attachment meta data for SVG.
 *
 * @param int $post_id Attachment ID.
 *
 * @return void
 */
function update_svg_content( int $post_id = 0 ): void {
	// Get Attachment.
	$post = get_post( $post_id );

	// If post is not instance of WP_Post then bail out.
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// If attachment is not SVG then bail out.
	if ( 'attachment' !== $post->post_type || 'image/svg+xml' !== $post->post_mime_type ) {
		return;
	}

	// Get attached file.
	$attached_file = get_attached_file( $post_id );

	// If Attached file is empty then bail out.
	if ( empty( $attached_file ) ) {
		return;
	}

	// Get SVG content.
	$content = file_get_contents( $attached_file );  // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	// If SVG content is empty then bail out.
	if ( empty( $content ) ) {
		return;
	}

	// Update SVG content.
	update_post_meta( $post_id, 'svg_content', wp_kses_post( $content ) );
}

/**
 * Allow mime type.
 *
 * @param array{}|array<string, string> $mime_types Mime types.
 *
 * @return array{} Mime types.
 */
function allow_mime_types( array $mime_types = [] ): array {
	// Adding svg extension.
	$mime_types['svg'] = 'image/svg+xml';

	// Return mime types.
	return $mime_types;
}

/**
 * Get a visitor's geo country.
 *
 * @note This uses Pantheon's AGCDN header which sets
 *       a two-letter ISO string for the visitor's country.
 *
 * @return string
 */
function get_visitor_geo_country(): string {
	// init geolocation.
	static $geolocation = null;

	// Return if already set.
	if ( null !== $geolocation ) {
		return $geolocation;
	}

	// Get headers.
	$headers = getallheaders();

	// Get geolocation.
	if ( ! empty( $headers['x-geocountry'] ) ) {
		$geolocation = strtoupper( sanitize_text_field( $headers['x-geocountry'] ) );
	} elseif ( ! empty( $headers['X-Geo-Country'] ) ) {
		$geolocation = strtoupper( sanitize_text_field( $headers['X-Geo-Country'] ) );
	} elseif ( ! empty( $headers['X-Geo-Country-Code'] ) ) {
		$geolocation = strtoupper( sanitize_text_field( $headers['X-Geo-Country-Code'] ) );
	} else {
		$geolocation = '';
	}

	// Return geolocation.
	return $geolocation;
}

/**
 * Check if we are currently in an automated test.
 *
 * @return bool
 */
function doing_automated_test(): bool {
	// Get automated test User Agent.
	if ( defined( 'QUARK_AUTOMATED_TEST_USER_AGENT' ) && ! empty( QUARK_AUTOMATED_TEST_USER_AGENT ) ) {
		$test_user_agent = QUARK_AUTOMATED_TEST_USER_AGENT;
	} else {
		$test_user_agent = '';
	}

	// Check if we have this option, and if it's the same as the current User Agent.
	if ( ! empty( $test_user_agent ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) && $test_user_agent === $_SERVER['HTTP_USER_AGENT'] ) {
		return true;
	}

	// Nope, user agent does not match, not an automated test.
	return false;
}

/**
 * Format price.
 *
 * @param int    $price Price.
 * @param string $currency Currency.
 *
 * @return string Formatted price.
 */
function format_price( int $price = 0, string $currency = 'USD' ): string {
	// Check if price is empty.
	if ( empty( $price ) ) {
		return '';
	}

	// Set default separators.
	$string_format = '%1$s%2$s %3$s';

	// Set Currency symbol.
	$currency_symbols = [
		'AUD' => '$',
		'CAD' => '$',
		'USD' => '$',
		'EUR' => '€',
		'GBP' => '£',
	];

	// Validate currency.
	$currency = array_key_exists( strtoupper( $currency ), $currency_symbols ) ? strtoupper( $currency ) : 'USD';

	// Current symbol.
	$currency_symbol     = $currency_symbols[ strtoupper( $currency ) ];
	$decimal_separator   = '.';
	$thousands_separator = ',';

	// Decimal separator.
	$decimals = fmod( $price, absint( $price ) ) ? 2 : 0;

	// Return formatted price.
	return sprintf(
		$string_format,
		$currency_symbol,
		number_format( $price, $decimals, $decimal_separator, $thousands_separator ),
		$currency
	);
}

/**
 * Get currencies.
 *
 * @return string[]
 */
function get_currencies(): array {
	// Return the currencies.
	return CURRENCIES;
}
