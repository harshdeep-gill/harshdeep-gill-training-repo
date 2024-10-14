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

	// Add Infinity Tracking class to phone numbers.
	$updated_markup = preg_replace_callback(
		'/<a([^>]*)href=["\'](?<href>[^"\']+)["\']([^>]*)>(.*?)<\/a>/mis',
		function ( $matches ) {
			$anchor_tag = $matches[0];

			// Add Infinity Tracking class to phone numbers.
			if ( str_starts_with( $matches['href'], 'tel:' ) ) {
				if ( str_contains( $anchor_tag, 'InfinityNumber' ) ) {
					return sprintf( '<dynamic-phone-number>%s</dynamic-phone-number>', $anchor_tag );
				}

				// Add Infinity Tracking class to phone number.
				if ( ! str_contains( $anchor_tag, 'class=' ) ) {
					return sprintf( '<dynamic-phone-number>%s</dynamic-phone-number>', str_replace( '<a ', '<a class="InfinityNumber" ', $anchor_tag ) );
				}

				// Add Infinity Tracking class to phone number.
				return sprintf( '<dynamic-phone-number>%s</dynamic-phone-number>', str_replace( 'class="', 'class="InfinityNumber ', $anchor_tag ) );
			}

			// Return the original markup if the href attribute is not a phone number.
			return $anchor_tag;
		},
		$markup
	);

	// Return early if the updated markup is empty.
	if ( empty( $updated_markup ) ) {
		return $markup;
	}

	// Return updated HTML.
	return $updated_markup;
}
