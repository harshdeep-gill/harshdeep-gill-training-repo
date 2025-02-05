<?php
/**
 * Namespace for the Ship functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Ships;

use WP_Post;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\get_id;
use function Quark\Ingestor\get_image_details;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ships\get as get_ship_post;
use function Quark\Softrip\Departures\get_related_ship;

const SHIP_SPECIFICATION_MAPPING = [
	[
		'meta_key'    => 'cruising_speed',
		'id'          => 'cruising_speed',
		'type'        => 'Cruising Speed',
		'payload_key' => 'cruisingSpeed',
	],
	[
		'meta_key'    => 'length',
		'id'          => 'length',
		'type'        => 'Length',
		'payload_key' => 'length',
	],
	[
		'meta_key'    => 'breadth',
		'id'          => 'breadth',
		'type'        => 'Breadth',
		'payload_key' => 'breadth',
	],
	[
		'meta_key'    => 'draft',
		'id'          => 'draft',
		'type'        => 'Draft',
		'payload_key' => 'draft',
	],
	[
		'meta_key'    => 'guests',
		'id'          => 'guests',
		'type'        => 'Guests',
		'payload_key' => 'guests',
	],
	[
		'meta_key'    => 'ice_class',
		'id'          => 'ice_class',
		'type'        => 'Ice Class',
		'payload_key' => 'iceClass',
	],
	[
		'meta_key'    => 'lifeboats',
		'id'          => 'lifeboats',
		'type'        => 'Lifeboats',
		'payload_key' => 'lifeboats',
	],
	[
		'meta_key'    => 'registration',
		'id'          => 'registration',
		'type'        => 'Registration',
		'payload_key' => 'registration',
	],
	[
		'meta_key'    => 'staff_and_crew',
		'id'          => 'staff',
		'type'        => 'Staff and Crew',
		'payload_key' => 'staffAndCrew',
	],
	[
		'meta_key'    => 'draft',
		'id'          => 'draft',
		'type'        => 'Draft',
		'payload_key' => 'draft',
	],
	[
		'meta_key'    => 'guest_ratio',
		'id'          => 'guest_ratio',
		'type'        => 'Guest Ratio',
		'payload_key' => 'guestRatio',
	],
	[
		'meta_key'    => 'stabilizers',
		'id'          => 'stabilizers',
		'type'        => 'Stabilizers',
		'payload_key' => 'stabilizers',
	],
	[
		'meta_key'    => 'propulsion',
		'id'          => 'propulsion',
		'type'        => 'Propulsion',
		'payload_key' => 'propulsion',
	],
	[
		'meta_key'    => 'zodiacs',
		'id'          => 'zodiacs',
		'type'        => 'Zodiacs',
		'payload_key' => 'zodiacs',
	],
	[
		'meta_key'    => 'voltage',
		'id'          => 'voltage',
		'type'        => 'Voltage',
		'payload_key' => 'voltage',
	],
	[
		'meta_key'    => 'gross_tonnage',
		'id'          => 'gross_tonnage',
		'type'        => 'Gross Tonnage',
		'payload_key' => 'grossTonnage',
	],
	[
		'meta_key'    => 'year_built',
		'id'          => 'year_built',
		'type'        => 'Year Built',
		'payload_key' => 'yearBuilt',
	],
	[
		'meta_key'    => 'year_refurbished',
		'id'          => 'year_refurbished',
		'type'        => 'Year Refurbished',
		'payload_key' => 'yearRefurbished',
	],
];

/**
 * Get ship data.
 *
 * @param int $departure_post_id Departure post ID.
 *
 * @return mixed[]
 */
function get_ship_data( int $departure_post_id = 0 ): array {
	// Initialize ship data.
	$ship_data = [];

	// Validate departure post ID.
	if ( empty( $departure_post_id ) ) {
		return $ship_data;
	}

	// Get related ship.
	$ship_id = get_related_ship( $departure_post_id );

	// Validate ship ID.
	if ( empty( $ship_id ) ) {
		return $ship_data;
	}

	// Get ship post.
	$ship_post = get_ship_post( $ship_id );

	// Validate ship post.
	if ( empty( $ship_post['post'] ) || ! $ship_post['post'] instanceof WP_Post ) {
		return $ship_data;
	}

	// Get code.
	$ship_code = strval( get_post_meta( $ship_id, 'ship_code', true ) );

	// Bail if no ship code.
	if ( empty( $ship_code ) ) {
		return $ship_data;
	}

	// Prepare ship data.
	$ship_data = [
		'id'             => get_id( $ship_id ),
		'code'           => $ship_code,
		'name'           => get_raw_text_from_html( $ship_post['post']->post_title ),
		'url'            => get_permalink( $ship_id ),
		'description'    => strval( $ship_post['post_meta']['description'] ?? '' ),
		'modified'       => get_post_modified_time( $ship_post['post'] ),
		'specifications' => [],
		'amenities'      => [],
		'deckPlanImage'  => [],
		'heroImage'      => [],
		'images'         => [],
	];

	// Get hero image.
	$featured_image_id = get_post_thumbnail_id( $ship_id );

	// Validate featured image ID.
	if ( ! empty( $featured_image_id ) ) {
		// Add hero image.
		$ship_data['heroImage'] = get_image_details( $featured_image_id );
	}

	// Get collage images.
	$collage_images = $ship_post['data']['collage'] ?? [];

	// Validate collage images.
	if ( is_array( $collage_images ) && ! empty( $collage_images ) ) {
		// Loop through collage images.
		foreach ( $collage_images as $collage_image ) {
			// Validate collage image.
			if ( empty( $collage_image ) || ! is_array( $collage_image ) || empty( $collage_image['image_id'] ) ) {
				continue;
			}

			// Get image ID.
			$image_id = absint( $collage_image['image_id'] );

			// Validate image ID.
			if ( empty( $image_id ) ) {
				continue;
			}

			// Get image details.
			$image_details = get_image_details( $image_id );

			// Validate.
			if ( empty( $image_details ) ) {
				continue;
			}

			// Add image.
			$ship_data['images'][] = $image_details;
		}
	}

	// Get deck plan image from meta.
	$deck_plan_image_id = absint( $ship_post['post_meta']['deck_plan_image'] ?? 0 );

	// Validate deck plan image ID.
	if ( ! empty( $deck_plan_image_id ) ) {
		// Add deck plan image.
		$ship_data['deckPlanImage'] = get_image_details( $deck_plan_image_id );
	}

	// Get ship specifications data.
	$ship_data['specifications'] = get_ship_specifications_data( $ship_id );

	// Add ship amenities data.
	$ship_data['amenities'] = get_ship_amenities_data( $ship_id );

	// Return ship data.
	return $ship_data;
}

/**
 * Get ship specification data.
 *
 * @param int $ship_post_id Ship post ID.
 *
 * @return mixed[]
 */
function get_ship_specifications_data( int $ship_post_id = 0 ): array {
	// Initialize ship specifications data.
	$ship_specifications_data = [];

	// Validate ship post ID.
	if ( empty( $ship_post_id ) ) {
		return $ship_specifications_data;
	}

	// Get ship post.
	$ship_post = get_ship_post( $ship_post_id );

	// Validate ship post.
	if ( empty( $ship_post['post'] ) || ! $ship_post['post'] instanceof WP_Post ) {
		return $ship_specifications_data;
	}

	// Loop through ship specification mapping.
	foreach ( SHIP_SPECIFICATION_MAPPING as $specification ) {
		// Get meta key.
		$meta_key = $specification['meta_key'];

		// Get type.
		$type = $specification['type'];

		// Get id.
		$id = $specification['id'];

		// Get payload key.
		$payload_key = $specification['payload_key'];

		// Get meta value.
		$meta_value = strval( $ship_post['post_meta'][ $meta_key ] ?? '' );

		// Validate meta value.
		if ( empty( $meta_value ) ) {
			continue;
		}

		// Add ship specification data.
		$ship_specifications_data[ $payload_key ] = [
			'id'    => $id,
			'type'  => $type,
			'value' => $meta_value,
		];
	}

	// Return ship specifications data.
	return $ship_specifications_data;
}

/**
 * Get ship amenities data.
 *
 * @param int $ship_post_id Ship post ID.
 *
 * @return mixed[]
 */
function get_ship_amenities_data( int $ship_post_id = 0 ): array {
	// Initialize ship amenities data.
	$ship_amenities_data = [
		'cabin'      => [],
		'aboard'     => [],
		'activities' => [],
	];

	// Validate ship post ID.
	if ( empty( $ship_post_id ) ) {
		return $ship_amenities_data;
	}

	// Get ship post.
	$ship_post = get_ship_post( $ship_post_id );

	// Validate ship post.
	if ( empty( $ship_post['post'] ) || ! $ship_post['post'] instanceof WP_Post ) {
		return $ship_amenities_data;
	}

	// Loop through meta.
	foreach ( $ship_post['post_meta'] as $meta_key => $meta_value ) {
		// Skip non-string values.
		if ( ! is_string( $meta_value ) ) {
			continue;
		}

		// Check for cabin.
		if ( str_starts_with( $meta_key, 'cabin_' ) ) {
			// @todo Suggest for a better way to handle this - maybe renaming the meta key to avoid collisions.
			$ship_amenities_data['cabin'][] = $meta_value;

			// Continue to next meta.
			continue;
		}

		// Check for aboard.
		if ( str_starts_with( $meta_key, 'aboard_' ) ) {
			// @todo Suggest for a better way to handle this - maybe renaming the meta key to avoid collisions.
			$ship_amenities_data['aboard'][] = $meta_value;

			// Continue to next meta.
			continue;
		}

		// Check for activities.
		if ( str_starts_with( $meta_key, 'activities_' ) ) {
			// @todo Suggest for a better way to handle this - maybe renaming the meta key to avoid collisions.
			$ship_amenities_data['activities'][] = $meta_value;

			// Continue to next meta.
			continue;
		}
	}

	// Return ship amenities data.
	return $ship_amenities_data;
}
