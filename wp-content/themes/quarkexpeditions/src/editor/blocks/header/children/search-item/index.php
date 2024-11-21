<?php
/**
 * Block: Header - Search Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\SearchItem;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
