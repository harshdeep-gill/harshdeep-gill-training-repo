<?php
/**
 * Block: Hero.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Hero;

use WP_Block;

const COMPONENT  = 'parts.hero';
const BLOCK_NAME = 'quark/hero';

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
			'render_callback'   => __NAMESPACE__ . '\\render',
			'skip_inner_blocks' => true, // Skip inner block rendering to avoid render callbacks to those blocks.
		]
	);

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize the component attributes.
	$component_attributes = [
		'image_id'        => 0,
		'immersive'       => $attributes['immersive'],
		'content_overlap' => $attributes['contentOverlap'],
		'text_align'      => $attributes['textAlign'],
		'overlay_opacity' => $attributes['overlayOpacity'],
		'left'            => [],
		'right'           => [],
		'breadcrumbs'     => '',
		'is_404'          => is_404(),
	];

	// Parse inner blocks.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Check for inner block.
		if ( 'quark/hero-content' === $inner_block->name ) {
			// Parse inner blocks.
			foreach ( $inner_block->inner_blocks as $left_or_right ) {
				// Check for inner block.
				if ( ! $left_or_right instanceof WP_Block ) {
					continue;
				}

				// Check for left and right.
				if ( ! in_array( $left_or_right->name, [ 'quark/hero-content-left', 'quark/hero-content-right' ], true ) ) {
					continue;
				}

				// Go a level deeper.
				foreach ( $left_or_right->inner_blocks as $child_block ) {
					// Check for child block.
					if ( ! $child_block instanceof WP_Block ) {
						continue;
					}

					// Processing based on block.
					switch ( $child_block->name ) {
						// Hero form.
						case 'quark/form-two-step':
							$form = [
								'type' => 'form',
							];

							// Add the form.
							$form['form'] = render_block( $child_block->parsed_block );

							// Add to attributes.
							$component_attributes['right'][] = $form;
							break;

						// Circle badge in the hero section.
						case 'quark/hero-circle-badge':
							$circle_badge = [
								'type' => 'circle-badge',
							];

							// Add the text.
							$circle_badge['text'] = $child_block->attributes['text'] ?? '';

							// Add to attributes.
							$component_attributes['right'][] = $circle_badge;
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
							$title['title']          = $child_block->attributes['title'] ?? '';
							$title['text_color']     = $child_block->attributes['textColor'] ?? '';
							$title['use_promo_font'] = $child_block->attributes['usePromoFont'] ?? false;

							// Add to attributes.
							$component_attributes['left'][] = $title;
							break;

						// Title bicolor.
						case 'quark/hero-title-bicolor':
							$title_bicolor = [
								'type' => 'title_bicolor',
							];

							// Get the props.
							$title_bicolor['white_text']     = $child_block->attributes['whiteText'] ?? '';
							$title_bicolor['yellow_text']    = $child_block->attributes['yellowText'] ?? '';
							$title_bicolor['switch_colors']  = $child_block->attributes['switchColors'] ?? false;
							$title_bicolor['use_promo_font'] = $child_block->attributes['usePromoFont'] ?? false;

							// Add to attributes.
							$component_attributes['left'][] = $title_bicolor;
							break;

						// Text Graphic.
						case 'quark/hero-text-graphic':
							$text_graphic = [
								'type' => 'text-graphic',
							];

							// Add image id.
							$text_graphic['image_id'] = $child_block->attributes['image']['id'] ?? '';

							// Add size.
							$text_graphic['size'] = $child_block->attributes['imageSize'] ?? 'large';

							// Add to attributes.
							$component_attributes['left'][] = $text_graphic;
							break;

						// Subtitle.
						case 'quark/hero-subtitle':
							$subtitle = [
								'type' => 'subtitle',
							];

							// Add subtitle.
							$subtitle['subtitle']       = $child_block->attributes['subtitle'] ?? '';
							$subtitle['text_color']     = $child_block->attributes['textColor'] ?? '';
							$subtitle['use_promo_font'] = $child_block->attributes['usePromoFont'] ?? false;

							// Add to attributes.
							$component_attributes['left'][] = $subtitle;
							break;

						// Description.
						case 'quark/hero-description':
							$description = [
								'type' => 'description',
							];

							// Add description.
							$description['description']    = implode( '', array_map( 'render_block', $child_block->parsed_block['innerBlocks'] ) );
							$description['text_color']     = $child_block->attributes['textColor'] ?? '';
							$description['use_promo_font'] = $child_block->attributes['usePromoFont'] ?? false;

							// Add to attributes.
							$component_attributes['left'][] = $description;
							break;

						// Hero tag.
						case 'quark/icon-badge':
							$tag = [
								'type' => 'tag',
							];

							// Update the block.
							$child_block = update_block_attribute( $child_block, 'className', 'hero__tag' );

							// Add tag.
							$tag['tag'] = '';

							// Check if the child block is an instance of WP_Block.
							if ( $child_block instanceof WP_Block ) {
								$tag['tag'] = render_block( $child_block->parsed_block );
							}

							// Add to attributes.
							$component_attributes['left'][] = $tag;
							break;

						// CTA.
						case 'quark/lp-form-modal-cta':
							$cta = [
								'type' => 'cta',
							];

							// Update the block.
							$child_block = update_block_attribute( $child_block, 'className', 'hero__form-modal-cta color-context--dark' );

							// Add cta.
							$cta['cta'] = '';

							// Check if the child block is an instance of WP_Block.
							if ( $child_block instanceof WP_Block ) {
								$cta['cta'] = render_block( $child_block->parsed_block );
							}

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

						// Quark buttons.
						case 'quark/buttons':
							$buttons = [
								'type' => 'buttons',
							];

							// Add buttons.
							$buttons['buttons'] = render_block( $child_block->parsed_block );

							// Add to attributes.
							$component_attributes['left'][] = $buttons;
							break;
					}
				}
			}
		} elseif ( 'quark/breadcrumbs' === $inner_block->name ) {
			$component_attributes['breadcrumbs'] = render_block( $inner_block->parsed_block );
		}
	}

	// Image.
	if ( ! empty( $attributes['image'] ) && is_array( $attributes['image'] ) && ! empty( $attributes['image']['id'] ) ) {
		$component_attributes['image_id'] = $attributes['image']['id'];
	}

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Wrapper function to update block attribute to avoid "Indirect modification of overloaded property" Notice.
 *
 * @param WP_Block|null $block The block instance.
 * @param string        $name The attribute name.
 * @param mixed         $value The attribute value.
 *
 * @return WP_Block|null The updated block.
 */
function update_block_attribute( WP_Block $block = null, string $name = '', mixed $value = '' ): WP_Block|null {
	// Validate the block.
	if ( ! $block instanceof WP_Block ) {
		return $block;
	}

	// Create reflection of attributes.
	$attributes = $block->attributes;

	// Unset the block attributes.
	unset( $block->attributes );

	// Update the attribute.
	$attributes[ $name ] = $value;

	// Set the attributes.
	$block->attributes = $attributes;

	// Unset the attributes.
	unset( $attributes );

	// Return the block.
	return $block;
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
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-circle-badge' ] = [
		'text' => [ 'text' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-subtitle' ] = [
		'text' => [ 'subtitle' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-title' ] = [
		'text' => [ 'title' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-overline' ] = [
		'text' => [ 'overline' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-text-graphic' ] = [
		'image' => [ 'image' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
