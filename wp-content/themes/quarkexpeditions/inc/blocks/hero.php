<?php
/**
 * Block: Hero.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Hero;

const BLOCK_NAME = 'quark/hero';
const COMPONENT  = 'parts.hero';

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
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize the attrs.
	$attributes = [
		'image_id'   => 0,
		'immersive'  => $block['attrs']['isImmersive'] ?? false,
		'text_align' => $block['attrs']['textAlign'] ?? '',
		'left'       => [
			'overline' => '',
			'title'    => '',
			'subtitle' => '',
			'tag'      => '',
			'cta'      => '',
		],
		'right'      => [
			'form' => '',
		],
	];

	// Parse inner blocks.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Check for left and right.
		if ( ! in_array( $inner_block['blockName'], [ 'quark/hero-content-left', 'quark/hero-content-right' ], true ) ) {
			continue;
		}

		// Go a level deeper.
		foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
			switch ( $inner_inner_block['blockName'] ) {

				// Inquiry form.
				case 'quark/inquiry-form':
					$attributes['right']['form'] = render_block( $inner_inner_block );
					break;

				// Overline.
				case 'quark/hero-overline':
					$attributes['left']['overline'] = $inner_inner_block['attrs']['overline'] ?? '';
					break;

				// Title.
				case 'quark/hero-title':
					$attributes['left']['title'] = $inner_inner_block['attrs']['title'] ?? '';
					break;

				// Subtitle.
				case 'quark/hero-subtitle':
					$attributes['left']['subtitle'] = $inner_inner_block['attrs']['subtitle'] ?? '';
					break;

				// Hero tag.
				case 'quark/icon-badge':
					$inner_inner_block['attrs']['className'] = 'hero__tag';
					$attributes['left']['tag']               = render_block( $inner_inner_block );
					break;

				// CTA.
				case 'quark/lp-form-modal-cta':
					$inner_inner_block['attrs']['className'] = 'hero__form-modal-cta color-context--dark';
					$attributes['left']['cta']               = render_block( $inner_inner_block );
					break;
			}
		}
	}

	// Image.
	if ( ! empty( $block['attrs']['image']['id'] ) ) {
		$attributes['image_id'] = absint( $block['attrs']['image']['id'] );
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
