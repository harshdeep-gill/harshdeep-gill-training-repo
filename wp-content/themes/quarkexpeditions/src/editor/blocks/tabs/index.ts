/**
 * WordPress dependencies.
 */
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';

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
export const settings: Omit<BlockConfiguration, 'providesContext'> = {
	...metadata,
	edit,
	save,
};

/**
 * Styles.
 */
import '../../../front-end/components/tabs/style.scss';

/**
 * Children.
 */
import * as tab from './children/tab';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( tab.name, tab.settings );
};
