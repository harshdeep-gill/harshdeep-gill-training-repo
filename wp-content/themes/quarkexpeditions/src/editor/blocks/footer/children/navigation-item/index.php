<?php
/**
 * Block: Footer - Navigation Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer\NavigationItem;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
