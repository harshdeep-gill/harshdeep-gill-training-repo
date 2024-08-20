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
import '../../../front-end/components/specifications/style.scss';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import edit from './edit';

/**
 * External dependencies.
 */
const { typenow } = window;

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
	// Check for the intended post type.
	if ( typenow && 'qrk_ship' !== typenow ) {
		// Do not register block.
		return;
	}

	// Register block.
	registerBlockType( name, settings );
};
