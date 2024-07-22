<?php
/**
 * Block: Form Two Step - Sub Region.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep\SubRegion;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
