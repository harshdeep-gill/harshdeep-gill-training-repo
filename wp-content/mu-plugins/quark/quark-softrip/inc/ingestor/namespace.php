<?php
/**
 * Namespace for the Softrip Ingestor.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Ingestor;

use WP_Post;
use WP_Query;

use function Quark\Expeditions\get as get_expedition;
use function Quark\Itineraries\get as get_itinerary;

use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;

/**
 * Prepare data to be sent to ingestor.
 *
 * @return mixed[]
 */
function prepare_data(): array {
    // Prepare args.
    $args = [
        'post_type' => EXPEDITION_POST_TYPE,
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'no_found_rows' => true,
    ];

    // Get all expedition IDs.
    $expeditions = new WP_Query( $args );
    $expedition_post_ids = $expeditions->posts;
    $expedition_post_ids = array_map( 'absint', $expedition_post_ids );

    // Initialize results.
    $results = [];

    // Get data for each expedition.
    foreach ( $expedition_post_ids as $expedition_post_id ) {
        $results[] = get_expedition_data( $expedition_post_id );
        break;
    }
    
    return $results;
}

/**
 * Get expedition data.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return mixed[]
 */
function get_expedition_data( int $expedition_post_id ): array {
    // Initialize expedition data.
    $expedition_data = [
        'id' => $expedition_post_id,
        'name' => get_the_title( $expedition_post_id ),
        'description' => '', // @todo Get description after parsing post content.
        'images' => [], // @todo Get images after parsing post content for hero-slider block.
        'destinations' => [],
        'itineraries'  => [],
    ];

    // Get destination terms.
    $expedition_data['destinations'] = get_destination_terms( $expedition_post_id );

    // Get itineraries.
    $expedition_data['itineraries'] = get_itineraries( $expedition_post_id );

    return $expedition_data;
}

/**
 * Get destination terms.
 *
 * @param int $expedition_post_id Expedition ID.
 *
 * @return array{}|array<int,
 *  array{
 *   id: int,
 *   name: string,
 *   region: array{
 *     name: string,
 *     code: string,
 *   }
 *  }
 * >
 */
function get_destination_terms( int $expedition_post_id ): array {
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
        ! is_array( $expedition_post['post_taxonomies'][DESTINATION_TAXONOMY] )
    ) {
        return $regions;
    }

    // Loop through each destination term.
    foreach (  $expedition_post['post_taxonomies'][DESTINATION_TAXONOMY] as $destination_term ) {
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
            'id' => $destination_term['term_id'],
            'name' => $destination_term['name'],
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
 *     startLocation: string,
 *     endLocation: string,
 *     departures: mixed[],
 *   }
 * >
 */
function get_itineraries( int $expedition_post_id = 0 ): array {
    // Initialize itineraries.
    $itineraries_data = [];

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

    // Filter itineraries.
    $itinerary_post_ids = array_filter( $itinerary_post_ids, function( $itinerary_id ) {
        return get_post_status( $itinerary_id ) === 'publish';
    } );

    // Loop through each itinerary.
    foreach ( $itinerary_post_ids as $itinerary_post_id ) {
        // Get itinerary post.
        $itinerary_post = get_itinerary( $itinerary_post_id );

        // Check for post.
        if ( empty( $itinerary_post['post'] ) || ! $itinerary_post['post'] instanceof WP_Post ) {
            continue;
        }

        // Validate softrip_package_code
        if ( ! array_key_exists( 'softrip_package_code', $itinerary_post['post_meta'] ) ) {
            continue;
        }

        // Initialize softrip_package_code.
        $softrip_package_code = strval( $itinerary_post['post_meta']['softrip_package_code'] );

        if ( empty( $softrip_package_code ) ) {
            continue;
        }

        $itinerary_data = [
            'id' => $itinerary_post_id,
            'packageId' => $softrip_package_code,
            'name' => $itinerary_post['post']->post_title,
            'startLocation' => '',
            'endLocation' => '',
            'ship' => [],
            'departures' => [],
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
 * @return mixed[]
 */
function get_departures( int $expedition_post_id = 0, int $itinerary_post_id = 0 ): array {
    $departures_data = [];

    // Return departures data.
    return $departures_data;
}
