<?php
/**
 * Block: Button.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Button;

use WP_Block;

const COMPONENT  = 'button';
const BLOCK_NAME = 'quark/button';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
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
		'class'        => $block->attributes['className'] ?? '',
		'color'        => $block->attributes['backgroundColor'],
		'href'         => $block->attributes['url']['url'] ?? '',
		'target'       => ! empty( $block->attributes['url']['newWindow'] ) ? '_blank' : '',
		'appearance'   => $block->attributes['appearance'],
		'size'         => ! empty( $block->attributes['isSizeBig'] ) ? 'big' : '',
		'icon'         => ! empty( $block->attributes['hasIcon'] ) && ! empty( $block->attributes['icon'] ) ? $block->attributes['icon'] : '',
		'iconPosition' => $block->attributes['iconPosition'],
		'slot'         => $block->attributes['btnText'],
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME ] = [
		'text' => [ 'btnText' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
