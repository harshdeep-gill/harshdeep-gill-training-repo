<?php
/**
 * Block: Book Departures Expeditions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BookDeparturesExpeditions;

use WP_Block;
use WP_Post;

use function Quark\Departures\get_cards_data;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Search\Departures\search;

const COMPONENT = 'parts.book-departures-expeditions';

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
 * @param string        $content    The block content.
 * @param WP_Block|null $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Get the expedition.
	$expedition = get_expedition();

	// Check if the expedition is empty.
	if ( empty( $expedition['post'] ) || ! $expedition['post'] instanceof WP_Post ) {
		return $content;
	}

	// Get the expedition ID.
	$expedition_id = $expedition['post']->ID;

	// Set the currency.
	$currency = 'USD';

	// Init selected filters.
	$selected_filter = [
		'posts_per_load' => 4,
		'currency'       => $currency,
		'expeditions'    => [ absint( $expedition_id ) ],
	];

	// Search for Departure post.
	$search_results = search( $selected_filter );

	// Build component attributes.
	$cards_data = get_cards_data( array_map( 'absint', $search_results['ids'] ), $currency );

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards'         => $cards_data,
			'results_count' => $search_results['result_count'],
			'payload'       => [
				'resultCount'    => $search_results['result_count'],
				'page'           => $search_results['current_page'],
				'nextPage'       => $search_results['next_page'],
				'remainingCount' => $search_results['remaining_count'],
				'expeditions'    => [ $expedition_id ],
			],
		]
	);
}
