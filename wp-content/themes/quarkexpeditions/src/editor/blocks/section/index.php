<?php
/**
 * Block: Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Section;

const COMPONENT = 'section';

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
		'id'               => $attributes['anchor'],
		'title'            => '',
		'title_align'      => $attributes['titleAlignment'],
		'description'      => '',
		'background'       => $attributes['hasBackground'],
		'background_color' => $attributes['backgroundColor'],
		'padding'          => $attributes['hasPadding'],
		'narrow'           => $attributes['isNarrow'],
		'slot'             => $content,
	];

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
	if ( ( $attributes['hasCta'] ?? false ) && ! empty( $attributes['ctaButton'] ) ) {
		$component_attributes['cta_button'] = [
			'url'        => $attributes['ctaButton']['url'] ?? '',
			'text'       => $attributes['ctaButton']['text'] ?? '',
			'new_window' => $attributes['ctaButton']['newWindow'] ?? '',
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
