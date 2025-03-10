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
import '../../../front-end/components/highlights/style.scss';

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
import * as item from './children/highlight-item';
import * as title from './children/title';
import * as overline from './children/overline';
import * as text from './children/text';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child block.
	registerBlockType( item.name, item.settings );
	registerBlockType( title.name, title.settings );
	registerBlockType( overline.name, overline.settings );
	registerBlockType( text.name, text.settings );
};
