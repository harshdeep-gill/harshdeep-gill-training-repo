<?php
/**
 * Namespace functions.
 *
 * @package quark-policy-pages
 */

namespace Quark\PolicyPages;

const POST_TYPE   = 'qrk_agreement';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type.
	add_action( 'init', __NAMESPACE__ . '\\register_agreement_post_type' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/policy-pages.php';
	}
}

/**
 * Register Policy Pages post type.
 *
 * @return void
 */
function register_agreement_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Terms and Conditions / Policy Pages',
			'singular_name'      => 'Terms and Conditions / Policy Page',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Terms and Conditions / Policy Page',
			'edit_item'          => 'Edit Terms and Conditions / Policy Page',
			'new_item'           => 'New Terms and Conditions / Policy Page',
			'view_item'          => 'View Terms and Conditions / Policy Page',
			'search_items'       => 'Search Terms and Conditions / Policy Pages',
			'not_found'          => 'No Terms and Conditions / Policy Pages found',
			'not_found_in_trash' => 'No Terms and Conditions / Policy Pages found in Trash',
			'parent_item_colon'  => 'Parent Terms and Conditions / Policy Page:',
			'menu_name'          => 'Terms and Conditions / Policy Pages',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-printer',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
		],
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'agreements',
			'with_front' => false,
		],
		'capability_type'     => 'page',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}
