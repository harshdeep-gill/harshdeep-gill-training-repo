/**
 * WordPress dependencies.
 */
import {
	BlockConfiguration,
	registerBlockType,
} from '@wordpress/blocks';

/**
 * Styles.
 */
import '../../../front-end/components/tabs/style.scss';
import '../../../front-end/components/section/style.scss';
import '../../../front-end/components/drawer/style.scss';
import '../../../front-end/components/media-detail-cards/style.scss';
import '../../../front-end/components/media-description-cards/style.scss';

/**
 * Vendor dependencies.
 */
import '../../../vendor/tp-tabs';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import edit from './edit';

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
	// Register block.
	registerBlockType( name, settings );
};
