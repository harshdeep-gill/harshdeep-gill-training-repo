<?php
/**
 * Block: Highlights.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Highlights;

use WP_Block;

const COMPONENT  = 'highlights';
const BLOCK_NAME = 'quark/highlights';

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

	// Start building slot.
	$slot = quark_get_component(
		COMPONENT . '.title',
		[
			'title' => $attributes['title'],
		]
	);

	// Columns.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for inner block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Process Inner Blocks.
		$content_slot = '';

		// Process content inner blocks.
		foreach ( $inner_block->inner_blocks as $content_block ) {
			// Check for content block.
			if ( ! $content_block instanceof WP_Block ) {
				continue;
			}

			// Switch on block name.
			switch ( $content_block->name ) {
				// Item Title.
				case 'quark/highlight-item-title':
					$content_slot .= quark_get_component(
						COMPONENT . '.item-title',
						[
							'title' => $content_block->attributes['title'],
						]
					);
					break;

				// Item Overline.
				case 'quark/highlight-item-overline':
					$content_slot .= quark_get_component(
						COMPONENT . '.overline',
						[
							'slot' => $content_block->attributes['overline'],
						]
					);
					break;

				// Item Text.
				case 'quark/highlight-item-text':
					$content_slot .= quark_get_component(
						COMPONENT . '.item-text',
						[
							'text' => $content_block->attributes['text'],
						]
					);
					break;
			}
		}

		// Build item slot.
		$slot .= quark_get_component(
			COMPONENT . '.item',
			[
				'slot' => quark_get_component(
					COMPONENT . '.icon',
					[
						'icon'   => $inner_block->attributes['icon'],
						'border' => $inner_block->attributes['border'],
					]
				) . quark_get_component(
					COMPONENT . '.content',
					[
						'slot' => $content_slot,
					]
				),
			]
		);
	}

	// End building slot.
	$slot .= quark_get_component(
		COMPONENT . '.info',
		[
			'slot' => $attributes['info'],
		]
	);

	// Build attributes.
	$component_attributes = [
		'slot' => $slot,
	];

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
		'text' => [
			'title',
			'info',
		],
	];

	// Add data to translate.
	$blocks_and_attributes['quark/highlight-item-overline'] = [
		'text' => [ 'overline' ],
	];

	// Add data to translate.
	$blocks_and_attributes['quark/highlight-item-text'] = [
		'text' => [ 'text' ],
	];

	// Add data to translate.
	$blocks_and_attributes['quark/highlight-item-title'] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
