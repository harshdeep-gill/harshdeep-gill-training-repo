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
import '../../../front-end/components/product-departures-card/style.scss';
import './editor.scss';

/**
 * Child blocks.
 */
import * as title from './children/title';
import * as departures from './children/departures';
import * as cta from './children/cta';
import * as dates from './children/dates';

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
	registerBlockType( title.name, title.settings );
	registerBlockType( departures.name, departures.settings );
	registerBlockType( cta.name, cta.settings );
	registerBlockType( dates.name, dates.settings );
};
