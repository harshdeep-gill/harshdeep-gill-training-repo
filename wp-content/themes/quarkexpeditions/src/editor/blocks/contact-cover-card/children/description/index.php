<?php
/**
 * Block: Contact Cover Card Description.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ContactCoverCardDescription;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
