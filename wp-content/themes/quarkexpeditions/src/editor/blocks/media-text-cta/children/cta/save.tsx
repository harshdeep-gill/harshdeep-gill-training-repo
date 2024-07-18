/**
 * WordPress dependencies.
 */
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Save component.
 */
export default function Save() {
	// Save inner block content.
	return <InnerBlocks.Content />;
}
