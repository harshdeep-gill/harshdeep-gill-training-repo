<?php
/**
 * Block: Search Title - Overline.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchHero\Overline;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
