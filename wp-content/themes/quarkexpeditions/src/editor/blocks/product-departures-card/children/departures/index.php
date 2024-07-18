<?php
/**
 * Block: Product Departures Card Departures.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ProductDeparturesCardDepartures;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
