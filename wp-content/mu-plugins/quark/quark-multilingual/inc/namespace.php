<?php
/**
 * Namespace functions.
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Hooks and filter.
	add_filter( 'travelopia_translation_adapter', __NAMESPACE__ . '\\get_translation_adapter', 10, 1 );
}

/**
 * Get translation adapter.
 *
 * @return string Translation adapter.
 */
function get_translation_adapter(): string {
	// Return DeepL as translation adapter.
	return 'deepl';
}
