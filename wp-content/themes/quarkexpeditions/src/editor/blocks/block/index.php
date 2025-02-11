<?php
/**
 * Block: Reusable block.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Block;

const BLOCK_NAME = 'core/block';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Translation.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME ] = [
		'post_id' => [ 'ref' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
