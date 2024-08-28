<?php
/**
 * Block: Book Departures Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BookDeparturesShips;

use WP_Post;

use function Quark\Departures\get_cards_data;
use function Quark\Ships\get as get_ship;
use function Quark\Search\Departures\search;

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
	$currency = 'USD';

	// Init selected filters.
	$selected_filter = [
		'posts_per_load' => 5,
		'currency'       => $currency,
		'ships'          => [ absint( $ship_id ) ],
	];

	// Search for Departure post.
	$search_results = search( $selected_filter );

	// Build component attributes.
	$cards_data = get_cards_data( array_map( 'absint', $search_results['ids'] ), $currency );

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards'           => $cards_data,
			'remaining_count' => $search_results['remaining_count'],
			'results_count'   => $search_results['result_count'],
			'ship_id'         => $ship_id,
		]
	);
}
