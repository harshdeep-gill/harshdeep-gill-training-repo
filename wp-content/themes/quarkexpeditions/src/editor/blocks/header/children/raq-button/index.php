<?php
/**
 * Block: Header - Request a Quote Button.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\RaqButton;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
