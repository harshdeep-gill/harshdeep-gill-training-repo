<?php
/**
 * Block: Included Activities.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IncludedActivities;

use WP_Block;
use WP_Post;

use function Quark\AdventureOptions\get as get_activity;
use function Quark\Expeditions\get as get_expedition;

const COMPONENT = 'parts.included-activities';

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
	if ( empty( $expedition['post_meta']['included_activities'] ) || ! is_array( $expedition['post_meta']['included_activities'] ) ) {
		return $content;
	}

	// Get the activities.
	$activities = [];

	// Loop through the included activities.
	foreach ( $expedition['post_meta']['included_activities'] as $activity ) {
		// Get the activity.
		$activity = get_activity( absint( $activity ) );

		// Check if the activity is empty.
		if ( ! $activity['post'] instanceof WP_Post ) {
			continue;
		}

		// Check if the activity is published.
		if ( 'publish' !== $activity['post']->post_status ) {
			continue;
		}

		// Prepare activity data.
		$activities[] = [
			'title'       => $activity['post']->post_title,
			'description' => apply_filters( 'the_content', $activity['post']->post_excerpt ),
			'thumbnail'   => $activity['post_thumbnail'],
			'permalink'   => $activity['permalink'],
		];
	}

	// Prepare component attributes.
	$component_attributes = [
		'show_title'       => $block->attributes['showTitle'],
		'show_description' => $block->attributes['showDescription'],
		'activities'       => $activities,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
