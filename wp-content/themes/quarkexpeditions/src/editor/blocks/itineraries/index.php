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

	// Check if the expedition is empty.
	if ( empty( $expedition['post_meta']['related_itineraries'] ) ) {
		return $content;
	}

	// Get the itineraries.
	$itineraries = $expedition['post_meta']['related_itineraries'];

	// Check if the itineraries is an array.
	if ( ! is_array( $itineraries ) ) {
		return $content;
	}

	// Build the component attributes.
	$component_attributes = [];

	// Loop through the itineraries.
	foreach ( $itineraries as $index => $itinerary ) {
		// Get the itinerary.
		$itinerary = get_itinerary( $itinerary );

		// Check if the itinerary is empty.
		if ( ! $itinerary['post'] instanceof WP_Post ) {
			continue;
		}

		// Initialize variables for the component attributes.
		$tab_id             = sprintf( 'tab-%d', absint( $index ) + 1 );
		$tab_title          = '';
		$tab_subtitle       = '';
		$duration           = '';
		$tab_content_header = '';
		$departing_from     = '';
		$itinerary_days     = [];
		$brochure           = '';
		$price              = '';
		$ship               = [];

		// Prepare the tab title.
		if ( ! empty( $itinerary['post_meta']['duration_in_days'] ) ) {
			$tab_title = sprintf( '%d %s', absint( $itinerary['post_meta']['duration_in_days'] ), _n( 'Day', 'Days', absint( $itinerary['post_meta']['duration_in_days'] ), 'quark' ) );
			$duration  = sprintf( '%d %s', absint( $itinerary['post_meta']['duration_in_days'] ), _n( 'day', 'days', absint( $itinerary['post_meta']['duration_in_days'] ), 'quark' ) );
		}

		// Prepare the tab subtitle.
		if ( ! empty( $itinerary['post_meta']['start_location'] ) ) {
			$start_location = get_term_by( 'id', absint( $itinerary['post_meta']['start_location'] ), DEPARTURE_LOCATION_TAXONOMY );

			// Check if the start location is not empty.
			if ( $start_location instanceof WP_Term ) {
				// Set the departing from.
				$departing_from = $start_location->name;
				$tab_subtitle   = sprintf( 'From %s', $start_location->name );
			}
		}

		// Prepare the tab content header.
		if ( ! empty( $tab_subtitle ) && ! empty( $duration ) ) {
			$tab_content_header = sprintf( '%s, %s, on Ultramarine', $tab_subtitle, $duration );
		}

		// Prepare the itinerary days accordion content.
		if ( ! empty( $itinerary['post_meta']['itinerary_days'] ) && is_array( $itinerary['post_meta']['itinerary_days'] ) ) {
			foreach ( $itinerary['post_meta']['itinerary_days'] as $itinerary_day ) {
				// Get the itinerary day.
				$itinerary_day = get_itinerary_day( $itinerary_day );

				// Check if the itinerary day is empty.
				if ( ! $itinerary_day['post'] instanceof WP_Post ) {
					continue;
				}

				// Append the itinerary day with the title and content.
				$itinerary_days[] = [
					'title'   => prepare_itinerary_day_title( $itinerary_day['post']->ID ),
					'content' => $itinerary_day['post']->post_content,
				];
			}
		}

		// Check if the itinerary has a brochure.
		if ( ! empty( $itinerary['post_meta']['brochure'] ) ) {
			$_brochure = get_brochure( absint( $itinerary['post_meta']['brochure'] ) );

			// Check if the brochure pdf is not empty.
			if ( ! empty( $_brochure['post_meta']['brochure_pdf'] ) ) {
				$brochure = wp_get_attachment_url( absint( $_brochure['post_meta']['brochure_pdf'] ) );
			}
		}

		// Append the itinerary to the component attributes.
		$component_attributes['itineraries'][] = [
			'tab_id'             => $tab_id,
			'tab_title'          => $tab_title,
			'tab_subtitle'       => $tab_subtitle,
			'tab_content_header' => $tab_content_header,
			'duration'           => $duration,
			'departing_from'     => $departing_from,
			'itinerary_days'     => $itinerary_days,
			'map'                => $itinerary['post_meta']['map'] ?? 0,
			'price'              => $price,
			'brochure'           => $brochure,
			'ship'               => $ship,
		];
	}

	// Build the component attributes.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Prepare the itinerary day title for display.
 *
 * @param int $itinerary_day The itinerary day ID.
 *
 * @return string The itinerary day title.
 */
function prepare_itinerary_day_title( int $itinerary_day = 0 ): string {
	// Get the itinerary day.
	$itinerary_day = get_itinerary_day( $itinerary_day );

	// Check if the itinerary day is empty.
	if ( ! $itinerary_day['post'] instanceof WP_Post ) {
		return '';
	}

	// Check if the itinerary day has a title.
	if ( empty( $itinerary_day['post_meta']['day_title'] ) ) {
		return '';
	}

	// Check if the itinerary day is empty.
	if ( empty( $itinerary_day['post_meta']['day_number_from'] ) || empty( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return strval( $itinerary_day['post_meta']['day_title'] );
	}

	// Example: Day 1: Day Title.
	if ( absint( $itinerary_day['post_meta']['day_number_from'] ) === absint( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return sprintf(
			'Day %s: %s',
			$itinerary_day['post_meta']['day_number_from'],
			strval( $itinerary_day['post_meta']['day_title'] )
		);
	}

	// Return: Day 1 & 2: Day Title.
	if ( absint( $itinerary_day['post_meta']['day_number_from'] ) + 1 === absint( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return sprintf(
			'Day %s & %s: %s',
			$itinerary_day['post_meta']['day_number_from'],
			$itinerary_day['post_meta']['day_number_to'],
			strval( $itinerary_day['post_meta']['day_title'] )
		);
	}

	// Return: Day 3 to 5: Day Title.
	if ( absint( $itinerary_day['post_meta']['day_number_from'] ) + 1 < absint( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return sprintf(
			'Day %s to %s: %s',
			$itinerary_day['post_meta']['day_number_from'],
			$itinerary_day['post_meta']['day_number_to'],
			strval( $itinerary_day['post_meta']['day_title'] )
		);
	}

	// Return: the day title.
	return strval( $itinerary_day['post_meta']['day_title'] );
}
