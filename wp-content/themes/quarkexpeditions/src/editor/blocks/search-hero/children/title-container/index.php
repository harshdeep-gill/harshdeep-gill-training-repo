<?php
/**
 * Block: Search Hero - Title Container.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchHero\TitleContainer;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
