<?php
/**
 * Block: Season Highlight - Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SeasonHighlights\Item;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
