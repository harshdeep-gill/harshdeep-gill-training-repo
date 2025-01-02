<?php
/**
 * Block: Search Hero.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SearchHero;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'parts.search-hero';

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
		'overlay_opacity' => $attributes['overlayOpacity'],
		'left'            => [],
		'right'           => [],
	];

	// Parse inner blocks.
	foreach ( $block->inner_blocks as $left_or_right ) {
		// Check for inner block.
		if ( ! $left_or_right instanceof WP_Block ) {
			continue;
		}

		// Check for left and right.
		if ( ! in_array( $left_or_right->name, [ 'quark/search-hero-content-left', 'quark/search-hero-content-right' ], true ) ) {
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
				// Title Container.
				case 'quark/search-hero-title-container':
					$title_container = [];

					// Check for inner blocks.
					if ( ! $child_block->inner_blocks instanceof WP_Block_List ) {
						break;
					}

					// Parse inner blocks.
					foreach ( $child_block->inner_blocks as $text_block ) {
						// Check for block.
						if ( ! $text_block instanceof WP_Block ) {
							continue;
						}

						// Processing based on block.
						switch ( $text_block->name ) {

							// Overline.
							case 'quark/search-hero-overline':
								$overline = [
									'type' => 'overline',
								];

								// Add overline.
								$overline['text']  = $text_block->attributes['overline'] ?? '';
								$overline['color'] = $text_block->attributes['textColor'] ?? 'blue';

								// Add to attributes.
								$title_container['overline'] = $overline;
								break;

							// Title.
							case 'quark/search-hero-title':
								$title = [
									'type' => 'title',
								];

								// Add title.
								$title['text']           = $text_block->attributes['title'] ?? '';
								$title['color']          = $text_block->attributes['textColor'] ?? '';
								$title['use_promo_font'] = $text_block->attributes['usePromoFont'] ?? false;

								// Add to attributes.
								$title_container['title'] = $title;
								break;

							// Title bicolor.
							case 'quark/search-hero-title-bicolor':
								$title_bicolor = [
									'type' => 'title_bicolor',
								];

								// Get the props.
								$title_bicolor['white_text']     = $text_block->attributes['whiteText'] ?? '';
								$title_bicolor['yellow_text']    = $text_block->attributes['yellowText'] ?? '';
								$title_bicolor['yellow_first']   = $text_block->attributes['yellowFirst'] ?? false;
								$title_bicolor['use_promo_font'] = $text_block->attributes['usePromoFont'] ?? false;

								// Add to attributes.
								$title_container['title_bicolor'] = $title_bicolor;
								break;

							// Subtitle.
							case 'quark/search-hero-subtitle':
								$subtitle = [
									'type' => 'subtitle',
								];

								// Add subtitle.
								$subtitle['text']           = $text_block->attributes['subtitle'] ?? '';
								$subtitle['color']          = $text_block->attributes['textColor'] ?? '';
								$subtitle['use_promo_font'] = $text_block->attributes['usePromoFont'] ?? false;

								// Add to title container.
								$title_container['subtitle'] = $subtitle;
								break;
						}
					}

					// Add to attributes.
					$component_attributes['left']['title_container'] = $title_container;
					break;

				// Search Bar.
				case 'quark/search-hero-search-bar':
					// Check for inner blocks.
					if ( ! $child_block instanceof WP_Block ) {
						break;
					}

					// Render inner blocks.
					$search_bar = render_block( $child_block->parsed_block );

					// Add to attributes.
					$component_attributes['left']['search_bar'] = $search_bar;
					break;

				// Thumbnail Cards.
				case 'quark/thumbnail-cards':
					// Check for block.
					if ( ! $child_block instanceof WP_Block ) {
						break;
					}

					// Parsed Block.
					$thumbnail_cards_attrs                            = $child_block->parsed_block;
					$component_attributes['thumbnail_cards']['items'] = [];

					// Render block.
					$thumbnail_cards = render_block( $child_block->parsed_block );

					// Loop through all hero slider items.
					if ( ! empty( $thumbnail_cards_attrs['innerBlocks'] ) ) {
						foreach ( $thumbnail_cards_attrs['innerBlocks'] as $card_item ) {
							// Initialize item.
							$item = [];

							// If item exists.
							if ( 'quark/thumbnail-cards-card' === $card_item['blockName'] ) {
								$item['image_id'] = $card_item['attrs']['image']['id'] ?? 0;
								$item['title']    = $card_item['attrs']['title'] ?? '';
								$item['url']      = $card_item['attrs']['url']['url'] ?? '';

								// Add item attributes.
								$component_attributes['left']['thumbnail_cards']['items'][] = $item;
							}
						}
					}

					// Add to attributes.
					$component_attributes['left']['thumbnail_cards']['slot'] = $thumbnail_cards;
					break;

				// Hero Card Slider.
				case 'quark/hero-details-card-slider':
					// Check for block.
					if ( ! $child_block instanceof WP_Block ) {
						break;
					}

					// Parsed Block.
					$hero_details_card_slider_attrs                       = $child_block->parsed_block;
					$component_attributes['hero_details_slider']['items'] = [];

					// Render block.
					$hero_details_card_slider = render_block( $hero_details_card_slider_attrs );

					// Loop through all hero slider items.
					if ( ! empty( $hero_details_card_slider_attrs['innerBlocks'] ) ) {
						foreach ( $hero_details_card_slider_attrs['innerBlocks'] as $card_item ) {
							// Initialize item.
							$item = [];

							// If item exists.
							if ( 'quark/hero-details-card-slider-item' === $card_item['blockName'] ) {
								if (
									( ! empty( $card_item['attrs']['mediaType'] ) && 'video' === $card_item['attrs']['mediaType'] ) ||
									empty( $card_item['attrs']['media']['id'] )
								) {
									continue;
								}

								// Add attributes.
								$item['media_id'] = $card_item['attrs']['media']['id'] ?? 0;
								$item['tag_text'] = ! empty( $card_item['attrs']['hasTag'] ) ? $card_item['attrs']['tagText'] : '';
								$item['title']    = $card_item['attrs']['title'] ?? '';
								$item['cta']      = ! empty( $card_item['attrs']['hasCtaLink'] ) && ! empty( $card_item['attrs']['cta'] ) ? $card_item['attrs']['cta'] : [];

								// Add item attributes.
								$component_attributes['hero_details_slider']['items'][] = $item;
							}
						}
					}

					// Add to attributes.
					$component_attributes['right'] = $hero_details_card_slider;
					break;
			}
		}
	}

	// Image.
	if ( ! empty( $attributes['backgroundImage'] ) && is_array( $attributes['backgroundImage'] ) && ! empty( $attributes['backgroundImage']['id'] ) ) {
		$component_attributes['image_id'] = $attributes['backgroundImage']['id'];
	}

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
