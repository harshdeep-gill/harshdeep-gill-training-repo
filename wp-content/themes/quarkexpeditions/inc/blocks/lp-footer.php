<?php
/**
 * Block: LP footer.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPFooter;

const BLOCK_NAME = 'quark/lp-footer';
const COMPONENT  = 'lp-footer';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap() : void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register() : void {
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
function render( ?string $content = null, array $block = [] ) : null | string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize slot.
	$slot = '';

	// Build slot.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Check for inner block.
		if (
			empty( $inner_block['innerBlocks'] )
			|| ! is_array( $inner_block['innerBlocks'] )
			|| 'quark/lp-footer-column' !== $inner_block['blockName']
		) {
			continue;
		}

		// Initialize inner content.
		$inner_content = '';

		// Build inner item content.
		foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
			// Check for list block.
			if ( 'core/list' === $inner_inner_block['blockName'] ) {
				$inner_content .= quark_get_component(
					COMPONENT . '.links',
					[
						'slot' => render_block( $inner_inner_block ),
					],
				);
			} elseif ( 'quark/lp-footer-featured-on' === $inner_inner_block['blockName'] ) {
				$inner_content .= quark_get_component(
					COMPONENT . '.featured-on',
					[
						'title' => $inner_inner_block['attrs']['title'],
						'slot'  => implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) ),
					],
				);
			} elseif ( 'quark/lp-footer-social-links' === $inner_inner_block['blockName'] ) {
				// Init social link slots.
				$social_link_slots = '';

				// Build social link slots.
				foreach ( $inner_inner_block['innerBlocks'] as $social_link ) {
					// Check if we have an url.
					if ( empty( $social_link['attrs']['url'] ) ) {
						continue;
					}

					// Add component to slot.
					$social_link_slots .= quark_get_component(
						COMPONENT . '.social-link',
						[
							'type' => $social_link['attrs']['type'] ?? 'facebook',
							'url'  => $social_link['attrs']['url'],
						]
					);
				}

				// Add component to slot.
				$inner_content .= quark_get_component(
					COMPONENT . '.social-links',
					[
						'slot' => $social_link_slots,
					],
				);
			} else {
				$inner_content .= render_block( $inner_inner_block );
			}
		}

		// Build component attributes.
		$slot .= quark_get_component(
			COMPONENT . '.column',
			[
				'slot' => $inner_content,
			],
		);
	}

	// Build component attributes.
	$attributes = [
		'slot' => quark_get_component(
			COMPONENT . '.columns',
			[
				'slot' => $slot,
			],
		),
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
