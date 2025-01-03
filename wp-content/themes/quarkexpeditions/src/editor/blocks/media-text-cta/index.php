<?php
/**
 * Block: Media Text CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaTextCTA;

use WP_Block;

const COMPONENT  = 'parts.media-text-cta';
const BLOCK_NAME = 'quark/media-text-cta';

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
	$attributes = [
		'image_id'           => 0,
		'cta_badge_text'     => '',
		'media_type'         => 'image',
		'image_aspect_ratio' => 'landscape',
		'media_align'        => 'left',
		'video_url'          => '',
		'content'            => [],
	];

	// Add Image Id.
	$attributes['image_id'] = $block->attributes['image']['id'];

	// Add Media Alignment.
	$attributes['media_align'] = $block->attributes['mediaAlignment'];

	// Add Media Type.
	$attributes['media_type'] = $block->attributes['mediaType'];

	// Add image aspect ratio.
	$attributes['image_aspect_ratio'] = $block->attributes['imageAspectRatio'];

	// Add Video URL.
	$attributes['video_url'] = $block->attributes['videoUrl'];

	// Add CTA Badge Text.
	if ( ! empty( $block->attributes['hasCtaBadge'] ) && ! empty( $block->attributes['ctaBadgeText'] ) ) {
		$attributes['cta_badge_text'] = $block->attributes['ctaBadgeText'];
	}

	// Looping though inner blocks.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if block is an instance of WP_Block.
		if ( ! $inner_block instanceof WP_Block ) {
			return $content;
		}

		// Media Text.
		if ( 'quark/media-text-cta-secondary-text' === $inner_block->name ) {
			$secondary_text = [
				'type' => 'secondary-text',
			];

			// Add the text.
			$secondary_text['text'] = render_block( $inner_block->parsed_block );

			// Add to content.
			$attributes['content'][] = $secondary_text;
		} elseif ( 'quark/media-text-cta-cta' === $inner_block->name ) {
			$cta = [
				'type' => 'cta',
			];

			// Add the CTA.
			$cta['cta'] = implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );

			// Add to content.
			$attributes['content'][] = $cta;

		} elseif ( 'quark/media-text-cta-overline' === $inner_block->name ) {
			$overline = [
				'type' => 'overline',
			];

			// Add the overline.
			$overline['text'] = $inner_block->attributes['text'];

			// Add to content.
			$attributes['content'][] = $overline;
		} else {
			$slot = [
				'type' => 'slot',
				'slot' => '',
			];

			// Get the slot.
			$slot['slot'] = render_block( $inner_block->parsed_block );

			// Add to content.
			$attributes['content'][] = $slot;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
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
		'text'  => [ 'ctaBadgeText' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-overline' ] = [
		'text' => [ 'text' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
