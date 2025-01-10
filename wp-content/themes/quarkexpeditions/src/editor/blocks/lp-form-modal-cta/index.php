<?php
/**
 * Block: LP Form Modal CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPFormModalCta;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

const COMPONENT  = 'parts.lp-form-modal-cta';
const BLOCK_NAME = 'quark/lp-form-modal-cta';

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
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Initialize the component attributes.
	$component_attributes = [
		'text'          => $attributes['text'],
		'form_id'       => 'inquiry-form',
		'class'         => $attributes['className'] ?? '',
		'color'         => $attributes['backgroundColor'],
		'countries'     => get_countries(),
		'states'        => get_states(),
		'hidden_fields' => [
			'polar_region' => $attributes['polarRegion'],
			'season'       => $attributes['season'],
			'ship'         => $attributes['ship'],
			'sub_region'   => $attributes['subRegion'],
			'expedition'   => $attributes['expedition'],
		],
	];

	// Return the component.
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
