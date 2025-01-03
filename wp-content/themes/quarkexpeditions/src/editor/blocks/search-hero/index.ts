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
 * Styles.
 */
import '../../../front-end/components/search-hero/style.scss';

/**
 * Children.
 */
import * as contentLeft from './children/content-left';
import * as contentRight from './children/content-right';
import * as overline from './children/overline';
import * as title from './children/title';
import * as titleBicolor from './children/title-bicolor';
import * as subtitle from './children/subtitle';
import * as titleContainer from './children/title-container';
import * as searchBar from './children/search-bar';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( contentLeft.name, contentLeft.settings );
	registerBlockType( contentRight.name, contentRight.settings );
	registerBlockType( overline.name, overline.settings );
	registerBlockType( title.name, title.settings );
	registerBlockType( titleBicolor.name, titleBicolor.settings );
	registerBlockType( subtitle.name, subtitle.settings );
	registerBlockType( titleContainer.name, titleContainer.settings );
	registerBlockType( searchBar.name, searchBar.settings );
};
