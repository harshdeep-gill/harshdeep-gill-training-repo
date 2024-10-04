<?php
/**
 * Block: Form.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Form;

use WP_Block;

use function Quark\Leads\Forms\get_countries;
use function Quark\Leads\Forms\get_states;

const COMPONENT_JOB_APPLICATION = 'form-job-application';
const COMPONENT_CONTACT_US      = 'form-contact-us';
const COMPONENT_NEWSLETTER      = 'form-newsletter';

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
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content The block content.
 * @param WP_Block|null $block The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Check for form.
	if ( 'none' === $attributes['form'] ) {
		return $content;
	}

	// Component attributes.
	$component_attributes = [
		'countries' => get_countries(),
		'states'    => get_states(),
	];

	// Set the form component.
	switch ( $attributes['form'] ) {

		// Job Application.
		case 'job-application':
			$component = COMPONENT_JOB_APPLICATION;
			break;

		// Contact Us.
		case 'contact-us':
			$component = COMPONENT_CONTACT_US;
			break;

		// Newsletter.
		case 'newsletter':
			$component = COMPONENT_NEWSLETTER;
			break;

		// Default.
		default:
			return $content;
	}

	// Return built component.
	return quark_get_component( $component, $component_attributes );
}
