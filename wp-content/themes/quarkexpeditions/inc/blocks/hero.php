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
		'left'       => [],
		'right'      => [],
		'dark_mode'  => $block['attrs']['darkMode'] ?? false,
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

				// Hero form.
				case 'quark/form-two-step':
				case 'quark/form-two-step-compact':
					$form = [
						'type' => 'form',
					];

					// Add the form.
					$form['form'] = render_block( $inner_inner_block );

					// Add to attributes.
					$attributes['right'][] = $form;
					break;

				// Overline.
				case 'quark/hero-overline':
					$overline = [
						'type' => 'overline',
					];

					// Add overline.
					$overline['overline']['text']  = $inner_inner_block['attrs']['overline'] ?? '';
					$overline['overline']['color'] = $inner_inner_block['attrs']['textColor'] ?? 'blue';

					// Add to attributes.
					$attributes['left'][] = $overline;
					break;

				// Title.
				case 'quark/hero-title':
					$title = [
						'type' => 'title',
					];

					// Add title.
					$title['title'] = $inner_inner_block['attrs']['title'] ?? '';

					// Add to attributes.
					$attributes['left'][] = $title;
					break;

				// Subtitle.
				case 'quark/hero-subtitle':
					$subtitle = [
						'type' => 'subtitle',
					];

					// Add subtitle.
					$subtitle['subtitle'] = $inner_inner_block['attrs']['subtitle'] ?? '';

					// Add to attributes.
					$attributes['left'][] = $subtitle;
					break;

				// Description.
				case 'quark/hero-description':
					$description = [
						'type' => 'description',
					];

					// Add description.
					$description['description'] = implode( '', array_map( 'render_block', $inner_inner_block['innerBlocks'] ) );

					// Add to attributes.
					$attributes['left'][] = $description;
					break;

				// Hero tag.
				case 'quark/icon-badge':
					$tag = [
						'type' => 'tag',
					];

					// Add tag.
					$inner_inner_block['attrs']['className'] = 'hero__tag';
					$tag['tag']                              = render_block( $inner_inner_block );

					// Add to attributes.
					$attributes['left'][] = $tag;
					break;

				// CTA.
				case 'quark/lp-form-modal-cta':
					$cta = [
						'type' => 'cta',
					];

					// Add cta.
					$inner_inner_block['attrs']['className'] = 'hero__form-modal-cta color-context--dark';
					$cta['cta']                              = render_block( $inner_inner_block );

					// Add to attributes.
					$attributes['left'][] = $cta;
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
