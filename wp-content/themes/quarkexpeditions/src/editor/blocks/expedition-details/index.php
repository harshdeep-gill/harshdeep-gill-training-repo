<?php
/**
 * Block: Expedition Details.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExpeditionDetails;

use WP_Block;

use function Quark\Core\is_china_website;
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
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize departures URL.
	$departures_url = '';
	$target         = '_self';

	// Check for block attributes.
	if ( is_array( $attributes['departuresUrl'] ) && isset( $attributes['departuresUrl']['url'] ) ) {
		$departures_url = $attributes['departuresUrl']['url'];
		$target         = ! empty( $attributes['departuresUrl']['newWindow'] ) ? '_blank' : '';
	}

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
	if ( is_array( $expedition_details_card_data['from_price'] ) && ! empty( $expedition_details_card_data['from_price']['discounted'] ) ) {
		$expedition_details_card_data['from_price'] = $expedition_details_card_data['from_price']['discounted'];
	}

	// Parse data.
	$expedition_details_card_data = wp_parse_args(
		$expedition_details_card_data,
		[
			'title'            => '',
			'sub_title'        => '',
			'region'           => '',
			'duration'         => '',
			'from_price'       => '',
			'starting_from'    => [],
			'ships'            => [],
			'tags'             => [],
			'total_departures' => 0,
			'from_date'        => '',
			'to_date'          => '',
			'departures_url'   => $departures_url,
			'target'           => $target,
		]
	);

	// Unset from price if china site.
	if ( is_china_website() ) {
		unset( $expedition_details_card_data['from_price'] );
		unset( $expedition_details_card_data['total_departures'] );
	}

	// Return built component - Expedition Details.
	return quark_get_component( COMPONENT, $expedition_details_card_data );
}
