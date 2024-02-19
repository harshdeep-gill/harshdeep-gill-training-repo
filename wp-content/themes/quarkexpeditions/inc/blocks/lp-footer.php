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
		'rows' => [],
	];

	// Columns for old block.
	$old_block_columns = [];

	// Old block flag.
	$is_old_block = false;

	// Prepare block data.
	foreach ( $block['innerBlocks'] as $maybe_row_block ) {
		// Check for row block.
		if (
			empty( $maybe_row_block['innerBlocks'] )
			|| ! is_array( $maybe_row_block['innerBlocks'] )
		) {
			continue;
		}

		// Process new block markup.
		if ( 'quark/lp-footer-row' === $maybe_row_block['blockName'] ) {
			// Columns for this row.
			$columns = [];

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

				// Get column contents.
				$columns[] = get_column_contents( $maybe_column_block );
			}

			// Prepare Rows data.
			$attributes['rows'][] = $columns;
		} elseif ( 'quark/lp-footer-column' === $maybe_row_block['blockName'] ) {
			// Old block.
			$is_old_block = true;

			// Build columns.
			$old_block_columns[] = get_column_contents( $maybe_row_block );
		}
	}

	// Old block markup built.
	if ( $is_old_block ) {
		$attributes['rows'] = [ $old_block_columns ];
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}

/**
 * Parses and bulids column block.
 *
 * @param mixed[] $maybe_column_block The column block.
 *
 * @return mixed[]
 */
function get_column_contents( array $maybe_column_block = [] ): array {
	// Initialize columns content.
	$column_contents = [];

	// @phpstan-ignore-next-line Build column content.
	foreach ( $maybe_column_block['innerBlocks'] as $column_inner_block ) {

		// @phpstan-ignore-next-line Check for footer social links block.
		if ( 'quark/lp-footer-social-links' === $column_inner_block['blockName'] ) {
			$links = [];

			// @phpstan-ignore-next-line Fetch social link attributes.
			foreach ( $column_inner_block['innerBlocks'] as $social_link ) {
				// Add component to slot.
				$links[] = [
					// @phpstan-ignore-next-line
					'type' => $social_link['attrs']['type'] ?? 'facebook',
					// @phpstan-ignore-next-line
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
					// @phpstan-ignore-next-line
					'name' => $column_inner_block['attrs']['icon'] ?? '',
				],
			];
		} else {
			$column_contents[] = [
				'type'       => 'block',
				'attributes' => [
					// @phpstan-ignore-next-line
					'content' => render_block( $column_inner_block ),
				],
			];
		}
	}

	// Add the column to the list.
	return $column_contents;
}
