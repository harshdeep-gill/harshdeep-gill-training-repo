/**
 * WordPress dependencies.
 */
import { BlockConfiguration } from '@wordpress/blocks';

/**
 * Internal dependencies.
 */
import metadata from './block.json';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	...metadata,
	edit() {
		// Return block.
		return (
			<p>Test</p>
		);
	},
};
