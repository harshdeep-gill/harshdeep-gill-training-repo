<?php
/**
 * Block: Sidebar Grid - Sidebar.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SidebarGrid\Sidebar;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
