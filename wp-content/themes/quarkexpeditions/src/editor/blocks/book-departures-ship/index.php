<?php
/**
 * Block: Book Departures Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BookDeparturesShips;

use WP_Post;

use function Quark\Core\is_china_website;
use function Quark\Localization\get_current_currency;
use function Quark\Ships\get as get_ship;

const COMPONENT  = 'parts.book-departures-ships';
const BLOCK_NAME = 'quark/book-departures-ships';

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
	// Bail out if its china site.
	if ( is_china_website() ) {
		return '';
	}

	// Get the ship.
	$ship = get_ship();

	// Check if the ship is empty.
	if ( empty( $ship['post'] ) || ! $ship['post'] instanceof WP_Post ) {
		return $content;
	}

	// Get the expedition ID.
	$ship_id = $ship['post']->ID;

	// Set the currency.
	$currency = get_current_currency();

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'ship_id'  => $ship_id,
			'currency' => $currency,
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
