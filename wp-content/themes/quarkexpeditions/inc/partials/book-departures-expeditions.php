<?php
/**
 * Partial: Book Departures Expeditions.
 *
 * @package quark
 */

namespace Quark\Theme\Partials\BookDeparturesExpeditions;

use function Quark\Search\Departures\search;
use function Quark\Departures\get_cards_data;
use function Quark\Localization\get_current_currency;

const PARTIAL_NAME = 'book-departures-expeditions';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Hook this partial in.
	add_filter( 'qrk_get_partial', __NAMESPACE__ . '\\render', 10, 3 );
}

/**
 * Render the partial.
 *
 * @param mixed[] $output Partial output.
 * @param string  $name   Partial name.
 * @param mixed[] $data   Partial data.
 *
 * @return mixed[]
 */
function render( array $output = [], string $name = '', array $data = [] ): array {
	// Check for partial name.
	if ( PARTIAL_NAME !== $name ) {
		return $output;
	}

	// Get currency.
	$currency = get_current_currency();

	// Init selected filters.
	$selected_filter = [
		'posts_per_load' => 4,
		'expeditions'    => [],
	];

	// Verify and get selected filters.
	if ( ! empty( $data['selectedFilters'] ) && is_array( $data['selectedFilters'] ) ) {
		$selected_filter = wp_parse_args(
			$data['selectedFilters'],
			$selected_filter
		);
	}

	// Search for Departure post.
	$search_results = search( $selected_filter );

	// Build component attributes.
	$attributes = [
		'cards' => get_cards_data( array_map( 'absint', $search_results['ids'] ), $currency ),
	];

	// Return rendered partial.
	return [
		'markup'          => quark_get_component(
			'parts.expedition-departure-cards',
			$attributes
		),
		'noResultsMarkup' => __( 'No results found.', 'qrk' ),
		'data'            => [
			'resultCount'    => $search_results['result_count'],
			'page'           => $search_results['current_page'],
			'nextPage'       => $search_results['next_page'],
			'remainingCount' => $search_results['remaining_count'],
		],
	];
}
