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
import '../../../front-end/components/media-content-card/style.scss';

/**
 * Child blocks.
 */
import * as column from './children/column';
import * as info from './children/content-info';

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
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child block.
	registerBlockType( column.name, column.settings );
	registerBlockType( info.name, info.settings );
};
