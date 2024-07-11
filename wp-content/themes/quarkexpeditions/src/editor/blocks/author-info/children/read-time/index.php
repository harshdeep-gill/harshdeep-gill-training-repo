<?php
/**
 * Block: Author Info - Read Time.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\AuthorInfo\ReadTime;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
