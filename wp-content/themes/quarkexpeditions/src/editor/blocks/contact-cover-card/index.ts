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
import '../../../front-end/components/contact-cover-card/style.scss';
import './editor.scss';

/**
 * Child blocks.
 */
import * as title from './children/title';
import * as description from './children/description';
import * as contactInfo from './children/contact-info';
import * as contactInfoItem from './children/contact-info-item';

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
	registerBlockType( description.name, description.settings );
	registerBlockType( contactInfo.name, contactInfo.settings );
	registerBlockType( contactInfoItem.name, contactInfoItem.settings );
};
