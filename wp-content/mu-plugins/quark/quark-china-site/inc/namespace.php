<?php
/**
 * Namespace functions.
 *
 * @package quark-china-site
 */

namespace Quark\ChinaSite;

/**
 * Bootstrap the plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Disable the default "Posts" post type.
	add_action( 'admin_menu', __NAMESPACE__ . '\\disable_default_post_type' );
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
