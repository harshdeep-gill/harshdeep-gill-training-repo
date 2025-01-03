<?php
/**
 * Block: Instagram Embed
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Instagram_Embed;

use WP_Block;

const COMPONENT = 'instagram-embed';

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

	// Get the attributes.
	$instagram_post_id = $attributes['instagramPostId'];

	// Check for Instagram post ID.
	if ( empty( $instagram_post_id ) ) {
		return $content;
	}

	// Return the component markup.
	return quark_get_component( COMPONENT, [ 'instagram_post_id' => $instagram_post_id ] );
}
