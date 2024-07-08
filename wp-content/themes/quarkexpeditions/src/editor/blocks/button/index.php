<?php
/**
 * Block: Button.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Button;

use WP_Block;

const COMPONENT = 'button';

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

	// Build component attributes.
	$component_attributes = [
		'color'        => $block->attributes['backgroundColor'] ?? '',
		'href'         => $block->attributes['url']['url'] ?? '',
		'target'       => ! empty( $block->attributes['url']['newWindow'] ) ? '_blank' : '',
		'appearance'   => $block->attributes['appearance'] ?? '',
		'size'         => ! empty( $block->attributes['isSizeBig'] ) ? 'big' : '',
		'icon'         => ! empty( $block->attributes['hasIcon'] ) && ! empty( $block->attributes['icon'] ) ? $block->attributes['icon'] : '',
		'iconPosition' => $block->attributes['iconPosition'] ?? '',
		'slot'         => $block->attributes['btnText'] ?? '',
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
