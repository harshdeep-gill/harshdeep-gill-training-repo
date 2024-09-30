<?php
/**
 * Block: Tabs - Tab.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Tabs\Tab;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
