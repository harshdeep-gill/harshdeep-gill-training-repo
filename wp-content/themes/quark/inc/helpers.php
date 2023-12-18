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
