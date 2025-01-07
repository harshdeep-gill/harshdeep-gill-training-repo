<?php
/**
 * Namespace for the Expedition functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Expeditions;

use WP_Post;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Ingestor\get_id;
use function Quark\Ingestor\get_image_details;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ingestor\Itineraries\get_itineraries;

use const Quark\Expeditions\DESTINATION_TAXONOMY;

/**
 * Get expedition data.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return array{}|array{
 *     id: int,
 *     name: string,
 *     published: bool,
 *     description: string,
 *     images: array{}|array<int,
 *       array{
 *         id: int,
 *         fullSizeUrl: string,
 *         thumbnailUrl: string,
 *         alt: string,
 *       }
 *     >,
 *     destinations: array{}|array<int,
 *        array{
 *          id: int,
 *          name: string,
 *          region: array{
 *             name: string,
 *             code: string,
 *          }
 *        }
 *     >,
 *     itineraries: array{}|array<int,
 *       array{
 *        id: int,
 *        packageId: string,
 *        name: string,
 *        startLocation: string,
 *        endLocation: string,
 *        departures: mixed[],
 *       }
 *     >
 *  }
 */
function get_expedition_data( int $expedition_post_id = 0 ): array {
	// Early return if no expedition post ID.
	if ( empty( $expedition_post_id ) ) {
		return [];
	}

	// Get expedition post.
	$expedition_post = get_expedition( $expedition_post_id );

	// Check for post.
	if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
		return [];
	}

	// Initialize expedition data.
	$expedition_data = [
		'id'           => get_id( $expedition_post_id ),
		'name'         => get_raw_text_from_html( $expedition_post['post']->post_title ),
		'published'    => 'publish' === $expedition_post['post']->post_status,
		'description'  => '',
		'images'       => [],
		'destinations' => [],
		'heroImage'    => [],
		'modified'     => get_post_modified_time( $expedition_post['post'] ),
		'highlights'   => [],
		'url'          => get_permalink( $expedition_post_id ),
		'itineraries'  => [],
	];

	// Get hero image.
	$featured_image_id = get_post_thumbnail_id( $expedition_post_id );

	// Validate featured image ID.
	if ( ! empty( $featured_image_id ) ) {
		// Add hero image.
		$expedition_data['heroImage'] = get_image_details( $featured_image_id );
	}

	// Check for data.
	if ( ! empty( $expedition_post['data'] ) ) {
		// Add hero card slider images.
		if ( ! empty( $expedition_post['data']['hero_card_slider_image_ids'] ) && is_array( $expedition_post['data']['hero_card_slider_image_ids'] ) ) {
			// Map image IDs.
			$image_ids = array_map( 'absint', $expedition_post['data']['hero_card_slider_image_ids'] );

			// Loop through image IDs.
			foreach ( $image_ids as $image_id ) {
				// Image details.
				$image_details = get_image_details( $image_id );

				// Check for image details.
				if ( empty( $image_details ) ) {
					continue;
				}

				// Add image.
				$expedition_data['images'][] = $image_details;
			}
		}

		// Add highlights.
		if ( ! empty( $expedition_post['data']['highlights'] ) && is_array( $expedition_post['data']['highlights'] ) ) {
			$expedition_data['highlights'] = $expedition_post['data']['highlights'];
		}
	}

	// Add description.
	if ( ! empty( $expedition_post['post_meta'] ) && ! empty( $expedition_post['post_meta']['overview'] ) && is_string( $expedition_post['post_meta']['overview'] ) ) {
		$expedition_data['description'] = get_raw_text_from_html( $expedition_post['post_meta']['overview'] );
	}

	// Get destination terms.
	$expedition_data['destinations'] = get_destination_terms( $expedition_post_id );

	// Get itineraries.
	$expedition_data['itineraries'] = get_itineraries( $expedition_post_id );

	// Return expedition data.
	return $expedition_data;
}

/**
 * Get destination terms.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return array{}|array<int,
 *  array{
 *    id: int,
 *    name: string,
 *    region: array{
 *      name: string,
 *      code: string,
 *    }
 *  }
 * >
 */
function get_destination_terms( int $expedition_post_id = 0 ): array {
	// Early return if no expedition post ID.
	if ( empty( $expedition_post_id ) ) {
		return [];
	}

	// Initialize region.
	$regions = [];

	// Get expedition post.
	$expedition_post = get_expedition( $expedition_post_id );

	// Check for post.
	if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
		return $regions;
	}

	// Check for taxonomies.
	if ( ! array_key_exists( DESTINATION_TAXONOMY, $expedition_post['post_taxonomies'] ) ||
		! is_array( $expedition_post['post_taxonomies'][ DESTINATION_TAXONOMY ] )
	) {
		return $regions;
	}

	// Loop through each destination term.
	foreach ( $expedition_post['post_taxonomies'][ DESTINATION_TAXONOMY ] as $destination_term ) {
		if ( empty( $destination_term['parent'] ) ) {
			continue;
		}

		// Parent term ID.
		$parent_term_id = get_parent_term_with_softrip_id( $destination_term['term_id'] );

		// If empty parent term ID.
		if ( empty( $parent_term_id ) ) {
			continue;
		}

		// Get parent term.
		$parent_term = get_term( $parent_term_id, DESTINATION_TAXONOMY, ARRAY_A );

		// Check for parent term.
		if ( empty( $parent_term ) || ! is_array( $parent_term ) ) {
			continue;
		}

		// Softrip ID for parent.
		$softrip_id = get_term_meta( $parent_term_id, 'softrip_id', true );

		// Check for Softrip ID.
		if ( empty( $softrip_id ) ) {
			continue;
		}

		// Add region.
		$regions[] = [
			'id'     => absint( $destination_term['term_id'] ),
			'name'   => $destination_term['name'],
			'region' => [
				'name' => $parent_term['name'],
				'code' => strval( $softrip_id ),
			],
		];
	}

	// Return region terms.
	return $regions;
}

/**
 * Get parent term with softrip id.
 *
 * @param int $term_id Term ID.
 *
 * @return int
 */
function get_parent_term_with_softrip_id( int $term_id = 0 ): int {
	// Initialize parent term ID.
	$parent_term_id = 0;

	// Check for term ID.
	if ( empty( $term_id ) ) {
		return $parent_term_id;
	}

	// Get term.
	$term = get_term( $term_id, DESTINATION_TAXONOMY, ARRAY_A );

	// Validate term.
	if ( empty( $term ) || ! is_array( $term ) ) {
		return $parent_term_id;
	}

	// Get parent term.
	$parent_term_id = absint( $term['parent'] );

	// Check for parent term.
	if ( empty( $parent_term_id ) ) {
		return $parent_term_id;
	}

	// Get softrip_id from meta.
	$softrip_id = get_term_meta( $parent_term_id, 'softrip_id', true );

	// Check for softrip_id.
	if ( ! empty( $softrip_id ) ) {
		// Return term ID.
		return $parent_term_id;
	}

	// Get parent term.
	$parent_term_id = get_parent_term_with_softrip_id( $parent_term_id );

	// Return parent term ID.
	return $parent_term_id;
}
