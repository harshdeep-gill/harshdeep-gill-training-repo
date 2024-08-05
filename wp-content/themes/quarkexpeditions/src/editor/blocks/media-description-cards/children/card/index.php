<?php
/**
 * Block: Media Description Cards - Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaDescriptionCards\Card;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
