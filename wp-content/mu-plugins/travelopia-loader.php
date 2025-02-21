<?php
/**
 * Plugin Name: MU Plugin Loader
 * Description: Loads the MU plugins required to run the site
 * Author: Travelopia Team
 * Author URI: https://www.travelopia.com
 * Version: 1.0
 *
 * Forked from https://github.com/humanmade/wp-api-demo/blob/master/content/plugins-mu/loader.php
 *
 * @package travelopia-loader
 */

if ( defined( 'WP_INSTALLING' ) && true === WP_INSTALLING ) {
	return;
}

// Check if sync disable constant is defined.
if ( ! defined( 'QUARK_SOFTRIP_SYNC_DISABLE' ) ) {
	// Get option.
	$is_softrip_sync_disabled = (bool) get_option( 'options_disable_softrip_sync', false );

	// Define Softrip sync disable constant.
	define( 'QUARK_SOFTRIP_SYNC_DISABLE', $is_softrip_sync_disabled );
}

// Check if ingestor push disable constant is defined.
if ( ! defined( 'QUARK_INGESTOR_PUSH_DISABLE' ) ) {
	// Get option.
	$is_ingestor_push_disabled = (bool) get_option( 'options_disable_ingestor_push', false );

	// Define Ingestor push disable constant.
	define( 'QUARK_INGESTOR_PUSH_DISABLE', $is_ingestor_push_disabled );
}

$tp_mu_plugins = array(

	// load mu-plugins here.
	'travelopia/travelopia-core/plugin.php',
	'travelopia/travelopia-security/plugin.php',
	'travelopia/travelopia-seo/plugin.php',
	'travelopia/travelopia-breadcrumbs/plugin.php',
	'travelopia/travelopia-media/plugin.php',
	'travelopia/travelopia-users/plugin.php',
	'travelopia/travelopia-tracking/plugin.php',
	'travelopia/travelopia-dynamic-permalinks/plugin.php',
	'travelopia/travelopia-salesforce/plugin.php',
	'travelopia/travelopia-cache/plugin.php',
	'travelopia/travelopia-multilingual/plugin.php',
	'travelopia/travelopia-translation/plugin.php',
	'wordpress-blade/plugin.php',
	'quark/quark-core/plugin.php',
	'quark/quark-pages/plugin.php',
	'quark/quark-landing-pages/plugin.php',
	'quark/quark-leads/plugin.php',
	'quark/quark-press-releases/plugin.php',
	'quark/quark-brochures/plugin.php',
	'quark/quark-blog/plugin.php',
	'quark/quark-expeditions/plugin.php',
	'quark/quark-itineraries/plugin.php',
	'quark/quark-departures/plugin.php',
	'quark/quark-exclusion-sets/plugin.php',
	'quark/quark-inclusion-sets/plugin.php',
	'quark/quark-itinerary-days/plugin.php',
	'quark/quark-ships/plugin.php',
	'quark/quark-cabin-categories/plugin.php',
	'quark/quark-ports/plugin.php',
	'quark/quark-staff-members/plugin.php',
	'quark/quark-ship-decks/plugin.php',
	'quark/quark-adventure-options/plugin.php',
	'quark/quark-offers/plugin.php',
	'quark/quark-regions/plugin.php',
	'quark/quark-policy-pages/plugin.php',
	'quark/quark-softrip/plugin.php',
	'quark/quark-office-phone-numbers/plugin.php',
	'quark/quark-search/plugin.php',
	'quark/quark-checkout/plugin.php',
	'quark/quark-localization/plugin.php',
	'quark/quark-ingestor/plugin.php',
	'quark/quark-seo/plugin.php',
	'quark/quark-tracking/plugin.php',
	'quark/quark-cache/plugin.php',
	'quark/quark-china/plugin.php',
	'quark/quark-multilingual/plugin.php',
);

foreach ( $tp_mu_plugins as $file ) {
	require_once WPMU_PLUGIN_DIR . '/' . $file;
}
unset( $file );

add_action(
	'pre_current_active_plugins',
	function () use ( $tp_mu_plugins ) {
		global $plugins, $wp_list_table;

		// Add our own mu-plugins to the page.
		foreach ( $tp_mu_plugins as $plugin_file ) {
			$plugin_data = get_plugin_data( WPMU_PLUGIN_DIR . "/$plugin_file", false, false ); // Do not apply markup/translate as it'll be cached.

			if ( empty( $plugin_data['Name'] ) ) {
				$plugin_data['Name'] = $plugin_file;
			}

			$plugins['mustuse'][ $plugin_file ] = $plugin_data; // phpcs:ignore
		}

		// Recount totals.
		$GLOBALS['totals']['mustuse'] = count( $plugins['mustuse'] ); // phpcs:ignore

		// Only apply the rest if we're actually looking at the page.
		if ( 'mustuse' !== $GLOBALS['status'] ) {
			return;
		}

		// Reset the list table's data.
		$wp_list_table->items = $plugins['mustuse'];
		foreach ( $wp_list_table->items as $plugin_file => $plugin_data ) {
			$wp_list_table->items[ $plugin_file ] = _get_plugin_data_markup_translate( $plugin_file, $plugin_data, false, true );
		}

		$total_this_page = $GLOBALS['totals']['mustuse'];

		if ( $GLOBALS['orderby'] ) {
			uasort( $wp_list_table->items, array( $wp_list_table, '_order_callback' ) );
		}

		// Force showing all plugins.
		// See https://core.trac.wordpress.org/ticket/27110.
		$plugins_per_page = $total_this_page;

		$wp_list_table->set_pagination_args(
			array(
				'total_items' => $total_this_page,
				'per_page'    => $plugins_per_page,
			)
		);
	}
);

add_filter(
	'network_admin_plugin_action_links',
	function ( $actions, $plugin_file, $plugin_data, $context ) use ( $tp_mu_plugins ) {
		if ( 'mustuse' !== $context || ! in_array( $plugin_file, $tp_mu_plugins, true ) ) {
			return $actions;
		}

		$actions[] = sprintf( '<span style="color:#333">File: <code>%s</code></span>', $plugin_file );
		return $actions;
	},
	10,
	4
);
