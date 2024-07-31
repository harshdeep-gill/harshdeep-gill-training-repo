<?php
/**
 * Block: Media CTA Banner.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaCtaBanner;

use WP_Block;

const COMPONENT = 'parts.media-cta-banner';

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
			'render_callback'   => __NAMESPACE__ . '\\render',
			'skip_inner_blocks' => true,
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[]       $attributes Block attributes.
 * @param string        $content Block default content.
 * @param WP_Block|null $block Block instance.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check if block is an instance of WP_Block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize component attributes.
	$component_attributes = [
		'image_id'   => 0,
		'appearance' => 'light',
		'content'    => '',
	];

	// Get Image ID.
	if ( is_array( $attributes['backgroundImage'] ) && isset( $attributes['backgroundImage']['id'] ) ) {
		$component_attributes['image_id'] = $attributes['backgroundImage']['id'];
	}

	// Set appearance.
	$component_attributes['appearance'] = $attributes['darkMode'] ? 'dark' : 'light';

	// Add content.
	$component_attributes['content'] = implode( '', array_map( 'render_block', $block->parsed_block['innerBlocks'] ) );

	// Render the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
