<?php
/**
 * Namespace for the Softrip Ingestor.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Ingestor;

use WP_Post;
use WP_Query;

use function Quark\AdventureOptions\get as get_adventure_option_post;
use function Quark\CabinCategories\get as get_cabin_category;
use function Quark\Core\get_raw_text_from_html;
use function Quark\Departures\get as get_departure;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Itineraries\get as get_itinerary;
use function Quark\Itineraries\get_mandatory_transfer_price;
use function Quark\Itineraries\get_supplemental_price;
use function Quark\Ships\get as get_ship;
use function Quark\Softrip\AdventureOptions\get_adventure_option_by_departure_post_id;
use function Quark\Softrip\Departures\get_related_ship;
use function Quark\Softrip\Occupancies\get_cabin_category_post_ids_by_departure;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\get_occupancies_by_cabin_category_and_departure;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy;
use function Quark\Softrip\Promotions\get_promotions_by_id;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\Core\AUD_CURRENCY;
use const Quark\Core\CAD_CURRENCY;
use const Quark\Core\CURRENCIES;
use const Quark\Core\EUR_CURRENCY;
use const Quark\Core\GBP_CURRENCY;
use const Quark\Core\USD_CURRENCY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;

/**
 * Get all data to be sent to ingestor.
 *
 * @return array{}|array<int,
 *   array{
 *       id: int,
 *       name: string,
 *       overview: string,
 *       images: array{}|array<int,
 *         array{
 *           id: int,
 *           fullSizeUrl: string,
 *           thumbnailUrl: string,
 *           alt: string,
 *         }
 *       >,
 *       destinations: array{}|array<int,
 *          array{
 *             id: int,
 *             name: string,
 *             region: array{
 *             name: string,
 *             code: string,
 *            }
 *          }
 *       >,
 *       itineraries: array{}|array<int,
 *         array{
 *           id: int,
 *           packageId: string,
 *           name: string,
 *           startLocation: string,
 *           endLocation: string,
 *           departures: mixed[],
 *         }
 *       >
 *   }
 * >
 */
function get_all_data(): array {
	// Prepare args.
	$args = [
		'post_type'              => EXPEDITION_POST_TYPE,
		'posts_per_page'         => -1,
		'post_status'            => [ 'publish', 'draft' ],
		'fields'                 => 'ids',
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'no_found_rows'          => true,
	];

	// Get all expedition IDs.
	$expeditions         = new WP_Query( $args );
	$expedition_post_ids = $expeditions->posts;
	$expedition_post_ids = array_map( 'absint', $expedition_post_ids );

	// Initialize results.
	$results = [];

	// Get data for each expedition.
	foreach ( $expedition_post_ids as $expedition_post_id ) {
		// Get expedition data.
		$expedition_data = get_expedition_data( $expedition_post_id );

		// Check for expedition data.
		if ( empty( $expedition_data ) ) {
			continue;
		}

		// Add expedition data to results.
		$results[] = $expedition_data;
	}

	// Return results.
	return $results;
}

/**
 * Get expedition data.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return array{}|array{
 *     id: int,
 *     name: string,
 *     published: bool,
 *     overview: string,
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
		'id'           => $expedition_post_id,
		'name'         => get_raw_text_from_html( $expedition_post['post']->post_title ),
		'published'    => 'publish' === $expedition_post['post']->post_status,
		'overview'  => '',
		'images'       => [],
		'destinations' => [],
		'itineraries'  => [],
	];

	// Get images.
	if ( ! empty( $expedition_post['data'] ) && ! empty( $expedition_post['data']['hero_card_slider_image_ids'] ) && is_array( $expedition_post['data']['hero_card_slider_image_ids'] ) ) {
		$image_ids = array_map( 'absint', $expedition_post['data']['hero_card_slider_image_ids'] );

		// Loop through image IDs.
		foreach ( $image_ids as $image_id ) {
			// Full size url.
			$full_size_url = wp_get_attachment_image_url( $image_id, 'full' );

			// Validate full size url.
			if ( empty( $full_size_url ) ) {
				continue;
			}

			// Thumbnail url.
			$thumbnail_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );

			// Validate thumbnail url.
			if ( empty( $thumbnail_url ) ) {
				continue;
			}

			// Alt text.
			$alt_text = strval( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );

			// Get title if alt text is empty.
			if ( empty( $alt_text ) ) {
				$alt_text = get_post_field( 'post_title', $image_id );
			}

			// Add image.
			$expedition_data['images'][] = [
				'id'           => $image_id,
				'fullSizeUrl'  => $full_size_url,
				'thumbnailUrl' => $thumbnail_url,
				'alt'          => $alt_text,
			];
		}
	}

	// Add description.
	if ( ! empty( $expedition_post['post_meta'] ) && ! empty( $expedition_post['post_meta']['overview'] ) && is_string( $expedition_post['post_meta']['overview'] ) ) {
		$expedition_data['overview'] = get_raw_text_from_html( $expedition_post['post_meta']['overview'] );
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
		$parent_term_id = absint( $destination_term['parent'] );

		// Softrip ID for parent.
		$softrip_id = get_term_meta( $parent_term_id, 'softrip_id', true );

		// Check for Softrip ID.
		if ( empty( $softrip_id ) ) {
			continue;
		}

		// Get parent term.
		$parent_term = get_term( $parent_term_id, DESTINATION_TAXONOMY, ARRAY_A );

		// Check for parent term.
		if ( empty( $parent_term ) || ! is_array( $parent_term ) ) {
			continue;
		}

		// Add region.
		$regions[] = [
			'id'     => $destination_term['term_id'],
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
 * Get itineraries for the expedition.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return array{}|array<int,
 *   array{
 *     id: int,
 *     packageId: string,
 *     name: string,
 *     published: bool,
 *     startLocation: string,
 *     endLocation: string,
 *     departures: mixed[],
 *   }
 * >
 */
function get_itineraries( int $expedition_post_id = 0 ): array {
	// Initialize itineraries.
	$itineraries_data = [];

	// Early return if no expedition post ID.
	if ( empty( $expedition_post_id ) ) {
		return $itineraries_data;
	}

	// Get expedition post.
	$expedition_post = get_expedition( $expedition_post_id );

	// Check for post.
	if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
		return $itineraries_data;
	}

	// Check for itineraries.
	if ( ! array_key_exists( 'related_itineraries', $expedition_post['post_meta'] ) ||
	! is_array( $expedition_post['post_meta']['related_itineraries'] )
	) {
		return $itineraries_data;
	}

	// Validate itineraries.
	$itinerary_post_ids = array_map( 'absint', $expedition_post['post_meta']['related_itineraries'] );

	// Loop through each itinerary.
	foreach ( $itinerary_post_ids as $itinerary_post_id ) {
		// Get itinerary post.
		$itinerary_post = get_itinerary( $itinerary_post_id );

		// Check for post.
		if ( empty( $itinerary_post['post'] ) || ! $itinerary_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Check for published or draft.
		if ( ! in_array( $itinerary_post['post']->post_status, [ 'publish', 'draft' ], true ) ) {
			continue;
		}

		// Validate softrip_package_code.
		if ( ! array_key_exists( 'softrip_package_code', $itinerary_post['post_meta'] ) ) {
			continue;
		}

		// Initialize softrip_package_code.
		$softrip_package_code = strval( $itinerary_post['post_meta']['softrip_package_code'] );

		// Bail if no softrip_package_code.
		if ( empty( $softrip_package_code ) ) {
			continue;
		}

		// Initialize itinerary data.
		$itinerary_data = [
			'id'            => $itinerary_post_id,
			'packageId'     => $softrip_package_code,
			'name'          => get_raw_text_from_html( $itinerary_post['post']->post_title ),
			'published'     => 'publish' === $itinerary_post['post']->post_status,
			'startLocation' => '',
			'endLocation'   => '',
			'departures'    => [],
		];

		// Get start location from meta.
		$start_location_id = absint( get_post_meta( $itinerary_post_id, 'start_location', true ) );

		// Get term name.
		$start_location_term = get_term( $start_location_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );

		// Check for term.
		if ( ! empty( $start_location_term ) && is_array( $start_location_term ) ) {
			$itinerary_data['startLocation'] = $start_location_term['name'];
		}

		// Get end location from meta.
		$end_location_id = absint( get_post_meta( $itinerary_post_id, 'end_location', true ) );

		// Get term name.
		$end_location_term = get_term( $end_location_id, DEPARTURE_LOCATION_TAXONOMY, ARRAY_A );

		// Check for term.
		if ( ! empty( $end_location_term ) && is_array( $end_location_term ) ) {
			$itinerary_data['endLocation'] = $end_location_term['name'];
		}

		// Add departure data.
		$itinerary_data['departures'] = get_departures_data( $expedition_post_id, $itinerary_post_id );

		// Add itinerary data to itineraries.
		$itineraries_data[] = $itinerary_data;
	}

	// Itineraries data.
	return $itineraries_data;
}

/**
 * Get departures for an itinerary.
 *
 * @param int $expedition_post_id Expedition post ID.
 * @param int $itinerary_post_id  Itinerary post ID.
 *
 * @return array{}|array<int,
 *   array{
 *    id: string,
 *    name: string,
 *    published: bool,
 *    startDate: string,
 *    endDate: string,
 *    durationInDays: int,
 *    ship: array{}|array{
 *      id: int,
 *      code: string,
 *      name: string,
 *    },
 *    languages: string,
 *    cabins: mixed[],
 *    adventureOptions: array{
 *       includedOptions: mixed[],
 *       paidOptions: mixed[],
 *    }
 *  }
 * >
 */
function get_departures_data( int $expedition_post_id = 0, int $itinerary_post_id = 0 ): array {
	// Initialize departures data.
	$departures_data = [];

	// Early return if no expedition or itinerary post ID.
	if ( empty( $itinerary_post_id ) || empty( $expedition_post_id ) ) {
		return $departures_data;
	}

	// Get departure post IDs by itinerary.
	$departure_post_ids = get_children(
		[
			'post_parent'            => $itinerary_post_id,
			'post_type'              => DEPARTURE_POST_TYPE,
			'post_status'            => [ 'publish', 'draft' ],
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'ID',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		],
		ARRAY_N
	);

	// Validate departure post IDs.
	$departure_post_ids = array_map( 'absint', $departure_post_ids );

	// Loop through each departure.
	foreach ( $departure_post_ids as $departure_post_id ) {
		$departure_post = get_departure( $departure_post_id );

		// Check for post.
		if ( empty( $departure_post['post'] ) || ! $departure_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Get softrip_id meta.
		if ( ! array_key_exists( 'softrip_id', $departure_post['post_meta'] ) ) {
			continue;
		}

		// Initialize softrip_id.
		$softrip_id = strval( $departure_post['post_meta']['softrip_id'] );

		// Initialize departure data.
		$departure_data = [
			'id'               => $softrip_id,
			'name'             => get_raw_text_from_html( $departure_post['post']->post_title ),
			'published'        => 'publish' === $departure_post['post']->post_status,
			'startDate'        => $departure_post['post_meta']['start_date'] ?? '',
			'endDate'          => $departure_post['post_meta']['end_date'] ?? '',
			'durationInDays'   => absint( $departure_post['post_meta']['duration'] ?? '' ),
			'ship'             => [],
			'languages'        => '',
			'cabins'           => [],
			'adventureOptions' => [
				'includedOptions' => [],
				'paidOptions'     => [],
			],
		];

		// Get related ship.
		$ship_id = get_related_ship( $departure_post_id );

		// Check for ship ID.
		if ( ! empty( $ship_id ) ) {
			// Get ship post.
			$ship_post = get_ship( $ship_id );

			// Get code.
			$ship_code = strval( get_post_meta( $ship_id, 'ship_code', true ) );

			// Check for ship code.
			if ( ! empty( $ship_post['post'] ) && $ship_post['post'] instanceof WP_Post && ! empty( $ship_code ) ) {
				$departure_data['ship'] = [
					'id'   => $ship_id,
					'code' => $ship_code,
					'name' => get_raw_text_from_html( $ship_post['post']->post_title ),
				];
			}
		}

		// Get languages.
		if ( array_key_exists( SPOKEN_LANGUAGE_TAXONOMY, $departure_post['post_taxonomies'] ) && is_array( $departure_post['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] ) && ! empty( $departure_post['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] ) ) {
			// Initialize languages.
			$departure_languages = [];

			// Iterate through languages.
			foreach ( $departure_post['post_taxonomies'][ SPOKEN_LANGUAGE_TAXONOMY ] as $language_term ) {
				// Get language term ID.
				$language_term_id = absint( $language_term['term_id'] );

				// Get language code from meta.
				$language_code = strval( get_term_meta( $language_term_id, 'language_code', true ) );

				// Check for language code.
				if ( ! empty( $language_code ) ) {
					$departure_languages[] = $language_code;
				}
			}

			// Set languages.
			$departure_data['languages'] = implode( ', ', $departure_languages );
		}

		// Add cabins data.
		$departure_data['cabins'] = get_cabins_data( $expedition_post_id, $itinerary_post_id, $departure_post_id );

		// Add included adventure options data.
		$departure_data['adventureOptions']['includedOptions'] = get_included_adventure_options_data( $expedition_post_id );
		$departure_data['adventureOptions']['paidOptions']     = get_paid_adventure_options_data( $departure_post_id );

		// Add departure data.
		$departures_data[] = $departure_data;
	}

	// Return departures data.
	return $departures_data;
}

/**
 * Get cabins data for a departure.
 *
 * @param int $expedition_post_id Expedition post ID.
 * @param int $itinerary_post_id  Itinerary post ID.
 * @param int $departure_post_id Departure post ID.
 *
 * @return array{}|array<int,
 *   array{
 *      id: string,
 *      name: string,
 *      code: string,
 *      description: string,
 *      bedDescription: string,
 *      location: string,
 *      type: string,
 *      size: string,
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
		$cabin_category_id = $departure_softrip_id . ':' . $cabin_category_code;

		// Initialize cabin category data.
		$cabin_category_data = [
			'id'             => $cabin_category_id,
			'name'           => get_raw_text_from_html( $cabin_category_post['post']->post_title ),
			'code'           => $cabin_category_code,
			'description'    => get_raw_text_from_html( $cabin_category_post['post']->post_content ),
			'bedDescription' => $cabin_category_post['post_meta']['cabin_bed_configuration'] ?? '',
			'type'           => '',
			'location'       => '',
			'size'           => '',
			'media'          => [],
			'occupancies'    => [],
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

		// Get cabin media from meta.
		if ( array_key_exists( 'cabin_images', $cabin_category_post['post_meta'] ) && is_array( $cabin_category_post['post_meta']['cabin_images'] ) ) {
			$media_ids = array_map( 'absint', $cabin_category_post['post_meta']['cabin_images'] );

			// Loop through media IDs.
			foreach ( $media_ids as $media_id ) {
				// Full size url.
				$full_size_url = wp_get_attachment_image_url( $media_id, 'full' );

				// Validate full size url.
				if ( empty( $full_size_url ) ) {
					continue;
				}

				// Thumbnail url.
				$thumbnail_url = wp_get_attachment_image_url( $media_id, 'thumbnail' );

				// Validate thumbnail url.
				if ( empty( $thumbnail_url ) ) {
					continue;
				}

				// Alt text.
				$alt_text = strval( get_post_meta( $media_id, '_wp_attachment_image_alt', true ) );

				// Get title if alt text is empty.
				if ( empty( $alt_text ) ) {
					$alt_text = get_post_field( 'post_title', $media_id );
				}

				// Add media.
				$cabin_category_data['media'][] = [
					'id'           => $media_id,
					'fullSizeUrl'  => $full_size_url,
					'thumbnailUrl' => $thumbnail_url,
					'alt'          => $alt_text,
				];
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

/**
 * Get occupancies data.
 *
 * @param int $itinerary_post_id Itinerary post ID.
 * @param int $departure_post_id Departure post ID.
 * @param int $cabin_category_post_id Cabin category ID.
 *
 * @return array{}|array<int,
 *   array{
 *     id: string,
 *     mask: string,
 *     description: string,
 *     availabilityStatus: string,
 *     availabilityDescription: string,
 *     spacesAvailable: int,
 *     prices: array{
 *       AUD: array{
 *         pricePerPerson: int,
 *         currencyCode: string,
 *         mandatoryTransferPricePerPerson: int,
 *         supplementalPricePerPerson: int,
 *         promotionsApplied: array{}|array<int,
 *           array{
 *             id: int,
 *             promotionCode: string,
 *             promoPricePerPerson: int,
 *           }
 *         >
 *       },
 *       USD: array{
 *         pricePerPerson: int,
 *         currencyCode: string,
 *         mandatoryTransferPricePerPerson: int,
 *         supplementalPricePerPerson: int,
 *         promotionsApplied: array{}|array<int,
 *           array{
 *             id: int,
 *             promotionCode: string,
 *             promoPricePerPerson: int,
 *           }
 *         >
 *       },
 *       EUR: array{
 *         pricePerPerson: int,
 *         currencyCode: string,
 *         mandatoryTransferPricePerPerson: int,
 *         supplementalPricePerPerson: int,
 *         promotionsApplied: array{}|array<int,
 *           array{
 *             id: int,
 *             promotionCode: string,
 *             promoPricePerPerson: int,
 *           }
 *         >
 *       },
 *       CAD: array{
 *         pricePerPerson: int,
 *         currencyCode: string,
 *         mandatoryTransferPricePerPerson: int,
 *         supplementalPricePerPerson: int,
 *         promotionsApplied: array{}|array<int,
 *           array{
 *             id: int,
 *             promotionCode: string,
 *             promoPricePerPerson: int,
 *           }
 *         >
 *       },
 *       GBP: array{
 *         pricePerPerson: int,
 *         currencyCode: string,
 *         mandatoryTransferPricePerPerson: int,
 *         supplementalPricePerPerson: int,
 *         promotionsApplied: array{}|array<int,
 *           array{
 *             id: int,
 *             promotionCode: string,
 *             promoPricePerPerson: int,
 *           }
 *         >
 *       }
 *     }
 *   }
 * >
 */
function get_occupancies_data( int $itinerary_post_id = 0, int $departure_post_id = 0, int $cabin_category_post_id = 0 ): array {
	// Initialize occupancies data.
	$occupancies_data = [];

	// Early return if no itinerary, departure or cabin category post ID.
	if ( empty( $itinerary_post_id ) || empty( $departure_post_id ) || empty( $cabin_category_post_id ) ) {
		return $occupancies_data;
	}

	// Get occupancies by departure post id and cabin category post id.
	$occupancies = get_occupancies_by_cabin_category_and_departure( $cabin_category_post_id, $departure_post_id );

	// Validate occupancies.
	if ( empty( $occupancies ) ) {
		return $occupancies_data;
	}

	// Initialize mandatory transfer price.
	$mandatory_transfer_price = [];
	$supplemental_price       = [];

	// Get mandatory transfer and supplemental price for each currency.
	foreach ( CURRENCIES as $currency ) {
		$mandatory_transfer_price[ $currency ] = get_mandatory_transfer_price( $itinerary_post_id, $currency );
		$supplemental_price[ $currency ]       = get_supplemental_price( $itinerary_post_id, $currency );
	}

	// Loop through each occupancy.
	foreach ( $occupancies as $occupancy ) {
		// Initialize occupancy data.
		$occupancy_data = [
			'id'                      => $occupancy['softrip_id'],
			'mask'                    => $occupancy['mask'],
			'description'             => get_description_and_pax_count_by_mask( $occupancy['mask'] )['description'],
			'availabilityStatus'      => $occupancy['availability_status'],
			'availabilityDescription' => $occupancy['availability_description'],
			'spacesAvailable'         => $occupancy['spaces_available'],
			'prices'                  => [
				AUD_CURRENCY => [
					'pricePerPerson'                  => 0,
					'currencyCode'                    => AUD_CURRENCY,
					'mandatoryTransferPricePerPerson' => 0,
					'supplementalPricePerPerson'      => 0,
					'promotionsApplied'               => [],
				],
				USD_CURRENCY => [
					'pricePerPerson'                  => 0,
					'currencyCode'                    => USD_CURRENCY,
					'mandatoryTransferPricePerPerson' => 0,
					'supplementalPricePerPerson'      => 0,
					'promotionsApplied'               => [],
				],
				EUR_CURRENCY => [
					'pricePerPerson'                  => 0,
					'currencyCode'                    => EUR_CURRENCY,
					'mandatoryTransferPricePerPerson' => 0,
					'supplementalPricePerPerson'      => 0,
					'promotionsApplied'               => [],
				],
				GBP_CURRENCY => [
					'pricePerPerson'                  => 0,
					'currencyCode'                    => GBP_CURRENCY,
					'mandatoryTransferPricePerPerson' => 0,
					'supplementalPricePerPerson'      => 0,
					'promotionsApplied'               => [],
				],
				CAD_CURRENCY => [
					'pricePerPerson'                  => 0,
					'currencyCode'                    => CAD_CURRENCY,
					'mandatoryTransferPricePerPerson' => 0,
					'supplementalPricePerPerson'      => 0,
					'promotionsApplied'               => [],
				],
			],
		];

		// Set price per person, mandatory transfer price per person and supplemental price per person for each currency.
		foreach ( CURRENCIES as $currency ) {
			// Set price per person.
			$occupancy_data['prices'][ $currency ]['pricePerPerson'] = $occupancy[ 'price_per_person_' . strtolower( $currency ) ];

			// Set mandatory transfer price per person.
			$occupancy_data['prices'][ $currency ]['mandatoryTransferPricePerPerson'] = $mandatory_transfer_price[ $currency ];

			// Set supplemental price per person.
			$occupancy_data['prices'][ $currency ]['supplementalPricePerPerson'] = $supplemental_price[ $currency ];
		}

		// Get occupancy promotions.
		$occupancy_promotions = get_occupancy_promotions_by_occupancy( $occupancy['id'] );

		// Loop through each promotion and add promotions applied to each price.
		foreach ( $occupancy_promotions as $occupancy_promotion ) {
			// Promotion code.
			$promotion = get_promotions_by_id( $occupancy_promotion['promotion_id'] );

			// Check for promotion code.
			if ( empty( $promotion ) ) {
				continue;
			}

			// Extract promotion code.
			$promotion_code = $promotion[0]['code'];

			// Add to each price.
			foreach ( CURRENCIES as $currency ) {
				// Price.
				$promo_price_per_person = $occupancy_promotion[ 'price_per_person_' . strtolower( $currency ) ];

				// Check for promo price per person.
				if ( empty( $promo_price_per_person ) ) {
					continue;
				}

				// Add promotion to prices.
				$occupancy_data['prices'][ $currency ]['promotionsApplied'][] = [
					'id'                  => $occupancy_promotion['promotion_id'],
					'promotionCode'       => $promotion_code,
					'promoPricePerPerson' => $promo_price_per_person,
				];
			}
		}

		// Add occupancy data.
		$occupancies_data[] = $occupancy_data;
	}

	// Return occupancies data.
	return $occupancies_data;
}

/**
 * Get included adventure options.
 *
 * @param int $expedition_post_id Expedition post ID.
 *
 * @return array{}|array<int,
 *   array{
 *      id: int,
 *      name: string,
 *      icon: string,
 *      optionIds: string,
 *   }
 * >
 */
function get_included_adventure_options_data( int $expedition_post_id = 0 ): array {
	// Initialize included options data.
	$included_options_data = [];

	// Early return if no expedition, itinerary or departure post ID.
	if ( empty( $expedition_post_id ) ) {
		return $included_options_data;
	}

	// Get expedition post.
	$expedition_post = get_expedition( $expedition_post_id );

	// Check for post.
	if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
		return $included_options_data;
	}

	// Check for included activities.
	if ( ! array_key_exists( 'included_activities', $expedition_post['post_meta'] ) || ! is_array( $expedition_post['post_meta']['included_activities'] ) ) {
		return $included_options_data;
	}

	// Get included options.
	$included_option_ids = $expedition_post['post_meta']['included_activities'];

	// Check for included options.
	$included_option_ids = array_map( 'absint', $included_option_ids );

	// Loop through each included option.
	foreach ( $included_option_ids as $adventure_option_post_id ) {
		$adventure_option_post = get_adventure_option_post( $adventure_option_post_id );

		// Check for post.
		if ( empty( $adventure_option_post['post'] ) || ! $adventure_option_post['post'] instanceof WP_Post ) {
			continue;
		}

		// Check for post taxonomies.
		if ( empty( $adventure_option_post['post_taxonomies'] ) || ! array_key_exists( ADVENTURE_OPTION_CATEGORY, $adventure_option_post['post_taxonomies'] ) || ! is_array( $adventure_option_post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] ) ) {
			continue;
		}

		// Get adventure option category.
		$adventure_option_category = $adventure_option_post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ];

		// Check for category.
		if ( empty( $adventure_option_category ) ) {
			continue;
		}

		// Get first category.
		$adventure_option_category    = $adventure_option_category[0];
		$adventure_option_category_id = absint( $adventure_option_category['term_id'] );

		// Get icon, images, option ids from adventure option category term.
		$adventure_option_category_data = get_adventure_option_category_data_from_meta( $adventure_option_category_id );

		// Add included option data.
		$included_options_data[] = [
			'id'        => $adventure_option_category_id,
			'name'      => get_raw_text_from_html( $adventure_option_category['name'] ),
			'icon'      => $adventure_option_category_data['icon'],
			'optionIds' => implode( ', ', $adventure_option_category_data['optionIds'] ),
		];
	}

	// Return included options data.
	return $included_options_data;
}

/**
 * Get icon, images, option ids from adventure option category term from meta.
 *
 * @param int $adventure_option_category_id Adventure option category term ID.
 *
 * @return array{
 *   icon: string,
 *   optionIds: string[],
 *   images: array{}|array<int,
 *     array{
 *       id: int,
 *       fullSizeUrl: string,
 *       thumbnailUrl: string,
 *       alt: string,
 *    }
 *   >
 * }
 */
function get_adventure_option_category_data_from_meta( int $adventure_option_category_id = 0 ): array {
	// Initialize adventure option category data.
	$adventure_option_category_data = [
		'icon'      => '',
		'optionIds' => [],
		'images'    => [],
	];

	// Early return if no adventure option category ID.
	if ( empty( $adventure_option_category_id ) ) {
		return $adventure_option_category_data;
	}

	// Get all term meta.
	$adventure_option_category_meta = get_term_meta( $adventure_option_category_id );

	// Check for meta.
	if ( empty( $adventure_option_category_meta ) || ! is_array( $adventure_option_category_meta ) ) {
		return $adventure_option_category_data;
	}

	// Loop through each meta key.
	foreach ( $adventure_option_category_meta as $meta_key => $meta_value ) {
		// Skip for empty meta value or non-array meta value.
		if ( ! is_array( $meta_value ) || empty( $meta_value ) ) {
			continue;
		}

		// Check for key.
		if ( preg_match( '/softrip_\d+_id/', $meta_key ) ) {
			// Get softrip option id.
			$adventure_option_category_data['optionIds'][] = strval( $meta_value[0] );
		} elseif ( 'image' === $meta_key ) {
			// Loop through each image.
			foreach ( $meta_value as $attachment_id ) {
				// Full size url.
				$full_size_url = wp_get_attachment_image_url( $attachment_id, 'full' );

				// Validate full size url.
				if ( empty( $full_size_url ) ) {
					continue;
				}

				// Thumbnail url.
				$thumbnail_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );

				// Validate thumbnail url.
				if ( empty( $thumbnail_url ) ) {
					continue;
				}

				// Alt text.
				$alt_text = strval( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

				// Get title if alt text is empty.
				if ( empty( $alt_text ) ) {
					$alt_text = get_post_field( 'post_title', $attachment_id );
				}

				// Add image.
				$adventure_option_category_data['images'][] = [
					'id'           => $attachment_id,
					'fullSizeUrl'  => $full_size_url,
					'thumbnailUrl' => $thumbnail_url,
					'alt'          => $alt_text,
				];
			}
		} elseif ( 'icon' === $meta_key ) {
			// Get icon attachment id.
			$attachment_id = absint( $meta_value[0] );

			// Get icon url.
			$icon_url = wp_get_attachment_image_url( $attachment_id, 'full' );

			// Check for icon url.
			if ( ! empty( $icon_url ) ) {
				$adventure_option_category_data['icon'] = $icon_url;
			}
		}
	}

	// Return adventure option category data.
	return $adventure_option_category_data;
}

/**
 * Get paid adventure option data.
 *
 * @param int $departure_post_id  Departure post ID.
 *
 * @return array{}|array<int,
 *    array{
 *       id: int,
 *       name: string,
 *       icon: string,
 *       optionIds: string,
 *       spacesAvailable: int,
 *       images: array{}|array<int,
 *          array{
 *            id: int,
 *            fullSizeUrl: string,
 *            thumbnailUrl: string,
 *            alt: string,
 *          }
 *       >,
 *       price: array{
 *          AUD: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *          },
 *          USD: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *          },
 *          EUR: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *          },
 *          GBP: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *          },
 *          CAD: array{
 *             pricePerPerson: int,
 *             currencyCode: string,
 *          }
 *       }
 *    }
 * >
 */
function get_paid_adventure_options_data( int $departure_post_id = 0 ): array {
	// Bail if no departure post ID.
	if ( empty( $departure_post_id ) ) {
		return [];
	}

	// Initialize paid adventure options data.
	$paid_adventure_options_data = [];

	// Get adventure option by departure post id.
	$adventure_options = get_adventure_option_by_departure_post_id( $departure_post_id );

	// Validate adventure options.
	if ( empty( $adventure_options ) ) {
		return $paid_adventure_options_data;
	}

	// Loop through each adventure option.
	foreach ( $adventure_options as $adventure_option ) {
		$adventure_option_category_term_id = absint( $adventure_option['adventure_option_term_id'] );

		// Validate term ID.
		if ( empty( $adventure_option_category_term_id ) ) {
			continue;
		}

		// Get adventure option category term.
		$adventure_option_category_term = get_term( $adventure_option_category_term_id, ADVENTURE_OPTION_CATEGORY, ARRAY_A );

		// Check for term.
		if ( empty( $adventure_option_category_term ) || ! is_array( $adventure_option_category_term ) ) {
			continue;
		}

		// Term name.
		$adventure_option_category_name = strval( $adventure_option_category_term['name'] );

		// Get icon, images, option ids from adventure option category term.
		$adventure_option_category_data = get_adventure_option_category_data_from_meta( $adventure_option_category_term_id );

		// Initialize adventure option data.
		$paid_adventure_option_data = [
			'id'              => $adventure_option_category_term_id,
			'name'            => get_raw_text_from_html( $adventure_option_category_name ),
			'icon'            => $adventure_option_category_data['icon'],
			'optionIds'       => implode( ', ', $adventure_option_category_data['optionIds'] ),
			'images'          => $adventure_option_category_data['images'],
			'spacesAvailable' => absint( $adventure_option['spaces_available'] ),
			'price'           => [
				AUD_CURRENCY => [
					'pricePerPerson' => 0,
					'currencyCode'   => AUD_CURRENCY,
				],
				USD_CURRENCY => [
					'pricePerPerson' => 0,
					'currencyCode'   => USD_CURRENCY,
				],
				EUR_CURRENCY => [
					'pricePerPerson' => 0,
					'currencyCode'   => EUR_CURRENCY,
				],
				GBP_CURRENCY => [
					'pricePerPerson' => 0,
					'currencyCode'   => GBP_CURRENCY,
				],
				CAD_CURRENCY => [
					'pricePerPerson' => 0,
					'currencyCode'   => CAD_CURRENCY,
				],
			],
		];

		// Set price per person for each currency.
		foreach ( CURRENCIES as $currency ) {
			$paid_adventure_option_data['price'][ $currency ]['pricePerPerson'] = $adventure_option[ 'price_per_person_' . strtolower( $currency ) ];
		}

		// Add paid adventure option data.
		$paid_adventure_options_data[] = $paid_adventure_option_data;
	}

	// Return paid adventure options data.
	return $paid_adventure_options_data;
}
