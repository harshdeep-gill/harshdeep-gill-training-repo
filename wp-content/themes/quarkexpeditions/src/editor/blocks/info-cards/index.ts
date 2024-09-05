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
import '../../../front-end/components/info-cards/style.scss';

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
 * Child blocks.
 */
import * as InfoCardsCard from './children/card';
import * as InfoCardsOverline from './children/overline';
import * as InfoCardsCta from './children/cta';
import * as InfoCardsDescription from './children/description';
import * as InfoCardsTitle from './children/title';
import * as InfoCardsTag from './children/tag';

/**
 * Initializations.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child block.
	registerBlockType( InfoCardsCard.name, InfoCardsCard.settings );
	registerBlockType( InfoCardsOverline.name, InfoCardsOverline.settings );
	registerBlockType( InfoCardsCta.name, InfoCardsCta.settings );
	registerBlockType( InfoCardsDescription.name, InfoCardsDescription.settings );
	registerBlockType( InfoCardsTitle.name, InfoCardsTitle.settings );
	registerBlockType( InfoCardsTag.name, InfoCardsTag.settings );
};
