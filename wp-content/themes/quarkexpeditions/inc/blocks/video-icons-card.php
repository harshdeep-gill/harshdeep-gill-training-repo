<?php
/**
 * Block: Video Icons card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\VideoIconsCard;

const BLOCK_NAME = 'quark/video-icons-card';
const COMPONENT  = 'video-icons-card';

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
	if ( BLOCK_NAME !== $block['blockName'] || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Initialize the slot.
	$slot = '';

	// Build the slot.
	foreach ( $block['innerBlocks'] as $maybe_icon_columns_block ) {
		// Check for inner block.
		if ( 'quark/icon-columns' !== $maybe_icon_columns_block['blockName'] ) {
			continue;
		}

		// Only render one icon columns block.
		$slot = quark_get_component(
			COMPONENT . '.icons',
			[
				'slot' => render_block( $maybe_icon_columns_block ),
			]
		);
		break;
	}

	// Image id for thumbnail.
	$image_id = '';

	// Check if it exists.
	if ( isset( $block['attrs']['image'] ) && is_array( $block['attrs']['image'] ) ) {
		$image_id = $block['attrs']['image']['id'];
	}

	// Build component attributes.
	$attributes = [
		'slot'     => $slot,
		'variant'  => $block['attrs']['variant'] ?? '',
		'image_id' => $image_id ?? '',
		'title'    => $block['attrs']['title'] ?? '',
		'url'      => $block['attrs']['url'] ?? '',
	];

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
