<?php
/**
 * Block: Form Two Step - Landing Form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep\LandingForm;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
