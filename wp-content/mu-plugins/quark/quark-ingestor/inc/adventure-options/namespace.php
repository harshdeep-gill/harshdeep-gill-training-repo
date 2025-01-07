<?php
/**
 * Namespace for the Adventure Options functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\AdventureOptions;

use WP_Post;

use function Quark\AdventureOptions\get as get_adventure_option_post;
use function Quark\Core\get_raw_text_from_html;
use function Quark\Expeditions\get as get_expedition;
use function Quark\Ingestor\get_image_details;
use function Quark\Localization\get_currencies;
use function Quark\Softrip\AdventureOptions\get_adventure_option_by_departure_post_id;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Departures\FLIGHT_SEEING_TID;
use const Quark\Departures\ULTRAMARINE_SHIP_CODE;
use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\USD_CURRENCY;

/**
 * Get included adventure options.
 *
 * @param int $expedition_post_id Expedition post ID.
 * @param int $departure_post_id  Departure post ID.
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
function get_included_adventure_options_data( int $expedition_post_id = 0, int $departure_post_id = 0 ): array {
	// Initialize included options data.
	$included_options_data = [];

	// Early return if no expedition, itinerary or departure post ID.
	if ( empty( $expedition_post_id ) || empty( $departure_post_id ) ) {
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
		$included_options_data[ $adventure_option_category_id ] = [
			'id'        => $adventure_option_category_id,
			'name'      => get_raw_text_from_html( $adventure_option_category['name'] ),
			'icon'      => $adventure_option_category_data['icon'],
			'optionIds' => implode( ', ', $adventure_option_category_data['optionIds'] ),
		];
	}

	// Get ship ID from departure.
	$ship_code = get_post_meta( $departure_post_id, 'ship_code', true );

	// Remove Flight seeing for all except Ultramarine.
	if ( ULTRAMARINE_SHIP_CODE !== $ship_code && array_key_exists( FLIGHT_SEEING_TID, $included_options_data ) ) {
		unset( $included_options_data[ FLIGHT_SEEING_TID ] );
	}

	// Return included options data.
	return array_values( $included_options_data );
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
				// Get image details.
				$image_details = get_image_details( $attachment_id );

				// Check if empty.
				if ( empty( $image_details ) ) {
					continue;
				}

				// Add image.
				$adventure_option_category_data['images'][] = $image_details;
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

		// Currencies.
		$currencies = get_currencies();

		// Set price per person for each currency.
		foreach ( $currencies as $currency ) {
			$paid_adventure_option_data['price'][ $currency ]['pricePerPerson'] = $adventure_option[ 'price_per_person_' . strtolower( $currency ) ];
		}

		// Add paid adventure option data.
		$paid_adventure_options_data[] = $paid_adventure_option_data;
	}

	// Return paid adventure options data.
	return $paid_adventure_options_data;
}
