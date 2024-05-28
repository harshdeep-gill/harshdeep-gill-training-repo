<?php
/**
 * Block: Sidebar Grid.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SidebarGrid;

const BLOCK_NAME = 'quark/sidebar-grid';
const COMPONENT  = 'sidebar-grid';

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

	// Initialize items.
	$content = '';
	$sidebar = [
		'slot'           => '',
		'sticky'         => true,
		'show_on_mobile' => false,
	];

	// Loop thorugh inner blocks.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Content.
		if ( 'quark/sidebar-grid-content' === $inner_block['blockName'] ) {
			$content .= implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );
		}

		// Sidebar.
		if ( 'quark/sidebar-grid-sidebar' === $inner_block['blockName'] ) {
			$sidebar['slot']          .= implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );
			$sidebar['sticky']         = $inner_block['attrs']['stickySidebar'] ?? true;
			$sidebar['show_on_mobile'] = $inner_block['attrs']['showOnMobile'] ?? false;
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
