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
import '../../../front-end/components/footer/style.scss';

/**
 * Children blocks
 */
import * as footerBottom from './children/bottom';
import * as footerColumn from './children/column';
import * as footerColumnTitle from './children/column-title';
import * as footerCopyright from './children/copyright';
import * as footerIcon from './children/icon';
import * as footerLogo from './children/logo';
import * as footerMiddle from './children/middle';
import * as footerNavigation from './children/navigation';
import * as footerNavigationItem from './children/navigation-item';
import * as footerPaymentOptions from './children/payment-options';
import * as footerSocialLink from './children/social-link';
import * as footerSocialLinks from './children/social-links';
import * as footerTop from './children/top';
import * as footerAssociations from './children/associations';
import * as footerAssociationLink from './children/association-link';

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

	// Register children blocks.
	registerBlockType( footerBottom.name, footerBottom.settings );
	registerBlockType( footerColumn.name, footerColumn.settings );
	registerBlockType( footerColumnTitle.name, footerColumnTitle.settings );
	registerBlockType( footerCopyright.name, footerCopyright.settings );
	registerBlockType( footerIcon.name, footerIcon.settings );
	registerBlockType( footerLogo.name, footerLogo.settings );
	registerBlockType( footerMiddle.name, footerMiddle.settings );
	registerBlockType( footerNavigation.name, footerNavigation.settings );
	registerBlockType( footerNavigationItem.name, footerNavigationItem.settings );
	registerBlockType( footerPaymentOptions.name, footerPaymentOptions.settings );
	registerBlockType( footerSocialLink.name, footerSocialLink.settings );
	registerBlockType( footerSocialLinks.name, footerSocialLinks.settings );
	registerBlockType( footerTop.name, footerTop.settings );
	registerBlockType( footerAssociations.name, footerAssociations.settings );
	registerBlockType( footerAssociationLink.name, footerAssociationLink.settings );
};
