<?php
/**
 * Helper functions.
 *
 * @package quark
 */

/**
 * Get a unique ID for a form field if none exists.
 *
 * @param string $id_string Original ID string.
 *
 * @return string
 */
function quark_get_form_field_id( string $id_string = '' ): string {
	// Check if passed ID is not empty.
	if ( ! empty( $id_string ) ) {
		return $id_string;
	}

	// Declare ID count as global.
	global $quark_form_field_id_count;

	// Check if ID count is empty and set to 0 if so.
	if ( empty( $quark_form_field_id_count ) ) {
		$quark_form_field_id_count = 0;
	}

	// Increment ID count.
	++$quark_form_field_id_count;

	// Return unique ID.
	return 'field-' . time() . '-' . $quark_form_field_id_count;
}

/**
 * Get the number of children within a slot.
 *
 * @param string $slot_content Slot content.
 *
 * @return int
 */
function quark_get_slot_child_count( string $slot_content = '' ): int {
	// Check if DOMDocument is enabled.
	if ( ! class_exists( 'DOMDocument' ) || ! class_exists( 'DOMElement' ) || empty( $slot_content ) ) {
		return 0;
	}

	// Enable errors.
	libxml_use_internal_errors( true );

	// Initialize DOMDocument.
	$document = new DOMDocument();

	// Load slot HTML.
	$document->loadHTML( '<?xml encoding="utf-8"?>' . strval( $slot_content ) );
	$nodes = $document->getElementsByTagName( '*' );

	// Initialize counter.
	$count = 0;

	// Traverse child nodes.
	foreach ( $nodes as $node ) {
		// Ignore if we didn't get a proper node.
		if (
			! $node instanceof DOMElement
			|| in_array( $node->tagName, [ 'html', 'body' ], true ) // phpcs:ignore
			|| ( ! $node->parentNode instanceof DOMElement ) // phpcs:ignore
			|| 'body' !== $node->parentNode->tagName // phpcs:ignore
		) {
			continue;
		}

		// Increment counter.
		++$count;
	}

	// Return found nodes.
	return $count;
}

/**
 * Parses Wistia embed url to extract the video_id from it.
 *
 * @param string $wistia_url The wistia embed URL.
 *
 * @return string Returns the id on success and empty string on failure.
 */
function quark_get_wistia_id( string $wistia_url = '' ): string {
	// Get the clean URL.
	$clean_url = esc_url_raw( $wistia_url, [ 'https' ] );

	// Check if we got a URL back.
	if ( empty( $clean_url ) ) {
		return ''; // Empty, bail.
	}

	// Get the parsed URL.
	$parsed_url = wp_parse_url( $clean_url );

	// Check if parsing failed.
	if ( empty( $parsed_url ) || ! is_array( $parsed_url ) ) {
		return ''; // Failed, bail.
	}

	// Get the URL path and host.
	$url_path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '';
	$url_host = $parsed_url['host'];

	// Get the path.
	if ( empty( $url_path ) ) {
		return '';
	}

	// Get the path components.
	$url_path_components = array_values( array_filter( explode( '/', $url_path ) ) );

	// Check if we have a valid URL.
	if ( empty( $url_host ) ||
		'quarkexpeditions.wistia.com' !== $url_host ||
		count( $url_path_components ) !== 2 ||
		'medias' !== $url_path_components[0]
	) {
		return '';
	}

	// Return the string.
	return $url_path_components[1];
}

/**
 * Returns the class to apply background color based on the color slug.
 *
 * @param string $color_slug The slug of the color.
 *
 * @return string
 */
function quark_get_background_color_class( string $color_slug = '' ): string {
	// Valid color slugs.
	$valid_color_slugs = [
		'black',
		'white',
		'yellow',
		'dark-blue',
		'blue',
		'magenta',
		'gray-90',
		'gray-80',
		'gray-70',
		'gray-60',
		'gray-50',
		'gray-40',
		'gray-30',
		'gray-20',
		'gray-10',
		'gray-5',
		'success-100',
		'success-50',
		'success-10',
		'attention-100',
		'attention-50',
		'attention-10',
		'error-100',
		'error-50',
		'error-10',
		'information-100',
		'information-50',
		'information-10',
	];

	// Check if color was passed.
	if ( empty( $color_slug ) || ! in_array( $color_slug, $valid_color_slugs, true ) ) {
		return '';
	}

	// Return the CSS class.
	return sprintf( 'has-background--%s', $color_slug );
}

/**
 * Checks if the component with the given ID has been rendered before.
 *
 * @param  string $id The ID of the component to check.
 *
 * @return boolean true if it has been rendered, false otherwise.
 */
function quark_has_this_rendered_once( string $id = '' ): bool {
	// A record for rendered IDs.
	static $has_rendered_once = [];

	// Check if it has already rendered.
	if ( empty( $id ) || isset( $has_rendered_once[ $id ] ) ) {
		return true;
	}

	// Set the record to true.
	$has_rendered_once[ $id ] = true;

	// It has not been rendered before.
	return false;
}

/**
 * Generates a DOM id of a specified length with an input as a base.
 *
 * @param string  $input The string to encode as the id.
 * @param integer $length The length of the id can be 2 <= $length <= 32.
 *
 * @return string The hashed id.
 */
function quark_generate_dom_id( string $input = '', int $length = 10 ): string {
	// Check for empty input.
	if ( empty( $input ) ) {
		return '';
	}

	// Correct for invalid $length values.
	if ( $length < 2 || $length > 32 ) {
		// Set the default value of 10.
		$length = 10;
	}

	/**
	 * The character 'm' has been added to prevent the id starting with a digit.
	 */
	return 'm' . substr( md5( $input ), 0, $length - 1 );
}

/**
 * Generates a unique DOM id of a specified length.
 *
 * @param integer $length The length of the id can be 2 <= $length <= 32.
 *
 * @return string The hashed id.
 */
function quark_generate_unique_dom_id( int $length = 10 ): string {
	/**
	 * The character 'm' has been added to prevent the id starting with a digit.
	 */
	return 'm' . substr( md5( strval( time() ) ), 0, $length - 1 );
}
