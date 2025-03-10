<?php
/**
 * Block: Icon Badge.
 *
 * @package quark
 */

namespace Quark\Theme\Block\GlobalMessage;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'global-message';

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
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content The block content.
 * @param WP_Block|null $block The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize an array to store the global message attributes.
	$attribute_content = [
		'slot' => $content,
	];

	// Return the markup.
	return quark_get_component( COMPONENT, $attribute_content );
}
