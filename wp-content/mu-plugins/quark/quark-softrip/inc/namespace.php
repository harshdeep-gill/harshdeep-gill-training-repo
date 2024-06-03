<?php
/**
 * Namespace functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_CLI;

const BASE_URL = 'https://softrip-data-adapter.travelopia.dev/';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Hooks.
	add_action( 'admin_menu', __NAMESPACE__ . '\\setup_settings' );

	// Custom fields.
	if ( is_admin() ) {
		require_once __DIR__ . '/../custom-fields/options-softrip.php';
	}

	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-softrip db', __NAMESPACE__ . '\\WP_CLI\\DB' );
	}
}

/**
 * Site settings.
 *
 * @return void
 */
function setup_settings(): void {
	// Check ACF is loaded.
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	// Pages setup settings.
	acf_add_options_sub_page(
		[
			'page_title'  => 'Softrip',
			'menu_title'  => 'Softrip',
			'parent_slug' => 'site-settings',
		]
	);
}
