<?php
/**
 * Block: Form - Request Quote
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormRequestQuote;

use WP_Block;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;
use function Quark\Search\Filters\get_expeditions_and_month_options_by_expedition;

use const Quark\Search\REST_API_NAMESPACE;

const COMPONENT  = 'form-request-quote';
const BLOCK_NAME = 'quark/form-request-quote';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);

	// Disable translation for this block.
	add_filter( 'qrk_translation_disable_blocks', __NAMESPACE__ . '\\disable_translation' );
}

/**
 * Render this block.
 *
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content The block content.
 * @param WP_Block|null $block The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Component attributes.
	$component_attributes = [
		'countries'        => get_countries(),
		'states'           => get_states(),
		'filters_endpoint' => get_rest_url( null, '/' . REST_API_NAMESPACE . '/filter-options/by-expedition' ),
		'home_url'         => home_url(),
	];

	// Build query args.
	$filter_options = get_expeditions_and_month_options_by_expedition();

	// Initialize.
	$expeditions = [];

	// Get expeditions.
	if ( ! empty( $filter_options['expeditions'] ) ) {
		$expeditions = $filter_options['expeditions'];
	}

	// Add expeditions to component attributes.
	$component_attributes['expeditions'] = $expeditions;

	// Return built component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Disable translation for this block.
 *
 * @param string[] $blocks The block names.
 *
 * @return string[] The block names.
 */
function disable_translation( array $blocks = [] ): array {
	// Add block name to disable translation.
	$blocks[] = BLOCK_NAME;

	// Return block names.
	return $blocks;
}
