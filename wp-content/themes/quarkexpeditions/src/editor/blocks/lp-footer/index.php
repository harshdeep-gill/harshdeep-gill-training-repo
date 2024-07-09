<?php
/**
 * Block: LP footer.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\LPFooter;

use WP_Block;

const COMPONENT = 'parts.lp-footer';

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
 * @param mixed[]  $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block data.
 *
 * @return string
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Build component attributes.
	$component_attributes = [
		'rows' => [],
	];

	// Prepare block data.
	foreach ( $block->parsed_block['innerBlocks'] as $maybe_row_block ) {
		// Check for row block.
		if (
			empty( $maybe_row_block['innerBlocks'] )
			|| ! is_array( $maybe_row_block['innerBlocks'] )
			|| 'quark/lp-footer-row' !== $maybe_row_block['blockName']
		) {
			continue;
		}

		// Columns for this row.
		$columns = [];

		// Prepare row data.
		foreach ( $maybe_row_block['innerBlocks'] as $maybe_column_block ) {
			// Check for column block.
			if (
				empty( $maybe_column_block['innerBlocks'] )
				|| ! is_array( $maybe_column_block['innerBlocks'] )
				|| 'quark/lp-footer-column' !== $maybe_column_block['blockName']
			) {
				continue;
			}

			// Initialize columns content.
			$column_contents = [];

			// Prepare column data.
			foreach ( $maybe_column_block['innerBlocks'] as $column_inner_block ) {
				// Check for footer social links block.
				if ( 'quark/lp-footer-social-links' === $column_inner_block['blockName'] ) {
					// Initialize links.
					$links = [];

					// Fetch social link attributes.
					foreach ( $column_inner_block['innerBlocks'] as $social_link ) {
						// Add component to slot.
						$links[] = [
							'type' => $social_link['attrs']['type'] ?? 'facebook',
							'url'  => $social_link['attrs']['url'] ?? '',
						];
					}

					// Add component to slot.
					$column_contents[] = [
						'type'       => 'social-links',
						'attributes' => [
							'links' => $links,
						],
					];
				} elseif ( 'quark/lp-footer-icon' === $column_inner_block['blockName'] ) {
					$column_contents[] = [
						'type'       => 'icon',
						'attributes' => [
							'name' => $column_inner_block['attrs']['icon'] ?? '',
						],
					];
				} else {
					$column_contents[] = [
						'type'       => 'block',
						'attributes' => [
							'content' => render_block( $column_inner_block ),
						],
					];
				}
			}

			// Initialize url.
			$column_url = '';

			// Check if url is present.
			if ( isset( $maybe_column_block['attrs'] ) && ! empty( $maybe_column_block['attrs']['url'] ) ) {
				$column_url = $maybe_column_block['attrs']['url'];
			}

			// Add the column to the list.
			$columns[] = [
				'url'      => $column_url,
				'contents' => $column_contents,
			];
		}

		// Add row to component.
		$component_attributes['rows'][] = $columns;
	}

	// Render the block markup.
	return quark_get_component( COMPONENT, $component_attributes );
}
