<?php
/**
 * Namespace for the Cabin functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Cabins;

use WP_Post;

use function Quark\CabinCategories\get as get_cabin_category;
use function Quark\Core\get_raw_text_from_html;
use function Quark\Departures\get as get_departure;
use function Quark\Ingestor\get_image_details;
use function Quark\Ingestor\Occupancies\get_occupancies_data;
use function Quark\Softrip\Occupancies\get_cabin_category_post_ids_by_departure;

use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;

/**
 * Get cabins data for a departure.
 *
 * @param int $expedition_post_id Expedition post ID.
 * @param int $itinerary_post_id  Itinerary post ID.
 * @param int $departure_post_id Departure post ID.
 *
 * @return array{}|array<int,
 *   array{
 *      id: int,
 *      name: string,
 *      code: string,
 *      description: string,
 *      bedDescription: string,
 *      location: string,
 *      type: string,
 *      size: string,
 *      occupancySize: string,
 *      media: array{}|array<int,
 *        array{
 *          id: int,
 *          fullSizeUrl: string,
 *          thumbnailUrl: string,
 *          alt: string,
 *       }
 *      >,
 *      occupancies: array{}|array<int,
 *       array{
 *         id: string,
 *         mask: string,
 *         description: string,
 *         availabilityStatus: string,
 *         availabilityDescription: string,
 *         spacesAvailable: int,
 *         prices: array{
 *           AUD: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *             mandatoryTransferPricePerPerson: int,
 *             supplementalPricePerPerson: int,
 *             promotionsApplied: array{}|array<int,
 *               array{
 *                 id: int,
 *                 promotionCode: string,
 *                 promoPricePerPerson: int,
 *               }
 *             >
 *           },
 *           USD: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *             mandatoryTransferPricePerPerson: int,
 *             supplementalPricePerPerson: int,
 *             promotionsApplied: array{}|array<int,
 *               array{
 *                 id: int,
 *                 promotionCode: string,
 *                 promoPricePerPerson: int,
 *               }
 *             >
 *           },
 *           EUR: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *             mandatoryTransferPricePerPerson: int,
 *             supplementalPricePerPerson: int,
 *             promotionsApplied: array{}|array<int,
 *               array{
 *                 id: int,
 *                 promotionCode: string,
 *                 promoPricePerPerson: int,
 *               }
 *             >
 *           },
 *           CAD: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *             mandatoryTransferPricePerPerson: int,
 *             supplementalPricePerPerson: int,
 *             promotionsApplied: array{}|array<int,
 *               array{
 *                 id: int,
 *                 promotionCode: string,
 *                 promoPricePerPerson: int,
 *               }
 *             >
 *           },
 *           GBP: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *             mandatoryTransferPricePerPerson: int,
 *             supplementalPricePerPerson: int,
 *             promotionsApplied: array{}|array<int,
 *               array{
 *                 id: int,
 *                 promotionCode: string,
 *                 promoPricePerPerson: int,
 *               }
 *             >
 *           }
 *         }
 *       }
 *     >
 *   }
 * >
 */
function get_cabins_data( int $expedition_post_id = 0, int $itinerary_post_id = 0, int $departure_post_id = 0 ): array {
	// Initialize cabins data.
	$cabins_data = [];

	// Early return if no expedition, itinerary or departure post ID.
	if ( empty( $itinerary_post_id ) || empty( $expedition_post_id ) || empty( $departure_post_id ) ) {
		return $cabins_data;
	}

	// Get cabin category post ids by departure.
	$cabin_category_post_ids = get_cabin_category_post_ids_by_departure( $departure_post_id );

	// Validate cabin category post IDs.
	if ( empty( $cabin_category_post_ids ) ) {
		return $cabins_data;
	}

	// Get departure post.
	$departure_post = get_departure( $departure_post_id );

	// Check for post.
	if ( empty( $departure_post['post'] ) || ! $departure_post['post'] instanceof WP_Post ) {
		return $cabins_data;
	}

	// Get departure softrip_id meta.
	if ( ! array_key_exists( 'softrip_id', $departure_post['post_meta'] ) ) {
		return $cabins_data;
	}

	// Initialize departure softrip_id.
	$departure_softrip_id = strval( $departure_post['post_meta']['softrip_id'] );

	// Check for departure softrip_id.
	if ( empty( $departure_softrip_id ) ) {
		return $cabins_data;
	}

	// Loop through each cabin category.
	foreach ( $cabin_category_post_ids as $cabin_category_post_id ) {
		// Get cabin category post.
		$cabin_category_post = get_cabin_category( $cabin_category_post_id );

		// Check for post.
		if ( empty( $cabin_category_post['post'] ) || ! $cabin_category_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Check if cabin category has cabin_category_id meta.
		if ( ! array_key_exists( 'cabin_category_id', $cabin_category_post['post_meta'] ) ) {
			continue;
		}

		// Initialize cabin category code.
		$cabin_category_code = strval( $cabin_category_post['post_meta']['cabin_category_id'] );

		// Bail if no cabin category id.
		if ( empty( $cabin_category_code ) ) {
			continue;
		}

		// Cabin category id as per Softrip.
		$cabin_category_softrip_id = $departure_softrip_id . ':' . $cabin_category_code;

		// Initialize cabin category data.
		$cabin_category_data = [
			'id'              => $cabin_category_post_id,
			'drupalId'        => absint( $cabin_category_post['post_meta']['drupal_id'] ?? 0 ),
			'modified'        => get_post_modified_time( 'c', true, $cabin_category_post_id ),
			'softripId'       => $cabin_category_softrip_id,
			'name'            => strval( $cabin_category_post['post_meta']['cabin_name'] ?? '' ),
			'title'           => get_raw_text_from_html( $cabin_category_post['post']->post_title ),
			'code'            => $cabin_category_code,
			'description'     => get_raw_text_from_html( $cabin_category_post['post']->post_content ),
			'bedDescription'  => $cabin_category_post['post_meta']['cabin_bed_configuration'] ?? '',
			'spacesAvailable' => absint( get_post_meta( $departure_post_id, 'cabin_spaces_available_' . $cabin_category_post_id, true ) ),
			'type'            => '',
			'location'        => '',
			'size'            => '',
			'occupancySize'   => '',
			'media'           => [],
			'occupancies'     => [],
		];

		// Get cabin category type from cabin_class taxonomy.
		if ( array_key_exists( CABIN_CLASS_TAXONOMY, $cabin_category_post['post_taxonomies'] ) && is_array( $cabin_category_post['post_taxonomies'][ CABIN_CLASS_TAXONOMY ] ) && ! empty( $cabin_category_post['post_taxonomies'][ CABIN_CLASS_TAXONOMY ] ) ) {
			// Initialize cabin types.
			$cabin_types = [];

			// Iterate through cabin classes.
			foreach ( $cabin_category_post['post_taxonomies'][ CABIN_CLASS_TAXONOMY ] as $cabin_class_term ) {
				// Validate term name.
				if ( empty( $cabin_class_term['name'] ) ) {
					continue;
				}

				// Add cabin class term name.
				$cabin_types[] = $cabin_class_term['name'];
			}

			// Set cabin types separated by comma.
			$cabin_category_data['type'] = implode( ', ', $cabin_types );
		}

		// Get location from meta.
		if ( array_key_exists( 'related_decks', $cabin_category_post['post_meta'] ) && is_array( $cabin_category_post['post_meta']['related_decks'] ) ) {
			$decks = [];

			// Loop through related decks.
			foreach ( $cabin_category_post['post_meta']['related_decks'] as $deck_id ) {
				// Get deck name from meta.
				$deck_name = strval( get_post_meta( $deck_id, 'deck_name', true ) );

				// Validate deck name.
				if ( empty( $deck_name ) ) {
					continue;
				}

				// Add deck name.
				$decks[] = $deck_name;
			}

			// Set decks separated by comma.
			$cabin_category_data['location'] = implode( ', ', $decks );
		}

		// Get cabin size range from meta.
		if ( array_key_exists( 'cabin_category_size_range_from', $cabin_category_post['post_meta'] ) && array_key_exists( 'cabin_category_size_range_to', $cabin_category_post['post_meta'] ) ) {
			$from_range = strval( $cabin_category_post['post_meta']['cabin_category_size_range_from'] );
			$to_range   = strval( $cabin_category_post['post_meta']['cabin_category_size_range_to'] );

			// Validate range.
			if ( ! empty( $from_range ) && ! empty( $to_range ) ) {
				$cabin_category_data['size'] = $from_range . ' - ' . $to_range;
			}
		}

		// Get cabin occupancy size range from meta.
		if ( array_key_exists( 'cabin_occupancy_pax_range_from', $cabin_category_post['post_meta'] ) && array_key_exists( 'cabin_occupancy_pax_range_to', $cabin_category_post['post_meta'] ) ) {
			$from_range = strval( $cabin_category_post['post_meta']['cabin_occupancy_pax_range_from'] );
			$to_range   = strval( $cabin_category_post['post_meta']['cabin_occupancy_pax_range_to'] );

			// Validate range.
			if ( ! empty( $from_range ) && ! empty( $to_range ) ) {
				$cabin_category_data['occupancySize'] = $from_range . ' - ' . $to_range;
			}
		}

		// Get cabin media from meta.
		if ( array_key_exists( 'cabin_images', $cabin_category_post['post_meta'] ) && is_array( $cabin_category_post['post_meta']['cabin_images'] ) ) {
			$media_ids = array_map( 'absint', $cabin_category_post['post_meta']['cabin_images'] );

			// Loop through media IDs.
			foreach ( $media_ids as $media_id ) {
				// Get image details.
				$image_details = get_image_details( $media_id );

				// Check for image details.
				if ( empty( $image_details ) ) {
					continue;
				}

				// Add media.
				$cabin_category_data['media'][] = $image_details;
			}
		}

		// Add occupancies data.
		$cabin_category_data['occupancies'] = get_occupancies_data( $itinerary_post_id, $departure_post_id, $cabin_category_post_id );

		// Add cabin category data.
		$cabins_data[] = $cabin_category_data;
	}

	// Return cabins data.
	return $cabins_data;
}
