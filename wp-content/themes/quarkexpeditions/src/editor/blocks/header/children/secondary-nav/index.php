<?php
/**
 * Block: Header - Secondary Nav.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header\SecondaryNav;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
