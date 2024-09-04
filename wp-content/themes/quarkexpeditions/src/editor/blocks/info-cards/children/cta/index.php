<?php
/**
 * Block: Info Cards - CTA.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards\CTA;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
