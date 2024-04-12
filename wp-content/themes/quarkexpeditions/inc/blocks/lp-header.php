<?php
/**
 * Block: Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPHeader;

const BLOCK_NAME = 'quark/lp-header';
const COMPONENT  = 'lp-header';

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
		'id'           => $block['attrs']['anchor'] ?? '',
		'logo_url'     => 'https://www.quarkexpeditions.com',
		'tc_image_id'  => 0,
		'phone_number' => $block['attrs']['ctaNumber'] ?? '',
		'cta_text'     => $block['attrs']['ctaText'] ?? '',
		'dark_mode'    => $block['attrs']['darkMode'] ?? false,
	];

	// TC Image.
	if ( ! empty( $block['attrs']['tcImage']['id'] ) ) {
		$attributes['tc_image_id'] = absint( $block['attrs']['tcImage']['id'] );
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
