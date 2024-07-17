<?php
/**
 * Block: LP Offer Masthead Content.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPOfferMastheadContent;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
