<?php
/**
 * Block: Collage.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Collage;

const BLOCK_NAME = 'quark/collage';
const COMPONENT  = 'parts.collage';

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
	// Set block count.
	static $count = 0;

	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize attributes.
	$attributes = [];

	// Set collage name.
	$attributes['name'] = sprintf( 'collage_%d', ++$count );

	// Build component attributes.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Check if we have an image.
		if ( empty( $inner_block['attrs']['image']['id'] ) ) {
			continue;
		}

		// Build collage items with attributes.
		$attributes['items'][] = [
			'media_type' => $inner_block['attrs']['mediaType'] ?? 'image',
			'size'       => $inner_block['attrs']['size'] ?? 'small',
			'image_id'   => $inner_block['attrs']['image']['id'],
			'title'      => $inner_block['attrs']['caption'] ?? '',
			'video_url'  => $inner_block['attrs']['videoUrl'] ?? '',
		];
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
