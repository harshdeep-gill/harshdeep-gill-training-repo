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
import '../../../front-end/components/product-cards/style.scss';

/**
 * Child blocks.
 */
import * as card from './children/card';
import * as reviews from './children/reviews';
import * as title from './children/title';
import * as description from './children/description';
import * as itinerary from './children/itinerary';
import * as price from './children/price';
import * as subtitle from './children/subtitle';
import * as buttons from './children/buttons';

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
	registerBlockType( reviews.name, reviews.settings );
	registerBlockType( title.name, title.settings );
	registerBlockType( description.name, description.settings );
	registerBlockType( itinerary.name, itinerary.settings );
	registerBlockType( subtitle.name, subtitle.settings );
	registerBlockType( buttons.name, buttons.settings );

	// Check if the block should be disabled on the China site.
	if ( ! ( window?.quarkSiteData && window.quarkSiteData?.isChinaSite ) ) {
		registerBlockType( price.name, price.settings );
	}
};
