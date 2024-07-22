<?php
/**
 * Block: Header - Mega Menu.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\MegaMenu;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
