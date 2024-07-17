<?php
/**
 * Block: Footer - Column Title.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer\ColumnTitle;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
