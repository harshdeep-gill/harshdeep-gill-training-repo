<?php
/**
 * Block: Book Departures Expeditions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BookDeparturesExpeditions;

use WP_Post;

use function Quark\Expeditions\get as get_expedition;
use function Quark\Localization\get_current_currency;

const COMPONENT  = 'parts.book-departures-expeditions';
const BLOCK_NAME = 'quark/book-departures-expeditions';

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
 * @param string  $content    The block content.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '' ): string {
	// Get the expedition.
	$expedition = get_expedition();

	// Check if the expedition is empty.
	if ( empty( $expedition['post'] ) || ! $expedition['post'] instanceof WP_Post ) {
		return $content;
	}

	// Get the expedition ID.
	$expedition_id = $expedition['post']->ID;

	// Set the currency.
	$currency = get_current_currency();

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'expedition_id' => $expedition_id,
			'currency'      => $currency,
		]
	);
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
