<?php
/**
 * Block: Info Cards - Overline.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards\Overline;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
