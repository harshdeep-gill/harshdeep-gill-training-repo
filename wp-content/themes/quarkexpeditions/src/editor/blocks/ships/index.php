<?php
/**
 * Block: Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Ships;

use WP_Query;

use const Quark\Ships\POST_TYPE as SHIPS_POST_TYPE;

const COMPONENT = 'parts.ships';

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
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {

	// If the selection is manual, we need to check if we have IDs.
	if ( 'manual' === $attributes['selectionType'] ) {
		// Return empty if manual select, but no IDs were selected.
		if ( empty( $attributes['ships'] ) ) {
			return '';
		}

		// Get the selected IDs.
		$ships_ids = $attributes['ships'];
	}

	// Check if we have posts.
	if ( empty( $ships_ids ) ) {
		return '';
	}

	// Get blog post cards data.
	$cards_data = [];

	// Query the posts.
	foreach( $ships_ids as $ship_id ) {
		$ship = get_post( $ship_id );

		// Skip if no post.
		if ( ! $ship ) {
			continue;
		}

		// Get Decks associated with the ship.

		// Get the post data.
		$cards_data[] = [
			'id'          => $ship->post_name,
			'title'       => $ship->post_title,
			'permalink'   => get_permalink( $ship ),
			'description' => $ship->post_content,
			'decks'       => [],
		];
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards' => $cards_data,
		]
	);
}
