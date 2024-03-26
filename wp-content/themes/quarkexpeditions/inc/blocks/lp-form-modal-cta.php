<?php
/**
 * Block: Form Modal CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormModalCta;

const BLOCK_NAME = 'quark/lp-form-modal-cta';
const COMPONENT  = 'parts.lp-form-modal-cta';

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
		'text'          => $block['attrs']['text'] ?? '',
		'form_id'       => 'inquiry-form',
		'class'         => $block['attrs']['className'] ?? '',
		'hidden_fields' => $block['attrs']['fields'] ?? '',
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
