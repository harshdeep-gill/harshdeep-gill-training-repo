<?php
/**
 * Block: Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPHeader;

const COMPONENT  = 'lp-header';
const BLOCK_NAME = 'quark/lp-header';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes The block attributes.
 *
 * @return string
 */
function render( array $attributes = [] ): string {
	// Build component attributes.
	$component_attributes = [
		'logo_url'     => 'https://www.quarkexpeditions.com',
		'tc_image_id'  => 0,
		'phone_number' => $attributes['ctaNumber'],
		'cta_text'     => $attributes['ctaText'],
		'dark_mode'    => $attributes['darkMode'],
	];

	// TC Image.
	if ( is_array( $attributes['tcImage'] ) && ! empty( $attributes['tcImage']['id'] ) ) {
		$component_attributes['tc_image_id'] = absint( $attributes['tcImage']['id'] );
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
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
		'text'  => [ 'ctaText' ],
		'image' => [ 'tcImage' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
