<?php
/**
 * Block: Header - Menu Item Featured Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\MenuItemFeaturedSection;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
