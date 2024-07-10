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
import '../../../front-end/components/lp-offer-masthead/style.scss';
import './editor.scss';

/**
 * Child blocks.
 */
import * as caption from './children/caption';
import * as content from './children/content';
import * as offerImage from './children/offer-image';

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
	registerBlockType( caption.name, caption.settings );
	registerBlockType( content.name, content.settings );
	registerBlockType( offerImage.name, offerImage.settings );
};
