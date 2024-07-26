/**
 * WordPress dependencies.
 */
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies.
 */
import metadata from './block.json';
import edit from './edit';
import save from './save';

/**
 * Styles.
 */
import '../../../front-end/components/secondary-navigation/style.scss';

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
import * as secondaryNavigationItem from './children/secondary-navigation-item';
import * as secondaryNavigationMenu from './children/secondary-navigation-menu';

/**
 * Initialization.
 */
export const init = (): void => {
	// Register block.
	registerBlockType( name, settings );

	// Register children.
	registerBlockType( secondaryNavigationItem.name, secondaryNavigationItem.settings );
	registerBlockType( secondaryNavigationMenu.name, secondaryNavigationMenu.settings );
};
