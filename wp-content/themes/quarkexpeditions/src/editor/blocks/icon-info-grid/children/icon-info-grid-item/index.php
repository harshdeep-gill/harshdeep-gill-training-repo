<?php
/**
 * Block: Icon Info Grid Item.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\IconInfoGridItem;

use WP_Block;

const COMPONENT = 'icon-info-grid.item';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__
	);
}
