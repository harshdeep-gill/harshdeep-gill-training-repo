<?php
/**
 * Block: Ship Features & Amenities - Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ShipFeaturesAmenities\Card;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
