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
import '../../../front-end/components/lp-footer/style.scss';

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
import * as lpFooterIcon from './children/icon';
import * as lpSocialLinksItem from './children/social-links-item';
import * as lpSocialLinks from './children/social-links';
import * as lpFooterColumn from './children/column';
import * as lpFooteritem from './children/lp-footer-item';

/**
 * Initializations.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register child block.
	registerBlockType( lpFooterIcon.name, lpFooterIcon.settings );
	registerBlockType( lpSocialLinksItem.name, lpSocialLinksItem.settings );
	registerBlockType( lpSocialLinks.name, lpSocialLinks.settings );
	registerBlockType( lpFooterColumn.name, lpFooterColumn.settings );
	registerBlockType( lpFooteritem.name, lpFooteritem.settings );
};
