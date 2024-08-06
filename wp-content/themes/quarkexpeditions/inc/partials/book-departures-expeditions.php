<?php
/**
 * Partial: Book Departures Expeditions.
 *
 * @package quark
 */

namespace Quark\Theme\Partials\BookDeparturesExpeditions;

use function Quark\Search\Departures\search;

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

	// Search for Departure post.
	$search_results = search( (array) $data['selectedFilters'] );

	// Build component attributes.
	$attributes = [
		'cards' => '',
	];

	// Return rendered partial.
	return [
		'markup'          => quark_get_component(
			'parts.expedition-departure-cards',
			$attributes
		),
		'noResultsMarkup' => 'No results found.', // TODO: Add no results markup.
		'data'            => [
			'resultCount' => $search_results['result_count'],
			'page'        => $search_results['current_page'],
			'nextPage'    => $search_results['next_page'],
		],
	];
}
