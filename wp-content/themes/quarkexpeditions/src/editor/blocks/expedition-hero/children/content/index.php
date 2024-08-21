<?php
/**
 * Block: Expedition Hero Content.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExpeditionHero\Content;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
