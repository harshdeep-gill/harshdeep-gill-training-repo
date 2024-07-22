<?php
/**
 * Block: Season Highlight - Season.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SeasonHighlights\Season;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
