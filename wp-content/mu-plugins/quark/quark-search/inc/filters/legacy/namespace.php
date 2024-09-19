<?php
/**
 * Legacy query param namespace.
 *
 * @package quark-search
 */

namespace Quark\Search\Filters\Legacy;

use function Quark\Search\Filters\build_filter_options;

use const Quark\Search\Filters\ADVENTURE_OPTION_FILTER_KEY;
use const Quark\Search\Filters\DURATION_FILTER_KEY;
use const Quark\Search\Filters\EXPEDITION_FILTER_KEY;
use const Quark\Search\Filters\MONTH_FILTER_KEY;
use const Quark\Search\Filters\SEASON_FILTER_KEY;
use const Quark\Search\Filters\SHIP_FILTER_KEY;

const LEGACY_SEASON_FILTER_KEY            = 'season';
const LEGACY_EXPEDITION_FILTER_KEY        = 'expedition';
const LEGACY_ADVENTURE_OPTIONS_FILTER_KEY = 'adventure_options';
const LEGACY_MONTH_FILTER_KEY             = 'departure';
const LEGACY_DURATION_FILTER_KEY          = 'duration';
const LEGACY_SHIP_FILTER_KEY              = 'ship';

const LEGACY_QUERY_PARAMS_MAPPING = [
	LEGACY_SEASON_FILTER_KEY            => [
		'key'     => LEGACY_SEASON_FILTER_KEY,
		'new_key' => SEASON_FILTER_KEY,
		'retain'  => false,
	],
	LEGACY_EXPEDITION_FILTER_KEY        => [
		'key'     => LEGACY_EXPEDITION_FILTER_KEY,
		'new_key' => EXPEDITION_FILTER_KEY,
		'retain'  => false,
	],
	LEGACY_ADVENTURE_OPTIONS_FILTER_KEY => [
		'key'     => LEGACY_ADVENTURE_OPTIONS_FILTER_KEY,
		'new_key' => ADVENTURE_OPTION_FILTER_KEY,
		'retain'  => false,
	],
	LEGACY_MONTH_FILTER_KEY             => [
		'key'     => LEGACY_MONTH_FILTER_KEY,
		'new_key' => MONTH_FILTER_KEY,
		'parser'  => __NAMESPACE__ . '\\parse_month_filter',
		'retain'  => true,
	],
	LEGACY_DURATION_FILTER_KEY          => [
		'key'     => LEGACY_DURATION_FILTER_KEY,
		'new_key' => DURATION_FILTER_KEY,
		'retain'  => true,
	],
	LEGACY_SHIP_FILTER_KEY              => [
		'key'     => LEGACY_SHIP_FILTER_KEY,
		'new_key' => SHIP_FILTER_KEY,
		'retain'  => false,
	],
];

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Redirect legacy search query params.
	add_action( 'init', __NAMESPACE__ . '\\redirect_legacy_search_query_params' );
}

/**
 * Parser for month filter.
 *
 * @param string $month Month.
 *
 * @return string
 */
function parse_month_filter( string $month = '' ): string {
	// Check if duration is empty.
	if ( empty( $month ) ) {
		return '';
	}

	// Unix timestamp.
	$timestamp = absint( strtotime( $month ) );

	// Check if timestamp is valid.
	if ( empty( $timestamp ) ) {
		return '';
	}

	// Return month and year.
	return gmdate( 'm-Y', $timestamp );
}

/**
 * Redirect legacy search query params.
 * This updates the legacy query params to new query params without changing URL route. Then, redirects 302.
 *
 * @return void
 */
function redirect_legacy_search_query_params(): void {
	// Raw query params.
	$query_params = filter_input_array( INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS );

	// Bail if no query params.
	if ( empty( $query_params ) ) {
		return;
	}

	// Get all filter options.
	$filter_options = build_filter_options( array_column( LEGACY_QUERY_PARAMS_MAPPING, 'new_key' ) );

	// Is query params modified.
	$is_query_params_modified = false;

	// Loop over each legacy query param and map it to new query param.
	foreach ( LEGACY_QUERY_PARAMS_MAPPING as $legacy_key => $value ) {
		// Check if current legacy key is available in query params.
		if ( empty( $query_params[ $legacy_key ] ) ) {
			continue;
		}

		// New key.
		$new_key = $value['new_key'];

		// Get json data.
		$json_data = $query_params[ $legacy_key ];

		// Decode json data.
		$json_data        = html_entity_decode( $json_data );
		$selected_filters = json_decode( $json_data, true );

		// Check if json data is valid.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			continue;
		}

		// If not array, skip.
		if ( ! is_array( $selected_filters ) ) {
			continue;
		}

		// Parse if required.
		if ( isset( $value['parser'] ) ) {
			$selected_filters = array_map( $value['parser'], $selected_filters );
		}

		// Skip if selected filters are empty.
		if ( empty( $selected_filters ) ) {
			continue;
		}

		// Retain the filter if required.
		if ( true === $value['retain'] ) {
			// Unset the legacy key.
			unset( $query_params[ $legacy_key ] );
			$is_query_params_modified = true;

			// Add new key.
			$query_params[ $new_key ] = $selected_filters;
			continue;
		}

		// Check if key is available in filter options.
		if ( empty( $filter_options[ $new_key ] ) ) {
			continue;
		}

		// Create an index of the filter options by label for faster lookup.
		$filter_map = array_column( (array) $filter_options[ $new_key ], 'value', 'label' );

		// To store matched values.
		$matched_values = [];

		// Loop over each filter and find corresponding value.
		foreach ( $selected_filters as $selected_filter ) {
			foreach ( $filter_map as $label => $id ) {
				// Skip if label is not a string.
				if ( ! is_string( $label ) ) {
					continue;
				}

				// Check if the label exists in the selected filter string.
				if ( strpos( $selected_filter, $label ) !== false ) {
					$matched_values[] = $id;
					break; // Once found, move to the next selection.
				}
			}
		}

		// If we have matched values, add them to the query.
		if ( ! empty( $matched_values ) ) {
			// Unset the legacy key.
			unset( $query_params[ $legacy_key ] );
			$is_query_params_modified = true;

			// Add new key.
			$query_params[ $new_key ] = $matched_values;
		}
	}

	// Bail if query params are not modified.
	if ( ! $is_query_params_modified ) {
		return;
	}

	// Get current path.
	$current_path = $_SERVER['REQUEST_URI'];

	// Remove all query params.
	$current_path = strtok( $current_path, '?' );

	// Initialize new query string.
	$new_query = '';

	// Loop over new query params.
	foreach ( $query_params as $key => $value ) {
		// Skip if value is empty.
		if ( empty( $value ) ) {
			continue;
		}

		// Implode if array.
		if ( is_array( $value ) ) {
			$new_query .= $key . '=' . urlencode_deep( implode( ',', $value ) ) . '&';
		} else {
			$new_query .= $key . '=' . urlencode_deep( $value ) . '&';
		}
	}

	// Redirect path.
	$new_query     = rtrim( $new_query, '&' );
	$redirect_path = $current_path . '?' . $new_query;

	// Redirect to new path. This is a permanent redirect.
	wp_safe_redirect( $redirect_path, 301 );
	exit;
}
