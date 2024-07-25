<?php
/**
 * Block: Secondary Navigation.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SecondaryNavigation;

/**
 * Bootstrap the block.
 *
 * @return void
 */
function bootstrap(): void {
    // Register the block.
    register_block_type_from_metadata( __DIR__ );
}