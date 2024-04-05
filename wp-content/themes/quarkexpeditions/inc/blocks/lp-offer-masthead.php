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
		'content'             => [],
	];

	// Parse inner blocks.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		switch ( $inner_block['blockName'] ) {

			// Offer Image.
			case 'quark/lp-offer-masthead-offer-image':
				$offer_image = [
					'type' => 'offer-image',
				];

				// Add offer image.
				$offer_image['offer_image_id'] = $inner_block['attrs']['offerImage']['id'] ?? 0;

				// Add to attributes.
				$attributes['content'][] = $offer_image;
				break;

			// Caption.
			case 'quark/lp-offer-masthead-caption':
				$caption = [
					'type' => 'caption',
				];

				// Add caption.
				$caption['caption'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );

				// Add to attributes.
				$attributes['content'][] = $caption;
				break;

			// Content.
			case 'quark/lp-offer-masthead-content':
				$inner_content = [
					'type' => 'inner-content',
				];

				// Add inner content.
				$inner_content['inner_content'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );

				// Add to attributes.
				$attributes['content'][] = $inner_content;
				break;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
