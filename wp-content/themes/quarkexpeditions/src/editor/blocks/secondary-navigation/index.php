<?php
/**
 * Block: Secondary Navigation.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\SecondaryNavigation;

use WP_Block;
use WP_Block_List;

const COMPONENT  = 'secondary-navigation';
const BLOCK_NAME = 'quark/secondary-navigation';

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

	// Set component attributes.
	$component_attributes = [
		'slot' => '',
	];

	// Check for inner blocks.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Loop through inner blocks.
		foreach ( $block->inner_blocks as $inner_block ) {
			if ( ! $inner_block instanceof WP_Block ) {
				continue;
			}

			// Check for inner block is secondary navigation menu.
			if ( 'quark/secondary-navigation-menu' === $inner_block->name ) {
				// Set secondary navigation menu component attributes.
				$secondary_navigation_menu_component_attributes = [
					'slot'               => '',
					'jump_to_navigation' => true,
				];

				// Loop through inner blocks of secondary navigation menu.
				foreach ( $inner_block->inner_blocks as $menu_item ) {
					if ( ! $menu_item instanceof WP_Block ) {
						continue;
					}

					// Check for inner block is secondary navigation item.
					if ( 'quark/secondary-navigation-item' === $menu_item->name ) {
						$secondary_navigation_menu_component_attributes['slot'] .= quark_get_component(
							COMPONENT . '.nav-item',
							[
								'href'   => $menu_item->attributes['url']['url'] ?? '',
								'target' => ! empty( $menu_item->attributes['url']['newWindow'] ) ? '_blank' : '',
								'slot'   => $menu_item->attributes['title'],
							]
						);
					}
				}

				// Set secondary navigation menu component attributes.
				$component_attributes['slot'] .= quark_get_component( COMPONENT . '.navigation', $secondary_navigation_menu_component_attributes );
			}

			// Check for inner block is secondary navigation cta buttons.
			if ( 'quark/secondary-navigation-cta-buttons' === $inner_block->name ) {
				$component_attributes['slot'] .= quark_get_component( COMPONENT . '.cta-buttons', [ 'slot' => $inner_block->render() ] );
			}
		}
	}

	// Return component.
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
	$blocks_and_attributes[ BLOCK_NAME . '-item' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
