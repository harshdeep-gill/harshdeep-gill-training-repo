<?php
/**
 * Block: Expedition Details.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ExpeditionDetails;

use WP_Error;
use WP_Query;

use function Quark\Expeditions\get_expedition_details_card_data;

const BLOCK_NAME = 'quark/expedition-details';
const COMPONENT  = 'expedition-details';

/**
 * Block initialization.
 *
 * @return void
 */
function bootstrap(): void {
	// Avoid registering in admin to fix a conflict with Blade views.
	if ( ! is_admin() ) {
		add_action( 'wp_loaded', __NAMESPACE__ . '\\register' );
	}
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Register block.
	register_block_type(
		BLOCK_NAME,
		[
			'attributes'      => [],
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
	$expedition_details_card_data = get_expedition_details_card_data( $current_post_id );

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

	// Initialize slot.
	$slot = '';

	// Build component - Expedition Details overline.
	$slot .= quark_get_component(
		COMPONENT . '.overline',
		[
			'region'     => $expedition_details_card_data['region'],
			'duration'   => $expedition_details_card_data['duration'],
			'from_price' => $expedition_details_card_data['from_price'],
		]
	);

	// Build component - Expedition Details Title.
	$slot .= quark_get_component(
		COMPONENT . '.title',
		[
			'title' => $expedition_details_card_data['title'],
		]
	);

	// Build component - Expedition Details regions(Tags).
	$slot .= quark_get_component(
		COMPONENT . '.regions',
		[
			'regions' => $expedition_details_card_data['tags'],
		]
	);

	// Build component - Expedition Details Starting From.
	$slot_part = quark_get_component(
		COMPONENT . '.starting-from',
		[
			'starting_from' => $expedition_details_card_data['starting_from'],
		]
	);

	// Build component - Expedition Details Ships.
	$slot_part .= quark_get_component(
		COMPONENT . '.ships',
		[
			'ships' => $expedition_details_card_data['ships'],
		]
	);

	// Build component - Expedition Details Row for Starting From and Ships.
	$slot .= quark_get_component(
		COMPONENT . '.row',
		[
			'slot' => $slot_part,
		]
	);

	// Build component - Expedition Details Departures.
	$slot .= quark_get_component(
		COMPONENT . '.row',
		[
			'slot' => quark_get_component(
				COMPONENT . '.departures',
				[
					'total_departures' => $expedition_details_card_data['total_departures'],
					'from_date'        => $expedition_details_card_data['from_date'],
					'to_date'          => $expedition_details_card_data['to_date'],
				]
			),
		]
	);

	// Build component - Expedition Details CTA.
	$slot .= quark_get_component(
		COMPONENT . '.cta',
		[
			'slot' => quark_get_component(
				'button',
				[
					'slot'   => 'View All Departures',
					'href'   => '#bookings',
					'target' => '_self',
					'size'   => 'big',
					'color'  => 'black',
				]
			),
		]
	);

	// Return built component - Expedition Details.
	return quark_get_component(
		COMPONENT,
		[
			'slot'       => $slot,
			'appearance' => 'dark',
		]
	);
}
