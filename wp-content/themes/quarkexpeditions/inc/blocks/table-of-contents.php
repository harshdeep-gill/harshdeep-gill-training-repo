<?php
/**
 * Block: Table of Contents.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\TableOfContents;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;

const BLOCK_NAME = 'quark/table-of-contents';
const COMPONENT  = 'table-of-contents';

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
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Initialize items.
	$attributes = [
		'title'    => $block['attrs']['title'] ?? '',
		'contents' => [],
	];

	// Load the post content into a dom document.
	$dom                   = new DOMDocument();
	$libxml_previous_state = libxml_use_internal_errors( true );
	$is_dom_loaded         = $dom->loadHTML( get_the_content(), LIBXML_COMPACT );

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
					$attributes['contents'][] = [
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
	return quark_get_component( COMPONENT, $attributes );
}
