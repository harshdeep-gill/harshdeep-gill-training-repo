<?php
/**
 * Block: Hero Details Card Slider.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\HeroDetailsCardSlider;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'parts.hero-details-card-slider';

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

	// Prepare the component attributes.
	$component_attributes = [
		'show_controls'   => $attributes['showControls'],
		'transition_type' => $attributes['transitionType'],
		'interval'        => $attributes['interval'],
		'items'           => [],
	];

	/**
	 * ServerSideRender uses the Block Renderer API endpoint, which does not parse Inner Blocks.
	 * So we fall back to the parsed block instead. But SSR block stores it as attributes in the editor,
	 * and in the parsed block on the front-end!
	 */
	if ( ! empty( $block->parsed_block['attrs']['innerBlocks'] ) && is_array( $block->parsed_block['attrs']['innerBlocks'] ) ) {
		$inner_blocks = $block->parsed_block['attrs']['innerBlocks'];
	} elseif ( ! empty( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] ) ) {
		$inner_blocks = $block->parsed_block['innerBlocks'];
	} else {
		// Inner blocks not found!
		return $content;
	}

	// Check for inner blocks.
	foreach ( $inner_blocks as $inner_block ) {
		// Attributes are stored differently in SSR in the editor, and on the front-end!
		if ( ! empty( $inner_block['attributes'] ) && is_array( $inner_block['attributes'] ) ) {
			$block_attributes = $inner_block['attributes'];
		} elseif ( ! empty( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) ) {
			$block_attributes = $inner_block['attrs'];
		} else {
			continue;
		}

		// Prepare the card attributes.
		$card_attributes = [];

		// Prepare the card attributes.
		if ( 'quark/hero-details-card-slider-item' === $inner_block['name'] || 'quark/hero-details-card-slider-item' === $inner_block['blockName'] ) {
			// Prepare the card attributes.
			$media_type = $block_attributes['mediaType'];

			// Check for media.
			if ( ! empty( $media_type ) ) {
				$card_attributes['media_type'] = $media_type;
				$card_attributes['media_id']   = $block_attributes['media']['id'];
			} else {
				$card_attributes['media_type'] = 'image';
				$card_attributes['media_id']   = $block_attributes['media']['id'];
			}

			// Set title.
			$card_attributes['title']       = $block_attributes['title'];
			$card_attributes['description'] = $block_attributes['descriptionText'];

			// Check for tag.
			if ( $block_attributes['hasTag'] ) {
				$card_attributes['tag']['text'] = $block_attributes['tagText'];
				$card_attributes['tag']['type'] = $block_attributes['tagType'] ?? 'tag';
			}

			// Check for CTA.
			if ( $block_attributes['hasCtaLink'] ) {
				$card_attributes['cta'] = [
					'text' => $block_attributes['cta']['text'],
					'url'  => $block_attributes['cta']['url'],
				];
			}
		}

		// Add the card attributes.
		$component_attributes['items'][] = $card_attributes;
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
