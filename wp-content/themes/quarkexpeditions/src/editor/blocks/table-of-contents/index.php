<?php
/**
 * Block: Table of Contents.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TableOfContents;

use WP_Block;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

const COMPONENT  = 'table-of-contents';
const BLOCK_NAME = 'quark/table-of-contents';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Initialize Attributes.
	$component_attributes = [
		'title'    => $attributes['title'],
		'contents' => [],
	];

	// Get the post content.
	$post_content = get_the_content();

	// Check if post content is empty.
	if ( empty( $post_content ) ) {
		return $content;
	}

	// Load the post content into a dom document.
	$dom                   = new DOMDocument();
	$libxml_previous_state = libxml_use_internal_errors( true );
	$is_dom_loaded         = $dom->loadHTML( $post_content, LIBXML_COMPACT );

	// Check if dom is loaded.
	if ( $is_dom_loaded ) {
		// Clear errors and restore previous state.
		libxml_clear_errors();
		libxml_use_internal_errors( $libxml_previous_state );

		// Get xpath object to query the DOMDocument.
		$xpath    = new DOMXPath( $dom );
		$headings = $xpath->query( '//h2' );

		// Check if headings is a DOMNodeList.
		if ( $headings instanceof DOMNodeList ) {
			foreach ( $headings as $heading ) {
				// Check instance type.
				if ( $heading instanceof DOMNode ) {
					$component_attributes['contents'][] = [
						// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
						'title'  => $heading->textContent,
						'anchor' => sanitize_title( $heading->textContent ),
						// phpcs:enable
					];
				}
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
