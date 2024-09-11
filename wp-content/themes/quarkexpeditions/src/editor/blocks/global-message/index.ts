/**
 * Wordpress dependencies.
 */
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';

/**
 * Internal dependancies.
 */
import metadata from './block.json';
import edit from './edit';

/**
 * Styles.
 */
import '../../../front-end/components/global-message/style.scss';

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
};

/**
 * Initialization.
 */
export const init = (): void => {
	// Rigester block.
	registerBlockType( name, settings );
};
