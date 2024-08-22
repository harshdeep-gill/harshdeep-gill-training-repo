<?php
/**
 * Block: Expedition Hero.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExpeditionHero;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'parts.expedition-hero';

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
			'render_callback'   => __NAMESPACE__ . '\\render',
			'skip_inner_blocks' => true,
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize component attributes.
	$component_attributes = [
		'expedition_details' => '',
		'hero_card_slider'   => '',
	];

	// Process child blocks.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Get the first child block.
		$hero_content = $block->inner_blocks->offsetGet( '0' );

		// Check for child block and its name.
		if (
			! $hero_content instanceof WP_Block
			|| 'quark/expedition-hero-content' !== $hero_content->name
			|| ! $hero_content->inner_blocks instanceof WP_Block_List
		) {
			return $content;
		}

		// Loop through the child blocks.
		foreach ( $hero_content->inner_blocks as $child_block ) {
			// Check for child block.
			if (
				! $child_block instanceof WP_Block
				|| ! $child_block->inner_blocks instanceof WP_Block_List
			) {
				continue;
			}

			// Switch on the child block name.
			switch ( $child_block->name ) {

				// Build the expedition details.
				case 'quark/expedition-hero-content-left':
					// Check for its child block.
					$expedition_details = $child_block->inner_blocks->offsetGet( '0' );

					// Check for child block.
					if (
						! $expedition_details instanceof WP_Block
						|| 'quark/expedition-details' !== $expedition_details->name
					) {
						break;
					}

					// Render the child block.
					$component_attributes['expedition_details'] = render_block( $expedition_details->parsed_block );
					break;

				// Build the hero card slider.
				case 'quark/expedition-hero-content-right':
					// Check for its child block.
					$hero_card_slider = $child_block->inner_blocks->offsetGet( '0' );

					// Check for child block.
					if (
						! $hero_card_slider instanceof WP_Block
						|| 'quark/hero-card-slider' !== $hero_card_slider->name
					) {
						break;
					}

					// Render the child block.
					$component_attributes['hero_card_slider'] = render_block( $hero_card_slider->parsed_block );
					break;
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
