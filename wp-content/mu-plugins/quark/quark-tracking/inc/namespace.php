<?php
/**
 * Namespace functions.
 *
 * @package quark-tracking
 */

namespace Quark\Tracking;

use DOMDocument;
use DOMException;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Get front-end markup for manipulation.
	add_filter( 'template_redirect', __NAMESPACE__ . '\\start_output_buffering' );
	add_filter( 'wp_print_footer_scripts', __NAMESPACE__ . '\\end_output_buffering' );

	// Add Infinity Tracking class to phone numbers.
	add_filter( 'quark_front_end_markup', __NAMESPACE__ . '\\add_infinity_tracking_class' );
}

/**
 * Start output buffering.
 *
 * @return void
 */
function start_output_buffering(): void {
	// Start output buffering.
	ob_start();
}

/**
 * End output buffering.
 *
 * @return void
 */
function end_output_buffering(): void {
	// Get the buffered content.
	$content = ob_get_clean();

	// Apply filters before rendered.
	$content = strval( apply_filters( 'quark_front_end_markup', $content ) );

	// Render the markup.
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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

	// Load the HTML into a DOMDocument.
	$dom = new DOMDocument( '1.0', 'utf-8' );

	// Suppress errors due to malformed HTML.
	libxml_use_internal_errors( true );
	$dom->loadHTML( $markup );
	libxml_clear_errors();

	// Get all <a> tags.
	$anchor_tags = $dom->getElementsByTagName( 'a' );

	// Add a class to each <a> tag.
	foreach ( $anchor_tags as $anchor_tag ) {
		$existing_class = $anchor_tag->getAttribute( 'class' );

		// Check if the href attribute contains 'tel:'.
		if ( str_starts_with( $anchor_tag->getAttribute( 'href' ), 'tel:' ) ) {
			// Add the new class if it doesn't exist.
			if ( empty( $existing_class ) ) {
				$anchor_tag->setAttribute( 'class', 'InfinityNumber' );
			} else {
				$anchor_tag->setAttribute( 'class', sprintf( '%s InfinityNumber', $existing_class ) );
			}

			// Create dynamic-phone-number tag.
			try {
				$dynamic_phone_number = $dom->createElement( 'dynamic-phone-number' );
				$anchor_tag->parentNode->insertBefore( $dynamic_phone_number, $anchor_tag );
				$dynamic_phone_number->appendChild( $anchor_tag );
			} catch ( DOMException $exception ) {
				continue;
			}

			// Add child node to dynamic-phone-number tag.
			// $dynamic_phone_number->appendChild( $anchor_tag->cloneNode( true ) );
			// Move the link into the new div
			// $anchor_tag->parentNode->insertBefore($dynamic_phone_number, $anchor_tag);
			// $dynamic_phone_number->appendChild($anchor_tag);
			// Replace anchor tag with dynamic-phone-number tag.
			// $anchor_tag->parentNode->replaceChild( $dynamic_phone_number, $anchor_tag );
		}
	}

	// Updated markup.
	$updated_markup = $dom->saveHTML( $dom );

	// Return markup if updated markup is empty.
	if ( empty( $updated_markup ) ) {
		return $markup;
	}

	// Return updated HTML.
	return $updated_markup;
}
