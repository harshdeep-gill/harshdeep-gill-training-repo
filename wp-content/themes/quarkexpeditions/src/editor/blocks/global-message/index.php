<?php
/**
 * Block: Icon Badge.
 *
 * @package quark
 */

namespace Quark\Theme\Block\GlobalMessage;

const COMPONENT = 'global-message';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	//Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
		);
}

/**
 * Render this block.
 *
 * @return string The block markup.
 */
function render(): string {
	//Return the markup.
	return quark_get_component( COMPONENT );
}
