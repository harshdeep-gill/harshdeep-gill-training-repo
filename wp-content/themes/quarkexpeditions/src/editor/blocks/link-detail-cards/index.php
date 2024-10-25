<?php
/**
 * Block: Link Detail Cards.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LinkDetailCards;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'link-detail-cards';

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

	// // Process inner blocks.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Loop through inner blocks.
		foreach ( $block->inner_blocks as $inner_block ) {
			// Check for inner block.
			if ( ! $inner_block instanceof WP_Block ) {
				continue;
			}

			// Check for inner block name.
			if ( 'quark/link-detail-card' === $inner_block->name ) {
				// Check for URL.
				if ( empty( $inner_block->attributes['url']['url'] ) ) {
					continue;
				}

				// Initialize attributes.
				$url     = $inner_block->attributes['url']['url'];
				$target  = $inner_block->attributes['url']['newWindow'] ? '_blank' : '';
				$content = '';

				// Build title slot.
				$content .= quark_get_component(
					COMPONENT . '.title',
					[
						'title' => $inner_block->attributes['title'],
					]
				);

				// Build description slot.
				$content .= quark_get_component(
					COMPONENT . '.description',
					[
						'slot' => $inner_block->attributes['description'],
					]
				);

				// Build content slot.
				$cards .= quark_get_component(
					COMPONENT . '.card',
					[
						'url'    => $url,
						'target' => $target,
						'slot'   => $content,
					]
				);
			}
		}
	}

	// Return the component markup.
	return quark_get_component(
		COMPONENT,
		[
			'slot' => $cards,
		]
	);
}
