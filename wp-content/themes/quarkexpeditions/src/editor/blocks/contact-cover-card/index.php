<?php
/**
 * Block: Contact Cover Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ContactCoverCard;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.contact-cover-card';
const BLOCK_NAME = 'quark/contact-cover-card';

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

	// Check if we have an image.
	if ( empty( $block->attributes['image']['id'] ) ) {
		return '';
	}

	// Initialize attributes.
	$component_attributes = [
		'image_id' => 0,
		'content'  => [],
	];

	// Add Image Id.
	$component_attributes['image_id'] = $block->attributes['image']['id'];

	// Loop through innerblocks and build attributes.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if block is an instance of WP_Block.
		if ( ! $inner_block instanceof WP_Block ) {
			return $content;
		}

		// Title.
		if ( 'quark/contact-cover-card-title' === $inner_block->name ) {
			// Initialize title.
			$title = [];

			// Add block type.
			$title['type'] = 'title';

			// Add title.
			$title['title'] = $inner_block->attributes['title'];

			// Add title to children.
			$component_attributes['content'][] = $title;
		}

		// Description.
		if ( 'quark/content-cover-card-description' === $inner_block->name ) {
			// Initialize description.
			$description = [];

			// Add block type.
			$description['type'] = 'description';

			// Add description.
			$description['description'] = implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );

			// Add title to children.
			$component_attributes['content'][] = $description;
		}

		// Contact Info.
		if ( 'quark/contact-cover-card-contact-info' === $inner_block->name ) {
			// Initialize contact_info.
			$contact_info = [];

			// Add block type.
			$contact_info['type'] = 'contact-info';

			// Loop through the inner blocks.
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				foreach ( $inner_block->inner_blocks as $inner_inner_block ) {
					// Check if block is an instance of WP_Block.
					if ( ! $inner_inner_block instanceof WP_Block ) {
						return $content;
					}

					// Add children.
					$contact_info['children'][] = [
						'label'  => $inner_inner_block->attributes['label'],
						'value'  => $inner_inner_block->attributes['value'],
						'url'    => ! empty( $inner_inner_block->attributes['url']['url'] ) ? $inner_inner_block->attributes['url']['url'] : '',
						'target' => ! empty( $inner_inner_block->attributes['url']['newWindow'] ) ? '_blank' : '',
					];
				}
			}

			// Add title to children.
			$component_attributes['content'][] = $contact_info;
		}
	}

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
		'image' => [ 'image' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-contact-info-item' ] = [
		'text' => [
			'label',
			'value',
		],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-description' ] = [
		'text' => [ 'helpText' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-title' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
