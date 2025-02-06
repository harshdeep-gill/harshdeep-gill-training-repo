<?php
/**
 * Block: Author Info.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\AuthorInfo;

use WP_Block;
use WP_Block_List;

use function Quark\Blog\get_blog_post_author_info;

const COMPONENT  = 'parts.post-author-info';
const BLOCK_NAME = 'quark/author-info';

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

	// Disable translation for this block.
	add_filter( 'qrk_translation_disable_blocks', __NAMESPACE__ . '\\disable_translation' );
}

/**
 * Prepare attributes for this block.
 *
 * @return array{}|array{
 *     image_id?: int,
 *     title?: string,
 *     duration?: int,
 * }
 */
function prepare_attributes(): array {
	// Get author info.
	$author_info = get_blog_post_author_info();

	// Initialize component attributes.
	$attributes = [];

	// Check if $author_info['authors'] has child array.
	if ( is_array( $author_info['authors'] ) ) {
		// Iterate through authors.
		foreach ( $author_info['authors'] as $author ) {
			$attributes['image_id'] = absint( $author['image_id'] );
			$attributes['title']    = $author['title'];

			// Break the loop.
			break;
		}
	}

	// Check if $author_info['duration'] has value.
	if ( ! empty( $author_info['duration'] ) ) {
		$attributes['duration'] = $author_info['duration'];
	}

	// Return component attributes.
	return $attributes;
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Build component attributes.
	$component_attributes = [
		'image_id' => 0,
		'title'    => '',
		'duration' => 0,
	];

	// Add Image ID.
	if ( ! empty( $attributes['authorImage'] ) && is_array( $attributes['authorImage'] ) && isset( $attributes['authorImage']['id'] ) ) {
		$component_attributes['image_id'] = $attributes['authorImage']['id'];
	}

	// Check if inner blocks are empty.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Iterate through inner blocks.
		foreach ( $block->inner_blocks as $inner_block ) {
			// Check for block.
			if ( ! $inner_block instanceof WP_Block ) {
				continue;
			}

			// Process inner block.
			switch ( $inner_block->name ) {
				// Author name block.
				case 'quark/author-info-name':
					$component_attributes['title'] = $inner_block->attributes['title'];
					break;

				// Read time block.
				case 'quark/author-info-read-time':
					$component_attributes['duration'] = $inner_block->attributes['duration'];
					break;
			}
		}
	}

	// Merge attributes.
	$component_attributes = wp_parse_args(
		prepare_attributes(),
		$component_attributes
	);

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Disable translation for this block.
 *
 * @param string[] $blocks The block names.
 *
 * @return string[] The block names.
 */
function disable_translation( array $blocks = [] ): array {
	// Add block name to disable translation.
	$blocks[] = BLOCK_NAME;

	// Return block names.
	return $blocks;
}
