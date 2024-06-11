/**
 * WordPress dependencies.
 */
import { __ } from '@wordpress/i18n';
import { BlockConfiguration, registerBlockType } from '@wordpress/blocks';
import {
	InnerBlocks,
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

/**
 * Internal dependencies.
 */
import icons from '../icons';

/**
 * External dependencies.
 */
import classnames from 'classnames';

/**
 * Styles.
 */
import '../../../front-end/components/header/css/header.scss';
import '../../../front-end/components/header/css/nav.scss';
import './editor.scss';

/**
 * Child blocks.
 */
import * as megaMenu from './mega-menu';
import * as secondaryNav from './secondary-nav';
import * as ctaButtons from './cta-buttons';

/**
 * Register child blocks.
 */
registerBlockType( megaMenu.name, megaMenu.settings );
registerBlockType( secondaryNav.name, secondaryNav.settings );
registerBlockType( ctaButtons.name, ctaButtons.settings );

/**
 * Block name.
 */
export const name: string = 'quark/header';

/**
 * Block configuration settings.
 */
export const settings: BlockConfiguration = {
	apiVersion: 3,
	title: __( 'Header', 'qrk' ),
	description: __( 'Header block.', 'qrk' ),
	category: 'layout',
	icon: 'menu',
	keywords: [
		__( 'header', 'qrk' ),
	],
	attributes: {},
	supports: {
		alignWide: false,
		anchor: true,
		className: false,
		html: false,
		customClassName: false,
	},
	edit( { className }: BlockEditAttributes ) {
		// eslint-disable-next-line react-hooks/rules-of-hooks
		const blockProps = useBlockProps( {
			className: classnames( className, 'header', 'full-width' ),
		} );

		// eslint-disable-next-line react-hooks/rules-of-hooks
		const innerBlockProps = useInnerBlocksProps( { ...blockProps },
			{
				allowedBlocks: [ megaMenu.name, secondaryNav.name, ctaButtons.name ],
				template: [ [ megaMenu.name ], [ secondaryNav.name ], [ ctaButtons.name ] ],
				orientation: 'horizontal',
				templateLock: 'all',
			}
		);

		// Return block.
		return (
			<header { ...blockProps }>
				<a href="/" className="header__logo">
					{ icons.logo }
				</a>
				<nav { ...innerBlockProps } />
			</header>
		);
	},
	save() {
		// Save InnerBlock Content.
		return <InnerBlocks.Content />;
	},
};
