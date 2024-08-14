<?php
/**
 * Block: Ship Cabin Categories
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ShipCabinCategories;

use WP_Block;
use WP_Post;

use function Quark\Ships\get as get_ship;
use function Quark\Ships\get_cabins_and_decks;

const COMPONENT = 'parts.ship-cabin-categories';

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
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Get the ship.
	$ship = get_ship();

	// Check if the ship is empty.
	if ( ! $ship['post'] instanceof WP_Post ) {
		return $content;
	}

	// Get the cabin categories data.
	$component_attributes = [
		'items' => get_cabins_and_decks( $ship['post']->ID ),
	];

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
