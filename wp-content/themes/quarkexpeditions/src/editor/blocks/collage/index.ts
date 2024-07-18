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
import '../../../front-end/components/collage/style.scss';
import '../../../front-end/components/media-lightbox/style.scss';

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
import * as item from './children/collage-media-item';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child block.
	registerBlockType( item.name, item.settings );
};
