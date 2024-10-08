<?php
/**
 * Block: Itineraries.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Itineraries;

use WP_Block;
use WP_Post;
use WP_Term;

use function Quark\Brochures\get as get_brochure;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Itineraries\get as get_itinerary;
use function Quark\Itineraries\get_details_tabs_data;
use function Quark\Itineraries\get_season as get_itinerary_season;
use function Quark\ItineraryDays\get as get_itinerary_day;

use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;

const COMPONENT = 'parts.itineraries';

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

	// Get the expedition.
	$expedition = get_expedition();

	// Validate the expedition.
	if ( empty( $expedition['post'] ) || ! $expedition['post'] instanceof WP_Post ) {
		return $content;
	}

	// Check if the expedition meta is empty.
	if ( empty( $expedition['post_meta']['related_itineraries'] ) ) {
		return $content;
	}

	// Get the itineraries.
	$itineraries = $expedition['post_meta']['related_itineraries'];

	// Check if the itineraries is an array.
	if ( ! is_array( $itineraries ) ) {
		return $content;
	}

	// Get detail tabs data.
	$component_attributes = get_details_tabs_data( $itineraries, $expedition['post']->ID );

	// Build the component attributes.
	return quark_get_component( COMPONENT, $component_attributes );
}
