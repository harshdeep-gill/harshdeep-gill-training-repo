<?php
/**
 * Block: Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Ships;

use WP_Post;

use function Quark\Expeditions\get as get_expedition;
use function Quark\Expeditions\get_ships;
use function Quark\ShipDecks\get_deck_data;
use function Quark\Ships\get_ship_data;

const COMPONENT = 'parts.ships';

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
		// Get the expedition.
		$expedition      = get_expedition();
		$expedition_post = $expedition['post'];

		// Check for post.
		if ( ! $expedition_post instanceof WP_Post ) {
			return '';
		}

		// Get the ships IDs.
		$ships = get_ships( $expedition_post->ID );

		// Check for ships.
		if ( empty( $ships ) ) {
			return '';
		}

		// Get the ships IDs.
		foreach ( $ships as $ship ) {
			$ships_ids[] = $ship['post']->ID;
		}
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
		$ship           = get_ship_data( $ship_id );
		$ship_decks_ids = $ship['related_decks'];
		$decks_data     = [];

		// Get Decks associated with the ship.
		if ( ! empty( $ship_decks_ids ) && is_array( $ship_decks_ids ) ) {
			// Loop through the deck IDs.
			foreach ( $ship_decks_ids as $deck_id ) {
				// Get the deck data.
				$deck_data = get_deck_data( $deck_id );

				// Check for deck data.
				if ( empty( $deck_data ) ) {
					continue;
				}

				// Add labels to cabin options details.
				if ( ! empty( $deck_data['cabin_options'] ) && is_array( $deck_data['cabin_options'] ) ) {
					// Initialize cabin options data.
					$cabin_options_data = [];

					// Loop through the cabin options.
					foreach ( $deck_data['cabin_options'] as $cabin_option ) {
						// Initialize cabin option data.
						$cabin_option_data            = $cabin_option;
						$cabin_option_data['details'] = [];

						// Check for details.
						if ( empty( $cabin_option['details'] ) ) {
							continue;
						}

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
			'id'          => $ship['name'],
			'title'       => $ship['title'],
			'permalink'   => $ship['permalink'],
			'description' => $ship['description'],
			'content'     => '', // TODO: Will accommodate Ships feature, amenities & carousel Images.
			'decks_id'    => $ship['name'] . '_decks',
			'decks'       => $decks_data,
		];
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'ships' => $ships_data,
		]
	);
}
