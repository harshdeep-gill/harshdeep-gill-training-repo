<?php
/**
 * Block: Footer - Copyright.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer\Copyright;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
