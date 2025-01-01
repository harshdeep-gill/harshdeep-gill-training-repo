<?php
/**
 * Block: Hero Details Card Slider.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\HeroDetailsCardSlider;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.hero-details-card-slider';
const BLOCK_NAME = 'quark/hero-details-card-slider';

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
		if (
			( ! empty( $inner_block['name'] ) && 'quark/hero-details-card-slider-item' === $inner_block['name'] ) ||
			( ! empty( $inner_block['blockName'] ) && 'quark/hero-details-card-slider-item' === $inner_block['blockName'] )
		) {
			// Prepare the card attributes.
			if ( ! empty( $block_attributes['mediaType'] ) ) {
				$media_type = $block_attributes['mediaType'];
			} else {
				$media_type = 'image';
			}

			// Check for media.
			if ( empty( $block_attributes['media'] ) ) {
				continue;
			}

			// Set media.
			$card_attributes['media_type'] = $media_type;
			$card_attributes['media_id']   = $block_attributes['media']['id'];

			// Set title.
			$card_attributes['title']       = ! empty( $block_attributes['title'] ) ? $block_attributes['title'] : '';
			$card_attributes['description'] = ! empty( $block_attributes['descriptionText'] ) ? $block_attributes['descriptionText'] : '';

			// Check for tag.
			if ( ! empty( $block_attributes['hasTag'] ) ) {
				// Check for tag.
				if ( ! empty( $block_attributes['tagText'] ) ) {
					// Prepare the tag attributes.
					$card_attributes['tag'] = [
						'text' => $block_attributes['tagText'],
						'type' => $block_attributes['tagType'] ?? 'tag',
					];
				}
			}

			// Check for CTA.
			if ( ! empty( $block_attributes['hasCtaLink'] ) ) {
				// Check for CTA.
				if ( ! empty( $block_attributes['cta'] ) ) {
					// Prepare the CTA attributes.
					$card_attributes['cta'] = [
						'text'   => $block_attributes['cta']['text'],
						'url'    => $block_attributes['cta']['url'],
						'target' => ! empty( $block_attributes['cta']['newWindow'] ) ? '_blank' : '',
					];
				}
			}
		}

		// Add the card attributes.
		$component_attributes['items'][] = $card_attributes;
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
	$blocks_and_attributes[ BLOCK_NAME . '-item' ] = [
		'image' => [ 'media' ],
		'text'  => [
			'title',
			'descriptionText',
			'tagText',
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
