<?php
/**
 * Block: Book Departures Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BookDeparturesShips;

use WP_Post;

use function Quark\Localization\get_current_currency;
use function Quark\Ships\get as get_ship;

const COMPONENT = 'parts.book-departures-ships';

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
 * @param mixed[] $attributes The block attributes.
 * @param string  $content    The block content.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '' ): string {
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
