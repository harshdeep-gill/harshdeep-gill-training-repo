<?php
/**
 * Block: Header.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header;

const BLOCK_NAME = 'quark/header';
const COMPONENT  = 'parts.header';

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
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize attributes.
	$attributes = [
		'primary_nav'   => [
			'items' => [],
		],
		'secondary_nav' => [
			'items' => [],
		],
		'cta_buttons'   => [],
	];

	// Loop through inner blocks.
	foreach ( $block['innerBlocks'] as $nav_inner_block ) {
		switch ( $nav_inner_block['blockName'] ) {
			// Mega menu block.
			case 'quark/header-mega-menu':
				if ( ! empty( $nav_inner_block['innerBlocks'] ) ) {
					foreach ( $nav_inner_block['innerBlocks'] as $mega_menu_item ) {
						$mega_menu_item_attributes = [];

						// Add title.
						$mega_menu_item_attributes['title'] = $mega_menu_item['attrs']['title'] ?? '';

						// Get the menu item contents.
						$mega_menu_item_contents = $mega_menu_item['innerBlocks'][0]['innerBlocks'];

						// Loop through contents.
						foreach ( $mega_menu_item_contents as $content_column ) {
							foreach ( $content_column['innerBlocks'] as $content_item ) {
								if ( 'quark/header-menu-item-featured-section' === $content_item['blockName'] ) {
									$mega_menu_item_attributes['contents'][] = [
										'type'     => 'featured-section',
										'image_id' => $content_item['attrs']['image']['id'] ?? 0,
										'title'    => $content_item['attrs']['title'] ?? '',
										'subtitle' => $content_item['attrs']['subtitle'] ?? '',
										'cta_text' => $content_item['attrs']['ctaText'] ?? '',
									];
								} elseif ( 'quark/two-columns' === $content_item['blockName'] ) {
									$mega_menu_item_attributes['contents'][] = [
										'type' => 'slot',
										'slot' => implode( '', array_map( 'render_block', $content_column['innerBlocks'] ) ),
									];
								}
							}
						}

						// Add attributes of current item.
						$attributes['primary_nav']['items'][] = $mega_menu_item_attributes;
					}
				}
				break;

			// Secondary Navigation Block.
			case 'quark/secondary-nav':
				break;

			// CTA Buttons block.
			case 'quark/header-cta-buttons':
				break;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
