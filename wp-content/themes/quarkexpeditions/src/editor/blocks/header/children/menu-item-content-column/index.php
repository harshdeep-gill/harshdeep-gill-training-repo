<?php
/**
 * Block: Header - Menu Item Content Column.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\MenuItemContentColumn;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
