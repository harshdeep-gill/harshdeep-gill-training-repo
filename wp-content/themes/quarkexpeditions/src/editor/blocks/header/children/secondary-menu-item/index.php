<?php
/**
 * Block: Header - Secondary Menu Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\SecondaryMenuItem;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
