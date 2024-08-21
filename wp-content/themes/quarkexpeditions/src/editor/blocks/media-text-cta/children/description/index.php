<?php
/**
 * Block: Media Text CTA Description.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaTextCTADescription;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
