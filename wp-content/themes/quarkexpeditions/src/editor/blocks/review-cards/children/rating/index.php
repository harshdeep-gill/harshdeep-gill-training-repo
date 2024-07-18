<?php
/**
 * Block: Review Cards - Rating.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewCards\Rating;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
