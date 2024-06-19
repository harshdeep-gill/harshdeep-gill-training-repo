<?php
/**
 * Block: Author Info.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\AuthorInfo;

use function Quark\Blog\get_blog_post_author_info;

const BLOCK_NAME = 'quark/author-info';
const COMPONENT  = 'parts.post-author-info';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Prepare attributes for this block.
 *
 * @return mixed[]
 */
function prepare_attributes(): array {
	// Get author info.
	$author_info = get_blog_post_author_info();

	// init $attributes array.
	$attributes = [];

	// Check if $author_info['authors'] has child array.
	if ( is_array( $author_info['authors'] ) ) {
		// Iterate through authors.
		foreach ( $author_info['authors'] as $author ) {
			$attributes['image_id'] = $author['image_id'];
			$attributes['title']    = $author['title'];

			// Break the loop.
			break;
		}
	}

	// Check if $author_info['duration'] has value.
	if ( ! empty( $author_info['duration'] ) ) {
		$attributes['duration'] = $author_info['duration'];
	}

	// Return attributes.
	return $attributes;
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Build component attributes.
	$attributes = [
		'image_id' => empty( $block['attrs']['authorImage']['id'] ) ? 0 : $block['attrs']['authorImage']['id'],
		'title'    => '',
		'duration' => 0,
	];

	// Check if inner blocks are empty.
	if ( ! empty( $block['innerBlocks'] ) ) {
		// Iterate through inner blocks.
		foreach ( $block['innerBlocks'] as $inner_block ) {
			switch ( $inner_block['blockName'] ) {

				// Author name block.
				case 'quark/author-info-name':
					$attributes['title'] = $inner_block['attrs']['title'] ?? '';
					break;

				// Read time block.
				case 'quark/author-info-read-time':
					$attributes['duration'] = $inner_block['attrs']['duration'] ?? 0;
					break;
			}
		}
	}

	// Merge attributes.
	$attributes = wp_parse_args(
		prepare_attributes(),
		$attributes
	);

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
