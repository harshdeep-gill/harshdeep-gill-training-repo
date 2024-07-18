<?php
/**
 * Block: Form Two Step - Pax Count.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep\PaxCount;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
