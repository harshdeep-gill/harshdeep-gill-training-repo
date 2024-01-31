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

	// Build component attributes.
	$attributes = [
		'image_id'  => 0,
		'title'     => $block['attrs']['title'] ?? '',
		'sub_title' => $block['attrs']['subTitle'] ?? '',
	];

	// Image.
	if ( ! empty( $block['attrs']['image']['id'] ) ) {
		$attributes['image_id'] = absint( $block['attrs']['image']['id'] );
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
