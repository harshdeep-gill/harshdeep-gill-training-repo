<?php
/**
 * Namespace functions.
 *
 * @package quark-press-releases
 */

namespace Quark\PressReleases;

const POST_TYPE = 'qrk_press_release';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_press_release_post_type' );
}

/**
 * Register press release post type.
 *
 * @return void
 */
function register_press_release_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Press Release',
			'singular_name'      => 'Press Release',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Press Release',
			'edit_item'          => 'Edit Press Release',
			'new_item'           => 'New Press Release',
			'view_item'          => 'View Press Release',
			'search_items'       => 'Search Press Releases',
			'not_found'          => 'No Press Releases found',
			'not_found_in_trash' => 'No Press Releases found in Trash',
			'parent_item_colon'  => 'Parent Press Releasee:',
			'menu_name'          => 'Press Releases',
		],
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor', 'excerpt', 'revisions' ],
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-welcome-write-blog',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'press-releases',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_position'       => 5,
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}
