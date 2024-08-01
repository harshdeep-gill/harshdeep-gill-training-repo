<?php
/**
 * Block: Secondary Navigation - Buttons.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SecondaryNavigation\Buttons;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
