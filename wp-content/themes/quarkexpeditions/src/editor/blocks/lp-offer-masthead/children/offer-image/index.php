<?php
/**
 * Block: LP Offer Masthead Offer Image.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPOfferMastheadOfferImage;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
