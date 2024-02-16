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
		'row_slots' => [],
	];

	// Prepare block data.
	foreach ( $block['innerBlocks'] as $maybe_row_block ) {
		// Check for row block.
		if (
			empty( $maybe_row_block['innerBlocks'] )
			|| ! is_array( $maybe_row_block['innerBlocks'] )
			|| 'quark/lp-footer-row' !== $maybe_row_block['blockName']
		) {
			continue;
		}

		// Columns for this row.
		$row_slots = '';

		// Build Column inner content.
		foreach ( $maybe_row_block['innerBlocks'] as $maybe_column_block ) {
			// Check for inner block.
			if (
				empty( $maybe_column_block['innerBlocks'] )
				|| ! is_array( $maybe_column_block['innerBlocks'] )
				|| 'quark/lp-footer-column' !== $maybe_column_block['blockName']
			) {
				continue;
			}

			// Initialize columns content.
			$column_content = '';

			// Build column content.
			foreach ( $maybe_column_block['innerBlocks'] as $column_inner_block ) {

				// Check for footer social links block.
				if ( 'quark/lp-footer-social-links' === $column_inner_block['blockName'] ) {
					// Fetch social link attributes.
					foreach ( $column_inner_block['innerBlocks'] as $social_link ) {
						// Add component to slot.
						$links[] = [
							'type' => $social_link['attrs']['type'] ?? 'facebook',
							'url'  => $social_link['attrs']['url'] ?? '',
						];
					}

					// Add component to slot.
					$column_content .= quark_get_component(
						'parts.social-links',
						[
							'links' => $links ?? [],
						],
					);
				} elseif ( 'quark/lp-footer-icon' === $column_inner_block['blockName'] ) {
					$column_content .= quark_get_component(
						'lp-footer.icon',
						[
							'name' => $column_inner_block['attrs']['icon'] ?? '',
						]
					);
				} else {
					$column_content .= render_block( $column_inner_block );
				}
			}

			// Add the column to the list.
			$row_slots .= quark_get_component(
				'lp-footer.column',
				[
					'slot' => $column_content,
				]
			);
		}

		// Prepare Rows data.
		$attributes['row_slots'][] = $row_slots;
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
