<?php
/**
 * Block: Ship Related Adventure Options.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ShipRelatedAdventureOptions;

use WP_Block;

use function Quark\Ships\get as get_ship;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;

const COMPONENT = 'simple-cards';

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

	// Get the ship.
	$ship = get_ship();

	// Check if the ship is empty.
	if ( empty( $ship['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] ) || ! is_array( $ship['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] ) ) {
		return $content;
	}

	// Prepare the component attributes.
	$component_attributes = [
		'slot' => '',
	];

	// Loop through the related adventure options.
	foreach ( $ship['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] as $ship_related_adventure_option ) {
		// Continue if the term ID is empty.
		if ( empty( $ship_related_adventure_option['term_id'] ) ) {
			continue;
		}

		// Append the card.
		$component_attributes['slot'] .= quark_get_component(
			COMPONENT . '.card',
			[
				'image_id' => get_term_meta( $ship_related_adventure_option['term_id'], 'image', true ),
				'title'    => $ship_related_adventure_option['name'],
			]
		);
	}

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
