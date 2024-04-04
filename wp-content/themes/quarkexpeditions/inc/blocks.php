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
	// List of blocks to register.
	$blocks = [
		'Section'          => 'section.php',
		'LPHeader'         => 'lp-header.php',
		'TwoColumns'       => 'two-columns.php',
		'IconInfoColumns'  => 'icon-info-columns.php',
		'ReviewsCarousel'  => 'reviews-carousel.php',
		'Hero'             => 'hero.php',
		'InquiryForm'      => 'inquiry-form.php',
		'LogoGrid'         => 'logo-grid.php',
		'LPFooter'         => 'lp-footer.php',
		'IconColumns'      => 'icon-columns.php',
		'Collage'          => 'collage.php',
		'ReviewCards'      => 'review-cards.php',
		'VideoIconsCard'   => 'video-icons-card.php',
		'SeasonHighlights' => 'season-highlights.php',
		'SimpleCards'      => 'simple-cards.php',
		'ProductCards'     => 'product-cards.php',
		'IconBadge'        => 'icon-badge.php',
		'LPFormModalCta'   => 'lp-form-modal-cta.php',
		'MediaContentCard' => 'media-content-card.php',
		'Button'           => 'button.php',
		'ContactCoverCard' => 'contact-cover-card.php',
		'OfferCards'       => 'offer-cards.php',
	];

	// Register blocks.
	foreach ( $blocks as $namespace => $file_name ) {
		// Include and bootstrap blocks.
		require_once __DIR__ . '/blocks/' . $file_name;

		// Get callable function name.
		$callable = __NAMESPACE__ . '\\' . $namespace . '\\bootstrap';

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
