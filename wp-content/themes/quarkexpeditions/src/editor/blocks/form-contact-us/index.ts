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
	// Check if the block should be disabled on the China site.
	if ( window?.quarkSiteData && window.quarkSiteData?.isChinaSite && metadata.supports?.disableOnChinaSite ) {
		// bail early.
		return;
	}

	// Register block.
	registerBlockType( name, settings );
};
