<?php
/**
 * Block: Footer - Middle.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer\Middle;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
