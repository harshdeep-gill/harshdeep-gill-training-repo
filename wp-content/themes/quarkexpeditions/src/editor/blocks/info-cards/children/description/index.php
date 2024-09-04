<?php
/**
 * Block: Info Cards - Description.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards\Description;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
