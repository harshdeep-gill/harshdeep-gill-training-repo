/**
 * WordPress dependencies.
 */
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import edit from './edit';
import save from './save';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	...metadata,
	edit,
	save,
};

/**
 * Child blocks.
 */
import * as card from './children/card';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child blocks.
	registerBlockType( card.name, card.settings );
};
