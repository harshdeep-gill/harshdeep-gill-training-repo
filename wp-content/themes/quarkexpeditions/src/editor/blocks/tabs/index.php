<?php
/**
 * Block: Tabs.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Tabs;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'parts.tabs';
const BLOCK_NAME = 'quark/tabs';

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
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content    The block default content.
 * @param WP_Block|null $block The block object.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Block index static variable.
	static $block_index = 0;

	// If block is not a WP_Block instance, return.
	if ( ! $block instanceof WP_Block
		|| ! $block->inner_blocks instanceof WP_Block_List
	) {
		return $content;
	}

	// Initialize tabs.
	$tabs = [];

	// Loop through inner blocks.
	while ( $block->inner_blocks->valid() ) {
		$inner_block = $block->inner_blocks->current();

		// If inner block is not a WP_Block instance or not a tabs tab block, skip it.
		if ( ! $inner_block instanceof WP_Block || 'quark/tabs-tab' !== $inner_block->name ) {
			$block->inner_blocks->next();
			continue;
		}

		// Add tab to tabs.
		$tabs[] = [
			'title'   => $inner_block->parsed_block['attrs']['title'] ?? '',
			'id'      => get_tab_index( $block_index, $block->inner_blocks->key() + 1 ),
			'active'  => $attributes['defaultTabIndex'] === $block->inner_blocks->key() + 1,
			'content' => implode(
				'',
				array_map(
					'render_block',
					$inner_block->parsed_block['innerBlocks']
				)
			),
		];

		// Move to next inner block.
		$block->inner_blocks->next();
	}

	// Increment block index.
	++$block_index;

	// Return rendered component.
	return quark_get_component(
		COMPONENT,
		[
			'tabs'       => $tabs,
			'update_url' => $attributes['updateURL'],
		]
	);
}

/**
 * Get tab index.
 *
 * @param int $block_index Block index.
 * @param int $tab_index   Tab index.
 *
 * @return string Tab index.
 */
function get_tab_index( int $block_index = 0, int $tab_index = 1 ): string {
	// Initialize tab id.
	$tab_id = 'tab';

	// If block index is greater than 0, append it to tab id.
	if ( $block_index > 0 ) {
		$tab_id .= '-' . $block_index;
	}

	// Return tab id.
	return $tab_id . '-' . $tab_index;
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
	$blocks_and_attributes[ BLOCK_NAME . '-tab' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
