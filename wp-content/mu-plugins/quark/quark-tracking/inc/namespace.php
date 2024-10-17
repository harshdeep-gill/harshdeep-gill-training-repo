<?php
/**
 * Namespace functions.
 *
 * @package quark-tracking
 */

namespace Quark\Tracking;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Add Infinity Tracking class to phone numbers.
	add_filter( 'quark_front_end_markup', __NAMESPACE__ . '\\add_infinity_tracking_class' );
}

/**
 * Add Infinity Tracking class to phone numbers.
 *
 * @param string $markup Current markup.
 *
 * @return string Updated markup.
 */
function add_infinity_tracking_class( string $markup = '' ): string {
	// Return early if the markup is empty.
	if ( empty( $markup ) ) {
		return $markup;
	}

	// Callback function to add Infinity Tracking class to phone numbers.
	$callback = function ( $matches ) {
		$anchor_tag = $matches[0];

		// Add Infinity Tracking class to phone numbers.
		if ( str_starts_with( $matches['href'], 'tel:' ) ) {
			if ( ! str_contains( $anchor_tag, 'class=' ) ) {
				// If class attribute is not present.
				$anchor_tag = str_replace( '<a ', '<a class="InfinityNumber" ', $anchor_tag );
			} else {
				// If class attribute is present.
				$anchor_tag = str_replace( 'class="', 'class="InfinityNumber ', $anchor_tag );
			}

			// Return the updated markup.
			return quark_get_component(
				'dynamic-phone-number',
				[
					'slot' => $anchor_tag,
				]
			);
		}

		// Return the original markup if the href attribute is not a phone number.
		return $anchor_tag;
	};

	// Add Infinity Tracking class to phone numbers.
	$updated_markup = preg_replace_callback(
		'/<a([^>]*)href=["\'](?<href>[^"\']+)["\']([^>]*)>(.*?)<\/a>/mis',
		$callback,
		$markup
	);

	// Return early if the updated markup is empty.
	if ( empty( $updated_markup ) ) {
		return $markup;
	}

	// Return updated HTML.
	return $updated_markup;
}
