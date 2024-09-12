<?php
/**
 * Block: Info Cards - Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards\Card;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
