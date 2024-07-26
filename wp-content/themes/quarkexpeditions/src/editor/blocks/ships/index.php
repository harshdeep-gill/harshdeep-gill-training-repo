<?php
/**
 * Block: Ships.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Ships;

use WP_Post;
use Quark\Softrip\Itinerary;

use function Quark\CabinCategories\get as get_cabin_categories;
use function Quark\ShipDecks\get as get_ship_deck;
use function Quark\Ships\get as get_ships;
use function Quark\Expeditions\get as get_expedition;

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
		$expedition_meta = $expedition['post_meta'];

		// Get related itineraries for the expedition.
		if (
			empty( $expedition_meta['related_itineraries'] )
			|| ! is_array( $expedition_meta['related_itineraries'] )
		) {
			return '';
		}

		// Convert to integers for fetching post.
		$related_itineraries = array_map( 'absint', $expedition_meta['related_itineraries'] );

		// Initialize related ships IDs.
		$related_ship_ids = [];

		// Loop through the related itineraries.
		foreach ( $related_itineraries as $itinerary_id ) {
			// Get the itinerary.
			$itinierary = new Itinerary( $itinerary_id );

			// Get related ships for the itinerary.
			$related_ships = $itinierary->get_related_ships();

			// Check for array.
			if ( ! is_array( $related_ships ) ) {
				continue;
			}

			// Loop through the related ships.
			foreach ( $related_ships as $related_ship ) {
				// Check for post.
				if ( ! isset( $related_ship['post'] ) && ! $related_ship['post'] instanceof WP_Post ) {
					continue;
				}

				// Add to list.
				$related_ship_ids[] = $related_ship['post']->ID;
			}
		}

		// Remove duplicates.
		$ships_ids = array_unique( $related_ship_ids );

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
		$ship = get_ships( $ship_id );

		// Get the post and post meta.
		$ship_post = $ship['post'];
		$ship_meta = $ship['post_meta'];

		// Check for post.
		if ( ! $ship_post instanceof WP_Post ) {
			continue;
		}

		// Prepare deck data.
		$decks_data = [];

		// Get Decks associated with the ship.
		if ( ! empty( $ship_meta['related_decks'] ) && is_array( $ship_meta['related_decks'] ) ) {
			$deck_ids = array_map( 'absint', $ship_meta['related_decks'] );

			// Loop through the deck IDs.
			foreach ( $deck_ids as $deck_id ) {
				// Get the deck.
				$deck = get_ship_deck( $deck_id );

				// Get the post and post meta.
				$deck_post = $deck['post'];
				$deck_meta = $deck['post_meta'];

				// Check for post.
				if ( ! $deck_post instanceof WP_Post ) {
					continue;
				}

				// Prepare public spaces data.
				$public_spaces = [];

				// Check if we have deck meta.
				if ( ! empty( $deck_meta ) ) {
					$public_spaces = prepare_public_spaces( $deck_meta );
				}

				// Prepare Cabin Options data.
				$cabin_options = [];

				// Check if we have cabin categories.
				if ( ! empty( $deck_meta['cabin_categories'] ) && is_array( $deck_meta['cabin_categories'] ) ) {
					$cabin_options_ids = array_map( 'absint', $deck_meta['cabin_categories'] );
					$cabin_options     = get_cabin_options( $cabin_options_ids );
				}

				// Get the post data.
				$decks_data[] = [
					'id'            => $deck_post->post_name,
					'title'         => $deck_meta['deck_name'],
					'image_id'      => absint( $deck_meta['deck_plan_image'] ),
					'description'   => apply_filters( 'the_content', $deck_post->post_content ),
					'cabin_options' => $cabin_options,
					'public_spaces' => $public_spaces,
				];
			}
		}

		// Get the post data.
		$ships_data[] = [
			'id'          => $ship_post->post_name,
			'title'       => $ship_post->post_title,
			'permalink'   => $ship['permalink'],
			'description' => apply_filters( 'the_content', $ship_post->post_content ),
			'content'     => '', // TODO: Will accommodate Ships feature, amenities & carousel Images.
			'decks_id'    => $ship_post->post_name . '_decks',
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

/**
 * Prepare public spaces data.
 *
 * @param mixed[] $deck_meta The deck meta.
 *
 * @return mixed[] The public spaces data.
 */
function prepare_public_spaces( array $deck_meta = [] ): array {
	// Check if we have public spaces.
	if ( empty( $deck_meta ) ) {
		return [];
	}

	// Prepare public spaces data.
	$public_spaces = [];

	// Search for public spaces meta keys and store its values.
	foreach ( $deck_meta as $key => $value ) {
		// Check if this is a public space meta key.
		if ( false !== strpos( $key, 'public_spaces_' ) ) {
			// Split the key into parts.
			$key_parts          = explode( '_', $key );
			$key_name           = end( $key_parts );
			$key_index          = $key_parts[2];
			$public_space_value = null;

			// If key contains 'title' string, then it's a title.
			if ( 'title' === $key_name ) {
				$public_space_value = $value;
			}

			// If key contains 'description' string, then it's a description.
			if ( 'description' === $key_name ) {
				$public_space_value = apply_filters( 'the_content', $value );
			}

			// If key contains 'image' string, then it's an image.
			if ( 'image' === $key_name ) {
				$public_space_value = absint( $value );
			}

			// If we have all the data, then add it to the public spaces at the index specifeid by last second part of the key.
			$public_spaces[ $key_index ][ $key_name ] = $public_space_value;
		}
	}

	// Return public spaces data.
	return $public_spaces;
}

/**
 * Get Cabin Options data.
 *
 * @param int[] $cabin_options_ids The cabin options IDs.
 *
 * @return mixed[] The Cabin Options data.
 */
function get_cabin_options( array $cabin_options_ids = [] ): array {
	// Check if we have cabin options.
	if ( empty( $cabin_options_ids ) ) {
		return [];
	}

	// Get the cabin options.
	$cabin_options = [];

	// Loop through the cabin options IDs.
	foreach ( $cabin_options_ids as $cabin_option_id ) {
		// Get the cabin option.
		$cabin_option_data = get_cabin_categories( $cabin_option_id );

		// Get the post and post meta.
		$cabin_option_post       = $cabin_option_data['post'];
		$cabin_option_meta       = $cabin_option_data['post_meta'];
		$cabin_option_taxonomies = $cabin_option_data['post_taxonomies'];
		$cabin_option_thumbnail  = $cabin_option_data['post_thumbnail'];

		// Check for post.
		if ( ! $cabin_option_post instanceof WP_Post ) {
			continue;
		}

		// Prepare cabin option data.
		$cabin_option = [
			'id'          => $cabin_option_post->post_name,
			'title'       => $cabin_option_meta['cabin_name'] ?? '',
			'image_id'    => $cabin_option_thumbnail,
			'description' => apply_filters( 'the_content', $cabin_option_post->post_content ) ?? '',
		];

		// Add size range if available.
		if ( ! empty( $cabin_option_meta['cabin_category_size_range_from'] ) || ! empty( $cabin_option_meta['cabin_category_size_range_to'] ) ) {
			$cabin_option['details'][] = [
				'label' => __( 'Size', 'qrk' ),
				'value' => $cabin_option_meta['cabin_category_size_range_from'] === $cabin_option_meta['cabin_category_size_range_to']
					? $cabin_option_meta['cabin_category_size_range_from']
					: $cabin_option_meta['cabin_category_size_range_from'] . ' - ' . $cabin_option_meta['cabin_category_size_range_to'],
			];
		}

		// Add occupancy range if available.
		if ( ! empty( $cabin_option_meta['cabin_occupancy_pax_range_from'] ) || ! empty( $cabin_option_meta['cabin_occupancy_pax_range_to'] ) ) {
			$cabin_option['details'][] = [
				'label' => __( 'Occupancy', 'qrk' ),
				'value' => $cabin_option_meta['cabin_occupancy_pax_range_from'] === $cabin_option_meta['cabin_occupancy_pax_range_to']
					? $cabin_option_meta['cabin_occupancy_pax_range_from']
					: $cabin_option_meta['cabin_occupancy_pax_range_from'] . ' - ' . $cabin_option_meta['cabin_occupancy_pax_range_to'],
			];
		}

		// Add bed configuration if available.
		if ( ! empty( $cabin_option_meta['cabin_bed_configuration'] ) ) {
			$cabin_option['details'][] = [
				'label' => __( 'Bed Config.', 'qrk' ),
				'value' => apply_filters( 'the_content', $cabin_option_meta['cabin_bed_configuration'] ),
			];
		}

		// Add Class if available.
		if ( is_array( $cabin_option_taxonomies['qrk_cabin_class'] ) && isset( $cabin_option_taxonomies['qrk_cabin_class'][0]['name'] ) ) {
			$cabin_option['details'][] = [
				'label' => __( 'Class', 'qrk' ),
				'value' => $cabin_option_taxonomies['qrk_cabin_class'][0]['name'],
			];
		}

		// Add location if available.
		if ( ! empty( $cabin_option_meta['related_decks'] ) ) {
			// Prepare location data.
			$related_decks_ids = array_map( 'absint', $cabin_option_meta['related_decks'] );

			// Loop through the related decks IDs.
			$locations = [];

			// Loop through the related decks to get locations.
			foreach ( $related_decks_ids as $related_deck_id ) {
				// Get the deck.
				$related_deck = get_ship_deck( $related_deck_id );

				// Get the post and post meta.
				$related_deck_meta = $related_deck['post_meta'];

				// Prepare location data.
				$locations[] = $related_deck_meta['deck_name'];
			}

			// Prepare comma separated location.
			$cabin_locations = implode( ', ', $locations );

			// Add location to details.
			$cabin_option['details'][] = [
				'label' => __( 'Location', 'qrk' ),
				'value' => $cabin_locations,
			];
		}

		// Append the cabin option to the cabin options.
		$cabin_options[] = $cabin_option;
	}

	// Return cabin options data.
	return $cabin_options;
}
