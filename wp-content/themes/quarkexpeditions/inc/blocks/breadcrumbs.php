<?php
/**
 * Block: Breadcrumbs.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Breadcrumbs;

use function Travelopia\Breadcrumbs\get_breadcrumbs;

const BLOCK_NAME = 'quark/breadcrumbs';
const COMPONENT  = 'breadcrumbs';

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

	// Get breadcrumbs.
	$breadcrumbs = get_breadcrumbs();

	// Check if breadcrumbs are empty.
	if ( empty( $breadcrumbs ) || ! is_array( $breadcrumbs ) ) {
		$breadcrumbs = [];
	}

	// Build component attributes.
	$attributes['breadcrumbs'] = $breadcrumbs;

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
