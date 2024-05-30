<?php
/**
 * Block: Author Info.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\AuthorInfo;

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

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
