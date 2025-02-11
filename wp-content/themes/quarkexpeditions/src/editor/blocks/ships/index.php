<?php
/**
 * Block: Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Ships;

use WP_Post;

use function Quark\Expeditions\get_expedition_ship_ids;
use function Quark\ShipDecks\get_deck_data;
use function Quark\Ships\get_ship_data;

const COMPONENT_SHIPS = 'parts.ships';
const COMPONENT_SHIP  = 'parts.ship';
const COLLAGE_PART    = 'parts.collage';
const BLOCK_NAME      = 'quark/ships';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Initialize ships IDs.
	$ships_ids = [];

	// If the selection is manual, we need to check if we have IDs.
	if ( 'manual' === $attributes['selectionType'] ) {
		// Return empty if manual select, but no IDs were selected.
		if ( empty( $attributes['ships'] ) ) {
			return '';
		}

		// Get the selected IDs.
		$ships_ids = $attributes['ships'];
	} elseif ( 'auto' === $attributes['selectionType'] ) {
		// Get the expedition ship IDs. This will only work for the expedition post type.
		$ships_ids = get_expedition_ship_ids();
	}

	// Check if we have posts.
	if ( empty( $ships_ids ) ) {
		return '';
	}

	// Get blog post cards data.
	$ships_data = [];

	// Query the posts.
	foreach ( $ships_ids as $ship_id ) {
		// Get the ship data.
		$ship = get_ship_data( $ship_id );

		// Check for ship data.
		if ( empty( $ship ) || ! is_array( $ship ) ) {
			continue;
		}

		// Initialize content variables.
		$collage = '';

		// Add collage images.
		if ( ! empty( $ship['collage_images'] ) ) {
			$collage = quark_get_component(
				COLLAGE_PART,
				[
					'name'  => $ship['name'] . '_collage',
					'items' => $ship['collage_images'],
				]
			);
		}

		// Initialize decks data.
		$ship_decks_ids = $ship['related_decks'];
		$decks_data     = [];

		// Get Decks associated with the ship.
		if ( ! empty( $ship_decks_ids ) && is_array( $ship_decks_ids ) ) {
			// Loop through the deck IDs.
			foreach ( $ship_decks_ids as $deck_id ) {
				// Get the deck data.
				$deck_data = get_deck_data( $deck_id );

				// Add labels to cabin options details.
				if ( ! empty( $deck_data['cabin_options'] ) ) {
					// Initialize cabin options data.
					$cabin_options_data = [];

					// Loop through the cabin options.
					foreach ( $deck_data['cabin_options'] as $cabin_option ) {
						// Initialize cabin option data.
						$cabin_option_data            = $cabin_option;
						$cabin_option_data['details'] = [];

						// Add Size.
						if ( ! empty( $cabin_option['details']['size_from'] ) && ! empty( $cabin_option['details']['size_to'] ) ) {
							$cabin_option_data['details'][] = [
								'label' => __( 'Size', 'qrk' ),
								'value' => $cabin_option['details']['size_from'] === $cabin_option['details']['size_to']
									? $cabin_option['details']['size_from']
									: $cabin_option['details']['size_from'] . ' - ' . $cabin_option['details']['size_to'],
							];
						}

						// Add Occupancy.
						if ( ! empty( $cabin_option['details']['occupancy_from'] ) && ! empty( $cabin_option['details']['occupancy_to'] ) ) {
							$cabin_option_data['details'][] = [
								'label' => __( 'Occupancy', 'qrk' ),
								'value' => $cabin_option['details']['occupancy_from'] === $cabin_option['details']['occupancy_to']
									? $cabin_option['details']['occupancy_from']
									: $cabin_option['details']['occupancy_from'] . ' - ' . $cabin_option['details']['occupancy_to'],
							];
						}

						// Add Bed configuration.
						if ( ! empty( $cabin_option['details']['bed_configuration'] ) ) {
							$cabin_option_data['details'][] = [
								'label' => __( 'Bed Config.', 'qrk' ),
								'value' => $cabin_option['details']['bed_configuration'],
							];
						}

						// Add Class.
						if ( ! empty( $cabin_option['details']['class'] ) ) {
							$cabin_option_data['details'][] = [
								'label' => __( 'Class', 'qrk' ),
								'value' => $cabin_option['details']['class'],
							];
						}

						// Add Location.
						if ( ! empty( $cabin_option['details']['location'] ) ) {
							$cabin_option_data['details'][] = [
								'label' => __( 'Location', 'qrk' ),
								'value' => $cabin_option['details']['location'],
							];
						}

						// Add Data.
						$cabin_options_data[] = $cabin_option_data;
					}

					// Replace the cabin options data.
					$deck_data['cabin_options'] = $cabin_options_data;
				}

				// Append the deck data to the ship data.
				$decks_data[] = $deck_data;
			}
		}

		// Get the post data.
		$ships_data[] = [
			'id'              => $ship['name'],
			'title'           => $ship['title'],
			'permalink'       => $ship['permalink'],
			'description'     => $ship['description'],
			'collage'         => $collage,
			'vessel_features' => $ship['vessel_features'],
			'amenities'       => $ship['ship_amenities'],
			'decks_id'        => $ship['name'] . '_decks',
			'decks'           => $decks_data,
		];
	}

	// Return single ship if only one ship found.
	if ( 1 === count( $ships_data ) ) {
		return quark_get_component(
			COMPONENT_SHIP,
			[
				'ship' => array_shift( $ships_data ),
			]
		);
	}

	// Return built component.
	return quark_get_component(
		COMPONENT_SHIPS,
		[
			'ships' => $ships_data,
		]
	);
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
		'post_id' => [ 'ships' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
