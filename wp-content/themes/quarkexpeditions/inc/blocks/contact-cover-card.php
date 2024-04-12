<?php
/**
 * Block: Contact Cover Card.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\ContactCoverCard;

const BLOCK_NAME = 'quark/contact-cover-card';
const COMPONENT  = 'parts.contact-cover-card';

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
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Check if we have an image.
	if ( empty( $block['attrs']['image']['id'] ) ) {
		return '';
	}

	// Initialize attributes.
	$attributes = [
		'image_id' => 0,
		'content'  => [],
	];

	// Add Image Id.
	$attributes['image_id'] = $block['attrs']['image']['id'];

	// Loop through innerblocks and build attributes.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Title.
		if ( 'quark/contact-cover-card-title' === $inner_block['blockName'] ) {
			// Initialize title.
			$title = [];

			// Add block type.
			$title['type'] = 'title';

			// Add title.
			$title['title'] = ! empty( $inner_block['attrs']['title'] ) ? $inner_block['attrs']['title'] : '';

			// Add title to children.
			$attributes['content'][] = $title;
		}

		// Description.
		if ( 'quark/content-cover-card-description' === $inner_block['blockName'] ) {
			// Initialize description.
			$description = [];

			// Add block type.
			$description['type'] = 'description';

			// Add description.
			$description['description'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );

			// Add title to children.
			$attributes['content'][] = $description;
		}

		// Contact Info.
		if ( 'quark/contact-cover-card-contact-info' === $inner_block['blockName'] ) {
			// Initialize contact_info.
			$contact_info = [];

			// Add block type.
			$contact_info['type'] = 'contact-info';

			// Loop through the inner blocks.
			if ( ! empty( $inner_block['innerBlocks'] ) ) {
				foreach ( $inner_block['innerBlocks'] as $inner_inner_block ) {
					$contact_info['children'][] = [
						'label'  => $inner_inner_block['attrs']['label'] ?? '',
						'value'  => $inner_inner_block['attrs']['value'] ?? '',
						'url'    => ! empty( $inner_inner_block['attrs']['url']['url'] ) ? $inner_inner_block['attrs']['url']['url'] : '',
						'target' => ! empty( $inner_inner_block['attrs']['url']['newWindow'] ) ? '_blank' : '',
					];
				}
			}

			// Add title to children.
			$attributes['content'][] = $contact_info;
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
}
