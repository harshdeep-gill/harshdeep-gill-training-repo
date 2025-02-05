<?php
/**
 * Block: Media Description Cards - Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\MediaDescriptionCards\Card;

const BLOCK_NAME = 'quark/media-description-card';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata( __DIR__ );

	// Add block attributes to translate.
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
		'image' => [ 'image' ],
		'text'  => [
			'title',
			'description',
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
