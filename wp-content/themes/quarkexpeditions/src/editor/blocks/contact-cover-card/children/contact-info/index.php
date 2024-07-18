<?php
/**
 * Block: Contact Cover Card Contact Info.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ContactCoverCardContactInfo;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
