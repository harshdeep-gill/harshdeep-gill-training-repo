<?php
/**
 * Block: Season Highlights.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SeasonHighlights;

const BLOCK_NAME = 'quark/season-highlights';
const COMPONENT  = 'parts.season-highlights';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize Attributes.
	$attributes = [
		'seasons' => [],
	];

	// Prepare block data.
	foreach ( $block['innerBlocks'] as $season_block ) {
		// Check for season block.
		if (
			empty( $season_block['innerBlocks'] )
			|| ! is_array( $season_block['innerBlocks'] )
			|| 'quark/season-highlights-season' !== $season_block['blockName']
		) {
			continue;
		}

		// Initialize Season.
		$season = [];

		// Add attributes.
		$season['title'] = $season_block['attrs']['title'] ?? '';

		// Build block data.
		foreach ( $season_block['innerBlocks'] as $season_item_block ) {
			if (
				empty( $season_item_block['innerBlocks'] )
				|| ! is_array( $season_item_block['innerBlocks'] )
				|| 'quark/season-highlights-season-item' !== $season_item_block['blockName']
			) {
				continue;
			}

			// Initialize Item.
			$season_item = [];

			// Add attributes.
			$season_item['title'] = $season_item_block['attrs']['title'];
			$season_item['light'] = $season_item_block['attrs']['hasLightBackground'] ?? false;

			// Loop through highlights.
			foreach ( $season_item_block['innerBlocks'] as $highlight_block ) {
				if ( 'quark/season-highlights-highlight' !== $highlight_block['blockName'] ) {
					continue;
				}

				// Initialize highlight.
				$highlight = [];

				// Add attributes.
				$highlight['icon']  = $highlight_block['attrs']['icon'] ?? '';
				$highlight['title'] = $highlight_block['attrs']['title'] ?? '';

				// Add highlight.
				$season_item['highlights'][] = $highlight;
			}

			// Add current season item to items.
			$season['items'][] = $season_item;
		}

		// Add current season to seasons.
		$attributes['seasons'][] = $season;
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
