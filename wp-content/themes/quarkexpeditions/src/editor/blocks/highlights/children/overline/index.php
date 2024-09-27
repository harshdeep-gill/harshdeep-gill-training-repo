<?php
/**
 * Block: Highlight Item Overline.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\HighlightItemOverline;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
