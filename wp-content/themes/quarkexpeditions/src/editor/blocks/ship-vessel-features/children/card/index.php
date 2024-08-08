<?php
/**
 * Block: Ship Vessel Features - Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Ship_Vessel_Features\Card;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
