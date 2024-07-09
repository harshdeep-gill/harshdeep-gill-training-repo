<?php
/**
 * Block: Sidebar Grid.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SidebarGrid;

use WP_Block;

const COMPONENT = 'sidebar-grid';

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

	// Initialize items.
	$content = '';
	$sidebar = [
		'slot'           => '',
		'sticky'         => true,
		'show_on_mobile' => false,
	];

	// Loop thorugh inner blocks.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Skip if not a block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Content.
		if ( 'quark/sidebar-grid-content' === $inner_block->name ) {
			$content .= implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );
		}

		// Sidebar.
		if ( 'quark/sidebar-grid-sidebar' === $inner_block->name ) {
			$sidebar['slot']          .= implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );
			$sidebar['sticky']         = $inner_block->attributes['stickySidebar'] ?? true;
			$sidebar['show_on_mobile'] = $inner_block->attributes['showOnMobile'] ?? false;
		}
	}

	// Build attributes.
	$attributes = [
		'slot' => quark_get_component(
			COMPONENT . '.content',
			[
				'slot' => $content,
			]
		) . quark_get_component(
			COMPONENT . '.sidebar',
			$sidebar
		),
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
