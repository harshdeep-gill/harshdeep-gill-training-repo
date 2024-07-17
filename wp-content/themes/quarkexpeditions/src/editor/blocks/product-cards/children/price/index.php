<?php
/**
 * Block: Product Cards Price.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ProductCardsPrice;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
