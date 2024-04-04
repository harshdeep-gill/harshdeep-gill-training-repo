<?php
/**
 * Block: LP Offer Masthead.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPOfferMasthead;

const BLOCK_NAME = 'quark/lp-offer-masthead';
const COMPONENT  = 'parts.lp-offer-masthead';

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

	// Initialize the attrs.
	$attributes = [
		'background_image_id' => $block['attrs']['bgImage']['id'] ?? 0,
		'logo_image_id'       => $block['attrs']['logoImage']['id'] ?? 0,
		'offer_image_id'      => 0,
		'caption'             => '',
		'inner_content'       => '',
	];

	// Parse inner blocks.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		switch ( $inner_block['blockName'] ) {

			// Offer Image.
			case 'quark/lp-offer-masthead-offer-image':
				$attributes['offer_image_id'] = $inner_block['attrs']['offerImage']['id'] ?? 0;
				break;

			// Caption.
			case 'quark/lp-offer-masthead-caption':
				$attributes['caption'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );
				break;

			// Content.
			case 'quark/lp-offer-masthead-content':
				$attributes['inner_content'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );
				break;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
