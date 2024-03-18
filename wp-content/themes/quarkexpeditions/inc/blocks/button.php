<?php
/**
 * Block: Button.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Button;

const BLOCK_NAME = 'quark/button';
const COMPONENT  = 'button';

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
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Build component attributes.
	$attributes = [
		'color'        => $block['attrs']['backgroundColor'] ?? '',
		'href'         => $block['attrs']['url']['url'] ?? '',
		'target'       => ! empty( $block['attrs']['url']['newWindow'] ) ? '_blank' : '',
		'appearance'   => $block['attrs']['appearance'] ?? '',
		'size'         => ! empty( $block['attrs']['isSizeBig'] ) ? 'big' : '',
		'icon'         => $block['attrs']['icon'] ?? '',
		'iconPosition' => $block['attrs']['iconPosition'] ?? '',
		'slot'         => $block['attrs']['btnText'] ?? '',
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
