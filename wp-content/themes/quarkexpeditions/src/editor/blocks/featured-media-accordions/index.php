<?php
/**
 * Block: Featured Media Accordions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FeaturedMediaAccordions;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.featured-media-accordions';
const BLOCK_NAME = 'quark/featured-media-accordions';

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

	// Initialize Accordions attributes.
	$component_attributes = [];

	// Process child blocks.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Initialize accordion items.
		$accordion_items = [];

		// Loop through the child blocks.
		foreach ( $block->inner_blocks as $child_block ) {
			// Accordion item attributes.
			$accordion_item_attributes = [];

			// Check for child block.
			if ( ! $child_block instanceof WP_Block ) {
				continue;
			}

			// Check for child block name.
			if ( 'quark/featured-media-accordions-item' !== $child_block->name ) {
				continue;
			}

			// Get the attributes.
			$child_block_attributes = $child_block->attributes;

			// Check for empty attributes.
			if ( empty( $child_block_attributes ) ) {
				continue;
			}

			// loop through the attributes.
			foreach ( $child_block_attributes as $key => $value ) {
				// Check for empty value.
				if ( empty( $value ) ) {
					continue;
				}

				// Check for array value.
				switch ( $key ) {
					// Process the title.
					case 'title':
						$accordion_item_attributes['title'] = $value;

						// Create an id for the accordion item.
						$accordion_item_attributes['id'] = 'accordion-item-' . $value;
						break;

					// Process the image.
					case 'image':
						$accordion_item_attributes['image_id'] = $value['id'];
						break;
				}
			}

			// Check for inner blocks.
			if ( ! empty( $child_block->parsed_block['innerBlocks'] ) && ! empty( $child_block->parsed_block['attrs'] ) ) {
				$accordion_item_attributes['content'] = implode( '', array_map( 'render_block', $child_block->parsed_block['innerBlocks'] ) );
			}

			// Add accordion item attributes.
			$accordion_items[] = $accordion_item_attributes;
		}
	}

	// Add accordion items.
	$component_attributes['items'] = $accordion_items;

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
	$blocks_and_attributes[ BLOCK_NAME . '-item' ] = [
		'image' => [ 'image' ],
		'text'  => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
