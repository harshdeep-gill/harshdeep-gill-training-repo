<?php
/**
 * Block: Form - CCPA Access Deletion Request
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormAccessDeletionRequest;

use WP_Block;

use function Quark\Leads\Forms\get_states;

const COMPONENT  = 'form-account-management';
const BLOCK_NAME = 'quark/form-access-deletion-request';

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

	// Extrating states of US.
	$states = get_states()['US'] ?? [];

	// Component attributes.
	$component_attributes = [
		'states'         => $states,
		'thank_you_page' => isset( $attributes['thankYouPage'] ) && is_array( $attributes['thankYouPage'] ) ? $attributes['thankYouPage']['url'] : '',
	];

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
