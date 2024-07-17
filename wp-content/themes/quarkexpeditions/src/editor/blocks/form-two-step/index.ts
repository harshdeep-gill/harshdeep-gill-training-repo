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
import '../../../front-end/components/form-two-step/style.scss';

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
 * Children.
 */
import * as expeditionName from './children/expedition-name';
import * as landingForm from './children/landing-form';
import * as modalForm from './children/modal-form';
import * as mostImportantFactors from './children/most-important-factors';
import * as paxCount from './children/pax-count';
import * as season from './children/season';
import * as subRegion from './children/sub-region';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( expeditionName.name, expeditionName.settings );
	registerBlockType( landingForm.name, landingForm.settings );
	registerBlockType( modalForm.name, modalForm.settings );
	registerBlockType( mostImportantFactors.name, mostImportantFactors.settings );
	registerBlockType( paxCount.name, paxCount.settings );
	registerBlockType( season.name, season.settings );
	registerBlockType( subRegion.name, subRegion.settings );
};
