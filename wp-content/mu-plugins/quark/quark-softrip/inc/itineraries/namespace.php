<?php
/**
 * Namespace for the Softrip itinerary data.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Itineraries;

use const Quark\Core\CURRENCIES;

use WP_Post;

use function Quark\Softrip\Departure\get_departures_by_itinerary;
use function Quark\Softrip\Departure\get_lowest_price as get_departure_lowest_price;
use function Quark\Softrip\Departure\get_related_ship;

/**
 * Get lowest price for itinerary.
 *
 * @param int $post_id Itinerary post ID.
 * @param string $currency Currency code.
 *
 * @return array{
 *  original: float,
 *  discounted: float,
 * }
 */
function get_lowest_price( int $post_id = 0, string $currency = 'USD' ): array {
    // Uppercase the currency code.
    $currency = strtoupper( $currency );

    // Setup default return values.
    $lowest_price = [
        'original'   => 0,
        'discounted' => 0,
    ];

    // Return default values if no post ID.
    if ( empty( $post_id ) || ! in_array( $currency, CURRENCIES, true ) ) {
        return $lowest_price;
    }

    // Get all departure posts for the itinerary.
    $departure_post_ids = get_departures_by_itinerary( $post_id );

    // Loop through each departure post.
    foreach ( $departure_post_ids as $departure_post_id ) {
        // Get the lowest price for the departure.
        $departure_price = get_departure_lowest_price( $departure_post_id, $currency );

        // If the lowest price is less than the current lowest price, update it.
        if ( empty( $lowest_price['original'] ) || $departure_price['original'] < $lowest_price['original'] ) {
            $lowest_price['original'] = $departure_price['original'];
        }

        // If the discounted price is less than the current discounted price, update it.
        if ( empty( $lowest_price['discounted'] ) || $departure_price['discounted'] < $lowest_price['discounted'] ) {
            $lowest_price['discounted'] = $departure_price['discounted'];
        }
    }

    return $lowest_price;
}

/**
 * Get related ships for itinerary.
 *
 * @param int $post_id Itinerary post ID.
 *
 * @return array<int, array{post: WP_Post, permalink: string, post_meta: mixed[]}>
 */
function get_related_ships( int $post_id = 0 ): array {
    // Setup default return value.
    $ships = [];

    // Return default value if no post ID.
    if ( empty( $post_id ) ) {
        return $ships;
    }

    // Get all departure posts for the itinerary.
    $departure_post_ids = get_departures_by_itinerary( $post_id );

    // Loop through each departure post.
    foreach ( $departure_post_ids as $departure_post_id ) {
        // Get the ship post for the departure.
        $ship = get_related_ship( $departure_post_id );

        // Skip if ship is empty.
        if ( empty( $ship ) ) {
            continue;
        }

        // Add the ship to the list.
        $ships[ $ship['post']->ID ] = $ship;
    }
    return $ships;
}