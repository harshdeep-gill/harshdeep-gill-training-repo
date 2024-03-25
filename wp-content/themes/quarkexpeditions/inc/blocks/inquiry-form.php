<?php
/**
 * Block: Inquiry Form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\InquiryForm;

const BLOCK_NAME = 'quark/inquiry-form';
const COMPONENT  = 'inquiry-form';

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
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Setup valid form types and a default.
	$valid_form_types = [ 'inquiry-form', 'inquiry-form-compact' ];
	$form_type        = 'inquiry-form';

	// Set form type if not empty or default.
	if ( ! empty( $block['attrs']['formType'] ) && in_array( $block['attrs']['formType'], $valid_form_types, true ) ) {
		$form_type = $block['attrs']['formType'];
	}

	// Component name for the form.
	$form_component = sprintf( 'forms.%s', $form_type );

	// Build attributes.
	$attributes = [
		'slot' => quark_get_component(
			$form_component,
			[ 'thank_you_page' => $block['attrs']['thankYouPageUrl'] ?? '' ]
		),
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
