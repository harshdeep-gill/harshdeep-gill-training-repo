<?php
/**
 * Blocks.
 *
 * @package quark-migration
 *
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */

namespace Quark\Migration\WordPress;

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
