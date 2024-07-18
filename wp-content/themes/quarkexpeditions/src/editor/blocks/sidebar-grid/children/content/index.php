<?php
/**
 * Block: Sidebar Grid - Content.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SidebarGrid\Content;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
