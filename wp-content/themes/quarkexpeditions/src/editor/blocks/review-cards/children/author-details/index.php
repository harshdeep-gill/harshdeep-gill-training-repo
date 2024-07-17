<?php
/**
 * Block: Author Details.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewCards\AuthorDetails;

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );
}
