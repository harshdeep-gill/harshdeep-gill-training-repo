<?php
/**
 * Block: Form - Onboard Email Opt In
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormOnboardEmailOptIn;

use WP_Block;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

const COMPONENT = 'form-communications-opt-in';

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
		'thank_you_page' => isset( $attributes['thankYouPage'] ) && is_array( $attributes['thankYouPage'] ) ? $attributes['thankYouPage']['url'] : '',
	];

	// Return built component.
	return quark_get_component( COMPONENT, $component_attributes );
}
