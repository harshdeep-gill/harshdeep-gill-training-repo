<?php
/**
 * Block: Video Icons card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\VideoIconsCard;

const COMPONENT = 'video-icons-card';

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
 * @param mixed[]   $attributes The block attributes.
 * @param string    $content    The block content.
 * @param \WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', \WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof \WP_Block ) {
		return $content;
	}

	// Initialize the slot.
	$slot = '';

	// Build the slot.
	foreach ( $block->inner_blocks as $maybe_icon_columns_block ) {
		// Check for block.
		if ( ! $maybe_icon_columns_block instanceof \WP_Block ) {
			continue;
		}

		// Check for inner block.
		if ( 'quark/icon-columns' !== $maybe_icon_columns_block->name ) {
			continue;
		}

		// Only render one icon columns block.
		$slot = quark_get_component(
			COMPONENT . '.icons',
			[
				'slot' => $maybe_icon_columns_block->render(),
			]
		);

		// Stop the loop.
		break;
	}

	// Image id for thumbnail.
	$image_id = 0;

	// Check if it exists.
	if ( ! empty( $attributes ) && is_array( $attributes['image'] ) && isset( $attributes['image']['id'] ) ) {
		// Get the image id.
		$image_id = $attributes['image']['id'];
	}

	// Build component attributes.
	$component_attributes = [
		'slot'     => $slot,
		'variant'  => $attributes['variant'] ?? '',
		'image_id' => $image_id,
		'title'    => $attributes['title'] ?? '',
		'url'      => $attributes['url'] ?? '',
	];

	// Return the component.
	return quark_get_component( COMPONENT, $component_attributes );
}
