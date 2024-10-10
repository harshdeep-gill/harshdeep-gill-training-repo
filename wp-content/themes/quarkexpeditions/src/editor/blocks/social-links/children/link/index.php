<?php
/**
 * Block: Social Links - Link.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SocialLinks\Link;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
