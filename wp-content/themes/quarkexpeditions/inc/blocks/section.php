<?php
/**
 * Block: Section.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Section;

const BLOCK_NAME = 'quark/section';
const COMPONENT  = 'parts.section';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Build component attributes.
	$attributes = [
		'id'               => $block['attrs']['anchor'] ?? '',
		'title'            => '',
		'title_align'      => $block['attrs']['titleAlignment'] ?? 'center',
		'description'      => '',
		'background'       => $block['attrs']['hasBackground'] ?? false,
		'background_color' => $block['attrs']['backgroundColor'] ?? '',
		'padding'          => $block['attrs']['hasPadding'] ?? false,
		'narrow'           => $block['attrs']['isNarrow'] ?? false,
		'heading_link'     => [
			'text'       => $block['attrs']['headingLink']['text'] ?? '',
			'url'        => $block['attrs']['headingLink']['url'] ?? '',
			'new_window' => $block['attrs']['headingLink']['newWindow'] ?? false,
		],
		'has_heading_link' => $block['attrs']['hasHeadingLink'] ?? false,
		'slot'             => '',
	];

	// Set title if it exists.
	if ( ( $block['attrs']['hasTitle'] ?? true ) && ! empty( $block['attrs']['title'] ) ) {
		$attributes['title'] = $block['attrs']['title'];
	}

	// Set heading level if it exists.
	if ( ! empty( $block['attrs']['headingLevel'] ) ) {
		$attributes['heading_level'] = $block['attrs']['headingLevel'];
	}

	// Set description if it exists.
	if ( ( $block['attrs']['hasDescription'] ?? false ) && ! empty( $block['attrs']['description'] ) ) {
		$attributes['description'] = $block['attrs']['description'];
	}

	// Set slot if it exists.
	if ( ! empty( $block['innerBlocks'] ) ) {
		$attributes['slot'] .= implode( '', array_map( 'render_block', $block['innerBlocks'] ) );
	}

	// Set CTA if it exists.
	if ( ( $block['attrs']['hasCta'] ?? false ) && ! empty( $block['attrs']['ctaButton'] ) ) {
		$attributes['cta_button'] = [
			'url'        => $block['attrs']['ctaButton']['url'] ?? '',
			'text'       => $block['attrs']['ctaButton']['text'] ?? '',
			'new_window' => $block['attrs']['ctaButton']['newWindow'] ?? '',
			'class'      => '',
		];

		// If background is dark, then add dark context class.
		if (
			! empty( $block['attrs']['hasBackground'] ) &&
			( ! empty( $block['attrs']['backgroundColor'] ) && 'black' === $block['attrs']['backgroundColor'] )
		) {
			$attributes['cta_button']['class'] = 'color-context--dark';
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
