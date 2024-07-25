<?php
/**
 * Block: Footer - Bottom
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer\Bottom;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
