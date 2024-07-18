<?php
/**
 * Block: Media Content Card Column.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaContentCardColumn;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
