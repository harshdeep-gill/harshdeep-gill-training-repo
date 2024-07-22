<?php
/**
 * Block: Header - Menu Item Content.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\MenuItemContent;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
