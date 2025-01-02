<?php
/**
 * Block: Product Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ProductCards;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.product-cards';
const BLOCK_NAME = 'quark/product-cards';

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
			'skip_inner_blocks' => true,
		]
	);

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[]       $attributes Block attributes.
 * @param string        $content Block default content.
 * @param WP_Block|null $block Block instance.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check if block is an instance of WP_Block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize attributes.
	$attributes = [
		'layout' => $block->attributes['layout'] ?? 'carousel',
	];

	// Add the alignment only if grid is selected.
	if ( 'grid' === $attributes['layout'] ) {
		$attributes['align'] = $block->attributes['align'] ?? 'left';
	}

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if block is an instance of WP_Block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Product Card.
		if ( 'quark/product-cards-card' === $inner_block->name ) {
			// Check if we have an image.
			if ( empty( $inner_block->attributes['image']['id'] ) ) {
				continue;
			}

			// Initialize current card attributes.
			$card_attributes = [
				'type'     => 'product-card',
				'children' => [],
			];

			// Add Image attributes.
			$card_attributes['image']['id']           = $inner_block->attributes['image']['id'];
			$card_attributes['image']['is_immersive'] = $inner_block->attributes['isImmersiveImage'];

			// Add CTA Badge Text.
			if ( ! empty( $inner_block->attributes['hasCtaBadge'] ) && ! empty( $inner_block->attributes['ctaBadgeText'] ) ) {
				$card_attributes['cta_badge_text'] = $inner_block->attributes['ctaBadgeText'];
			}

			// Add Time Badge Text.
			if ( ! empty( $inner_block->attributes['hasTimeBadge'] ) && ! empty( $inner_block->attributes['timeBadgeText'] ) ) {
				$card_attributes['time_badge_text'] = $inner_block->attributes['timeBadgeText'];
			}

			// Add Sold Out Badge Text.
			if ( ! empty( $inner_block->attributes['isSoldOut'] ) && ! empty( $inner_block->attributes['soldOutText'] ) ) {
				$card_attributes['sold_out_badge_text'] = $inner_block->attributes['soldOutText'];
			}

			// Add Info Ribbon Text.
			if ( ! empty( $inner_block->attributes['hasInfoRibbon'] ) && ! empty( $inner_block->attributes['infoRibbonText'] ) ) {
				$card_attributes['info_ribbon_text'] = $inner_block->attributes['infoRibbonText'];
			}

			// Loop through inner blocks of the card.
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				foreach ( $inner_block->inner_blocks as $inner_inner_block ) {
					// Check if block is an instance of WP_Block.
					if ( ! $inner_inner_block instanceof WP_Block ) {
						continue;
					}

					// Reviews.
					if ( 'quark/product-cards-reviews' === $inner_inner_block->name ) {
						// Initialize reviews array.
						$reviews = [];

						// Add block type.
						$reviews['type'] = 'reviews';

						// Add rating.
						$reviews['rating'] = $inner_inner_block->attributes['rating'];

						// Add reviews text.
						$reviews['total_reviews_text'] = $inner_inner_block->attributes['reviewsText'];

						// Add reviews to children.
						$card_attributes['children'][] = $reviews;
					}

					// Itinerary.
					if ( 'quark/product-cards-itinerary' === $inner_inner_block->name ) {
						// Initialize Itinerary.
						$itinerary = [];

						// Add block type.
						$itinerary['type'] = 'itinerary';

						// Add departure date text.
						$itinerary['departure_date_text'] = $inner_inner_block->attributes['departureDate'];

						// Add duration text.
						$itinerary['duration_text'] = $inner_inner_block->attributes['durationText'];

						// Add itinerary to children.
						$card_attributes['children'][] = $itinerary;
					}

					// Title.
					if ( 'quark/product-cards-title' === $inner_inner_block->name ) {
						// Initialize title.
						$title = [];

						// Add block type.
						$title['type'] = 'title';

						// Add title.
						$title['title'] = $inner_inner_block->attributes['title'];

						// Add title to children.
						$card_attributes['children'][] = $title;
					}

					// Subtitle.
					if ( 'quark/product-cards-subtitle' === $inner_inner_block->name ) {
						// Initialize subtitle.
						$subtitle = [];

						// Add block type.
						$subtitle['type'] = 'subtitle';

						// Add subtitle.
						$subtitle['subtitle'] = $inner_inner_block->attributes['subtitle'];

						// Add title to children.
						$card_attributes['children'][] = $subtitle;
					}

					// Description.
					if ( 'quark/product-cards-description' === $inner_inner_block->name ) {
						// Initialize description.
						$description = [];

						// Add block type.
						$description['type'] = 'description';

						// Add description.
						$description['description'] = $inner_inner_block->attributes['description'];

						// Add description to children.
						$card_attributes['children'][] = $description;
					}

					// Price.
					if ( 'quark/product-cards-price' === $inner_inner_block->name ) {
						// Initialize price.
						$price = [];

						// Add block type.
						$price['type'] = 'price';

						// Add price now.
						$price['discounted'] = $inner_inner_block->attributes['price'];

						// Add price was.
						$price['original'] = $inner_inner_block->attributes['originalPrice'];

						// Add price to children.
						$card_attributes['children'][] = $price;
					}

					// Buttons.
					if ( 'quark/product-cards-buttons' === $inner_inner_block->name ) {
						// Initialize buttons.
						$buttons = [];

						// Add block type.
						$buttons['type'] = 'buttons';

						// Get the slot.
						$buttons['slot'] = render_block( $inner_inner_block->parsed_block );

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
				'slot' => render_block( $inner_block->parsed_block ),
			];

			// Add attributes.
			$attributes['items'][] = $media_content_card_attributes;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
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
	$blocks_and_attributes[ BLOCK_NAME . '-description' ] = [
		'text' => [ 'description' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-itinerary' ] = [
		'text' => [
			'departureDate',
			'durationText',
		],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-reviews' ] = [
		'text' => [ 'reviewsText' ],
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
	$blocks_and_attributes[ BLOCK_NAME . '-card' ] = [
		'image' => [ 'image' ],
		'text'  => [
			'ctaBadgeText',
			'timeBadgeText',
			'infoRibbonText',
			'soldOutText',
		],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
