<?php
/**
 * Block: Header - Contact Button.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\ContactButton;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
