<?php
/**
 * Block Name: Post Featured Image.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\PostFeaturedImage;

const COMPONENT = 'parts.image';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Unregister the core block.
	unregister_block_type( 'core/post-featured-image' );

	// Register the block.
	register_block_type_from_metadata(
		ABSPATH . '/wp-includes/blocks/post-featured-image',
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes Block attributes.
 * @param string  $content Block default content.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '' ): string {
	// Get image ID.
	$image_id = get_post_thumbnail_id();

	// Check if image ID is empty.
	if ( ! $image_id ) {
		return $content;
	}

	// Build component attributes.
	$attributes = [
		'image_id' => $image_id,
		'link'     => $attributes['isLink'] ? get_permalink() : '',
		'target'   => $attributes['linkTarget'],
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
