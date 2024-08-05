<?php
/**
 * Block: Secondary Navigation - Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SecondaryNavigation\Item;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
