<?php
/**
 * Block: Search Hero - Search Bar.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchHero\SearchBar;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
