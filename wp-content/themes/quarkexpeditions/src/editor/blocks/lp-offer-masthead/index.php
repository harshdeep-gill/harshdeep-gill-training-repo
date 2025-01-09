<?php
/**
 * Block: LP Offer Masthead.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPOfferMasthead;

use WP_Block;

const COMPONENT  = 'parts.lp-offer-masthead';
const BLOCK_NAME = 'quark/lp-offer-masthead';

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

	// Initialize the attrs.
	$component_attributes = [
		'background_image_id' => $block->attributes['bgImage']['id'] ?? 0,
		'logo_image_id'       => $block->attributes['logoImage']['id'] ?? 0,
		'content'             => [],
	];

	// Parse inner blocks.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if block is an instance of WP_Block.
		if ( ! $inner_block instanceof WP_Block ) {
			return $content;
		}

		// Switch to inner block.
		switch ( $inner_block->name ) {

			// Offer Image.
			case 'quark/lp-offer-masthead-offer-image':
				$offer_image = [
					'type' => 'offer-image',
				];

				// Add offer image.
				$offer_image['offer_image_id'] = $inner_block->attributes['offerImage']['id'] ?? 0;

				// Add to attributes.
				$component_attributes['content'][] = $offer_image;
				break;

			// Caption.
			case 'quark/lp-offer-masthead-caption':
				$caption = [
					'type' => 'caption',
				];

				// Add caption.
				$caption['caption'] = implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );

				// Add to attributes.
				$component_attributes['content'][] = $caption;
				break;

			// Content.
			case 'quark/lp-offer-masthead-content':
				$inner_content = [
					'type' => 'inner-content',
				];

				// Add inner content.
				$inner_content['inner_content'] = implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );

				// Add to attributes.
				$component_attributes['content'][] = $inner_content;
				break;
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
		'image' => [
			'bgImage',
			'logoImage',
		],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-offer-image' ] = [
		'image' => [ 'offerImage' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
