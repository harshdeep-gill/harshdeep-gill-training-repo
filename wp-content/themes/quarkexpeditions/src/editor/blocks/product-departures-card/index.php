<?php
/**
 * Block: Product Departures Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ProductDeparturesCard;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.product-departures-card';
const BLOCK_NAME = 'quark/product-departures-card';

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
		'images'         => [],
		'title'          => '',
		'cta_badge_text' => '',
		'cta'            => '',
		'departures'     => [],
	];

	// Add Images.
	$component_attributes['images'][0] = $block->attributes['image1']['id'] ?? 0;
	$component_attributes['images'][1] = $block->attributes['image2']['id'] ?? 0;

	// Add CTA Badge Text.
	if ( ! empty( $block->attributes['hasCtaBadge'] ) && ! empty( $block->attributes['ctaBadgeText'] ) ) {
		$component_attributes['cta_badge_text'] = $block->attributes['ctaBadgeText'];
	}

	// Build slot.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check if block is an instance of WP_Block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Title.
		if ( 'quark/product-departures-card-title' === $inner_block->name ) {
			// Add title.
			$component_attributes['title'] = $inner_block->attributes['title'];
		}

		// CTA.
		if ( 'quark/product-departures-card-cta' === $inner_block->name ) {
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				foreach ( $inner_block->inner_blocks as $child_block ) {
					// Check if block is an instance of WP_Block.
					if ( ! $child_block instanceof WP_Block ) {
						continue;
					}

					// Get the slot.
					$component_attributes['cta'] .= render_block( $child_block->parsed_block );
				}
			}
		}

		// Departures.
		if ( 'quark/product-departures-card-departures' === $inner_block->name ) {
			$component_attributes['departures']['overline'] = $inner_block->attributes['overline'];

			// Initialize departure dates.
			$component_attributes['departures']['dates'] = [];

			// Loop through inner blocks.
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				// Initialize current departure date.
				$current_departure_date = [];

				// Loop through inner blocks.
				foreach ( $inner_block->inner_blocks as $index => $inner_inner_block ) {
					// Check if block is an instance of WP_Block.
					if ( ! $inner_inner_block instanceof WP_Block ) {
						continue;
					}

					// Process dates block.
					if ( 'quark/product-departures-card-dates' === $inner_inner_block->name ) {
						// Add offers.
						$current_departure_date['offer']      = $inner_inner_block->attributes['offer'];
						$current_departure_date['offer_text'] = $inner_inner_block->attributes['offerText'];

						// Add Is Sold Out.
						$current_departure_date['is_sold_out'] = ! empty( $inner_inner_block->attributes['isSoldOut'] );

						// Initialize date text.
						$current_departure_date['dates'] = '';

						// Loop through child blocks of departures dates.
						if ( $inner_inner_block->inner_blocks instanceof WP_Block_List ) {
							foreach ( $inner_inner_block->inner_blocks as $child_block ) {
								// Check if block is an instance of WP_Block.
								if ( ! $child_block instanceof WP_Block ) {
									continue;
								}

								// Render dates.
								$current_departure_date['dates'] .= render_block( $child_block->parsed_block );
							}
						}
					}

					// Add current departure date to attributes.
					$component_attributes['departures']['dates'][] = $current_departure_date;
				}
			}
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
	$blocks_and_attributes[ BLOCK_NAME ] = [
		'image' => [
			'image1',
			'image2',
		],
		'text'  => [ 'ctaBadgeText' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-dates' ] = [
		'text' => [
			'offer',
			'offerText',
		],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-departures' ] = [
		'text' => [ 'overline' ],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-title' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
