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

						// Check if the menu item has dropdown content.
						if ( ! empty( $mega_menu_item['innerBlocks'] ) ) {
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
											'url'      => $content_item['attrs']['url']['url'] ?? '',
										];
									} elseif ( 'quark/two-columns' === $content_item['blockName'] ) {
										$mega_menu_item_attributes['contents'][] = [
											'type' => 'slot',
											'slot' => implode( '', array_map( 'render_block', $content_column['innerBlocks'] ) ),
										];

										// Get the menu items data.
										$mega_menu_item_attributes['contents'][] = [
											'type'  => 'menu-items',
											'items' => extract_menu_list_items( $content_column['innerBlocks'] ),
										];
									}
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
				if ( ! empty( $nav_inner_block['innerBlocks'] ) ) {
					foreach ( $nav_inner_block['innerBlocks'] as $secondary_menu_item ) {
						$secondary_menu_item_attributes = [];

						// Add title.
						$secondary_menu_item_attributes['title'] = $secondary_menu_item['attrs']['title'] ?? '';
						$secondary_menu_item_attributes['icon']  = ! empty( $secondary_menu_item['attrs']['hasIcon'] ) ? 'search' : '';
						$secondary_menu_item_attributes['url']   = $secondary_menu_item['attrs']['url']['url'] ?? '';

						// Check if the menu item has dropdown content.
						if ( ! empty( $secondary_menu_item['innerBlocks'] ) ) {
							// Get the menu item contents.
							$secondary_menu_item_contents = $secondary_menu_item['innerBlocks'][0]['innerBlocks'];

							// Loop through contents.
							foreach ( $secondary_menu_item_contents as $content_column ) {
								foreach ( $content_column['innerBlocks'] as $content_item ) {
									if ( 'quark/header-menu-item-featured-section' === $content_item['blockName'] ) {
										$secondary_menu_item_attributes['contents'][] = [
											'type'     => 'featured-section',
											'image_id' => $content_item['attrs']['image']['id'] ?? 0,
											'title'    => $content_item['attrs']['title'] ?? '',
											'subtitle' => $content_item['attrs']['subtitle'] ?? '',
											'cta_text' => $content_item['attrs']['ctaText'] ?? '',
										];
									} elseif ( 'quark/two-columns' === $content_item['blockName'] ) {
										$secondary_menu_item_attributes['contents'][] = [
											'type' => 'slot',
											'slot' => implode( '', array_map( 'render_block', $content_column['innerBlocks'] ) ),
										];
									}
								}
							}
						}

						// Add attributes of current item.
						$attributes['secondary_nav']['items'][] = $secondary_menu_item_attributes;
					}
				}
				break;

			// CTA Buttons block.
			case 'quark/header-cta-buttons':
				if ( ! empty( $nav_inner_block['innerBlocks'] ) ) {
					foreach ( $nav_inner_block['innerBlocks'] as $cta_button_item ) {
						$current_cta_button = [];

						// Contact Button.
						if ( 'quark/contact-button' === $cta_button_item['blockName'] ) {
							$current_cta_button = [
								'type'       => 'contact',
								'class'      => 'header__phone-btn',
								'text'       => $cta_button_item['attrs']['btnText'] ?? '',
								'url'        => $cta_button_item['attrs']['url']['url'] ?? '',
								'appearance' => $cta_button_item['appearance'] ?? 'outline',
								'color'      => $cta_button_item['attrs']['backgroundColor'] ?? 'black',
							];

							// Add to cta buttons.
							$attributes['cta_buttons'][] = $current_cta_button;
						} elseif ( 'quark/raq-button' === $cta_button_item['blockName'] ) {
							$current_cta_button = [
								'type'       => 'raq',
								'class'      => 'header__request-quote-btn',
								'text'       => $cta_button_item['attrs']['btnText'] ?? '',
								'url'        => $cta_button_item['attrs']['url']['url'] ?? '',
								'appearance' => $cta_button_item['appearance'] ?? '',
								'color'      => $cta_button_item['attrs']['backgroundColor'] ?? '',
							];

							// Add to cta buttons.
							$attributes['cta_buttons'][] = $current_cta_button;
						}
					}
				}
				break;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}

/**
 * Extract list items from blocks.
 *
 * @param mixed[] $blocks Blocks.
 *
 * @return mixed[]
 */
function extract_menu_list_items( array $blocks = [] ): array {
	// Check if blocks exist.
	if ( empty( $blocks ) ) {
		return [];
	}

	// Initialize items.
	$items = [];

	// Check if the first blocks is an array.
	if ( empty( $blocks[0] && ! is_array( $blocks[0] ) ) ) {
		return [];
	}

	// Loop through the two columns block innerblocks.
	if ( 'quark/two-columns' === $blocks[0]['blockName'] && ! empty( $blocks[0]['innerBlocks'] ) ) {
		foreach ( $blocks[0]['innerBlocks'] as $column ) {
			if ( 'quark/two-columns-column' === $column['blockName'] && ! empty( $column['innerBlocks'] ) ) {
				// Initialize column items.
				$column_items = [];

				// Loop through columns.
				foreach ( $column['innerBlocks'] as $inner_block ) {
					// Loop through items in the column.
					if ( 'quark/menu-list' === $inner_block['blockName'] && ! empty( $inner_block['innerBlocks'] ) ) {
						// Initialize menu list items.
						$menu_list_items = [];

						// Get the title of the menu list.
						$menu_list_items['title'] = $inner_block['attrs']['title'] ?? '';

						// Loop through menu list items.
						foreach ( $inner_block['innerBlocks'] as $menu_list_item ) {
							if ( 'quark/menu-list-item' === $menu_list_item['blockName'] ) {
								$list_item_attrs = [
									'title' => $menu_list_item['attrs']['title'] ?? '',
									'url'   => $menu_list_item['attrs']['url']['url'] ?? '',
								];

								// Add to the menu list items.
								$menu_list_items['items'][] = $list_item_attrs;
							}

							// Add to column items.
							$column_items['menu_list_items'] = $menu_list_items;
						}
					} elseif ( 'quark/thumbnail-cards' === $inner_block['blockName'] && ! empty( $inner_block['innerBlocks'] ) ) {
						// Initialize menu list items.
						$thumbnail_card_items = [];

						// Loop through the items.
						foreach ( $inner_block['innerBlocks'] as $thumbnail_card ) {
							$thumbnail_card_item = [
								'title' => $thumbnail_card['attrs']['title'] ?? '',
								'url'   => $thumbnail_card['attrs']['url']['url'] ?? '',
							];

							// Add to thumbnail card items.
							$thumbnail_card_items[] = $thumbnail_card_item;
						}

						// Add to column items.
						$column_items['thumbnail_card_items'] = $thumbnail_card_items;
					}
				}

				// Add column items to all items.
				$items[] = $column_items;
			}
		}
	}

	// Return extracted items.
	return $items;
}
