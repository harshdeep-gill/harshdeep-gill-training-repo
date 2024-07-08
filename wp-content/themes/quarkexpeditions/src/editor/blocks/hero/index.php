<?php
/**
 * Block: Hero.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Hero;

const COMPONENT = 'parts.hero';

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
 * @param mixed[]   $attributes The block attributes.
 * @param string    $content    The block content.
 * @param \WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', \WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof \WP_Block ) {
		return $content;
	}

	// Initialize the component attributes.
	$component_attributes = [
		'image_id'        => $attributes['imageId'] ?? 0,
		'immersive'       => $attributes['isImmersive'] ?? false,
		'text_align'      => $attributes['textAlign'] ?? '',
		'overlay_opacity' => $attributes['overlayOpacity'] ?? 0,
		'left'            => [],
		'right'           => [],
	];

	// Parse inner blocks.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for inner block.
		if ( ! $inner_block instanceof \WP_Block ) {
			continue;
		}

		// Check for left and right.
		if ( ! in_array( $inner_block->name, [ 'quark/hero-content-left', 'quark/hero-content-right' ], true ) ) {
			continue;
		}

		// Go a level deeper.
		foreach ( $inner_block->inner_blocks as $child_block ) {
			// Check for child block.
			if ( ! $child_block instanceof \WP_Block ) {
				continue;
			}

			// Processing based on block.
			switch ( $child_block->name ) {
				// Hero form.
				case 'quark/form-two-step':
				case 'quark/form-two-step-compact':
					$form = [
						'type' => 'form',
					];

					// Add the form.
					$form['form'] = render_block( $child_block->parsed_block );

					// Add to attributes.
					$component_attributes['right'][] = $form;
					break;

				// Overline.
				case 'quark/hero-overline':
					$overline = [
						'type' => 'overline',
					];

					// Add overline.
					$overline['overline']['text']  = $child_block->attributes['overline'] ?? '';
					$overline['overline']['color'] = $child_block->attributes['textColor'] ?? 'blue';

					// Add to attributes.
					$component_attributes['left'][] = $overline;
					break;

				// Title.
				case 'quark/hero-title':
					$title = [
						'type' => 'title',
					];

					// Add title.
					$title['title']      = $child_block->attributes['title'] ?? '';
					$title['text_color'] = $child_block->attributes['textColor'] ?? '';

					// Add to attributes.
					$component_attributes['left'][] = $title;
					break;

				// Subtitle.
				case 'quark/hero-subtitle':
					$subtitle = [
						'type' => 'subtitle',
					];

					// Add subtitle.
					$subtitle['subtitle']   = $child_block->attributes['subtitle'] ?? '';
					$subtitle['text_color'] = $child_block->attributes['textColor'] ?? '';

					// Add to attributes.
					$component_attributes['left'][] = $subtitle;
					break;

				// Description.
				case 'quark/hero-description':
					$description = [
						'type' => 'description',
					];

					// Add description.
					$description['description'] = $child_block->attributes['description'] ?? '';
					$description['text_color']  = $child_block->attributes['textColor'] ?? '';

					// Add to attributes.
					$component_attributes['left'][] = $description;
					break;

				// Hero tag.
				case 'quark/icon-badge':
					$tag = [
						'type' => 'tag',
					];

					// Add tag.
					$child_block->attributes['className'] = 'hero__tag';
					$tag['tag']                           = render_block( $child_block->parsed_block );

					// Add to attributes.
					$component_attributes['left'][] = $tag;
					break;

				// CTA.
				case 'quark/lp-form-modal-cta':
					$cta = [
						'type' => 'cta',
					];

					// Add cta.
					$child_block->attributes['className'] = 'hero__form-modal-cta color-context--dark';
					$cta['cta']                           = render_block( $child_block->parsed_block );

					// Add to attributes.
					$component_attributes['left'][] = $cta;
					break;

				// Quark button.
				case 'quark/button':
					$button = [
						'type' => 'button',
					];

					// Add button.
					$button['button'] = render_block( $child_block->parsed_block );

					// Add to attributes.
					$component_attributes['left'][] = $button;
					break;
			}
		}
	}

	// Image.
	if ( ! empty( $attributes['image']['id'] ) ) {
		$component_attributes['image_id'] = $attributes['image']['id'];
	}

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
