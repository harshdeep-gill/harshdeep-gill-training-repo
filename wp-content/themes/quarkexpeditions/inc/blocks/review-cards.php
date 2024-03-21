<?php
/**
 * Block: Review Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewCards;

const BLOCK_NAME = 'quark/review-cards';
const COMPONENT  = 'parts.review-cards';

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

	// Add is carousel attributes.
	$attributes['is_carousel'] = array_key_exists( 'isCarousel', $block['attrs'] ) ? 'false' : 'true';

	// Build data.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Review Card.
		if ( 'quark/review-cards-card' === $inner_block['blockName'] ) {
			// Initialize current card attributes.
			$card_attributes = [
				'type'     => 'review-card',
				'children' => [],
			];

			// Loop through inner blocks of the card.
			if ( ! empty( $inner_block['innerBlocks'] ) ) {
				foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
					// Review.
					if ( 'quark/review-cards-review' === $inner_inner_block['blockName'] ) {
						// Initialize review array.
						$review = [];

						// Add block type.
						$review['type'] = 'review';

						// Add review text.
						$review['review'] = implode( '', array_map( 'render_block', $inner_inner_block['innerBlocks'] ) );

						// Add review to children.
						$card_attributes['children'][] = $review;
					}

					// Title.
					if ( 'quark/review-cards-title' === $inner_inner_block['blockName'] ) {
						// Initialize title.
						$title = [];

						// Add block type.
						$title['type'] = 'title';

						// Add title.
						$title['title'] = ! empty( $inner_inner_block['attrs']['title'] ) ? $inner_inner_block['attrs']['title'] : '';

						// Add title to children.
						$card_attributes['children'][] = $title;
					}

					// Rating.
					if ( 'quark/review-cards-rating' === $inner_inner_block['blockName'] ) {
						// Initialize rating.
						$rating = [];

						// Add block type.
						$rating['type'] = 'rating';

						// Add rating.
						$rating['rating'] = ! empty( $inner_inner_block['attrs']['rating'] ) ? $inner_inner_block['attrs']['rating'] : '5';

						// Add title to children.
						$card_attributes['children'][] = $rating;
					}

					// Author.
					if ( 'quark/review-cards-author' === $inner_inner_block['blockName'] ) {
						// Initialize description.
						$author = [];

						// Add block type.
						$author['type'] = 'author';

						// Add author.
						$author['author'] = ! empty( $inner_inner_block['attrs']['author'] ) ? $inner_inner_block['attrs']['author'] : '';

						// Add author to children.
						$card_attributes['children'][] = $author;
					}

					// Author Details.
					if ( 'quark/review-cards-author-details' === $inner_inner_block['blockName'] ) {
						// Initialize author details.
						$author_details = [];

						// Add block type.
						$author_details['type'] = 'author-details';

						// Add author details.
						$author_details['author_details'] = ! empty( $inner_inner_block['attrs']['authorDetails'] ) ? $inner_inner_block['attrs']['authorDetails'] : '';

						// Add author details to children.
						$card_attributes['children'][] = $author_details;
					}
				}
			}

			// Add card attributes.
			$attributes['items'][] = $card_attributes;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
