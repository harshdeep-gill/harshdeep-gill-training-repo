<?php
/**
 * Block: Media Text CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaTextCTA;

const BLOCK_NAME = 'quark/media-text-cta';
const COMPONENT  = 'parts.media-text-cta';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Check if we have an image.
	if ( empty( $block['attrs']['image']['id'] ) ) {
		return '';
	}

	// Initialize attributes.
	$attributes = [
		'image_id'       => 0,
		'cta_badge_text' => '',
		'media_type'     => 'image',
		'media_align'    => 'left',
		'video_url'      => '',
		'content'        => [],
	];

	// Add Image Id.
	$attributes['image_id'] = $block['attrs']['image']['id'];

	// Add Media Alignment.
	$attributes['media_align'] = empty( $block['attrs']['mediaAlignment'] ) ? 'left' : $block['attrs']['mediaAlignment'];

	// Add Media Type.
	$attributes['media_type'] = empty( $block['attrs']['mediaType'] ) ? 'image' : $block['attrs']['mediaType'];

	// Add Video URL.
	$attributes['video_url'] = ! empty( $block['attrs']['videoUrl'] ) ? $block['attrs']['videoUrl'] : '';

	// Add CTA Badge Text.
	if ( ! empty( $block['attrs']['hasCtaBadge'] ) && ! empty( $block['attrs']['ctaBadgeText'] ) ) {
		$attributes['cta_badge_text'] = $block['attrs']['ctaBadgeText'];
	}

	// Looping though inner blocks.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		if ( 'quark/media-text-cta-secondary-text' === $inner_block['blockName'] ) {
			$secondary_text = [
				'type' => 'secondary-text',
			];

			// Add the text.
			$secondary_text['text'] = $inner_block['attrs']['secondaryText'] ?? '';

			// Add to content.
			$attributes['content'][] = $secondary_text;
		} elseif ( 'quark/media-text-cta-cta' === $inner_block['blockName'] ) {
			$cta = [
				'type' => 'cta',
			];

			// Add the CTA.
			$cta['cta'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );

			// Add to content.
			$attributes['content'][] = $cta;

		} else {
			$slot = [
				'type' => 'slot',
				'slot' => '',
			];

			// Get the slot.
			$slot['slot'] = render_block( $inner_block );

			// Add to content.
			$attributes['content'][] = $slot;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
