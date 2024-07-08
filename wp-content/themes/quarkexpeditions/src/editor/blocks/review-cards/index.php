<?php
/**
 * Block: Review Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewCards;

const COMPONENT = 'parts.review-cards';

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

	// Initialize component attributes.
	$component_attributes = [];

	// Add is carousel attributes.
	$component_attributes['is_carousel'] = $attributes['isCarousel'] ? 'true' : 'false';

	// Build data.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for inner block.
		if ( ! $inner_block instanceof \WP_Block ) {
			continue;
		}

		// Review Card.
		if ( 'quark/review-cards-card' === $inner_block->name ) {
			// Initialize current card attributes.
			$card_attributes = [
				'type'     => 'review-card',
				'children' => [],
			];

			// Build data.
			if ( $inner_block->inner_blocks instanceof \WP_Block_List ) {
				foreach ( $inner_block->inner_blocks as $inner_block_child ) {
					// Check for inner block.
					if ( ! $inner_block_child instanceof \WP_Block ) {
						continue;
					}

					// Review Card Image.
					if ( 'quark/review-cards-review' === $inner_block_child->name ) {
						// Initialize review array.
						$review = [];

						// Add block type.
						$review['type'] = 'review';

						// Add review content.
						$review['review'] = implode( '', array_map( 'render_block', $inner_block_child->parsed_block['innerBlocks'] ) );

						// Add review to children.
						$card_attributes['children'][] = $review;
					}

					// Title.
					if ( 'quark/review-cards-title' === $inner_block_child->name ) {
						// Initialize title.
						$title = [];

						// Add block type.
						$title['type'] = 'title';

						// Add title.
						$title['title'] = ! empty( $inner_block_child->attributes['title'] ) ? $inner_block_child->attributes['title'] : '';

						// Add title to children.
						$card_attributes['children'][] = $title;
					}

					// Rating.
					if ( 'quark/review-cards-rating' === $inner_block_child->name ) {
						// Initialize rating.
						$rating = [];

						// Add block type.
						$rating['type'] = 'rating';

						// Add rating.
						$rating['rating'] = ! empty( $inner_block_child->attributes['rating'] ) ? $inner_block_child->attributes['rating'] : '5';

						// Add rating to children.
						$card_attributes['children'][] = $rating;
					}

					// Author.
					if ( 'quark/review-cards-author' === $inner_block_child->name ) {
						// Initialize author.
						$author = [];

						// Add block type.
						$author['type'] = 'author';

						// Add author.
						$author['author'] = ! empty( $inner_block_child->attributes['author'] ) ? $inner_block_child->attributes['author'] : '';

						// Add author to children.
						$card_attributes['children'][] = $author;
					}

					// Author Details.
					if ( 'quark/review-cards-author-details' === $inner_block_child->name ) {
						// Initialize author details.
						$author_details = [];

						// Add block type.
						$author_details['type'] = 'author-details';

						// Add author details.
						$author_details['author_details'] = ! empty( $inner_block_child->attributes['authorDetails'] ) ? $inner_block_child->attributes['authorDetails'] : '';

						// Add author details to children.
						$card_attributes['children'][] = $author_details;
					}
				}
			}

			// Add the card to the component attributes.
			$component_attributes['items'][] = $card_attributes;
		}
	}

	// Return the component markup.
	return quark_get_component( COMPONENT, $component_attributes );
}
