<?php
/**
 * Block: LP footer.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPFooter;

const BLOCK_NAME = 'quark/lp-footer';
const COMPONENT  = 'parts.lp-footer';

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

	// Build component attributes.
	$attributes = [
		'columns' => [],
	];

	// Prepare block data.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Check for inner block.
		if (
			empty( $inner_block['innerBlocks'] )
			|| ! is_array( $inner_block['innerBlocks'] )
			|| 'quark/lp-footer-column' !== $inner_block['blockName']
		) {
			continue;
		}

		// Initialize inner content var.
		$inner_content = '';

		// Build inner item content.
		foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {

			// Check for footer social links block.
			if ( 'quark/lp-footer-social-links' === $inner_inner_block['blockName'] ) {
				// Fetch social link attributes.
				foreach ( $inner_inner_block['innerBlocks'] as $social_link ) {
					// Add component to slot.
					$links[] = [
						'type' => $social_link['attrs']['type'] ?? 'facebook',
						'url'  => $social_link['attrs']['url'] ?? '',
					];
				}

				// Add component to slot.
				$inner_content .= quark_get_component(
					'parts.social-links',
					[
						'links' => $links ?? [],
					],
				);
			} else {
				$inner_content .= render_block( $inner_inner_block );
			}
		}

		// prepare columns data.
		$attributes['columns'][] = $inner_content;
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
