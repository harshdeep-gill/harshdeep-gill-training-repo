<?php
/**
 * Block: Media Text CTA CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaTextCTACTA;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
