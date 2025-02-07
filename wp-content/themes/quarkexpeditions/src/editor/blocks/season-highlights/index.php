<?php
/**
 * Block: Season Highlights.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SeasonHighlights;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.season-highlights';
const BLOCK_NAME = 'quark/season-highlights';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
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

	// Initialize Attributes.
	$component_attributes = [
		'seasons' => [],
	];

	// Prepare block data.
	foreach ( $block->inner_blocks as $season_block ) {
		// Check for season block.
		if (
			! $season_block instanceof WP_Block
			|| ! $season_block->inner_blocks instanceof WP_Block_List
			|| 'quark/season-highlights-season' !== $season_block->name
		) {
			continue;
		}

		// Initialize Season.
		$season = [];

		// Add attributes.
		$season['title'] = $season_block->attributes['title'];

		// Build block data.
		foreach ( $season_block->inner_blocks as $season_block_child ) {
			// Check for season block child.
			if (
				! $season_block_child instanceof WP_Block
				|| ! $season_block_child->inner_blocks instanceof WP_Block_List
				|| 'quark/season-highlights-season-item' !== $season_block_child->name
			) {
				continue;
			}

			// Initialize Item.
			$season_item = [];

			// Add attributes.
			$season_item['title'] = $season_block_child->attributes['title'];
			$season_item['light'] = $season_block_child->attributes['hasLightBackground'];

			// Loop through highlights.
			foreach ( $season_block_child->inner_blocks as $highlight_block ) {
				// Check for highlight block.
				if ( ! $highlight_block instanceof WP_Block || 'quark/season-highlights-highlight' !== $highlight_block->name ) {
					continue;
				}

				// Initialize Highlight.
				$highlight = [];

				// Add attributes.
				$highlight['icon']  = $highlight_block->attributes['icon'];
				$highlight['title'] = $highlight_block->attributes['title'];

				// Add highlight to item.
				$season_item['highlights'][] = $highlight;
			}

			// Add current season item to items.
			$season['items'][] = $season_item;
		}

		// Add season to seasons.
		$component_attributes['seasons'][] = $season;
	}

	// Render the block.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-highlight' ] = [
		'text' => [ 'title' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-season' ] = [
		'text' => [ 'title' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-season-item' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
