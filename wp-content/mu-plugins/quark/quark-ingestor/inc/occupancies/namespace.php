<?php
/**
 * Namespace for the Occupancy functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Occupancies;

use function Quark\Itineraries\get_mandatory_transfer_price;
use function Quark\Itineraries\get_supplemental_price;
use function Quark\Localization\get_currencies;
use function Quark\Softrip\Occupancies\get_description_and_pax_count_by_mask;
use function Quark\Softrip\Occupancies\get_occupancies_by_cabin_category_and_departure;
use function Quark\Softrip\OccupancyPromotions\get_occupancy_promotions_by_occupancy;
use function Quark\Softrip\Promotions\get_promotions_by_id;

use const Quark\Localization\AUD_CURRENCY;
use const Quark\Localization\CAD_CURRENCY;
use const Quark\Localization\EUR_CURRENCY;
use const Quark\Localization\GBP_CURRENCY;
use const Quark\Localization\USD_CURRENCY;

/**
 * Get occupancies data.
 *
 * @param int $itinerary_post_id      Itinerary post ID.
 * @param int $departure_post_id      Departure post ID.
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

	// Available currencies.
	$available_currencies = get_currencies();

	// Get mandatory transfer and supplemental price for each currency.
	foreach ( $available_currencies as $currency ) {
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
		foreach ( $available_currencies as $currency ) {
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
			foreach ( $available_currencies as $currency ) {
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
