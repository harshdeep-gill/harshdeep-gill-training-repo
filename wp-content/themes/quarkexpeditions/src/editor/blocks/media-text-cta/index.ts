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
import '../../../front-end/components/media-text-cta/style.scss';
import '../../../front-end/components/fancy-video/style.scss';

/**
 * Child blocks.
 */
import * as cta from './children/cta';
import * as secondaryText from './children/secondary-text';
import * as contentTitle from './children/content-title';
import * as overline from './children/overline';
import * as description from './children/description';

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
	registerBlockType( cta.name, cta.settings );
	registerBlockType( secondaryText.name, secondaryText.settings );
	registerBlockType( contentTitle.name, contentTitle.settings );
	registerBlockType( overline.name, overline.settings );
	registerBlockType( description.name, description.settings );
};
