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
 * Styles.
 */
import '../../../front-end/components/logo-grid/style.scss';

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
 * Child block.
 */
import * as logoGridItem from './children/logo-grid-item';

/**
 * Initializations.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child block.
	registerBlockType( logoGridItem.name, logoGridItem.settings );
};
