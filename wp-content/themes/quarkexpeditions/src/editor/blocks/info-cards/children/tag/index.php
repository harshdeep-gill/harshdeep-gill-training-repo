<?php
/**
 * Block: Info Cards - Tag.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards\Tags;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
