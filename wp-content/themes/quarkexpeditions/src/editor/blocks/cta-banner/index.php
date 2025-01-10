<?php
/**
 * Block: CTA Banner.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\CtaBanner;

use WP_Block;

const COMPONENT  = 'parts.media-cta-banner';
const BLOCK_NAME = 'quark/cta-banner';

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
	$component_attributes['appearance'] = $attributes['appearance'];

	// If appearance is solid, set the BG color.
	if ( 'solid' === $attributes['appearance'] ) {
		$component_attributes['background_color'] = $attributes['backgroundColor'];
	}

	// Inner Blocks to process.
	$blocks_content = $block->parsed_block['innerBlocks'];

	// Extract overline from inner blocks and remove it from blocks content.
	$blocks_content = array_filter(
		$blocks_content,
		static function ( $inner_block ) use ( &$component_attributes ) {
			// Check if inner block is a CTA Banner Overline.
			if ( 'quark/cta-banner-overline' === $inner_block['blockName'] ) {
				$component_attributes['overline'] = ! empty( $inner_block['attrs']['text'] ) ? $inner_block['attrs']['text'] : '';

				// Return false to remove the block.
				return false;
			}

			// Return true to keep the block.
			return true;
		}
	);

	// Add content.
	$component_attributes['content'] = implode( '', array_map( 'render_block', $blocks_content ) );

	// Render the component.
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
		'image' => [ 'backgroundImage' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-overline' ] = [
		'text' => [ 'text' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
