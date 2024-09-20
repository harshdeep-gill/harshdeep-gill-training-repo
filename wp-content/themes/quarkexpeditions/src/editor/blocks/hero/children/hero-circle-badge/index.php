<?php
/**
 * Block: Circle Badge.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Hero_Circle_Badge;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
