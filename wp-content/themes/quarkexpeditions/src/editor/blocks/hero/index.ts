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
 * Styles.
 */
import '../../../front-end/components/hero/style.scss';

/**
 * Children.
 */
import * as heroContent from './children/hero-content';
import * as heroContentLeft from './children/hero-content-left';
import * as heroContentRight from './children/hero-content-right';
import * as heroDescription from './children/hero-description';
import * as heroTitle from './children/hero-title';
import * as heroTitleBicolor from './children/hero-title-bicolor';
import * as heroSubtitle from './children/hero-subtitle';
import * as overline from './children/overline';
import * as heroTextGraphic from './children/hero-text-graphic';
import * as heroCircleBadge from './children/hero-circle-badge';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( heroContent.name, heroContent.settings );
	registerBlockType( heroContentLeft.name, heroContentLeft.settings );
	registerBlockType( heroContentRight.name, heroContentRight.settings );
	registerBlockType( heroDescription.name, heroDescription.settings );
	registerBlockType( heroTitle.name, heroTitle.settings );
	registerBlockType( heroTitleBicolor.name, heroTitleBicolor.settings );
	registerBlockType( heroTextGraphic.name, heroTextGraphic.settings );
	registerBlockType( heroSubtitle.name, heroSubtitle.settings );
	registerBlockType( overline.name, overline.settings );
	registerBlockType( heroCircleBadge.name, heroCircleBadge.settings );
};
