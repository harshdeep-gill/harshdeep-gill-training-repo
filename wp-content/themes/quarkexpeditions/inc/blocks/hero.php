<?php
/**
 * Block: Hero.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Hero;

const BLOCK_NAME = 'qrk/hero';
const COMPONENT  = 'parts.hero';

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
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize the slot.
	$slot = '';

	// Get inner block.
	$inner_block = $block['innerBlocks'][0];

	// Inner block name.
	$inner_block_name = $inner_block['blockName'];

	// Check the inner block name.
	if ( 'quark/hero-form-cta' === $inner_block_name ) {
		$slot = $inner_block['attrs']['text'];
	} else {
		$slot = render_block( $inner_block );
	}

	// Build component attributes.
	$attributes = [
		'image_id'  => 0,
		'title'     => $block['attrs']['title'] ?? '',
		'sub_title' => $block['attrs']['subTitle'] ?? '',
		'slot'      => $slot,
		'immersive' => $block['attrs']['isImmersive'] ?? false,
		'show_form' => $block['attrs']['showForm'] ?? true,
	];

	// Image.
	if ( ! empty( $block['attrs']['image']['id'] ) ) {
		$attributes['image_id'] = absint( $block['attrs']['image']['id'] );
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
