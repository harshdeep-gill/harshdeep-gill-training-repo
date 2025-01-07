<?php
/**
 * Namespace for the Promotion functions.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Promotions;

use WP_Post;

use function Quark\Departures\get as get_departure_post;
use function Quark\Softrip\Promotions\get_promotions_by_code;

/**
 * Get promotions for a departure.
 *
 * @param int $departure_post_id Departure post ID.
 *
 * @return array{}|array<int, array{
 *   id: int,
 *   code: string,
 *   startDate: string,
 *   endDate: string,
 *   description: string,
 *   discountType: string,
 *   discountValue: string,
 *   isPIF: bool,
 * }>
 */
function get_promotions_data( int $departure_post_id = 0 ): array {
	// Initialize promotions data.
	$promotions_data = [];

	// Early return if no departure post ID.
	if ( empty( $departure_post_id ) ) {
		return $promotions_data;
	}

	// Get departure post.
	$departure_post = get_departure_post( $departure_post_id );

	// Validate departure post.
	if ( empty( $departure_post['post'] ) || ! $departure_post['post'] instanceof WP_Post ) {
		return $promotions_data;
	}

	// Check if promotions are present.
	if ( empty( $departure_post['post_meta']['promotion_codes'] ) || ! is_array( $departure_post['post_meta']['promotion_codes'] ) ) {
		return $promotions_data;
	}

	// Loop through promotion codes.
	foreach ( $departure_post['post_meta']['promotion_codes'] as $promotion_code ) {
		// Validate promotion code.
		if ( empty( $promotion_code ) || ! is_string( $promotion_code ) ) {
			continue;
		}

		// Get promotion data by code.
		$promotion_data = get_promotions_by_code( $promotion_code );

		// Validate promotion data.
		if ( empty( $promotion_data ) || ! is_array( $promotion_data ) ) {
			continue;
		}

		// Pick first element.
		$promotion_data = reset( $promotion_data );

		// Add promotion data to promotions data.
		$promotions_data[] = [
			'id'            => $promotion_data['id'],
			'code'          => $promotion_data['code'],
			'startDate'     => $promotion_data['start_date'],
			'endDate'       => $promotion_data['end_date'],
			'description'   => $promotion_data['description'],
			'discountType'  => $promotion_data['discount_type'],
			'discountValue' => $promotion_data['discount_value'],
			'isPIF'         => boolval( $promotion_data['is_pif'] ),
		];
	}

	// Return promotions data.
	return $promotions_data;
}
