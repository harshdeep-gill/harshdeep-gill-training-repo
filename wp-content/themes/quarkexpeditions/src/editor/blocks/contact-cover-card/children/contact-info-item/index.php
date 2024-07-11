<?php
/**
 * Block: Contact Cover Card Contact Info Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ContactCoverCardContactInfoItem;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
