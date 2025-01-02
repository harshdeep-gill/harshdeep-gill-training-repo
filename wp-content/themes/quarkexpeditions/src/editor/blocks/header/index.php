<?php
/**
 * Block: Header.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Header;

use WP_Block;
use WP_Block_List;

use function Quark\OfficePhoneNumbers\get_corporate_office_phone_number;

const COMPONENT  = 'parts.header';
const BLOCK_NAME = 'quark/header';

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

	// Get the corporate office phone number.
	$office_data = get_corporate_office_phone_number();

	// Initialize phone number and prefix.
	$phone_number = '';
	$prefix       = '';

	// Check if office data is not empty.
	if ( ! empty( $office_data ) ) {
		$phone_number = $office_data['phone_number'];
		$prefix       = $office_data['prefix'];
	}

	// Initialize component attributes.
	$component_attributes = [
		'primary_nav'   => [
			'items' => [],
		],
		'secondary_nav' => [
			'items' => [],
		],
		'cta_buttons'   => [],
	];

	// Loop through inner blocks.
	foreach ( $block->inner_blocks as $nav_inner_block ) {
		// Check the block.
		if ( ! $nav_inner_block instanceof WP_Block ) {
			continue;
		}

		// Process the block.
		switch ( $nav_inner_block->name ) {
			// Mega menu block.
			case 'quark/header-mega-menu':
				if ( $nav_inner_block->inner_blocks instanceof WP_Block_List ) {
					foreach ( $nav_inner_block->inner_blocks as $mega_menu_item ) {
						// Check for block.
						if ( ! $mega_menu_item instanceof WP_Block ) {
							continue;
						}

						// Initialize mega menu item attributes.
						$mega_menu_item_attributes = [];

						// Add title.
						$mega_menu_item_attributes['title']  = $mega_menu_item->attributes['title'];
						$mega_menu_item_attributes['url']    = $mega_menu_item->attributes['url']['url'] ?? '';
						$mega_menu_item_attributes['target'] = ! empty( $mega_menu_item->attributes['url']['newWindow'] ) ? '_blank' : '';

						// Check if the menu item has dropdown content.
						if ( $mega_menu_item->inner_blocks instanceof WP_Block_List ) {
							// Get the menu item contents.
							$mega_menu_item_contents = $mega_menu_item->inner_blocks->offsetGet( '0' );

							// Check for block.
							if ( ! $mega_menu_item_contents instanceof WP_Block ) {
								continue;
							}

							// Loop through contents.
							foreach ( $mega_menu_item_contents->inner_blocks as $content_column ) {
								// Check for block.
								if ( ! $content_column instanceof WP_Block ) {
									continue;
								}

								// Loop through columns.
								foreach ( $content_column->inner_blocks as $content_item ) {
									// Check for block.
									if ( ! $content_item instanceof WP_Block ) {
										continue;
									}

									// Process the block.
									if ( 'quark/header-menu-item-featured-section' === $content_item->name ) {
										$mega_menu_item_attributes['contents'][] = [
											'type'     => 'featured-section',
											'image_id' => $content_item->attributes['image']['id'] ?? '',
											'title'    => $content_item->attributes['title'],
											'subtitle' => $content_item->attributes['subtitle'],
											'cta_text' => $content_item->attributes['ctaText'],
											'url'      => $content_item->attributes['url']['url'] ?? '',
											'target'   => ! empty( $content_item->attributes['url']['newWindow'] ) ? '_blank' : '',
										];
									} elseif ( 'quark/two-columns' === $content_item->name ) {
										$mega_menu_item_attributes['contents'][] = [
											'type' => 'slot',
											'slot' => implode( '', array_map( 'render_block', $content_column->parsed_block['innerBlocks'] ) ),
										];

										// Get the menu items data.
										$mega_menu_item_attributes['contents'][] = [
											'type'  => 'menu-items',
											'items' => extract_menu_list_items( $content_column->parsed_block['innerBlocks'] ),
										];
									}
								}
							}
						}

						// Add attributes of current item.
						$component_attributes['primary_nav']['items'][]  = $mega_menu_item_attributes;
						$component_attributes['primary_nav']['has_more'] = $nav_inner_block->attributes['hasMoreButton'] ?? false;
					}
				}
				break;

			// Secondary Navigation Block.
			case 'quark/secondary-nav':
				if ( $nav_inner_block->inner_blocks instanceof WP_Block_List ) {
					foreach ( $nav_inner_block->inner_blocks as $secondary_menu_item ) {
						// Check for block.
						if ( ! $secondary_menu_item instanceof WP_Block ) {
							continue;
						}

						// Initialize secondary menu item attributes.
						$secondary_menu_item_attributes = [];

						// Add title.
						$secondary_menu_item_attributes['title']  = $secondary_menu_item->attributes['title'] ?? '';
						$secondary_menu_item_attributes['icon']   = ! empty( $secondary_menu_item->attributes['hasIcon'] ) ? 'search' : '';
						$secondary_menu_item_attributes['url']    = $secondary_menu_item->attributes['url']['url'] ?? '';
						$secondary_menu_item_attributes['target'] = ! empty( $secondary_menu_item->attributes['url']['newWindow'] ) ? '_blank' : '';
						$secondary_menu_item_attributes['type']   = 'default-item';

						// Check if the menu item has dropdown content.
						if ( $secondary_menu_item->inner_blocks instanceof WP_Block_List ) {
							// Get the menu item contents.
							$secondary_menu_item_contents = $secondary_menu_item->inner_blocks->offsetGet( '0' );

							// Check for block.
							if ( ! $secondary_menu_item_contents instanceof WP_Block ) {
								continue;
							}

							// Loop through contents.
							foreach ( $secondary_menu_item_contents->inner_blocks as $content_column ) {
								// Check for block.
								if ( ! $content_column instanceof WP_Block ) {
									continue;
								}

								// Check for block name.
								if ( 'quark/search-filters-bar' === $content_column->name ) {
									// Set the type to search.
									$secondary_menu_item_attributes['type'] = 'search-item';
								} else {
									// Loop through columns.
									foreach ( $content_column->inner_blocks as $content_item ) {
										// Check for block.
										if ( ! $content_item instanceof WP_Block ) {
											continue;
										}

										// Process the block.
										if ( 'quark/header-menu-item-featured-section' === $content_item->name ) {
											$secondary_menu_item_attributes['contents'][] = [
												'type'     => 'featured-section',
												'image_id' => $content_item->attributes['image']['id'] ?? '',
												'title'    => $content_item->attributes['title'],
												'subtitle' => $content_item->attributes['subtitle'],
												'cta_text' => $content_item->attributes['ctaText'],
											];
										} elseif ( 'quark/two-columns' === $content_item->name ) {
											$secondary_menu_item_attributes['contents'][] = [
												'type' => 'slot',
												'slot' => implode( '', array_map( 'render_block', $content_column->parsed_block['innerBlocks'] ) ),
											];
										}
									}
								}
							}
						}

						// Add attributes of current item.
						$component_attributes['secondary_nav']['items'][] = $secondary_menu_item_attributes;
					}
				}
				break;

			// CTA Buttons block.
			case 'quark/header-cta-buttons':
				if ( $nav_inner_block->inner_blocks instanceof WP_Block_List ) {
					foreach ( $nav_inner_block->inner_blocks as $cta_button_item ) {
						// Check for block.
						if ( ! $cta_button_item instanceof WP_Block ) {
							continue;
						}

						// Initialize current cta button.
						$current_cta_button = [];

						// Contact Button.
						if ( 'quark/contact-button' === $cta_button_item->name ) {
							// Get the phone number.
							$btn_text        = ! empty( $phone_number ) ? $phone_number : $cta_button_item->attributes['btnText'];
							$btn_url         = ! empty( $phone_number ) ? 'tel:' . $phone_number : $cta_button_item->attributes['url']['url'];
							$drawer_btn_text = ! empty( $prefix ) ? $prefix . ' : ' : __( 'Call Now to Book : ', 'qrk' );

							// Add to cta buttons.
							$current_cta_button = [
								'type'        => 'contact',
								'class'       => 'header__phone-btn',
								'text'        => ! empty( $btn_text ) ? $btn_text : '',
								'url'         => ! empty( $btn_url ) ? $btn_url : '',
								'drawer_text' => $drawer_btn_text,
								'appearance'  => $cta_button_item->attributes['appearance'] ?? 'outline',
								'color'       => $cta_button_item->attributes['backgroundColor'] ?? 'black',
							];

							// Add to cta buttons.
							$component_attributes['cta_buttons'][] = $current_cta_button;
						} elseif ( 'quark/raq-button' === $cta_button_item->name ) {
							// Get the phone number.
							$btn_text   = $cta_button_item->attributes['btnText'] ?? '';
							$btn_url    = $cta_button_item->attributes['url']['url'] ?? '';
							$btn_target = ! empty( $cta_button_item->attributes['url']['newWindow'] ) ? '_blank' : '';

							// Add to cta buttons.
							$current_cta_button = [
								'type'       => 'raq',
								'class'      => 'header__request-quote-btn',
								'text'       => ! empty( $btn_text ) ? $btn_text : '',
								'url'        => ! empty( $btn_url ) ? $btn_url : '',
								'target'     => ! empty( $btn_target ) ? $btn_target : '',
								'appearance' => $cta_button_item->attributes['appearance'] ?? '',
								'color'      => $cta_button_item->attributes['backgroundColor'] ?? '',
							];

							// Add to cta buttons.
							$component_attributes['cta_buttons'][] = $current_cta_button;
						}
					}
				}
				break;

		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
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
	if ( empty( $blocks[0] ) || ! is_array( $blocks[0] ) ) {
		return [];
	}

	// Loop through the two columns block innerblocks.
	if ( 'quark/two-columns' === $blocks[0]['blockName'] && ! empty( $blocks[0]['innerBlocks'] ) ) {
		foreach ( $blocks[0]['innerBlocks'] as $column ) {
			if ( 'quark/column' === $column['blockName'] && ! empty( $column['innerBlocks'] ) ) {
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
									'title'  => $menu_list_item['attrs']['title'] ?? '',
									'url'    => $menu_list_item['attrs']['url']['url'] ?? '',
									'target' => ! empty( $menu_list_item['attrs']['url']['newWindow'] ) ? '_blank' : '',
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
								'title'  => $thumbnail_card['attrs']['title'] ?? '',
								'url'    => $thumbnail_card['attrs']['url']['url'] ?? '',
								'target' => ! empty( $thumbnail_card['attrs']['url']['newWindow'] ) ? '_blank' : '',
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

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-menu-item' ] = [
		'text' => [
			'title',
			'placeholder',
		],
	];

	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-menu-item-featured-section' ] = [
		'text'  => [
			'title',
			'subtitle',
			'ctaText',
		],
		'image' => [ 'image' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
