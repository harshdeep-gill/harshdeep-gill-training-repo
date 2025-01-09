<?php
/**
 * Namespace functions.
 *
 * @package quark-china-site
 */

namespace Quark\China;

use WP_Post;

use function Quark\Itineraries\get as get_itinerary;

/**
 * Bootstrap the plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Disable the default "Posts" post type.
	add_action( 'admin_menu', __NAMESPACE__ . '\\disable_default_post_type' );
	add_action( 'quark_expedition_title_separator', __NAMESPACE__ . '\\expedition_title_separator' );

	// Override ships for itinerary.
	add_filter( 'qrk_override_related_ships', __NAMESPACE__ . '\\override_related_ships', 10, 2 );

	// Update Frontend data - To modify front end data use hook with 11 priority.
	add_action( 'quark_front_end_data', __NAMESPACE__ . '\\update_front_end_data', 11 );

	// Remove plugin that are not needed for china website.
	remove_action( 'plugins_loaded', 'Quark\Departures\bootstrap' );
	remove_action( 'plugins_loaded', 'Quark\Softrip\bootstrap' );
	remove_action( 'plugins_loaded', 'Quark\Brochures\bootstrap' );
	remove_action( 'plugins_loaded', 'Quark\Softrip\ManualSync\bootstrap' );
	remove_action( 'plugins_loaded', 'Quark\Softrip\Cleanup\bootstrap' );
	remove_action( 'plugins_loaded', 'Quark\Softrip\Admin\bootstrap' );

	// Remove ACF field for brochure.
	add_filter( 'acf/load_field/name=brochure', __NAMESPACE__ . '\\deregister_brochure_acf_field_on_load' );
}

/**
 * Disable the default "Posts" post type.
 *
 * @return void
 */
function disable_default_post_type(): void {
	// Remove the "Posts" menu item from the admin dashboard.
	remove_menu_page( 'edit.php' );

	// Hook for Unregister the "Posts" post type.
	add_action( 'init', __NAMESPACE__ . '\\unregister_default_post_type' );
}

/**
 * Unregister the default "Posts" post type.
 *
 * @return void
 */
function unregister_default_post_type(): void {
	// Unregister the "Posts" post type.
	unregister_post_type( 'post' );
}

/**
 * Change the expedition title separator.
 *
 * @return string
 */
function expedition_title_separator(): string {
	// Return the separator for china site.
	return '：';
}

/**
 * Update Frontend data.
 *
 * @param mixed[] $data Frontend data.
 *
 * @return mixed[]
 */
function update_front_end_data( array $data = [] ): array {
	// Update the frontend data.
	$data['currencies'] = [];

	// Return the updated data.
	return $data;
}

/**
 * Deregister the brochure ACF field on load.
 *
 * @return false
 */
function deregister_brochure_acf_field_on_load(): false {
	// Deregister the brochure ACF field.
	return false;
}

/**
 * Override related ships for itinerary.
 *
 * @param int[] $ships Ship IDs.
 * @param int   $itinerary_id Itinerary ID.
 *
 * @return int[]
 */
function override_related_ships( array $ships = [], int $itinerary_id = 0 ): array {
	// get post.
	$itinerary_post = get_itinerary( $itinerary_id );

	// If post not found then return empty array.
	if ( ! $itinerary_post['post'] instanceof WP_Post ) {
		return $ships;
	}

	// Check if the expedition meta is empty.
	if ( empty( $itinerary_post['post_meta']['qrk_related_ships'] ) || ! is_array( $itinerary_post['post_meta']['qrk_related_ships'] ) ) {
		return $ships;
	}

	// return the related ship.
	return $itinerary_post['post_meta']['qrk_related_ships'];
}
