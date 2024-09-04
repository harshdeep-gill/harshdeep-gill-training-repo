<?php
/**
 * Block: Info Cards - Title.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards\Title;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
