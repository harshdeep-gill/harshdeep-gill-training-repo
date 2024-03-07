<?php
/**
 * Block: Media Content Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaContentCard;

const BLOCK_NAME = 'quark/media-content-card';
const COMPONENT  = 'parts.media-content-card';

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
		'is_compact' => ! empty( $block['attrs']['isCompact'] ) ? true : false,
		'content'    => [],
	];

	// Build attributes.
	$attributes['image_id'] = $block['attrs']['image']['id'];

	// abcd.
	if ( ! empty( $block['innerBlocks'] ) ) {
		foreach ( $block['innerBlocks'] as $index => $inner_block ) {
			foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
				if ( 'quark/media-content-info' !== $inner_inner_block['blockName'] ) {
					$attributes['content'][ $index ]['slot'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );
				} else {
					$attributes['content'][ $index ]['content_info'][] = [
						'label'  => $inner_inner_block['attrs']['label'] ?? '',
						'value'  => $inner_inner_block['attrs']['value'] ?? '',
						'url'    => ! empty( $inner_inner_block['attrs']['url']['url'] ) ? $inner_inner_block['attrs']['url']['url'] : '',
						'target' => ! empty( $inner_inner_block['attrs']['url']['newWindow'] ) ? '_blank' : '',
					];
				}
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
