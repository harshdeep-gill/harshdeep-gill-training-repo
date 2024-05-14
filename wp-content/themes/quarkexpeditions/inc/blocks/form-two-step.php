<?php
/**
 * Block: Two step form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

const BLOCK_NAME = 'quark/form-two-step';
const COMPONENT  = 'form-two-step';

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

	// Initialize attributes.
	$attributes = [
		'background_color' => $block['attrs']['backgroundColor'] ?? 'black',
		'thank_you_page'   => $block['attrs']['thankYouPageUrl'] ?? '',
		'countries'        => get_countries(),
		'states'           => get_states(),
		'hidden_fields'    => [
			'polar_region' => $block['attrs']['polarRegion'] ?? '',
			'ship'         => $block['attrs']['ship'] ?? '',
			'expedition'   => $block['attrs']['expedition'] ?? '',
		],
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
