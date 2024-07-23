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
import '../../../front-end/components/header/css/header.scss';
import '../../../front-end/components/header/css/nav.scss';

/**
 * Block name.
 */
export const { name }: { name: string } = metadata;

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	...metadata,
	icon: 'menu',
	edit,
	save,
};

/**
 * Children.
 */
import * as contactButton from './children/contact-button';
import * as ctaButtons from './children/cta-buttons';
import * as megaMenu from './children/mega-menu';
import * as menuItems from './children/menu-item';
import * as menuItemContent from './children/menu-item-content';
import * as menuItemContentColumn from './children/menu-item-content-column';
import * as menuItemFeaturedSection from './children/menu-item-featured-section';
import * as raqButton from './children/raq-button';
import * as secondaryMenuItem from './children/secondary-menu-item';
import * as secondaryNav from './children/secondary-nav';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( contactButton.name, contactButton.settings );
	registerBlockType( ctaButtons.name, ctaButtons.settings );
	registerBlockType( megaMenu.name, megaMenu.settings );
	registerBlockType( menuItems.name, menuItems.settings );
	registerBlockType( menuItemContent.name, menuItemContent.settings );
	registerBlockType( menuItemContentColumn.name, menuItemContentColumn.settings );
	registerBlockType( menuItemFeaturedSection.name, menuItemFeaturedSection.settings );
	registerBlockType( raqButton.name, raqButton.settings );
	registerBlockType( secondaryMenuItem.name, secondaryMenuItem.settings );
	registerBlockType( secondaryNav.name, secondaryNav.settings );
};
