<?php
/**
 * Block: LP Form Modal CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPFormModalCta;

use const Quark\LandingPages\POST_TYPE as LANDING_PAGE_POST_TYPE;

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
		'hidden_fields' => [
			'polar_region' => '',
			'season'       => '',
			'ship'         => '',
			'sub_region'   => '',
			'expedition'   => '',
		],
	];

	// Set if is landing page.
	static $is_landing_page = false;

	// Check if is landing page.
	if ( ! $is_landing_page && LANDING_PAGE_POST_TYPE === get_post_type() ) {
		$is_landing_page = true;
	}

	// Only add hidden field values, if this block is being used on a Landing Page.
	if ( $is_landing_page ) {
		// Get the hidden fields values.
		$attributes['hidden_fields'] = [
			'polar_region' => $block['attrs']['polarRegion'] ?? '',
			'season'       => $block['attrs']['season'] ?? '',
			'ship'         => $block['attrs']['ship'] ?? '',
			'sub_region'   => $block['attrs']['subRegion'] ?? '',
			'expedition'   => $block['attrs']['expedition'] ?? '',
		];
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
