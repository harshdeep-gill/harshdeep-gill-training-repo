<?php
/**
 * Block: Contact Cover Card Title.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ContactCoverCardTitle;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
