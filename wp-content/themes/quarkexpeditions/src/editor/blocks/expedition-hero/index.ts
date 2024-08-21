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
import '../../../front-end/components/two-columns/style.scss';
import '../../../front-end/components/expedition-details/style.scss';
import '../../../front-end/components/hero-card-slider/style.scss';

/**
 * Children.
 */
import * as expeditionHeroContent from './children/content';
import * as expeditionHeroContentLeft from './children/content-left';
import * as expeditionHeroContentRight from './children/content-right';

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

	// Register children.
	registerBlockType( expeditionHeroContent.name, expeditionHeroContent.settings );
	registerBlockType( expeditionHeroContentLeft.name, expeditionHeroContentLeft.settings );
	registerBlockType( expeditionHeroContentRight.name, expeditionHeroContentRight.settings );
};
