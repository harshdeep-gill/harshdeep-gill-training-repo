<?php
/**
 * Editor functions.
 *
 * @package quark
 */

namespace Quark\Theme\Editor;

use WP_Screen;

use function Quark\Theme\Core\get_assets_version;

/**
 * Setup.
 *
 * @return void
 */
function setup(): void {
	// Hooks.
	add_action( 'current_screen', __NAMESPACE__ . '\\block_editor_styles' );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_editor_assets' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\remove_styles_from_tinymce' );
}

/**
 * Setup Block Editor styles.
 *
 * @param ?WP_Screen $screen Screen object.
 *
 * @return void
 */
function block_editor_styles( ?WP_Screen $screen = null ): void {
	// Ignore if not block editor.
	if ( empty( $screen->is_block_editor ) || empty( $screen->post_type ) ) {
		return;
	}

	// Add theme support.
	add_theme_support( 'editor-styles' );

	// Add styles depending on post type.
	add_editor_style( 'dist/editor.css' );
}

/**
 * Enqueue Editor Assets.
 *
 * @return void
 */
function enqueue_block_editor_assets(): void {
	// CSS.
	wp_enqueue_style( 'tcs-editor-custom', get_stylesheet_directory_uri() . '/dist/editor-custom.css', [], '1' );
	wp_enqueue_style( 'nunito-sans', get_template_directory_uri() . '/src/assets/fonts/nunito-sans/nunito-sans.css', [], '1' );
	wp_enqueue_style( 'source-serif-4', get_template_directory_uri() . '/src/assets/fonts/source-serif-4/source-serif-4.css', [], '1' );

	// JavaScript.
	$assets_version = get_assets_version();
	$deps           = [
		'wp-i18n',
		'wp-blocks',
		'wp-components',
		'wp-editor',
		'wp-plugins',
		'wp-edit-post',
		'wp-api-fetch',
		'lodash',
		'gumponents',
		'travelopia-media',
	];

	// Enqueue editor JavaScript.
	wp_enqueue_script(
		'tcs-editor',
		get_stylesheet_directory_uri() . '/dist/editor.js',
		$deps,
		$assets_version,
		false
	);
}

/**
 * Remove block editor styles from TinyMCE.
 *
 * @return void
 */
function remove_styles_from_tinymce(): void {
	// Remove default editor styles for TinyMCE.
	remove_editor_styles();
}
