<?php
/**
 * Blocks.
 *
 * @package quark-migration
 *
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */

namespace Quark\Migration\WordPress;

use DOMDocument;
use DOMElement;

/**
 * Sanitize attributes
 *
 * @param mixed $value Value to sanitize.
 *
 * @return mixed Sanitized value.
 */
function qrk_sanitize_attribute( mixed $value = null ): mixed {
	// Sanitize values.
	if ( is_string( $value ) ) {
		$value = str_replace(
			[
				' ',
				"\n",
				"\t",
				"\r",
				"\0",
			],
			' ',
			$value
		);
		$value = trim( html_entity_decode( $value ), " \t\n\r\0\x0B\xC2\xA0" );
	} elseif ( is_numeric( $value ) ) {
		// Sanitize number.
		$value = intval( $value ); // phpcs:ignore Travelopia.PHP.PreferAbsintOverIntval.UseAbsInt
	} elseif ( is_array( $value ) ) {
		// Loop through each value.
		foreach ( $value as $key => $item_value ) {
			$value[ $key ] = qrk_sanitize_attribute( $item_value );
		}
	}

	// Return sanitized value.
	return $value;
}

/**
 * Convert a string to blocks.
 *
 * @param string $input Input string.
 *
 * @return string
 */
function convert_to_blocks( string $input = '' ): string {
	// Check if we have content.
	if ( empty( $input ) ) {
		return '';
	}

	// Prepare for conversion.
	$input    = wpautop( $input ); // phpcs:ignore Travelopia.PHP.PreferTheContentOverWPAutoP.UseTheContent
	$document = new DOMDocument();
	$output   = [];

	// Get child nodes.
	libxml_use_internal_errors( true );
	$document->loadHTML( '<?xml encoding="utf-8"?>' . $input );
	$nodes = $document->getElementsByTagName( '*' );

	// Traverse child nodes.
	foreach ( $nodes as $node ) {
		// Ignore if we didn't get a proper node.
		if (
			! $node instanceof DOMElement
			|| in_array( $node->tagName, [ 'html', 'body' ], true )
			|| ( ! $node->parentNode instanceof DOMElement )
			|| 'body' !== $node->parentNode->tagName
		) {
			continue;
		}

		// Convert node to block.
		$block = convert_node_to_block( $node );

		// Add block to output.
		if ( ! empty( $block ) ) {
			$output[] = $block;
		}
	}

	// Prepare output.
	if ( ! empty( $output ) ) {
		$output = implode( "\n\n", $output );
	} else {
		$output = '';
	}

	// All done!
	return $output;
}

/**
 * Convert a node into a block.
 *
 * @param DOMElement|null $node Node object.
 *
 * @return string
 */
function convert_node_to_block( ?DOMElement $node = null ): string {
	// Bail out if we didn't get a proper node.
	if ( ! $node instanceof DOMElement ) {
		return '';
	}

	// Convert node to block.
	$block = apply_filters( 'qrk_convert_to_blocks_' . $node->tagName, '', $node );

	// Fallback to generic block.
	if ( empty( $block ) ) {
		$block = apply_filters( 'qr_convert_to_blocks_fallback', '', $node );
	}

	// Return block.
	return strval( $block );
}
