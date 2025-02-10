<?php
/**
 * Block: Offer Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\OfferCards;

use WP_Block;
use WP_Block_List;

const BLOCK_NAME = 'quark/offer-cards';
const COMPONENT  = 'parts.offer-cards';

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
	$component_attributes = [
		'cards' => [],
	];

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if block is an instance of WP_Block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Offer Card.
		if ( 'quark/offer-cards-card' === $inner_block->name ) {
			// Initialize current card attributes.
			$current_card = [
				'children' => [],
			];

			// Add Heading Text.
			$current_card['heading'] = $inner_block->attributes['heading'] ?? '';

			// Loop through inner blocks of the card.
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				foreach ( $inner_block->inner_blocks as $inner_inner_block ) {
					// Check if block is an instance of WP_Block.
					if ( ! $inner_inner_block instanceof WP_Block ) {
						continue;
					}

					// Title.
					if ( 'quark/offer-card-title' === $inner_inner_block->name ) {
						// Initialize title.
						$title = [];

						// Add block type.
						$title['type'] = 'title';

						// Add title.
						$title['title'] = $inner_inner_block->attributes['title'];

						// Add title to children.
						$current_card['children'][] = $title;
					}

					// Promotion.
					if ( 'quark/offer-card-promotion' === $inner_inner_block->name ) {
						// Initialize promotion.
						$promotion = [];

						// Add block type.
						$promotion['type'] = 'promotion';

						// Add promotion.
						$promotion['promotion'] = $inner_inner_block->attributes['promotionText'];

						// Add title to children.
						$current_card['children'][] = $promotion;
					}

					// Help.
					if ( 'quark/offer-card-help' === $inner_inner_block->name ) {
						// Initialize help.
						$help = [];

						// Add block type.
						$help['type'] = 'help';

						// Add help.
						$help['slot'] = render_block( $inner_inner_block->parsed_block );

						// Add title to children.
						$current_card['children'][] = $help;
					}

					// CTA.
					if ( 'quark/offer-cards-cta' === $inner_inner_block->name ) {
						// Initialize cta.
						$cta = [];

						// Add block type.
						$cta['type'] = 'cta';

						// Get the slot.
						$cta['slot'] = render_block( $inner_inner_block->parsed_block );

						// Add cta to children.
						$current_card['children'][] = $cta;
					}
				}
			}

			// Add card attributes.
			$component_attributes['cards'][] = $current_card;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
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
	$blocks_and_attributes[ BLOCK_NAME . '-card' ] = [
		'text' => [ 'heading' ],
	];

	// Add data to translate.
	$blocks_and_attributes['quark/offer-card-help'] = [
		'text' => [ 'helpText' ],
	];

	// Add data to translate.
	$blocks_and_attributes['quark/offer-card-promotion'] = [
		'text' => [ 'promotionText' ],
	];

	// Add data to translate.
	$blocks_and_attributes['quark/offer-card-title'] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
