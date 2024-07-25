<?php
/**
 * Block: Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPHeader;

const COMPONENT = 'lp-header';

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
