<?php
/**
 * Block: Media Content Card Content Info.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaContentCardContentInfo;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
