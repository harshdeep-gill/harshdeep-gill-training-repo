<?php
/**
 * Block: Related Adventure Options.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\RelatedAdventureOptions;

use WP_Block;
use WP_Post;

use function Quark\AdventureOptions\get as get_adventure_option;
use function Quark\Expeditions\get as get_expedition;

const COMPONENT = 'parts.related-adventure-options';

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
	if ( empty( $expedition['post_meta']['related_adventure_options'] ) || ! is_array( $expedition['post_meta']['related_adventure_options'] ) ) {
		return $content;
	}

	// Get the adventure options.
	$adventure_options = [];

	// Loop through the related adventure options.
	foreach ( $expedition['post_meta']['related_adventure_options'] as $adventure_option ) {
		// Get the adventure option.
		$adventure_option = get_adventure_option( absint( $adventure_option ) );

		// Check if the related adventure option is empty.
		if ( ! $adventure_option['post'] instanceof WP_Post ) {
			continue;
		}

		// Prepare adventure option data.
		$adventure_options[] = [
			'title'          => $adventure_option['post']->post_title,
			'description'    => $adventure_option['post']->post_excerpt,
			'featured_image' => $adventure_option['post_thumbnail'],
		];
	}

	// Prepare component attributes.
	$component_attributes = [
		'show_title'        => $block->attributes['showTitle'],
		'show_description'  => $block->attributes['showDescription'],
		'adventure_options' => $adventure_options,
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
