<?php
/**
 * Block: Two step form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\FormTwoStep;

use WP_Block;
use WP_Block_List;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

const COMPONENT                    = 'form-two-step';
const FORM_FIELD_BLOCK_NAME_PREFIX = 'form-field-';
const BLOCK_NAME                   = 'quark/form-two-step';

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
			'render_callback'   => __NAMESPACE__ . '\\render',
			'skip_inner_blocks' => true,
		]
	);

	// Disable translation for this block.
	add_filter( 'qrk_translation_disable_blocks', __NAMESPACE__ . '\\disable_translation' );
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize attributes.
	$component_attributes = [
		'background_color' => $attributes['backgroundColor'],
		'thank_you_page'   => $attributes['thankYouPageUrl'],
		'countries'        => get_countries(),
		'states'           => get_states(),
		'hidden_fields'    => [
			'polar_region' => $attributes['polarRegion'],
			'ship'         => $attributes['ship'],
			'expedition'   => $attributes['expedition'],
			'season'       => $attributes['season'],
		],
		'fields'           => [],
	];

	// Loop through the form steps.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Check if the block is a form.
		if ( 'quark/form-two-step-landing-form' === $inner_block->name ) {
			if ( $inner_block->inner_blocks instanceof WP_Block_List ) {
				// Loop through the form fields.
				foreach ( $inner_block->inner_blocks as $field ) {
					// Check for block.
					if ( ! $field instanceof WP_Block ) {
						continue;
					}

					// Initialize current field data.
					$current_field_data = [];

					// Build current form field data.
					$current_field_data['field_type']  = explode( FORM_FIELD_BLOCK_NAME_PREFIX, $field->name )[1];
					$current_field_data['label']       = $field->attributes['label'];
					$current_field_data['options']     = parse_field_options( $field->attributes['options'] );
					$current_field_data['is_required'] = $field->attributes['isRequired'];

					// Add current field data to attribtues.
					$component_attributes['fields'][] = $current_field_data;
				}
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
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

/**
 * Disable translation for this block.
 *
 * @param string[] $blocks The block names.
 *
 * @return string[] The block names.
 */
function disable_translation( array $blocks = [] ): array {
	// Add block name to disable translation.
	$blocks[] = BLOCK_NAME;

	// Return block names.
	return $blocks;
}
