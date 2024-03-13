<?php
/**
 * Block: Icon Badge.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IconBadge;

const BLOCK_NAME = 'quark/icon-badge';
const COMPONENT  = 'icon-badge';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap() : void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register() : void {
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
function render( ?string $content = null, array $block = [] ) : null | string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Build component attributes.
	$attributes = [
		'background_color' => $block['attrs']['color'] ?? '',
		'icon'             => $block['attrs']['icon'] ?? '',
		'text'             => $block['attrs']['text'] ?? '',
		'class'            => $block['attrs']['className'] ?? '',
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
