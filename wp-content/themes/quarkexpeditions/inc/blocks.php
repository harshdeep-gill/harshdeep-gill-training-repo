<?php
/**
 * Blocks functions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks;

/**
 * Setup.
 *
 * @return void
 */
function setup(): void {
	// Fire hooks to bootstrap theme blocks.
	add_action( 'init', __NAMESPACE__ . '\\register_blocks' );
	add_action( 'template_redirect', __NAMESPACE__ . '\\customize_core_blocks_output' );
}

/**
 * Register all blocks.
 *
 * @return void
 */
function register_blocks(): void {
	// Path to blocks file.
	$blocks_path = __DIR__ . '/../dist/blocks.php';

	// Get blocks from file, if it exists.
	if ( ! file_exists( $blocks_path ) ) {
		// Block file does not exist, bail.
		return;
	}

	// Load blocks.
	$blocks = require $blocks_path;

	// Check if we have blocks.
	if ( empty( $blocks ) || ! is_array( $blocks ) ) {
		return;
	}

	// Register blocks.
	foreach ( $blocks as $path => $namespace ) {
		// Include and bootstrap blocks.
		$block_path = __DIR__ . '/../' . $path;

		// Check if block file exists.
		if ( ! file_exists( $block_path ) ) {
			continue;
		}

		// Require block.
		require_once $block_path;

		// Get callable function name.
		$callable = $namespace . '\\bootstrap';

		// If the function is callable, then call the function.
		if ( is_callable( $callable ) ) {
			call_user_func( $callable );
		}
	}
}

/**
 * Customize the output of core blocks.
 *
 * @return void
 */
function customize_core_blocks_output(): void {
	// Fire hook to manipulate block output.
	add_filter( 'render_block', __NAMESPACE__ . '\\change_core_block_output', 20, 2 );

	// Remove odd WordPress class behavior.
	remove_filter( 'render_block', 'wp_render_layout_support_flag' );
	add_filter( 'render_block', __NAMESPACE__ . '\\add_alignment_classes', 10, 2 );
}

/**
 * Customize core block output.
 *
 * @param string|null $block_content Original block content.
 * @param mixed[]     $block         Block data.
 *
 * @return string|null
 */
function change_core_block_output( ?string $block_content = '', array $block = [] ): ?string {
	// If block name is empty, return block content.
	if ( empty( $block['blockName'] ) ) {
		return $block_content;
	}

	// Modify core/columns block's css column classes based on availability of inner blocks.
	if ( 'core/columns' === $block['blockName'] && ! empty( $block['innerBlocks'] ) ) {
		$block_content = str_replace( '"wp-block-columns', '"wp-block-columns wp-block-columns--cols-' . count( $block['innerBlocks'] ), strval( $block_content ) );
	}

	// Return block content.
	return $block_content;
}

/**
 * Add alignment classes to blocks.
 *
 * @param string|null $block_content Original block content.
 * @param mixed[]     $block         Block data.
 *
 * @return string|null
 */
function add_alignment_classes( ?string $block_content = '', array $block = [] ): ?string {
	// Check for block name.
	if ( empty( $block['blockName'] ) ) {
		return $block_content;
	}

	// Build classes.
	$classes = [];

	// Construct classes based on block attributes.
	if ( ! empty( $block['attrs']['layout']['type'] ) ) {
		$classes = [
			'quark-block--display-' . $block['attrs']['layout']['type'],
		];

		// Add class to justify content based on block layout attrs.
		if ( ! empty( $block['attrs']['layout']['justifyContent'] ) ) {
			$classes[] = 'quark-block--justify-content-' . $block['attrs']['layout']['justifyContent'];
		}

		// Add class to modify vertical alignment based on block layout attrs.
		if ( ! empty( $block['attrs']['layout']['verticalAlignment'] ) ) {
			$classes[] = 'quark-block--vertical-alignment-' . $block['attrs']['layout']['verticalAlignment'];
		}

		// Add class to modify orientation based on block layout attrs.
		if ( ! empty( $block['attrs']['layout']['orientation'] ) ) {
			$classes[] = 'quark-block--orientation-' . $block['attrs']['layout']['orientation'];
		}
	}

	// Check for classes.
	if ( empty( $classes ) ) {
		return $block_content;
	}

	// Buttons.
	if ( 'core/buttons' === $block['blockName'] ) {
		$block_content = str_replace(
			'"wp-block-buttons',
			sprintf(
				'"wp-block-buttons %s',
				implode( ' ', $classes ),
			),
			strval( $block_content )
		);
	}

	// Return updated content.
	return $block_content;
}
