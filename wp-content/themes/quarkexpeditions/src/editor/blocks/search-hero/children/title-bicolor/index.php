<?php
/**
 * Block: Search Hero - Title Bicolor.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchHero\TitleBicolor;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
