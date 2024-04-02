<?php
/**
 * Block: Offer Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\OfferCards;

const BLOCK_NAME = 'quark/offer-cards';
const COMPONENT  = 'parts.offer-cards';

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
		'items' => [],
	];

	// Build slot.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Offer Card.
		if ( 'quark/offer-cards-card' === $inner_block['blockName'] ) {

			// Initialize current card attributes.
			$current_card = [
				'children' => [],
			];

			// Add Heading Text.
			$current_card['heading'] = $inner_block['attrs']['heading'] ?? '';

			// Loop through inner blocks of the card.
			if ( ! empty( $inner_block['innerBlocks'] ) ) {
				foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
					// Title.
					if ( 'quark/offer-card-title' === $inner_inner_block['blockName'] ) {
						// Initialize title.
						$title = [];

						// Add block type.
						$title['type'] = 'title';

						// Add title.
						$title['title'] = ! empty( $inner_inner_block['attrs']['title'] ) ? $inner_inner_block['attrs']['title'] : '';

						// Add title to children.
						$current_card['children'][] = $title;
					}

					// Promotion.
					if ( 'quark/offer-card-promotion' === $inner_inner_block['blockName'] ) {
						// Initialize promotion.
						$promotion = [];

						// Add block type.
						$promotion['type'] = 'promotion';

						// Add promotion.
						$promotion['promotion'] = ! empty( $inner_inner_block['attrs']['promotionText'] ) ? $inner_inner_block['attrs']['promotionText'] : '';

						// Add title to children.
						$current_card['children'][] = $promotion;
					}

					// Help.
					if ( 'quark/offer-card-help' === $inner_inner_block['blockName'] ) {
						// Initialize help.
						$help = [];

						// Add block type.
						$help['type'] = 'help';

						// Add help.
						$help['slot'] = render_block( $inner_inner_block );

						// Add title to children.
						$current_card['children'][] = $help;
					}

					// CTA.
					if ( 'quark/offer-cards-cta' === $inner_inner_block['blockName'] ) {
						// Initialize cta.
						$cta = [];

						// Add block type.
						$cta['type'] = 'cta';

						// Get the slot.
						$cta['slot'] = render_block( $inner_inner_block );

						// Add cta to children.
						$current_card['children'][] = $cta;
					}
				}
			}

			// Add card attributes.
			$attributes['items'][] = $current_card;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
