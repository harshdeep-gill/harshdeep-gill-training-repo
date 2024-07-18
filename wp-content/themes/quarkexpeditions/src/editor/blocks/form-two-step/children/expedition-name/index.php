<?php
/**
 * Block: Form Two Step - Expedition Name.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep\ExpeditionName;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
