<?php
/**
 * Block: Two step form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

const BLOCK_NAME                   = 'quark/form-two-step';
const COMPONENT                    = 'form-two-step';
const FORM_FIELD_BLOCK_NAME_PREFIX = 'form-field-';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize attributes.
	$attributes = [
		'background_color' => $block['attrs']['backgroundColor'] ?? 'black',
		'thank_you_page'   => $block['attrs']['thankYouPageUrl'] ?? '',
		'countries'        => get_countries(),
		'states'           => get_states(),
		'hidden_fields'    => [
			'polar_region' => $block['attrs']['polarRegion'] ?? '',
			'ship'         => $block['attrs']['ship'] ?? '',
			'expedition'   => $block['attrs']['expedition'] ?? '',
		],
		'fields'           => [],
	];

	// Loop through the form steps.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		if ( 'quark/form-two-step-landing-form' === $inner_block['blockName'] ) {
			if ( ! empty( $inner_block['innerBlocks'] ) ) {
				// Loop through the form fields.
				foreach ( $inner_block['innerBlocks'] as $field ) {
					$current_field_data = [];

					// Build current form field data.
					$current_field_data['field_type']  = explode( FORM_FIELD_BLOCK_NAME_PREFIX, $field['blockName'] )[1];
					$current_field_data['label']       = $field['attrs']['label'] ?? '';
					$current_field_data['options']     = parse_field_options( $field['attrs']['options'] ?? '' );
					$current_field_data['is_required'] = $field['attrs']['isRequired'] ?? false;

					// Add current field data to attribtues.
					$attributes['fields'][] = $current_field_data;
				}
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}

/**
 * Parse the field options.
 *
 * @param string $options String of options.
 *
 * @return array<int, array{text: string, value: string}>
 */
function parse_field_options( string $options = '' ): array {
	// Check if the options exist.
	if ( empty( $options ) ) {
		return [];
	}

	// Initialize options.
	$options_data = [];

	// Split the options string by the `\n` delimeter.
	$parsed_options = explode( PHP_EOL, $options );

	// Loop thorugh the parsed options.
	foreach ( $parsed_options as $option ) {
		// Split each option using the `::` delimeter.
		$option_parts = explode( '::', $option );

		// Add to options data.
		$options_data[] = [
			'text'  => trim( $option_parts[0] ?? '' ),
			'value' => trim( $option_parts[1] ?? '' ),
		];
	}

	// Return parsed options.
	return $options_data;
}
