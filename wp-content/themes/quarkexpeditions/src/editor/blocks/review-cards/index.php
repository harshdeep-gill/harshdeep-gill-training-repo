<?php
/**
 * Block: Review Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ReviewCards;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.review-cards';
const BLOCK_NAME = 'quark/review-cards';

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

	// Initialize component attributes.
	$component_attributes = [];

	// Add is carousel attributes.
	$component_attributes['is_carousel'] = ! empty( $attributes['isCarousel'] ) ? 'true' : 'false';

	// Build data.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for inner block.
		if ( ! $inner_block instanceof WP_Block ) {
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
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				foreach ( $inner_block->inner_blocks as $inner_block_child ) {
					// Check for inner block.
					if ( ! $inner_block_child instanceof WP_Block ) {
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
						$title['title'] = $inner_block_child->attributes['title'];

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
						$rating['rating'] = $inner_block_child->attributes['rating'];

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
						$author['author'] = $inner_block_child->attributes['author'];

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
						$author_details['author_details'] = $inner_block_child->attributes['authorDetails'];

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

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-author' ] = [
		'text' => [ 'author' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-details' ] = [
		'text' => [ 'authorDetails' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-title' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
