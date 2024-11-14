<?php
/**
 * Block: Info Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InfoCards;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'info-cards';

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

	// Build the sub-blocks.
	$cards = '';

	// Process inner blocks.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Loop through inner blocks.
		foreach ( $block->inner_blocks as $inner_block ) {
			// Check for inner block.
			if ( ! $inner_block instanceof WP_Block ) {
				continue;
			}

			// Parse Inner Blocks.
			if ( ! $inner_block->inner_blocks instanceof WP_Block_List ) {
				continue;
			}

			// Build card slot.
			$card = '';

			// Check for inner block name.
			if ( 'quark/info-card' === $inner_block->name ) {
				// Build content slot.
				$content = '';

				// Check for inner blocks.
				if ( ! isset( $inner_block->attributes['image']['id'] ) ) {
					continue;
				}

				// Build image slot.
				$card .= quark_get_component(
					COMPONENT . '.image',
					[
						'image_id' => $inner_block->attributes['image']['id'],
					]
				);

				// Loop through inner blocks.
				if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
					foreach ( $inner_block->inner_blocks as $content_inner_block ) {
						// Check for content block.
						if ( ! $content_inner_block instanceof WP_Block ) {
							continue;
						}

						// Switch on block name.
						switch ( $content_inner_block->name ) {

							// Process title block.
							case 'quark/info-card-title':
								// Check for title.
								if ( ! isset( $content_inner_block->attributes['title'] ) ) {
									break;
								}

								// Build title slot.
								$content .= quark_get_component(
									COMPONENT . '.title',
									[
										'title' => $content_inner_block->attributes['title'],
									]
								);
								break;

							// Process tag block.
							case 'quark/info-card-tag':
								// Check for text.
								if ( ! isset( $content_inner_block->attributes['text'] ) ) {
									break;
								}

								// Build text slot.
								$content .= quark_get_component(
									COMPONENT . '.tag',
									[
										'text'             => $content_inner_block->attributes['text'],
										'background_color' => $content_inner_block->attributes['backgroundColor'],
									]
								);
								break;

							// Process overline block.
							case 'quark/info-card-overline':
								// Check for text.
								if ( ! isset( $content_inner_block->attributes['overline'] ) ) {
									break;
								}

								// Build text slot.
								$content .= quark_get_component(
									COMPONENT . '.overline',
									[
										'slot' => $content_inner_block->attributes['overline'],
									]
								);
								break;

							// Process description block.
							case 'quark/info-card-description':
								// Get the paragraph block.
								$paragraph_block = $content_inner_block->inner_blocks->offsetGet( '0' );

								// Check for paragraph block.
								if ( ! $paragraph_block instanceof WP_Block ) {
									break;
								}

								// Build text slot.
								$content .= quark_get_component(
									COMPONENT . '.description',
									[
										'slot' => render_block( $paragraph_block->parsed_block ),
									]
								);
								break;

							// Process cta block.
							case 'quark/info-card-cta':
								// Check for text.
								if ( ! isset( $content_inner_block->attributes['text'] ) ) {
									break;
								}

								// Build text slot.
								$content .= quark_get_component(
									COMPONENT . '.cta',
									[
										'text' => $content_inner_block->attributes['text'],
									]
								);
								break;
						}
					}
				}

				// Build content slot.
				$card .= quark_get_component(
					COMPONENT . '.content',
					[
						'position' => $inner_block->attributes['contentPosition'],
						'slot'     => $content,
					]
				);
			}

			// Build item slot.
			$cards .= quark_get_component(
				COMPONENT . '.card',
				[
					'url'    => $inner_block->attributes['url']['url'] ?? '',
					'target' => ! empty( $inner_block->attributes['url']['newWindow'] ) ? '_blank' : '',
					'slot'   => $card,
				]
			);
		}
	}

	// Return the component markup.
	return quark_get_component(
		COMPONENT,
		[
			'layout'            => $attributes['layout'],
			'mobile_carousel'   => $attributes['mobileCarousel'],
			'carousel_overflow' => $attributes['carouselOverflow'],
			'slot'              => $cards,
		],
	);
}
