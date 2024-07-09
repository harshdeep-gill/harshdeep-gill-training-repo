<?php
/**
 * Block: Author Info - Name.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\AuthorInfo\Name;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
