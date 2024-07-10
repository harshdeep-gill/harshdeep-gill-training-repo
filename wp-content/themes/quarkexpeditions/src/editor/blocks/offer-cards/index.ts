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
import '../../../front-end/components/offer-cards/style.scss';

/**
 * Child blocks.
 */
import * as card from './children/card';
import * as title from './children/title';
import * as help from './children/help';
import * as cta from './children/cta';
import * as promotion from './children/promotion';

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
	registerBlockType( card.name, card.settings );
	registerBlockType( title.name, title.settings );
	registerBlockType( help.name, help.settings );
	registerBlockType( cta.name, cta.settings );
	registerBlockType( promotion.name, promotion.settings );
};
