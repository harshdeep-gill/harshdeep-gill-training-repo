<?php
/**
 * Block Name: Expedition Search.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExpeditionSearch;

use WP_Block;

use function Quark\Departures\get_cards_data;
use function Quark\Localization\get_current_currency;
use function Quark\Search\Departures\search;
use function Quark\Search\Filters\get_selected_filters_from_query_params;
use function Quark\Theme\Search_Filters\get_filters_for_sidebar_search;

const COMPONENT  = 'parts.expedition-search';
const BLOCK_NAME = 'quark/expedition-search';

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

	// Current currency.
	$currency = get_current_currency();

	// Selected filters from query params.
	$filter_query = get_selected_filters_from_query_params();

	// Init selected filters.
	$selected_filter = array_merge(
		[
			'posts_per_load' => 8,
			'currency'       => $currency,
			'sort'           => [ 'date-now' ],
		],
		$filter_query
	);

	// Search for Departure post.
	$search_results = search( $selected_filter );

	// Build component attributes.
	$component_attributes = [
		'result_count'    => $search_results['result_count'],
		'remaining_count' => $search_results['remaining_count'],
		'cards'           => get_cards_data( array_map( 'absint', $search_results['ids'] ), $currency ),
		'currency'        => $currency,
		'filters_data'    => get_filters_for_sidebar_search(),
		'page'            => $search_results['current_page'],
		'next_page'       => $search_results['next_page'],
	];

	// Return the rendered component.
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
