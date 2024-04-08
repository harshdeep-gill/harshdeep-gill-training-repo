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
	$attributes = [
		'align' => $block['attrs']['align'] ?? 'left',
	];

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
				'type'     => 'product-card',
				'children' => [],
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
					if ( 'quark/product-cards-reviews' === $inner_inner_block['blockName'] ) {
						// Initialize reviews array.
						$reviews = [];

						// Add block type.
						$reviews['type'] = 'reviews';

						// Add rating.
						$reviews['rating'] = ! empty( $inner_inner_block['attrs']['rating'] ) ? $inner_inner_block['attrs']['rating'] : '5';

						// Add reviews text.
						$reviews['total_reviews_text'] = ! empty( $inner_inner_block['attrs']['reviewsText'] ) ? $inner_inner_block['attrs']['reviewsText'] : '';

						// Add reviews to children.
						$card_attributes['children'][] = $reviews;
					}

					// Itinerary.
					if ( 'quark/product-cards-itinerary' === $inner_inner_block['blockName'] ) {
						// Initialize Itinerary.
						$itinerary = [];

						// Add block type.
						$itinerary['type'] = 'itinerary';

						// Add departure date text.
						$itinerary['departure_date_text'] = ! empty( $inner_inner_block['attrs']['departureDate'] ) ? $inner_inner_block['attrs']['departureDate'] : '';

						// Add duration text.
						$itinerary['duration_text'] = ! empty( $inner_inner_block['attrs']['durationText'] ) ? $inner_inner_block['attrs']['durationText'] : '';

						// Add itinerary to children.
						$card_attributes['children'][] = $itinerary;
					}

					// Title.
					if ( 'quark/product-cards-title' === $inner_inner_block['blockName'] ) {
						// Initialize title.
						$title = [];

						// Add block type.
						$title['type'] = 'title';

						// Add title.
						$title['title'] = ! empty( $inner_inner_block['attrs']['title'] ) ? $inner_inner_block['attrs']['title'] : '';

						// Add title to children.
						$card_attributes['children'][] = $title;
					}

					// Subtitle.
					if ( 'quark/product-cards-subtitle' === $inner_inner_block['blockName'] ) {
						// Initialize subtitle.
						$subtitle = [];

						// Add block type.
						$subtitle['type'] = 'subtitle';

						// Add subtitle.
						$subtitle['subtitle'] = ! empty( $inner_inner_block['attrs']['subtitle'] ) ? $inner_inner_block['attrs']['subtitle'] : '';

						// Add title to children.
						$card_attributes['children'][] = $subtitle;
					}

					// Description.
					if ( 'quark/product-cards-description' === $inner_inner_block['blockName'] ) {
						// Initialize description.
						$description = [];

						// Add block type.
						$description['type'] = 'description';

						// Add description.
						$description['description'] = ! empty( $inner_inner_block['attrs']['description'] ) ? $inner_inner_block['attrs']['description'] : '';

						// Add description to children.
						$card_attributes['children'][] = $description;
					}

					// Price.
					if ( 'quark/product-cards-price' === $inner_inner_block['blockName'] ) {
						// Initialize price.
						$price = [];

						// Add block type.
						$price['type'] = 'price';

						// Add price now.
						$price['discounted'] = ! empty( $inner_inner_block['attrs']['price'] ) ? $inner_inner_block['attrs']['price'] : '';

						// Add price was.
						$price['original'] = ! empty( $inner_inner_block['attrs']['originalPrice'] ) ? $inner_inner_block['attrs']['originalPrice'] : '';

						// Add price to children.
						$card_attributes['children'][] = $price;
					}

					// Buttons.
					if ( 'quark/product-cards-buttons' === $inner_inner_block['blockName'] ) {
						// Initialize buttons.
						$buttons = [];

						// Add block type.
						$buttons['type'] = 'buttons';

						// Get the slot.
						$buttons['slot'] = render_block( $inner_inner_block );

						// Add buttons to children.
						$card_attributes['children'][] = $buttons;
					}
				}
			}

			// Add card attributes.
			$attributes['items'][] = $card_attributes;
		} else {
			// Media Content Card.
			$media_content_card_attributes = [
				'type' => 'media-content-card',
				'slot' => render_block( $inner_block ),
			];

			// Add attributes.
			$attributes['items'][] = $media_content_card_attributes;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
