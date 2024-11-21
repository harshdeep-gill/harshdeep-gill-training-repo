<?php
/**
 * Block: Trip Extensions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TripExtensions;

use WP_Block;
use WP_Post;

use function Quark\Expeditions\get as get_expedition;
use function Quark\Expeditions\PrePostTripOptions\get as get_pre_post_trip_option;

const COMPONENT = 'parts.trip-extensions';

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
	if ( empty( $expedition['post_meta']['related_pre_post_trips'] ) || ! is_array( $expedition['post_meta']['related_pre_post_trips'] ) ) {
		return $content;
	}

	// Get the pre-post trips.
	$pre_post_trips = [];

	// Loop through the related pre-post trips.
	foreach ( $expedition['post_meta']['related_pre_post_trips'] as $pre_post_trip ) {
		// Get the pre-post trip option.
		$pre_post_trip = get_pre_post_trip_option( absint( $pre_post_trip ) );

		// Check if the related pre-post trip is empty.
		if ( ! $pre_post_trip['post'] instanceof WP_Post || 'publish' !== $pre_post_trip['post']->post_status ) {
			continue;
		}

		// Prepare pre-post trip data.
		$pre_post_trips[] = [
			'title'       => $pre_post_trip['post']->post_title,
			'description' => apply_filters( 'the_content', $pre_post_trip['post']->post_content ),
			'thumbnail'   => $pre_post_trip['post_thumbnail'],
		];
	}

	// Prepare component attributes.
	$component_attributes = [
		'show_title'       => $block->attributes['showTitle'],
		'show_description' => $block->attributes['showDescription'],
		'pre_post_trips'   => $pre_post_trips,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
