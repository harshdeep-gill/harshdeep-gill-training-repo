<?php
/**
 * Block: Collage Media Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\BentoCollageItem;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
