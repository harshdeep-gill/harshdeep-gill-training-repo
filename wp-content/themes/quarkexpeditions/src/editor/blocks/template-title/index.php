<?php
/**
 * Block: Template Title.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TemplateTitle;

const COMPONENT = 'template-title';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Init title.
	$title = '';

	// If title is set, use it.
	if ( is_array( $attributes ) && ! empty( $attributes['title'] ) ) {
		$title = $attributes['title'];
	}

	// If type is dynamic, and we are on an archive page, get the archive title.
	if ( ! empty( $attributes['type'] ) && 'dynamic' === $attributes['type'] && is_archive() ) {
		/*
		 * This is specifically for archive page configuration. This will only work for archive page.
		 *
		 * It will not be available for block editor.
		 */
		add_filter( 'get_the_archive_title_prefix', '__return_empty_string', 1 );
		$title = get_the_archive_title();
		remove_filter( 'get_the_archive_title_prefix', '__return_empty_string', 1 );
	}

	// If no title, return empty string.
	if ( empty( $title ) ) {
		return '';
	}

	// Return the block markup.
	return quark_get_component( COMPONENT, [ 'title' => $title ] );
}
