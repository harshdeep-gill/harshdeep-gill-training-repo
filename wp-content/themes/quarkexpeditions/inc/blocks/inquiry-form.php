<?php
/**
 * Block: Inquiry Form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InquiryForm;

use const Quark\LandingPages\POST_TYPE as LANDING_PAGE_POST_TYPE;

const BLOCK_NAME = 'quark/inquiry-form';
const COMPONENT  = 'inquiry-form';

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

	// Build attributes.
	$attributes = [
		'thank_you_page' => $block['attrs']['thankYouPageUrl'] ?? '',
		'hidden_fields'  => [
			'polar_region' => '',
			'sub_region'   => '',
			'ship'         => '',
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
			'ship'         => $block['attrs']['ship'] ?? '',
			'subRegion'    => $block['attrs']['subRegion'] ?? '',
			'expedition'   => $block['attrs']['expedition'] ?? '',
		];
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
