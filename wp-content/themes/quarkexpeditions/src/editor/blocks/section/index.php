<?php
/**
 * Block: Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Section;

const COMPONENT = 'parts.section';

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
 * @param string  $content    The block default content.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '' ): string {
	// Build component attributes.
	$component_attributes = [
		'id'                => $attributes['anchor'],
		'title'             => '',
		'title_align'       => $attributes['titleAlignment'],
		'description'       => '',
		'background'        => $attributes['hasBackground'],
		'background_color'  => $attributes['backgroundColor'],
		'background_image'  => isset( $attributes['backgroundImage'] ) ? $attributes['backgroundImage']['id'] : '',
		'gradient_position' => $attributes['gradientPosition'],
		'gradient_color'    => $attributes['gradientColor'],
		'padding'           => $attributes['hasPadding'],
		'narrow'            => $attributes['isNarrow'],
		'slot'              => $content,
		'heading_link'      => [],
		'has_heading_link'  => $attributes['hasHeadingLink'],
	];

	// Set heading link if it exists.
	if (
		is_array( $attributes['headingLink'] )
		&& ! empty( $attributes['headingLink']['text'] )
		&& ! empty( $attributes['headingLink']['url'] )
	) {
		$component_attributes['heading_link'] = [
			'text'       => $attributes['headingLink']['text'],
			'url'        => $attributes['headingLink']['url'],
			'new_window' => $attributes['headingLink']['newWindow'],
		];
	}

	// Set title if it exists.
	if ( ( $attributes['hasTitle'] ?? true ) && ! empty( $attributes['title'] ) ) {
		$component_attributes['title'] = $attributes['title'];
	}

	// Set heading level if it exists.
	if ( ! empty( $attributes['headingLevel'] ) ) {
		$component_attributes['heading_level'] = $attributes['headingLevel'];
	}

	// Set description if it exists.
	if ( ( $attributes['hasDescription'] ?? false ) && ! empty( $attributes['description'] ) ) {
		$component_attributes['description'] = $attributes['description'];
	}

	// Set CTA if it exists.
	if ( ( $attributes['hasCta'] ?? false ) && is_array( $attributes['ctaButton'] ) ) {
		$component_attributes['cta_button'] = [
			'url'        => $attributes['ctaButton']['url'],
			'text'       => $attributes['ctaButton']['text'],
			'new_window' => $attributes['ctaButton']['newWindow'],
			'class'      => '',
		];

		// If background is dark, then add dark context class.
		if (
			! empty( $attributes['hasBackground'] ) &&
			( ! empty( $attributes['backgroundColor'] ) && 'black' === $attributes['backgroundColor'] )
		) {
			$component_attributes['cta_button']['class'] = 'color-context--dark';
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
