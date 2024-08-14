<?php
/**
 * Block: Specifications.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Specifications;

use function Quark\Ships\get_ship_data;

const COMPONENT = 'parts.specifications';

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
 * @return string The block markup.
 */
function render(): string {
	// Initialize specifications.
	$specifications = [];

	// Get ship Id.
	$ship_id = absint( get_the_ID() );

	// Get ship data.
	$ship = get_ship_data( $ship_id );

	// Check for specifications.
	if ( empty( $ship['specifications'] ) ) {
		return '';
	}

	// Prepare the specifications and add labels.
	foreach ( $ship['specifications'] as $key => $value ) {
		$specifications[] = [
			'label' => str_replace( '_', ' ', $key ),
			'value' => $value,
		];
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'title'          => __( 'Ship Specifications', 'qrk' ),
			'specifications' => $specifications,
		]
	);
}
