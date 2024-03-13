<?php
/**
 * Block: Product Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ProductCards;

const BLOCK_NAME = 'quark/product-cards';
const COMPONENT  = 'parts.product-cards';

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

	// Initialize attributes.
	$attributes = [];

	// Build slot.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Product Card.
		if ( 'quark/product-cards-card' === $inner_block['blockName'] ) {
			// Check if we have an image.
			if ( empty( $inner_block['attrs']['image']['id'] ) ) {
				continue;
			}

			// Initialize current card attributes.
			$card_attributes = [
				'type' => 'product-card',
			];

			// Add Image attributes.
			$card_attributes['image']['id']           = $inner_block['attrs']['image']['id'];
			$card_attributes['image']['is_immersive'] = ! empty( $inner_block['attrs']['isImmersiveImage'] ) ? $inner_block['attrs']['isImmersiveImage'] : false;

			// Add CTA Badge Text.
			if ( ! empty( $inner_block['attrs']['hasCtaBadge'] ) && ! empty( $inner_block['attrs']['ctaBadgeText'] ) ) {
				$card_attributes['cta_badge_text'] = $inner_block['attrs']['ctaBadgeText'];
			}

			// Add Time Badge Text.
			if ( ! empty( $inner_block['attrs']['hasTimeBadge'] ) && ! empty( $inner_block['attrs']['timeBadgeText'] ) ) {
				$card_attributes['time_badge_text'] = $inner_block['attrs']['timeBadgeText'];
			}

			// Add Sold Out Badge Text.
			if ( ! empty( $inner_block['attrs']['isSoldOut'] ) && ! empty( $inner_block['attrs']['soldOutText'] ) ) {
				$card_attributes['sold_out_badge_text'] = $inner_block['attrs']['soldOutText'];
			}

			// Add Info Ribbon Text.
			if ( ! empty( $inner_block['attrs']['hasInfoRibbon'] ) && ! empty( $inner_block['attrs']['infoRibbonText'] ) ) {
				$card_attributes['info_ribbon_text'] = $inner_block['attrs']['infoRibbonText'];
			}

			// Loop through inner blocks of the card.
			if ( ! empty( $inner_block['innerBlocks'] ) ) {
				foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
					// Reviews.
					if ( 'quark/product-cards-card-reviews' === $inner_inner_block['blockName'] ) {
						// Add rating.
						$card_attributes['reviews']['rating'] = ! empty( $inner_inner_block['attrs']['rating'] ) ? $inner_inner_block['attrs']['rating'] : '';

						// Add reviews text.
						$card_attributes['reviews']['total_reviews_text'] = ! empty( $inner_inner_block['attrs']['reviewsText'] ) ? $inner_inner_block['attrs']['reviewsText'] : '';
					}

					// Itinerary.
					if ( 'quark/product-cards-card-itinerary' === $inner_inner_block['blockName'] ) {
						// Add deaprture date text.
						$card_attributes['itinerary']['departure_date_text'] = ! empty( $inner_inner_block['attrs']['departureDate'] ) ? $inner_inner_block['attrs']['departureDate'] : '';

						// Add duration text.
						$card_attributes['itinerary']['duration_text'] = ! empty( $inner_inner_block['attrs']['durationText'] ) ? $inner_inner_block['attrs']['durationText'] : '';
					}

					// Title.
					if ( 'quark/product-cards-card-title' === $inner_inner_block['blockName'] ) {
						// Add title.
						$card_attributes['title'] = ! empty( $inner_inner_block['attrs']['title'] ) ? $inner_inner_block['attrs']['title'] : '';
					}

					// Subtitle.
					if ( 'quark/product-cards-card-subtitle' === $inner_inner_block['blockName'] ) {
						// Add subtitle.
						$card_attributes['subtitle'] = ! empty( $inner_inner_block['attrs']['subtitle'] ) ? $inner_inner_block['attrs']['subtitle'] : '';
					}

					// Description.
					if ( 'quark/product-cards-card-description' === $inner_inner_block['blockName'] ) {
						// Add description.
						$card_attributes['description'] = ! empty( $inner_inner_block['attrs']['description'] ) ? $inner_inner_block['attrs']['description'] : '';
					}

					// Price.
					if ( 'quark/product-cards-card-price' === $inner_inner_block['blockName'] ) {
						// Add price now.
						$card_attributes['price']['discounted'] = ! empty( $inner_inner_block['attrs']['priceNow'] ) ? $inner_inner_block['attrs']['priceNow'] : '';

						// Add price was.
						$card_attributes['price']['original'] = ! empty( $inner_inner_block['attrs']['priceWas'] ) ? $inner_inner_block['attrs']['priceWas'] : '';
					}

					// Buttons.
					if ( 'quark/product-cards-card-buttons' === $inner_inner_block['blockName'] ) {
						// Add Call CTA Text.
						if ( ! empty( $inner_inner_block['attrs']['callCtaText'] ) ) {
							$card_attributes['buttons']['call_cta_text'] = $inner_inner_block['attrs']['callCtaText'];
						}

						// Add Call CTA URL.
						if ( ! empty( $inner_inner_block['attrs']['callCtaUrl']['url'] ) ) {
							$card_attributes['buttons']['call_cta_url'] = $inner_inner_block['attrs']['callCtaUrl']['url'];
						}
					}
				}
			}

			// Add card attributes.
			$attributes['items'][] = $card_attributes;
		}

		// Media Content Card.
		if ( 'quark/media-content-card' === $inner_block['blockName'] ) {

			// Initialize attributes.
			$media_content_card_attributes = [
				'type'       => 'media-content-card',
				'is_compact' => ! empty( $inner_block['attrs']['isCompact'] ) ? true : false,
				'content'    => [],
			];

			// Add Image Id.
			$media_content_card_attributes['image_id'] = $inner_block['attrs']['image']['id'];

			// Loop through innerblocks and build attributes.
			foreach ( $inner_block['innerBlocks'] as $index => $child_block ) {
				$media_content_card_attributes['content'][ $index ]['heading'] = $child_block['attrs']['heading'] ?? '';

				// Loop through inner inner blocks.
				foreach ( $child_block['innerBlocks'] as $grand_child_block ) {
					if ( 'quark/media-content-info' !== $grand_child_block['blockName'] ) {
						$media_content_card_attributes['content'][ $index ]['slot'] = implode( '', array_map( 'render_block', $child_block['innerBlocks'] ) );
					} else {
						$media_content_card_attributes['content'][ $index ]['content_info'][] = [
							'label'  => $grand_child_block['attrs']['label'] ?? '',
							'value'  => $grand_child_block['attrs']['value'] ?? '',
							'url'    => ! empty( $grand_child_block['attrs']['url']['url'] ) ? $grand_child_block['attrs']['url']['url'] : '',
							'target' => ! empty( $grand_child_block['attrs']['url']['newWindow'] ) ? '_blank' : '',
						];
					}
				}
			}

			// Add media content card attributes.
			$attributes['items'][] = $media_content_card_attributes;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
