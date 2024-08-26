<?php
/**
 * Block: Expedition Details.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExpeditionDetails;

use function Quark\Expeditions\get_details_data;

const COMPONENT = 'parts.expedition-details';

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
 * @return string
 */
function render(): string {
	// Current post ID.
	$current_post_id = get_the_ID();

	// Check if post id available.
	if ( empty( $current_post_id ) ) {
		return '';
	}

	// Get expedition details card data.
	$expedition_details_card_data = get_details_data( $current_post_id );

	// Check if data is available.
	if ( empty( $expedition_details_card_data ) ) {
		return '';
	}

	// Set the from price to the discounted price.
	$expedition_details_card_data['from_price'] = $expedition_details_card_data['from_price']['discounted'];

	// Parse data.
	$expedition_details_card_data = wp_parse_args(
		$expedition_details_card_data,
		[
			'title'            => '',
			'region'           => '',
			'duration'         => '',
			'from_price'       => '',
			'starting_from'    => [],
			'ships'            => [],
			'tags'             => [],
			'total_departures' => 0,
			'from_date'        => '',
			'to_date'          => '',
		]
	);

	// Return built component - Expedition Details.
	return quark_get_component( COMPONENT, $expedition_details_card_data );
}
